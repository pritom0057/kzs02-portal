<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzService
{
    private string $storeId;
    private string $storePassword;
    private string $apiUrl;
    private string $validationUrl;

    public function __construct()
    {
        $this->storeId       = config('sslcommerz.store_id');
        $this->storePassword = config('sslcommerz.store_password');
        $this->apiUrl        = config('sslcommerz.api_url');
        $this->validationUrl = config('sslcommerz.validation_url');
    }

    /**
     * Initiate payment and return the gateway redirect URL.
     * Returns null on failure.
     */
    public function initiate(array $params): ?string
    {
        $payload = array_merge([
            'store_id'       => $this->storeId,
            'store_passwd'   => $this->storePassword,
            'currency'       => 'BDT',
            'shipping_method'=> 'NO',
            'product_name'   => 'KZS 2002 Reunion Registration',
            'product_category'=> 'Event',
            'product_profile'=> 'general',
            // Address required by SSLCommerz even for events
            'cus_add1'       => 'Bangladesh',
            'cus_city'       => 'Kushtia',
            'cus_country'    => 'Bangladesh',
        ], $params);

        try {
            $response = Http::asForm()->post($this->apiUrl, $payload);
            $data = $response->json();

            if (($data['status'] ?? '') === 'SUCCESS' && !empty($data['GatewayPageURL'])) {
                return $data['GatewayPageURL'];
            }

            Log::error('SSLCommerz initiate failed', ['response' => $data]);
            return null;
        } catch (\Throwable $e) {
            Log::error('SSLCommerz HTTP error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validate IPN/success callback by re-querying SSLCommerz.
     */
    public function validate(string $valId, string $amount, string $currency = 'BDT'): bool
    {
        try {
            $response = Http::get($this->validationUrl, [
                'val_id'       => $valId,
                'store_id'     => $this->storeId,
                'store_passwd' => $this->storePassword,
                'v'            => 1,
                'format'       => 'json',
            ]);

            $data = $response->json();

            if (($data['status'] ?? '') !== 'VALID' && ($data['status'] ?? '') !== 'VALIDATED') {
                return false;
            }

            // Verify amount matches to prevent tampering
            if (abs((float)$data['amount'] - (float)$amount) > 1) {
                Log::warning('SSLCommerz amount mismatch', [
                    'expected' => $amount,
                    'received' => $data['amount'],
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validation error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
