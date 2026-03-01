<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class VoucherLookup implements Tool
{
    public function description(): Stringable|string
    {
        return 'Look up shipment tracking status and details using a voucher/tracking code. Use this ONLY when customer provides a tracking code to check shipment status. Returns current status, delivery details, and financial information.';
    }

    public function handle(Request $request): Stringable|string
    {
        Log::info('Received voucher lookup request', ['input' => $request->all()]);
        $validated = $this->validateVoucherCode($request);

        if (isset($validated['error'])) {
            return $validated['error'];
        }

        $voucherCode = $validated['voucher_code'];

        $voucherData = pegasusClient()->getVoucherInformation($voucherCode);

        if (empty($voucherData)) {
            return 'Δεν βρέθηκε αποστολή με τον κωδικό παρακολούθησης που δώσατε. Παρακαλώ ελέγξτε τον αριθμό και δοκιμάστε ξανά.';
        }

        $statuses = pegasusClient()->getVoucherStatuses();
        $statusCode = $voucherData['p014'] ?? null;
        $statusDescription = $statuses[$statusCode] ?? 'Άγνωστη Κατάσταση';

        $structured = $this->transformToAgentFormat($voucherData, $statusDescription);

        return json_encode($structured, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'voucher_code' => $schema->string()
                ->description('The voucher or tracking code for the shipment')
                ->required(),
        ];
    }

    private function transformToAgentFormat(array $voucher, string $status): array
    {
        $isCod = (int) ($voucher['p026'] ?? 0);
        $codAmount = (float) ($voucher['p022'] ?? 0);
        $shippingFee = (float) ($voucher['p012'] ?? 0);
        $payerCode = (int) ($voucher['p123'] ?? 0);

        $totalDue = $this->calculateTotalDueAtDoor($isCod, $codAmount, $shippingFee, $payerCode);

        Log::info('Voucher lookup', [
            'shipment_details' => [
                'tracking_id' => $voucher['p01'] ?? '',
                'delivery_address_area' => $voucher['p0203'] ?? '',
                'weight_kg' => (float) ($voucher['p101'] ?? 0),
                'status' => $status,
            ],
            'financial_summary' => [
                'is_cash_on_delivery' => (bool) $isCod,
                'cash_on_delivery_amount' => $codAmount,
                'shipping_fee' => $shippingFee,
                'payer' => $this->getPayerType($payerCode),
                'total_amount_due_at_delivery' => $totalDue,
            ],
        ]);

        return [
            'shipment_details' => [
                'tracking_id' => $voucher['p01'] ?? '',
                'delivery_address_area' => $voucher['p0203'] ?? '',
                'weight_kg' => (float) ($voucher['p101'] ?? 0),
                'status' => $status,
            ],
            'financial_summary' => [
                'is_cash_on_delivery' => (bool) $isCod,
                'cash_on_delivery_amount' => $codAmount,
                'shipping_fee' => $shippingFee,
                'payer' => $this->getPayerType($payerCode),
                'total_amount_due_at_delivery' => $totalDue,
            ],
        ];
    }

    private function calculateTotalDueAtDoor(int $isCod, float $codAmount, float $shippingFee, int $payerCode): float
    {
        if ($isCod === 1 && $payerCode === 1) {
            return $codAmount + $shippingFee;
        }

        if ($isCod === 1 && $payerCode === 2) {
            return $codAmount;
        }

        if ($isCod === 0 && $payerCode === 1) {
            return $shippingFee;
        }

        return 0.0;
    }

    private function getPayerType(int $code): string
    {
        return match ($code) {
            1 => 'recipient',
            2 => 'sender',
            3 => 'credit',
            default => 'unknown',
        };
    }

    private function validateVoucherCode(Request $request): array
    {
        $validator = Validator::make(
            $request->all(),
            [
                'voucher_code' => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z0-9\-_]+$/'],
            ],
            [
                'voucher_code.required' => 'Παρακαλώ δώστε έναν κωδικό παρακολούθησης.',
                'voucher_code.max' => 'Ο κωδικός παρακολούθησης είναι πολύ μεγάλος. Παρακαλώ ελέγξτε τον κωδικό και δοκιμάστε ξανά.',
                'voucher_code.regex' => 'Ο κωδικός παρακολούθησης περιέχει μη έγκυρους χαρακτήρες. Χρησιμοποιήστε μόνο γράμματα, αριθμούς, και παύλες.',
            ]
        );

        if ($validator->fails()) {
            return ['error' => (string) $validator->errors()->first('voucher_code')];
        }

        return [
            'voucher_code' => trim((string) $validator->validated()['voucher_code']),
        ];
    }
}
