<?php

declare(strict_types=1);

namespace Tawked;

use JsonSerializable;
use Psr\Http\Message\StreamInterface;
use Tawked\Exception\InvalidArgumentException;

/**
 * A file to send: an e-mail's attachment (in JSON, `{filename, content, content_type}` with the
 * content in base64) or a file of a multipart upload (a logo, a document).
 *
 *     'attachments' => [Tawked\Attachment::fromPath('/tmp/invoice-1042.pdf')]
 *
 * Refused before sending above the contract's bound (10 MiB, Contract::LIMITS).
 */
final class Attachment implements JsonSerializable
{
    private function __construct(
        private readonly string $content,
        public readonly string $filename,
        public readonly string $contentType,
    ) {
        $max = Contract::LIMITS['email_attachments_max_bytes'];
        if (strlen($content) > $max) {
            throw new InvalidArgumentException("`{$filename}` is ".strlen($content)." bytes; a file may be at most {$max}.");
        }
        if (trim($filename) === '') {
            throw new InvalidArgumentException('An attachment needs a file name.');
        }
    }

    /** A file on disk. The name is the file's own unless given; the type is guessed from the name. */
    public static function fromPath(string $path, ?string $filename = null, ?string $contentType = null): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException("`{$path}` is not a readable file.");
        }
        $content = file_get_contents($path);
        if ($content === false) {
            throw new InvalidArgumentException("`{$path}` could not be read.");
        }
        $filename ??= basename($path);

        return new self($content, $filename, $contentType ?? self::guessType($filename));
    }

    public static function fromBytes(string $content, string $filename, ?string $contentType = null): self
    {
        return new self($content, $filename, $contentType ?? self::guessType($filename));
    }

    /**
     * @param resource|StreamInterface $stream read from where it stands
     */
    public static function fromStream(mixed $stream, string $filename, ?string $contentType = null): self
    {
        if ($stream instanceof StreamInterface) {
            $content = $stream->getContents();
        } elseif (is_resource($stream)) {
            $content = stream_get_contents($stream);
        } else {
            throw new InvalidArgumentException('`fromStream` takes a stream resource or a PSR-7 stream.');
        }
        if ($content === false) {
            throw new InvalidArgumentException("`{$filename}` could not be read.");
        }

        return new self($content, $filename, $contentType ?? self::guessType($filename));
    }

    /**
     * A file of a multipart field from what a caller passes: an Attachment, a path, an SplFileInfo,
     * a stream resource or a PSR-7 stream.
     *
     * @internal
     */
    public static function from(mixed $value, string $field): self
    {
        if ($value instanceof self) {
            return $value;
        }
        if ($value instanceof \SplFileInfo) {
            return self::fromPath($value->getPathname());
        }
        if (is_string($value)) {
            return self::fromPath($value);
        }
        if ($value instanceof StreamInterface || is_resource($value)) {
            $name = $field;
            if (is_resource($value)) {
                $uri = stream_get_meta_data($value)['uri'] ?? null;
                $name = is_string($uri) && $uri !== '' ? basename($uri) : $field;
            }

            return self::fromStream($value, $name);
        }

        throw new InvalidArgumentException("`{$field}` must be a Tawked\\Attachment, a file path, an SplFileInfo or a stream.");
    }

    /** The file's bytes. */
    public function bytes(): string
    {
        return $this->content;
    }

    /**
     * The wire's attachment object.
     *
     * @return array{filename: string, content: string, content_type: string}
     */
    public function toArray(): array
    {
        return ['filename' => $this->filename, 'content' => base64_encode($this->content), 'content_type' => $this->contentType];
    }

    /**
     * @return array{filename: string, content: string, content_type: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['filename' => $this->filename, 'contentType' => $this->contentType, 'bytes' => strlen($this->content)];
    }

    private static function guessType(string $filename): string
    {
        $types = [
            'pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml', 'txt' => 'text/plain',
            'csv' => 'text/csv', 'html' => 'text/html', 'json' => 'application/json', 'zip' => 'application/zip',
            'ics' => 'text/calendar', 'xml' => 'application/xml',
            'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return $types[strtolower(pathinfo($filename, PATHINFO_EXTENSION))] ?? 'application/octet-stream';
    }
}
