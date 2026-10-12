<?php

declare(strict_types=1);

namespace App\Auth;

use FlintPHP\Framework\Authentication\IdentityInterface;
use FlintPHP\Framework\Authentication\UserProviderInterface;

/**
 * Demo-only token provider.
 *
 * Accepts a single pre-shared token (hashed at construction).
 * Replace with a real DB-backed provider in production apps.
 */
final class StaticTokenUserProvider implements UserProviderInterface
{
    private readonly string $hashedToken;

    public function __construct(string $plainToken)
    {
        $this->hashedToken = hash('sha256', $plainToken);
    }

    public function retrieveByToken(string $hashedToken): ?IdentityInterface
    {
        if (!hash_equals($this->hashedToken, $hashedToken)) {
            return null;
        }

        return new class implements IdentityInterface {
            public function getIdentifier(): string|int
            {
                return 'demo-user';
            }
        };
    }
}
