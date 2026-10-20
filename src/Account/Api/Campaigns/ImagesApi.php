<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Campaigns;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->campaigns->images`: the Account API's `campaigns.images` operations.
 */
final class ImagesApi extends AccountNamespace
{
    /**
     * Upload the image of a template that opens with one (a JPG or PNG up to 5 MB, multipart
     * `image`); answers its `path`, to save on the draft as `header_image_path`, and its public
     * URL. `POST /v1/applications/{application}/campaigns/images`.
     *
     * Permission `campaigns:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{image: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.images.upload
     */
    public function upload(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/images',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['image'],
        ));
    }
}
