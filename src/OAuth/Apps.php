<?php

declare(strict_types=1);

namespace Tawked\OAuth;

use Tawked\Exception\InvalidArgumentException;
use Tawked\Http\Transport;
use Tawked\Internal\ClientOptions;
use Tawked\Model\OAuth\AppConnection;
use Tawked\Model\OAuth\AuthorizeUrl;

/**
 * Connected apps: send the merchant to approve your app, then exchange the code for the
 * connection's key (an application key for the one application they chose).
 *
 *     $start = Tawked\OAuth\Apps::authorizeUrl(['client_id' => $id, 'redirect_uri' => $return]);
 *     // keep $start->state and $start->codeVerifier in the signed-in user's session, redirect to $start->url
 *
 *     // on the return, after checking ?state= against the session:
 *     $connection = Tawked\OAuth\Apps::exchangeCode([
 *         'client_id' => $id, 'client_secret' => $secret, 'code' => $_GET['code'],
 *         'redirect_uri' => $return, 'code_verifier' => $start->codeVerifier,
 *     ]);
 *     $key = $connection->accessToken; // shown once: store it now
 *
 * The exchange is never retried (the code is single-use, the key answered once) and waits up to
 * 120 s. `invalid_grant` after a timeout means the connection may have been made: ask the
 * merchant to approve the app again (a new request replaces it).
 */
final class Apps
{
    private const AUTHORIZE_PATH = '/oauth/authorize';

    private const TOKEN_PATH = '/oauth/apps/token';

    /**
     * The URL of the approval page, with a fresh PKCE verifier (S256) and state. No network call.
     *
     * @param array<string, mixed> $params client_id, redirect_uri; state and code_verifier (made when absent); base_url
     */
    public static function authorizeUrl(array $params): AuthorizeUrl
    {
        if (array_key_exists('scope', $params)) {
            throw new InvalidArgumentException('Send no scope: the permissions are the same for every app.');
        }
        foreach (['client_id', 'redirect_uri'] as $name) {
            if (! is_string($params[$name] ?? null) || trim($params[$name]) === '') {
                throw new InvalidArgumentException("`{$name}` is required.");
            }
        }
        $verifier = $params['code_verifier'] ?? self::newVerifier();
        if (! is_string($verifier) || preg_match('/^[A-Za-z0-9\-._~]{43,128}$/', $verifier) !== 1) {
            throw new InvalidArgumentException('`code_verifier` must be 43 to 128 characters of [A-Za-z0-9-._~].');
        }
        $state = $params['state'] ?? self::base64url(random_bytes(24));
        if (! is_string($state) || $state === '') {
            throw new InvalidArgumentException('`state` must be a non-empty string.');
        }
        $base = rtrim((string) ($params['base_url'] ?? ClientOptions::env('TAWKED_BASE_URL') ?? ClientOptions::DEFAULT_BASE_URL), '/');
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $params['client_id'],
            'redirect_uri' => $params['redirect_uri'],
            'state' => $state,
            'code_challenge' => self::challenge($verifier),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return AuthorizeUrl::fromArray(['url' => $base.self::AUTHORIZE_PATH.'?'.$query, 'state' => $state, 'code_verifier' => $verifier]);
    }

    /**
     * Exchanges the code of the return for the connection's key. `POST /oauth/apps/token`.
     *
     * @param array<string, mixed> $params client_id, client_secret, code, redirect_uri, code_verifier
     * @param array<string, mixed> $options base_url, http_client, timeout (120 s), connect_timeout, headers, client_auth (`basic`, the default, or `post`)
     *
     * @throws \Tawked\Exception\OAuthException invalid_client, invalid_grant, invalid_request, unsupported_grant_type
     */
    public static function exchangeCode(#[\SensitiveParameter] array $params, array $options = []): AppConnection
    {
        foreach (['client_id', 'client_secret', 'code', 'redirect_uri', 'code_verifier'] as $name) {
            if (! is_string($params[$name] ?? null) || $params[$name] === '') {
                throw new InvalidArgumentException("`{$name}` is required.");
            }
        }
        $auth = $options['client_auth'] ?? 'basic';
        unset($options['client_auth']);
        if (! in_array($auth, ['basic', 'post'], true)) {
            throw new InvalidArgumentException('`client_auth` must be basic or post.');
        }
        $call = [];
        foreach (['timeout', 'headers'] as $name) {
            if (array_key_exists($name, $options)) {
                $call[$name] = $options[$name];
            }
        }
        unset($options['timeout']);
        $options['max_retries'] ??= 2;
        $transport = ClientOptions::transport('', $options, Transport::SURFACE_OAUTH);

        $form = [
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'redirect_uri' => $params['redirect_uri'],
            'code_verifier' => $params['code_verifier'],
        ];
        $headers = [];
        if ($auth === 'basic') {
            $headers['Authorization'] = 'Basic '.base64_encode(rawurlencode($params['client_id']).':'.rawurlencode($params['client_secret']));
        } else {
            $form['client_id'] = $params['client_id'];
            $form['client_secret'] = $params['client_secret'];
        }

        $response = $transport->send(
            method: 'POST',
            path: self::TOKEN_PATH,
            options: $call,
            retry: Transport::NEVER,
            timeout: 120.0,
            form: $form,
            takesKey: false,
            secret: true,
            headers: $headers,
            bearer: false,
        );

        return AppConnection::fromArray($response->body)->withMeta($response->meta);
    }

    /** The S256 code challenge of a verifier: base64url, no padding, of its SHA-256. */
    public static function challenge(string $verifier): string
    {
        return self::base64url(hash('sha256', $verifier, true));
    }

    /** A new PKCE verifier: 32 random bytes, base64url without padding (43 characters). */
    public static function newVerifier(): string
    {
        return self::base64url(random_bytes(32));
    }

    private static function base64url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
