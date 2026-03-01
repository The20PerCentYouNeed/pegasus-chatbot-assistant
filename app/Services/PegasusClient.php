<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PegasusClient
{
    private const R18_FIELDS = [
        'p01',
        'p0203',
        'p101',
        'p014',
        'p026',
        'p022',
        'p012',
        'p123',
    ];

    private const R13_DRIVER_FIELDS = [
        'p119',
        'r15p07',
        'p110',
        'p111',
        'p04_r01_p02',
    ];

    protected string $baseUrl;
    protected string $appID;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.pegasus.base_url');
        $this->appID = config('services.pegasus.app_id');
        $this->username = config('services.pegasus.username');
        $this->password = config('services.pegasus.password');
    }

    /**
     * Get voucher fields from Pegasus.
     *
     * @return array<string, mixed>
     */
    public function getVoucherInformation(string $code): array
    {
        return Arr::only(
            $this->request('courier00/r18', [
                'filter[and][0][p01][equals]' => $code,
                'recperpage' => 1,
            ])[0] ?? [],
            self::R18_FIELDS
        );
    }

    /**
     * Get logistics fields from Pegasus .
     *
     * @return array<string, mixed>
     */
    public function getVoucherDriverInformation(string $code): array
    {

        return Arr::only(
            $this->request('courier00/r13_driver', [
                'filter[and][0][p01][equals]' => $code,
                'recperpage' => 1,
            ])[0] ?? [],
            self::R13_DRIVER_FIELDS
        );
    }

    /**
     * Get voucher tracking status only.
     */
    public function getVoucherStatuses(): array
    {
        return collect($this->request('courier00/r14'))
            ->mapWithKeys(fn ($item) => [$item['p01'] => $item['p02']])
            ->all();
    }

    public function login()
    {
        return Http::withQueryParameters([
            'appid' => $this->appID,
            'username' => $this->username,
            'password' => $this->password,
        ])->post("{$this->baseUrl}/pegapi/login")
            ->throw()
            ->json('data.sid');
    }

    public function request(string $endpoint, array $queryParams = [], string $method = 'GET'): array
    {
        if (!$sid = cache('pegasus_erp_sid')) {
            $sid = $this->login();
            cache()->put('pegasus_erp_sid', $sid, now()->addMinutes(30));
        }

        $queryParams = array_merge($queryParams, [
            'appid' => $this->appID,
            'sid' => $sid,
        ]);

        return Http::withQueryParameters($queryParams)
            ->$method("{$this->baseUrl}/{$endpoint}")
            ->throw()
            ->json('data', []);
    }
}
