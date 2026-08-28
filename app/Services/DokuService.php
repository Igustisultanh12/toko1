<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DokuService
{
    /**
     * AMBIL KONFIGURASI DARI PUSAT KOMANDO DENGAN MULTI-TIER FALLBACK
     */
    private function getConfig()
    {
        $settings = Setting::pluck('value', 'key')->all();

        $clientId = $settings['doku_client_id'] 
            ?? $settings['doku_mall_id'] 
            ?? env('DOKU_CLIENT_ID') 
            ?? env('DOKU_MALL_ID') 
            ?? config('services.doku.client_id');

        $secretKey = $settings['doku_secret_key'] 
            ?? $settings['doku_shared_key'] 
            ?? $settings['doku_api_key'] 
            ?? env('DOKU_SECRET_KEY') 
            ?? env('DOKU_SHARED_KEY') 
            ?? env('DOKU_API_KEY') 
            ?? config('services.doku.secret_key');

        // Deteksi mode produksi / sandbox
        $isProduction = filter_var($settings['doku_is_production'] ?? env('DOKU_IS_PRODUCTION') ?? config('services.doku.is_production', false), FILTER_VALIDATE_BOOLEAN);
        $defaultBaseUrl = $isProduction ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';

        $baseUrl = $settings['doku_base_url'] ?? env('DOKU_BASE_URL') ?? config('services.doku.base_url') ?? $defaultBaseUrl;
        if (empty($baseUrl) || !str_starts_with($baseUrl, 'http')) {
            $baseUrl = $defaultBaseUrl;
        }

        return [
            'client_id'  => trim($clientId ?? ''),
            'secret_key' => trim($secretKey ?? ''),
            'base_url'   => rtrim($baseUrl, '/'),
        ];
    }

    /**
     * GENERATE QRIS TRANSAKSI KASIR (POS)
     */
    public function generateQris($sale)
    {
        $config = $this->getConfig();
        $targetPath = '/checkout/v1/payment'; 

        $customerName = !empty($sale->customer_name) ? preg_replace('/[^A-Za-z0-9\s]/', '', $sale->customer_name) : 'Pelanggan Umum';
        if (trim($customerName) === '') {
            $customerName = 'Pelanggan Umum';
        }
        
        $body = [
            'order' => [
                'amount' => (int) round($sale->total_amount),
                'invoice_number' => $sale->transaction_number,
                'callback_url' => route('cashier.pos.index') . '?status=return&sale_id=' . $sale->id,
            ],
            'payment' => [
                'payment_due_date' => 60,
                'payment_method_types' => ['QRIS'], 
            ],
            'customer' => [
                'id'    => 'CUST-' . ($sale->user_id ?? 'GUEST') . '-' . $sale->id,
                'name'  => substr($customerName, 0, 50),
                'email' => 'admin@sultanweb.id',
            ]
        ];

        $response = $this->executeRequest($targetPath, $body, $config);

        if (isset($response['response']['payment']['url'])) {
            return $response['response']['payment']['url'];
        }

        Log::error('DOKU POS API Error: ' . json_encode($response));
        return null;
    }

    /**
     * GENERATE QRIS UNTUK PESANAN ONLINE PUBLIK
     */
    public function generateOrderQris($order)
    {
        $config = $this->getConfig();
        $targetPath = '/checkout/v1/payment'; 
        
        $customerName = !empty($order->customer_name) ? preg_replace('/[^A-Za-z0-9\s]/', '', $order->customer_name) : 'Pelanggan Online';
        if (trim($customerName) === '') {
            $customerName = 'Pelanggan Online';
        }

        $body = [
            'order' => [
                'amount' => (int) round($order->total_amount),
                'invoice_number' => $order->order_number,
                'callback_url' => route('order.receipt', $order->order_number),
            ],
            'payment' => [
                'payment_due_date' => 60, // 60 menit
                'payment_method_types' => ['QRIS'], 
            ],
            'customer' => [
                'id'    => 'CUST-ONLINE-' . $order->id,
                'name'  => substr($customerName, 0, 50),
                'email' => 'admin@sultanweb.id',
            ]
        ];

        $response = $this->executeRequest($targetPath, $body, $config);

        if (isset($response['response']['payment']['url'])) {
            return $response['response']['payment']['url'];
        }

        Log::error('DOKU Order API Error: ' . json_encode($response));
        return null;
    }

    /**
     * EKSEKUSI REQUEST (Protokol HMAC-SHA256 DOKU Jokul API)
     */
    private function executeRequest($targetPath, $body, $config)
    {
        if (empty($config['client_id']) || empty($config['secret_key']) || empty($config['base_url'])) {
            Log::error('DOKU Config missing: ', [
                'has_client_id'  => !empty($config['client_id']),
                'has_secret_key' => !empty($config['secret_key']),
                'base_url'       => $config['base_url'] ?? 'EMPTY'
            ]);
            return null;
        }

        $clientId  = trim($config['client_id']);
        $secretKey = trim($config['secret_key']);

        $requestId = (string) Str::uuid();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $digest = base64_encode(hash('sha256', $jsonBody, true));

        $signatureString = "Client-Id:" . $clientId . "\n" .
                           "Request-Id:" . $requestId . "\n" .
                           "Request-Timestamp:" . $timestamp . "\n" .
                           "Request-Target:" . $targetPath . "\n" .
                           "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        try {
            $fullUrl = $config['base_url'] . $targetPath;
            $response = Http::timeout(20)->withHeaders([
                'Client-Id'         => $clientId,
                'Request-Id'        => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature'         => "HMACSHA256=" . $signature,
                'Content-Type'      => 'application/json'
            ])->withBody($jsonBody, 'application/json')->post($fullUrl);

            $result = $response->json();
            Log::info("DOKU Request to {$fullUrl} (HTTP {$response->status()}):", [
                'body' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error("DOKU Connection Error ({$targetPath}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * CEK STATUS TRANSAKSI KE DOKU API SECARA LANGSUNG (ACTIVE POLLING / FALLBACK WEBHOOK)
     */
    public function checkPaymentStatus($invoiceNumber)
    {
        $config = $this->getConfig();
        if (empty($config['client_id']) || empty($config['secret_key']) || empty($config['base_url'])) {
            return null;
        }

        $targetPath = '/orders/v1/status/' . $invoiceNumber;
        $clientId  = trim($config['client_id']);
        $secretKey = trim($config['secret_key']);

        $requestId = (string) Str::uuid();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        // Format signature untuk GET request (tanpa digest body)
        $signatureString = "Client-Id:" . $clientId . "\n" .
                           "Request-Id:" . $requestId . "\n" .
                           "Request-Timestamp:" . $timestamp . "\n" .
                           "Request-Target:" . $targetPath;

        $signature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        try {
            $fullUrl = $config['base_url'] . $targetPath;
            $response = Http::timeout(6)->withHeaders([
                'Client-Id'         => $clientId,
                'Request-Id'        => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature'         => "HMACSHA256=" . $signature,
            ])->get($fullUrl);

            $result = $response->json();
            Log::info("DOKU Direct Status Check for {$invoiceNumber} (HTTP {$response->status()}):", [
                'body' => $result
            ]);

            if ($response->successful() && isset($result['transaction']['status'])) {
                $status = strtoupper($result['transaction']['status']);
                return in_array($status, ['SUCCESS', 'PAID', 'COMPLETED', 'SETTLED', 'OK', 'SUCCESSFUL', 'APPROVED']);
            }

            return false;
        } catch (\Exception $e) {
            Log::warning("DOKU Status Check Error for {$invoiceNumber}: " . $e->getMessage());
            return false;
        }
    }
}