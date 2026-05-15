<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Answer user questions and suggest relevant products.
     */
    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:800'],
        ]);

        $message = trim($validated['message']);
        $normalizedMessage = Str::of($message)->lower()->squish()->toString();

        if ($this->isGreeting($normalizedMessage)) {
            return response()->json([
                'reply' => 'Hi. I can help you find products by category, budget, brand, or stock status. Try: "phone under $300" or "in-stock running shoes".',
                'source' => 'assistant',
                'products' => [],
            ]);
        }

        if ($this->isSmallTalk($normalizedMessage)) {
            return response()->json([
                'reply' => 'I am here to help. You can ask general store questions (payment, delivery, returns) or search products, for example: "in-stock phone under $300".',
                'source' => 'assistant',
                'products' => [],
            ]);
        }

        if (Str::length($normalizedMessage) < 2) {
            return response()->json([
                'reply' => 'Please ask with a bit more detail, for example: "best budget phone" or "laptop for study".',
                'source' => 'assistant',
                'products' => [],
            ]);
        }

        $intent = $this->detectIntent($normalizedMessage);
        // Always attempt product retrieval so direct product-name queries (e.g., "MacBook")
        // still return results even when intent keywords are minimal.
        $products = $this->findRelevantProducts($message);

        // If we found products, treat the request as product-oriented unless explicitly mixed.
        if ($products->isNotEmpty() && $intent === 'general') {
            $intent = 'product';
        }

        $reply = $this->buildFallbackReply($message, $products, $intent);
        $source = 'fallback';

        if (!empty(config('services.groq.api_key'))) {
            try {
                $reply = $this->askGroq($message, $products, $intent);
                $source = 'groq';
            } catch (\Throwable $e) {
                Log::warning('Groq chatbot request failed: '.$e->getMessage());
            }
        }

        return response()->json([
            'reply' => $reply,
            'source' => $source,
            'products' => $products->map(function (Product $product): array {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => '$'.number_format((float) $product->price, 2),
                    'in_stock' => (bool) $product->in_stock,
                    'image_url' => $product->image_url,
                    'url' => route('products.show', $product->slug),
                ];
            })->values(),
        ]);
    }

    /**
     * Query product catalog by user question terms.
     */
    private function findRelevantProducts(string $message): Collection
    {
        $terms = $this->extractSearchTerms($message);
        $filters = $this->extractProductFilters($message);
        $normalizedMessage = Str::of($message)->lower()->squish()->toString();

        $canQueryByRawMessage = Str::length(trim($message)) >= 3;
        $hasPriceFilter = isset($filters['max_price']) || isset($filters['min_price']);
        $hasStockFilter = isset($filters['in_stock']);

        if ($terms->isEmpty() && !$hasPriceFilter && !$hasStockFilter) {
            return collect();
        }

        $query = Product::query()
            ->with('categories:id,name')
            ->active()
            ->where(function ($query) use ($message, $terms, $canQueryByRawMessage): void {
                if ($canQueryByRawMessage) {
                    $query->where('name', 'like', '%'.$message.'%')
                        ->orWhere('short_description', 'like', '%'.$message.'%')
                        ->orWhere('description', 'like', '%'.$message.'%');
                }

                foreach ($terms as $term) {
                    $query->orWhere('name', 'like', '%'.$term.'%')
                        ->orWhere('short_description', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%')
                        ->orWhereHas('categories', function ($categoryQuery) use ($term): void {
                            $categoryQuery->where('name', 'like', '%'.$term.'%');
                        });
                }

                if (!$canQueryByRawMessage && $terms->isEmpty()) {
                    $query->whereRaw('1 = 0');
                }
            });

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (($filters['in_stock'] ?? false) === true) {
            $query->where('in_stock', true);
        }

        if (($filters['sort'] ?? null) === 'price_asc') {
            $query->orderBy('price');
        } elseif (($filters['sort'] ?? null) === 'price_desc') {
            $query->orderByDesc('price');
        }

        $products = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('average_rating')
            ->limit(20)
            ->get();

        return $products
            ->sortByDesc(function (Product $product) use ($normalizedMessage, $terms): int {
                return $this->scoreProductMatch($product, $normalizedMessage, $terms);
            })
            ->values()
            ->take(5);
    }

    /**
     * Assign relevance score so exact product-name queries rank first.
     */
    private function scoreProductMatch(Product $product, string $normalizedMessage, Collection $terms): int
    {
        $name = Str::of((string) $product->name)->lower()->squish()->toString();
        $score = 0;

        if ($normalizedMessage !== '' && $name === $normalizedMessage) {
            $score += 1000;
        }

        if ($normalizedMessage !== '' && Str::startsWith($name, $normalizedMessage)) {
            $score += 350;
        }

        if ($normalizedMessage !== '' && Str::contains($name, $normalizedMessage)) {
            $score += 220;
        }

        foreach ($terms as $term) {
            if (Str::length($term) < 2) {
                continue;
            }

            if ($name === $term) {
                $score += 180;
                continue;
            }

            if (Str::startsWith($name, $term)) {
                $score += 90;
            }

            if (Str::contains($name, $term)) {
                $score += 60;
            }
        }

        // Keep useful ranking tie-breakers after textual relevance.
        if ((bool) $product->is_featured) {
            $score += 12;
        }

        $score += (int) round((float) $product->average_rating * 4);

        if ((bool) $product->in_stock) {
            $score += 6;
        }

        return $score;
    }

    /**
     * Detect whether user asks for product search, general help, or both.
     */
    private function detectIntent(string $normalizedMessage): string
    {
        $productSignals = [
            'buy', 'price', 'cheap', 'budget', 'under', 'below', 'over', 'stock', 'available',
            'recommend', 'best', 'product', 'products', 'phone', 'laptop', 'shoe', 'watch',
            'cart', 'checkout', 'category', 'in stock', 'out of stock',
        ];

        $generalSignals = [
            'help', 'how', 'what can you do', 'about', 'contact', 'support', 'shipping',
            'delivery', 'return', 'refund', 'policy', 'payment method', 'bakong',
            'language', 'dark mode',
        ];

        $hasProductSignal = collect($productSignals)
            ->contains(fn ($signal) => Str::contains($normalizedMessage, $signal));

        $hasGeneralSignal = collect($generalSignals)
            ->contains(fn ($signal) => Str::contains($normalizedMessage, $signal));

        if ($hasProductSignal && $hasGeneralSignal) {
            return 'mixed';
        }

        if ($hasProductSignal) {
            return 'product';
        }

        return 'general';
    }

    /**
     * Extract concise searchable terms from the message.
     */
    private function extractSearchTerms(string $message): Collection
    {
        $stopWords = [
            'the', 'and', 'for', 'with', 'from', 'this', 'that', 'want', 'need', 'show',
            'give', 'please', 'about', 'under', 'below', 'over', 'more', 'than', 'stock',
            'available', 'best', 'cheap', 'budget', 'find', 'search', 'product', 'products',
            'hi', 'hey', 'hello', 'bro', 'yo', 'sup', 'thanks', 'thank', 'ok', 'okay',
        ];

        return collect(preg_split('/[^\p{L}\p{N}]+/u', Str::lower($message)) ?: [])
            ->map(fn ($term) => trim($term))
            ->filter(fn ($term) => Str::length($term) >= 3)
            ->reject(fn ($term) => in_array($term, $stopWords, true))
            ->unique()
            ->take(10)
            ->values();
    }

    /**
     * Detect casual small-talk messages that should not trigger product search.
     */
    private function isSmallTalk(string $message): bool
    {
        $phrases = [
            'hi bro', 'hello bro', 'hey bro', 'yo bro', 'sup',
            'how are you', 'what are you doing', 'thank you', 'thanks bro',
        ];

        if (in_array($message, $phrases, true)) {
            return true;
        }

        // Treat very short casual messages as small-talk.
        $tokens = collect(preg_split('/[^\p{L}\p{N}]+/u', $message) ?: [])
            ->filter()
            ->values();

        if ($tokens->isEmpty() || $tokens->count() > 4) {
            return false;
        }

        $casualWords = ['hi', 'hello', 'hey', 'yo', 'bro', 'sup', 'thanks', 'thank', 'ok', 'okay'];

        return $tokens->every(fn ($token) => in_array(Str::lower((string) $token), $casualWords, true));
    }

    /**
     * Pull flexible product filters from natural language.
     */
    private function extractProductFilters(string $message): array
    {
        $normalized = Str::lower($message);
        $filters = [];

        if (preg_match('/(?:under|below|less than|up to)\s*\$?\s*(\d+(?:\.\d+)?)/i', $normalized, $matches)) {
            $filters['max_price'] = (float) $matches[1];
        }

        if (preg_match('/(?:over|above|more than|at least)\s*\$?\s*(\d+(?:\.\d+)?)/i', $normalized, $matches)) {
            $filters['min_price'] = (float) $matches[1];
        }

        if (Str::contains($normalized, ['in stock', 'available now', 'available'])) {
            $filters['in_stock'] = true;
        }

        if (Str::contains($normalized, ['cheapest', 'lowest price', 'budget'])) {
            $filters['sort'] = 'price_asc';
        }

        if (Str::contains($normalized, ['premium', 'expensive', 'high end'])) {
            $filters['sort'] = 'price_desc';
        }

        return $filters;
    }

    /**
     * Detect simple greetings so we do not treat them as product search.
     */
    private function isGreeting(string $message): bool
    {
        $greetings = [
            'hi',
            'hello',
            'hey',
            'good morning',
            'good afternoon',
            'good evening',
            'yo',
            'hi bot',
            'hello bot',
            'sousdey',
            'sous dey',
            'ជំរាបសួរ',
            'សួស្តី',
        ];

        return in_array($message, $greetings, true);
    }

    /**
     * Ask Groq model with compact product context.
     */
    private function askGroq(string $message, Collection $products, string $intent): string
    {
        $catalogContext = $products->map(function (Product $product): string {
            $summary = Str::limit(strip_tags((string) ($product->short_description ?: $product->description ?: '')), 140);
            $categories = $product->categories->pluck('name')->implode(', ');

            return sprintf(
                '- %s | $%s | %s | Categories: %s | URL: %s | Summary: %s',
                $product->name,
                number_format((float) $product->price, 2),
                $product->in_stock ? 'In stock' : 'Out of stock',
                $categories ?: 'General',
                route('products.show', $product->slug),
                $summary ?: 'No summary available'
            );
        })->implode("\n");

        if ($catalogContext === '') {
            $catalogContext = '- No strongly matched products for this query.';
        }

        $assistantMode = match ($intent) {
            'product' => 'Focus on product discovery and recommendations from catalog context.',
            'mixed' => 'Answer the general part first, then provide product recommendations from catalog context.',
            default => 'Focus on general shopping support and store guidance. Use catalog only if relevant.',
        };

        $response = Http::timeout(25)
            ->withToken((string) config('services.groq.api_key'))
            ->acceptJson()
            ->post(rtrim((string) config('services.groq.base_url'), '/').'/chat/completions', [
                'model' => (string) config('services.groq.model', 'llama-3.1-8b-instant'),
                'temperature' => 0.3,
                'max_tokens' => 350,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are the shopping assistant for E-Kampot Shop. '.$assistantMode.' Be concise, helpful, and honest. Never invent products that are not in catalog context. If context is insufficient, say so and ask a clarifying question. Keep responses under 120 words.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Customer question: {$message}\n\nDetected intent: {$intent}\n\nCatalog context:\n{$catalogContext}",
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Groq API returned status '.$response->status());
        }

        $content = (string) data_get($response->json(), 'choices.0.message.content', '');

        if (trim($content) === '') {
            throw new \RuntimeException('Groq API returned empty content.');
        }

        return trim($content);
    }

    /**
     * Deterministic fallback reply when model is unavailable.
     */
    private function buildFallbackReply(string $message, Collection $products, string $intent): string
    {
        if ($intent === 'general') {
            return 'I can help with shopping support and store guidance, like payment methods, delivery, account, checkout, and how to find products. You can also ask for product search, for example: "in-stock phone under $300".';
        }

        if ($products->isEmpty()) {
            return 'I could not find a close product match yet. Try adding product type, budget, brand, or stock preference, for example: "in-stock phone under $400" or "budget laptop for study".';
        }

        $lines = $products->take(3)->map(function (Product $product): string {
            return sprintf(
                '- %s (%s) %s',
                $product->name,
                '$'.number_format((float) $product->price, 2),
                $product->in_stock ? 'in stock' : 'out of stock'
            );
        })->implode("\n");

        $prefix = $intent === 'mixed'
            ? 'I answered your request and found these relevant products:'
            : 'Here are matching products:';

        return "{$prefix}\n{$lines}\nYou can open product details from the suggestions below.";
    }
}
