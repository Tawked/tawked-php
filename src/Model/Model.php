<?php

declare(strict_types=1);

namespace Tawked\Model;

use JsonSerializable;
use Tawked\Http\ResponseMeta;

/**
 * A typed object of the Tawked API. Every property is read-only; `toArray()` gives the
 * JSON object exactly as the API sent it, including any field this version does not know.
 */
abstract class Model implements JsonSerializable
{
    /** @var array<string, mixed> */
    protected array $raw = [];

    private ?ResponseMeta $meta = null;

    /**
     * The JSON object as the API sent it.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->raw;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->raw;
    }

    /**
     * The HTTP status, `X-Request-Id` and `Idempotent-Replayed` of the response that carried
     * this object. Null on an object nested inside another.
     */
    public function meta(): ?ResponseMeta
    {
        return $this->meta;
    }

    /**
     * The `X-Request-Id` of the response that carried this object. Quote it to support.
     */
    public function requestId(): ?string
    {
        return $this->meta?->requestId;
    }

    /**
     * @internal
     */
    public function withMeta(ResponseMeta $meta): static
    {
        $this->meta = $meta;

        return $this;
    }
}
