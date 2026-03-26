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

        if (Str::length($normalizedMessage) < 3) {
            return response()->json([
                'reply' => 'Please ask with a bit more detail, for example: "best budget phone" or "laptop for study".',
                'source' => 'assistant',
                'products' => [],
            ]);
        }

        $products = $this->findRelevantProducts($message);

        $reply = $this->buildFallbackReply($products);
        $source = 'fallback';

        if (!empty(config('services.groq.api_key'))) {
            try {
                $reply = $this->askGroq($message, $products);
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
        $canQueryByRawMessage = Str::length(trim($message)) >= 3;

        $terms = collect(preg_split('/\s+/', Str::lower($message)) ?: [])
            ->map(fn ($term) => trim($term))
            ->filter(fn ($term) => Str::length($term) >= 3)
            ->unique()
            ->take(8)
            ->values();

        return Product::query()
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
                        ->orWhere('description', 'like', '%'.$term.'%');
                }

                if (!$canQueryByRawMessage && $terms->isEmpty()) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('average_rating')
            ->limit(5)
            ->get();
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
    private function askGroq(string $message, Collection $products): string
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
                        'content' => 'You are the shopping assistant for E-Kampot Shop. Answer based on provided catalog context. Be concise, helpful, and honest. If context is insufficient, say so and ask a clarifying question. Keep responses under 120 words.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Customer question: {$message}\n\nCatalog context:\n{$catalogContext}",
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
    private function buildFallbackReply(Collection $products): string
    {
        if ($products->isEmpty()) {
            return 'I could not find a close match right now. Try product type, brand, budget, or category (for example: "phone under $400").';
        }

        $lines = $products->take(3)->map(function (Product $product): string {
            return sprintf(
                '- %s (%s) %s',
                $product->name,
                '$'.number_format((float) $product->price, 2),
                $product->in_stock ? 'in stock' : 'out of stock'
            );
        })->implode("\n");

        return "Here are matching products:\n{$lines}\nYou can open product details from the suggestions below.";
    }
}
