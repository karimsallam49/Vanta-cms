<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED'],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'SAR'],
            ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => 'QAR'],
            ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'symbol' => 'KWD'],
            ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => 'BHD'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
        ])->each(fn (array $row) => Currency::updateOrCreate(['code' => $row['code']], $row));
    }
}
