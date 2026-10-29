<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case DataEntry = 'data_entry';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::DataEntry => 'Data Entry',
        };
    }
}
