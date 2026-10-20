<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailKeyCheckResponse;
use Tawked\Service\Email\DomainsService;
use Tawked\Service\Email\InboundService;
use Tawked\Service\Email\MessagesService;
use Tawked\Service\Email\MetricsService;
use Tawked\Service\Email\SuppressionsService;
use Tawked\Service\Email\TemplatesService;

/**
 * `$tawked->email`: Tawked Email (early access: an account must be granted Email first).
 */
final class EmailService extends Service
{
    public readonly DomainsService $domains;

    public readonly InboundService $inbound;

    public readonly MessagesService $messages;

    public readonly MetricsService $metrics;

    public readonly SuppressionsService $suppressions;

    public readonly TemplatesService $templates;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->domains = new DomainsService($transport);
        $this->inbound = new InboundService($transport);
        $this->messages = new MessagesService($transport);
        $this->metrics = new MetricsService($transport);
        $this->suppressions = new SuppressionsService($transport);
        $this->templates = new TemplatesService($transport);
    }

    /**
     * Check an email key. `GET /v1/email/key`.
     *
     * Checks that a key may send email: a live key with `email:send` on an application that may
     * send, of an account Tawked's team has granted Email (else `403 email_early_access`). It sends
     * and records nothing. The SMTP relay (`smtp.tawked.com`) calls it when a client signs in with
     * its key as the password.
     *
     * Scope `email:send`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, email_early_access, insufficient_scope, internal_error,
     * ip_not_allowed, key_expired, live_key_required, service_not_found, service_required,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->checkKey([]);
     *
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-key
     * @tawked-stability early_access
     */
    public function checkKey(array $params = [], array $options = []): EmailKeyCheckResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/key',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailKeyCheckResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
