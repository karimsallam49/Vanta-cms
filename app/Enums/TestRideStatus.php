<?php

namespace App\Enums;

enum TestRideStatus: string
{
    case Pending = 'pending';
    case Contacted = 'contacted';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Contacted => 'Contacted',
            self::Confirmed => 'Confirmed',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function tone(): string
    {
        // One accent color means "act on this", green means "settled",
        // neutral smoke is passive history.
        return match ($this) {
            self::Pending => 'red',
            self::Contacted => '',
            self::Confirmed => 'lime',
            self::Completed => 'lime',
            self::Cancelled => '',
        };
    }
}
