<?php

declare(strict_types=1);

namespace Tawked\Account;

use Tawked\Pagination\CursorPage;

/**
 * A page of an Account API list: `{data: [...], next_cursor}`. `iterate()` on the same namespace
 * follows `next_cursor` until it is null.
 *
 * @implements CursorPage<mixed>
 */
final class AccountPage extends AccountResponse implements CursorPage
{
    /**
     * @return list<mixed>
     */
    public function items(): array
    {
        return is_array($this->data) ? array_values($this->data) : [];
    }

    public function cursor(): ?string
    {
        $cursor = $this->raw['next_cursor'] ?? null;

        return is_string($cursor) && $cursor !== '' ? $cursor : null;
    }

    public function more(): bool
    {
        return $this->cursor() !== null;
    }
}
