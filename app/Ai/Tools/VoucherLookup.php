<?php

namespace App\Ai\Tools;

use App\Services\PegasusClient;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class VoucherLookup implements Tool
{
    public function description(): Stringable|string
    {
        return 'Look up voucher/shipment tracking status, details, and pricing from the Pac-Man courier system. Use this when a customer asks about the status of their shipment or package.';
    }

    public function handle(Request $request): Stringable|string
    {
        $client = app(PegasusClient::class);

        $result = $client->getVoucher($request['voucher_code']);

        return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'voucher_code' => $schema->string()
                ->description('The voucher or tracking code for the shipment')
                ->required(),
        ];
    }
}
