<?php

namespace App\Enums;

enum OfferScope: string
{
    case Group = 'group';
    case Brand = 'brand';
    case Motorcycle = 'motorcycle';
    case TestRide = 'test_ride';

    public function label(): string
    {
        return match ($this) {
            self::Group => 'Group-wide',
            self::Brand => 'Brand',
            self::Motorcycle => 'Model',
            self::TestRide => 'Test ride',
        };
    }
}
