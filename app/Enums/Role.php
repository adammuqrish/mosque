<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case TREASURER = 'treasurer';
    case MEMBER = 'member';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $role): bool
    {
        return in_array($role, self::values());
    }
}
