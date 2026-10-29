<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\Offer;
use App\Models\Showroom;
use App\Models\TestRideRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------- Brands — mirrors the public site's four worlds ---------- */

        $brands = collect([
            'volta' => Brand::create([
                'name' => 'VOLTA', 'slug' => 'volta', 'segment' => 'urban',
                'origin' => 'Milan, Italy', 'accent' => '#c8ff3d',
                'tagline' => 'Street electric, tuned for the city grid',
            ]),
            'nomad' => Brand::create([
                'name' => 'NOMAD', 'slug' => 'nomad', 'segment' => 'adventure',
                'origin' => 'Salzburg, Austria', 'accent' => '#e8963c',
                'tagline' => 'Engineered for the long way home',
            ]),
            'kaizen' => Brand::create([
                'name' => 'KAIZEN', 'slug' => 'kaizen', 'segment' => 'performance',
                'origin' => 'Hamamatsu, Japan', 'accent' => '#ff3b30',
                'tagline' => 'Obsessive iteration. Zero compromise.',
            ]),
            'sovereign' => Brand::create([
                'name' => 'SOVEREIGN', 'slug' => 'sovereign', 'segment' => 'premium',
                'origin' => 'Coventry, England', 'accent' => '#d4af6e',
                'tagline' => 'Hand-built. Signed by its maker.',
            ]),
        ]);

        /* ---------- Motorcycles — same lineup as the frontend ---------- */

        $bikes = [
            ['volta', 'VOLTA SPARK 350', 9400, 199, 'Urban electric commuter', null, 48, 178, 129,
                [['name' => 'Acid Lime', 'hex' => '#c8ff3d'], ['name' => 'Graphite', 'hex' => '#1d1d21']]],
            ['volta', 'VOLTA GRID 125', 6900, 149, 'The gateway machine', null, 36, 142, 104,
                [['name' => 'Acid Lime', 'hex' => '#c8ff3d'], ['name' => 'Coal', 'hex' => '#0b0b0d']]],
            ['nomad', 'NOMAD ATLAS 900', 16900, 349, 'Continent-crushing flagship', 890, 96, 232, 199,
                [['name' => 'Dune Amber', 'hex' => '#e8963c'], ['name' => 'Basalt', 'hex' => '#26262b']]],
            ['nomad', 'NOMAD DUNE 450', 11400, 239, 'Rally DNA, daily manners', 450, 46, 178, 162,
                [['name' => 'Dune Amber', 'hex' => '#e8963c'], ['name' => 'Bone', 'hex' => '#edeae4']]],
            ['kaizen', 'KAIZEN RK-1', 21900, 459, 'Track weapon with a plate', 998, 182, 199, 299,
                [['name' => 'Lava Red', 'hex' => '#e10600'], ['name' => 'Void Black', 'hex' => '#050507']]],
            ['kaizen', 'KAIZEN STORM 1000R', 28900, 589, 'The apex predator', 999, 208, 201, 306,
                [['name' => 'Lava Red', 'hex' => '#e10600'], ['name' => 'Carbon', 'hex' => '#141417']]],
            ['sovereign', 'SOVEREIGN REGENT', 39000, 789, 'The gentleman express', 1200, 88, 267, 201,
                [['name' => 'Regent Gold', 'hex' => '#d4af6e'], ['name' => 'Midnight', 'hex' => '#0d0f14']]],
            ['sovereign', 'SOVEREIGN CROWN GT', 44500, 899, 'Grand touring, hand-finished', 1300, 102, 282, 212,
                [['name' => 'Regent Gold', 'hex' => '#d4af6e'], ['name' => 'Coal', 'hex' => '#0b0b0d']]],
        ];

        foreach ($bikes as [$brand, $name, $price, $mo, $tag, $cc, $hp, $kg, $top, $colors]) {
            Motorcycle::create([
                'brand_id' => $brands[$brand]->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'price' => $price,
                'monthly_from' => $mo,
                'tagline' => $tag,
                'engine_cc' => $cc,
                'power_hp' => $hp,
                'weight_kg' => $kg,
                'top_speed_kmh' => $top,
                'colors' => $colors,
            ]);
        }

        /* ---------- Offers ---------- */

        Offer::create([
            'brand_id' => null, 'is_active' => true, 'badge' => 'GROUP',
            'title' => '0% financing for 24 months',
            'description' => 'Ride now, pay over two years with zero interest across the full VANTA MOTO range.',
            'expires_on' => now()->addMonths(3),
        ]);
        Offer::create([
            'brand_id' => $brands['kaizen']->id, 'is_active' => true, 'badge' => 'LIMITED',
            'title' => 'KAIZEN RK-1 — track pack included',
            'description' => 'Every RK-1 this season ships with the factory track pack: quick-shifter, race ECU map, and paddock stand.',
            'expires_on' => now()->addWeeks(6),
        ]);
        Offer::create([
            'brand_id' => $brands['nomad']->id, 'is_active' => true, 'badge' => 'SEASON',
            'title' => 'Adventure readiness check — free',
            'description' => 'Free 21-point inspection plus luggage fitting on every NOMAD purchase before the touring season.',
            'expires_on' => now()->addMonth(),
        ]);

        /* ---------- Showrooms ---------- */

        $showrooms = collect([
            Showroom::create(['city' => 'Dubai', 'country' => 'UAE', 'name' => 'VANTA MOTO — SHEIKH ZAYED FLAGSHIP', 'address' => 'Sheikh Zayed Rd, Al Quoz 1, Dubai', 'phone' => '+971 4 555 0100', 'hours' => 'SAT–THU 9:00–21:00 · FRI 14:00–21:00', 'is_service_center' => true]),
            Showroom::create(['city' => 'Riyadh', 'country' => 'KSA', 'name' => 'VANTA MOTO — KING FAHD ROAD', 'address' => 'King Fahd Rd, Al Olaya, Riyadh', 'phone' => '+966 11 555 0130', 'hours' => 'SAT–THU 9:00–22:00', 'is_service_center' => true]),
            Showroom::create(['city' => 'Doha', 'country' => 'Qatar', 'name' => 'VANTA MOTO — WEST BAY', 'address' => 'West Bay, Doha', 'phone' => '+974 4 555 0160', 'hours' => 'SAT–THU 9:00–21:00', 'is_service_center' => false]),
            Showroom::create(['city' => 'Kuwait City', 'country' => 'Kuwait', 'name' => 'VANTA MOTO — SERVICE PARTNER', 'address' => 'Al Rai, Kuwait City', 'phone' => '+965 2 555 0180', 'hours' => 'SUN–THU 9:00–20:00', 'is_service_center' => true]),
            Showroom::create(['city' => 'Manama', 'country' => 'Bahrain', 'name' => 'VANTA MOTO — SEEF', 'address' => 'Seef District, Manama', 'phone' => '+973 1 555 0140', 'hours' => 'SAT–THU 9:00–21:00', 'is_service_center' => false]),
        ]);

        /* ---------- Users ---------- */

        User::create([
            'name' => 'Group Admin',
            'email' => 'admin@vantamoto.com',
            'password' => 'VantaAdmin2026',
            'role' => UserRole::Admin,
        ]);

        $entry = User::create([
            'name' => 'Sara Mansour',
            'email' => 'data@vantamoto.com',
            'password' => 'VantaEntry2026',
            'role' => UserRole::DataEntry,
        ]);
        $entry->brands()->sync([$brands['volta']->id, $brands['nomad']->id]);

        /* ---------- Demo test-ride requests ---------- */

        TestRideRequest::create([
            'motorcycle_id' => Motorcycle::where('name', 'VOLTA SPARK 350')->value('id'),
            'showroom_id' => $showrooms[0]->id,
            'name' => 'Omar Khalifa', 'email' => 'omar.k@example.com',
            'phone' => '+971 50 555 0123', 'preferred_date' => now()->addDays(5),
            'status' => 'pending',
        ]);
        TestRideRequest::create([
            'motorcycle_id' => Motorcycle::where('name', 'NOMAD ATLAS 900')->value('id'),
            'showroom_id' => $showrooms[1]->id,
            'name' => 'Layla Haddad', 'phone' => '+966 55 555 0177',
            'preferred_date' => now()->addDays(9),
            'status' => 'contacted',
            'internal_note' => 'Prefers morning slot — call after 9 AM.',
        ]);
        TestRideRequest::create([
            'motorcycle_id' => Motorcycle::where('name', 'KAIZEN RK-1')->value('id'),
            'showroom_id' => $showrooms[0]->id,
            'name' => 'Faris Al-Naimi', 'email' => 'f.naimi@example.com',
            'phone' => '+971 56 555 0199', 'preferred_date' => now()->addDays(3),
            'status' => 'confirmed',
        ]);
    }
}
