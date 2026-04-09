<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfo;

class BakongService
{
    protected string $accountId;
    protected string $merchantName;
    protected string $merchantCity;
    protected string $apiToken;
    protected bool $isTest;
    protected int $expirationMinutes;
    protected string $currency;

    public function __construct()
    {
        $this->accountId = config('bakong.account_id', '');
        $this->merchantName = config('bakong.merchant_name', 'E-Kampot Shop');
        $this->merchantCity = config('bakong.merchant_city', 'KAMPOT');
        $this->apiToken = config('bakong.api_token', '');
        $this->isTest = config('bakong.api_env', 'sandbox') === 'sandbox';
        $this->expirationMinutes = config('bakong.qr_expiration_minutes', 15);
        $this->currency = config('bakong.currency', 'USD');
    }

    /**
     * Generate a KHQR code for an individual payment.
     *
     * @param float $amount The total amount to charge
     * @param string|null $billNumber Optional bill/order number for reference
     * @return array{success: bool, qr: string|null, md5: string|null, message: string|null}
     */
    public function generateQRCode(float $amount, ?string $billNumber = null): array
    {
        try {
            if (empty($this->accountId)) {
                return [
                    'success' => false,
                    'qr' => null,
                    'md5' => null,
                    'message' => 'Bakong account ID is not configured.',
                ];
            }

            $currencyCode = $this->currency === 'KHR'
                ? KHQRData::CURRENCY_KHR
                : KHQRData::CURRENCY_USD;

            // Ensure amount has maximum 2 decimal places to prevent SDK floating point validation errors
            $cleanAmount = round($amount, 2);

            // Expiration timestamp in milliseconds
            $expirationMs = strval(
                floor(microtime(true) * 1000) + ($this->expirationMinutes * 60 * 1000)
            );

            $individualInfo = new IndividualInfo(
                bakongAccountID: $this->accountId,
                merchantName: $this->merchantName,
                merchantCity: $this->merchantCity,
                currency: $currencyCode,
                amount: $cleanAmount,
                expirationTimestamp: $expirationMs,
            );

            // Set bill number if provided
            if ($billNumber) {
                $individualInfo->billNumber = $billNumber;
            }

            $response = BakongKHQR::generateIndividual($individualInfo);

            if ($response->status['code'] === 0) {
                return [
                    'success' => true,
                    'qr' => $response->data['qr'],
                    'md5' => $response->data['md5'],
                    'message' => null,
                ];
            }

            return [
                'success' => false,
                'qr' => null,
                'md5' => null,
                'message' => $response->status['message'] ?? 'Failed to generate QR code.',
            ];

        } catch (\Exception $e) {
            Log::error('BakongService::generateQRCode error: ' . $e->getMessage(), [
                'amount' => $amount,
                'billNumber' => $billNumber,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'qr' => null,
                'md5' => null,
                'message' => 'Failed to generate KHQR code: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check payment status by MD5 hash of the QR string.
     *
     * @param string $md5 The MD5 hash returned during QR generation
     * @return array{success: bool, paid: bool, data: mixed, message: string|null}
     */
    public function checkPaymentStatus(string $md5): array
    {
        try {
            if (empty($this->apiToken)) {
                return [
                    'success' => false,
                    'paid' => false,
                    'data' => null,
                    'message' => 'Bakong API token is not configured. Cannot verify payment status.',
                ];
            }

            // Using Laravel HTTP client instead of SDK to avoid Windows cURL SSL issues
            $url = $this->isTest 
                ? 'https://sit-api-bakong.nbc.gov.kh/v1/check_transaction_by_md5' 
                : 'https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5';

            $httpResponse = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'md5' => $md5,
                ]);

            if ($httpResponse->failed()) {
                throw new \Exception('API request failed: ' . $httpResponse->body());
            }

            $response = $httpResponse->json();

            Log::info('BakongService::checkPaymentStatus response', [
                'md5' => $md5,
                'response' => $response,
            ]);

            // The API response structure
            if (isset($response['responseCode']) && $response['responseCode'] === 0) {
                // responseCode 0 means the request was successful
                // Check the data to see if the transaction was found/paid
                $data = $response['data'] ?? null;

                if ($data && isset($data['hash'])) {
                    // Transaction found — payment confirmed
                    return [
                        'success' => true,
                        'paid' => true,
                        'data' => $data,
                        'message' => 'Payment confirmed.',
                    ];
                }

                // Request succeeded but no transaction found yet
                return [
                    'success' => true,
                    'paid' => false,
                    'data' => $data,
                    'message' => 'Payment not yet received.',
                ];
            }

            return [
                'success' => true,
                'paid' => false,
                'data' => $response['data'] ?? null,
                'message' => $response['responseMessage'] ?? 'Transaction not found.',
            ];

        } catch (\Exception $e) {
            Log::error('BakongService::checkPaymentStatus error: ' . $e->getMessage(), [
                'md5' => $md5,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'paid' => false,
                'data' => null,
                'message' => 'Error checking payment status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if the Bakong API token is configured and valid.
     */
    public function hasValidToken(): bool
    {
        if (empty($this->apiToken)) {
            return false;
        }

        try {
            return !BakongKHQR::isExpiredToken($this->apiToken);
        } catch (\Exception $e) {
            Log::error('BakongService::hasValidToken error: ' . $e->getMessage());
            return false;
        }
    }
}
