<?php

declare(strict_types=1);

namespace Tawked;

use Tawked\Http\Transport;
use Tawked\Internal\ClientOptions;
use Tawked\Service\ClientNamespaces;

/**
 * The client of the Tawked API's products: Verify, WhatsApp, Email, Chat (connected apps) and the
 * Partner API.
 *
 *     $tawked = new Tawked\TawkedClient('tk_live_...');
 *     $started = $tawked->verify->start(['to' => '+966512345678'], ['idempotency_key' => 'signup-42']);
 *
 * Keys are server-side secrets: never ship one to a browser or a mobile app. The client never
 * logs or prints the key. The Account API (`tk_acct_` tokens) has its own client,
 * Tawked\TawkedAccountClient; connected apps' OAuth exchange is Tawked\OAuth\Apps.
 */
class TawkedClient extends ClientNamespaces
{
    public const DEFAULT_BASE_URL = ClientOptions::DEFAULT_BASE_URL;

    public const DEFAULT_TIMEOUT = 30.0;

    public const DEFAULT_CONNECT_TIMEOUT = 10.0;

    public const DEFAULT_MAX_RETRIES = 2;

    private readonly Transport $transport;

    private readonly string $keyMode;

    /**
     * @param string|null $apiKey the key, or null to read the TAWKED_API_KEY environment variable
     * Options:
     * - `base_url` (string): TAWKED_BASE_URL, else https://tawked.com; a local mock's URL in tests.
     * - `timeout` (float, seconds): per attempt; each call's own by default (30 s, 120 s for a
     *   batch, a secret or the OAuth exchange).
     * - `connect_timeout` (float, seconds): 10 by default.
     * - `max_retries` (int): 2 by default. Only what each call's retry class allows: see the README.
     * - `in_progress_wait` (float, seconds): how long a keyed write waits on
     *   `idempotency_in_progress`, 30 by default.
     * - `http_client` (Psr\Http\Client\ClientInterface): a Guzzle client by default.
     * - `headers` (array<string, string>): sent on every request.
     * - `sleep` (callable(float): void): waits between retries; for tests.
     *
     * @param array<string, mixed> $options
     */
    public function __construct(#[\SensitiveParameter] ?string $apiKey = null, array $options = [])
    {
        $apiKey = ClientOptions::credential($apiKey, 'TAWKED_API_KEY', 'API key');
        $this->keyMode = self::modeOf($apiKey);
        $this->transport = ClientOptions::transport($apiKey, $options, Transport::SURFACE_PRODUCT);
        parent::__construct($this->transport);
    }

    /**
     * What kind of key this client holds, by its prefix (the longest wins): `live`, `test`,
     * `partner`, `partner_test`, or `unknown`. A connected app's key cannot be told from its text.
     * What a test key does is in Tawked\Contract::SANDBOX_NOTES: it sends real, charged messages to
     * the owner's own number only. There is no "is test" boolean on purpose.
     */
    public function keyMode(): string
    {
        return $this->keyMode;
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['apiKey' => '[hidden]', 'keyMode' => $this->keyMode, 'baseUrl' => $this->transport->baseUrl()];
    }

    /** @internal */
    public static function modeOf(#[\SensitiveParameter] string $key): string
    {
        foreach (Contract::KEY_PREFIXES as $mode => $prefix) {
            if (str_starts_with($key, $prefix)) {
                return $mode;
            }
        }

        return 'unknown';
    }
}
