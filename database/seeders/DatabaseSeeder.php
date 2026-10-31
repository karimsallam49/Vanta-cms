<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Currency;
use App\Models\Motorcycle;
use App\Models\Offer;
use App\Models\Role;
use App\Models\SiteSection;
use App\Models\Showroom;
use App\Models\TestRideRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------- Currencies — group pricing configuration ---------- */

        $this->call(CurrencySeeder::class);
        $usd = Currency::where('code', 'USD')->value('id');

        /* ---------- Brands — mirrors the public site's four worlds ---------- */

        $brands = collect([
            'volta' => Brand::create([
                'name' => 'VOLTA', 'name_ar' => 'فولتا', 'slug' => 'volta', 'segment' => 'urban',
                'origin' => 'Milan, Italy', 'origin_ar' => 'ميلانو، إيطاليا', 'accent' => '#c8ff3d',
                'tagline' => 'Street electric, tuned for the city grid',
                'tagline_ar' => 'كهربائي للمدينة، مضبوط لإيقاع الشوارع',
            ]),
            'nomad' => Brand::create([
                'name' => 'NOMAD', 'name_ar' => 'نوماد', 'slug' => 'nomad', 'segment' => 'adventure',
                'origin' => 'Salzburg, Austria', 'origin_ar' => 'سالزبورغ، النمسا', 'accent' => '#e8963c',
                'tagline' => 'Engineered for the long way home',
                'tagline_ar' => 'مصمم للطريق الطويل نحو البيت',
            ]),
            'kaizen' => Brand::create([
                'name' => 'KAIZEN', 'name_ar' => 'كايزن', 'slug' => 'kaizen', 'segment' => 'performance',
                'origin' => 'Hamamatsu, Japan', 'origin_ar' => 'هاماماتسو، اليابان', 'accent' => '#ff3b30',
                'tagline' => 'Obsessive iteration. Zero compromise.',
                'tagline_ar' => 'تطوير بلا توقف. بلا مساومة.',
            ]),
            'sovereign' => Brand::create([
                'name' => 'SOVEREIGN', 'name_ar' => 'سوفيرين', 'slug' => 'sovereign', 'segment' => 'premium',
                'origin' => 'Coventry, England', 'origin_ar' => 'كوفنتري، إنجلترا', 'accent' => '#d4af6e',
                'tagline' => 'Hand-built. Signed by its maker.',
                'tagline_ar' => 'صناعة يدوية. موقّع من صانعه.',
            ]),
        ]);

        /* ---------- Motorcycles — same lineup as the frontend ---------- */

        $bikes = [
            ['volta', 'VOLTA SPARK 350', 9400, 199, 'Urban electric commuter', 'A silent 350cc-equivalent commuter with instant torque, a removable battery and a chassis tuned for tight city gaps.', null, 48, 178, 129, 14,
                [['name' => 'Acid Lime', 'hex' => '#c8ff3d'], ['name' => 'Graphite', 'hex' => '#1d1d21']]],
            ['volta', 'VOLTA GRID 125', 6900, 149, 'The gateway machine', 'The lightest way into the VOLTA range — 125cc-class electric, one-hour fast charge, effortless daily range.', null, 36, 142, 104, 22,
                [['name' => 'Acid Lime', 'hex' => '#c8ff3d'], ['name' => 'Coal', 'hex' => '#0b0b0d']]],
            ['nomad', 'NOMAD ATLAS 900', 16900, 349, 'Continent-crushing flagship', 'Long-travel suspension, 35L of luggage volume and a twin that pulls from sea level to the Empty Quarter.', 890, 96, 232, 199, 7,
                [['name' => 'Dune Amber', 'hex' => '#e8963c'], ['name' => 'Basalt', 'hex' => '#26262b']]],
            ['nomad', 'NOMAD DUNE 450', 11400, 239, 'Rally DNA, daily manners', 'A rally-bred single with navigation tower, handguards and a seat height that welcomes every rider.', 450, 46, 178, 162, 11,
                [['name' => 'Dune Amber', 'hex' => '#e8963c'], ['name' => 'Bone', 'hex' => '#edeae4']]],
            ['kaizen', 'KAIZEN RK-1', 21900, 459, 'Track weapon with a plate', 'Moto-derived electronics, forged wheels and a 998cc inline-four homologated for the street.', 998, 182, 199, 299, 4,
                [['name' => 'Lava Red', 'hex' => '#e10600'], ['name' => 'Void Black', 'hex' => '#050507']]],
            ['kaizen', 'KAIZEN STORM 1000R', 28900, 589, 'The apex predator', 'Carbon bodywork, Öhlins electronic suspension and the fastest road-legal machine KAIZEN has ever shipped.', 999, 208, 201, 306, 3,
                [['name' => 'Lava Red', 'hex' => '#e10600'], ['name' => 'Carbon', 'hex' => '#141417']]],
            ['sovereign', 'SOVEREIGN REGENT', 39000, 789, 'The gentleman express', 'Hand-assembled in Coventry — every engine signed, every leather seat stitched to order.', 1200, 88, 267, 201, 2,
                [['name' => 'Regent Gold', 'hex' => '#d4af6e'], ['name' => 'Midnight', 'hex' => '#0d0f14']]],
            ['sovereign', 'SOVEREIGN CROWN GT', 44500, 899, 'Grand touring, hand-finished', 'A bespoke grand tourer with heated everything, a champagne-cooled ride and a 500km touring range.', 1300, 102, 282, 212, 2,
                [['name' => 'Regent Gold', 'hex' => '#d4af6e'], ['name' => 'Coal', 'hex' => '#0b0b0d']]],
        ];

        // Cinematic shots shared with the public site's bundled set —
        // one photo per paint swatch so colour picking swaps the image.
        $photos = [
            'photo-1591637333184-19aa84b3e01f', 'photo-1626847037657-fd3622613ce3',
            'photo-1547549082-6bc09f2049ae', 'photo-1531327431456-837da4b1d562',
            'photo-1622185135505-2d795003994a', 'photo-1614165936126-2ed18e471b3b',
            'photo-1609630875171-b1321377ee65', 'photo-1591768793355-74d04bb6608f',
            'photo-1568772585407-9361f9bf3a87', 'photo-1449426468159-d96dbf08f19f',
            'photo-1591370874773-6702e8f12fd8', 'photo-1517846693594-1567da72af75',
            'photo-1562979314-bee7453e911c', 'photo-1601758228041-f3b2795255f1',
        ];

        foreach ($bikes as $i => [$brand, $name, $price, $mo, $tag, $desc, $cc, $hp, $kg, $top, $qty, $colors]) {
            $moto = Motorcycle::create([
                'brand_id' => $brands[$brand]->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'price' => $price,
                'monthly_from' => $mo,
                'currency_id' => $usd,
                'tagline' => $tag,
                'description' => $desc,
                'engine_cc' => $cc,
                'power_hp' => $hp,
                'weight_kg' => $kg,
                'top_speed_kmh' => $top,
                'is_active' => true,
                'quantity' => $qty,
                'colors' => $colors,
            ]);

            foreach ($colors as $c => $color) {
                $photo = $photos[($i * 2 + $c) % count($photos)];
                $moto->images()->create([
                    'path' => "https://images.unsplash.com/{$photo}?q=75&w=1600&auto=format&fit=crop",
                    'color_name' => $color['name'],
                    'color_hex' => $color['hex'],
                    'sort_order' => $c,
                ]);
            }
        }

        /* ---------- Offers ---------- */

        Offer::create([
            'brand_id' => null, 'scope' => 'group', 'is_active' => true, 'badge' => 'FINANCE',
            'title' => '0% financing for 24 months',
            'description' => 'Ride now, pay over two years with zero interest across the full VANTA MOTO range.',
            'expires_on' => now()->addMonths(3),
        ]);
        Offer::create([
            'brand_id' => $brands['kaizen']->id, 'scope' => 'brand', 'is_active' => true, 'badge' => 'LIMITED', 'discount_percent' => 15,
            'title' => '15% off every KAIZEN this month',
            'description' => 'Fifteen percent off the full KAIZEN range — track pack still included on the RK-1.',
            'expires_on' => now()->addWeeks(6),
        ]);
        Offer::create([
            'brand_id' => $brands['nomad']->id, 'scope' => 'motorcycle',
            'motorcycle_id' => Motorcycle::where('slug', 'nomad-atlas-900')->value('id'),
            'is_active' => true, 'badge' => 'SEASON', 'discount_percent' => 10,
            'title' => 'NOMAD ATLAS — 10% off + free luggage set',
            'description' => 'Ten percent off the ATLAS plus panniers, crash bars and a GPS rally mount installed at no cost.',
            'expires_on' => now()->addMonth(),
        ]);
        Offer::create([
            'brand_id' => null, 'scope' => 'test_ride', 'is_active' => true, 'badge' => 'RIDE',
            'title' => 'Book a test ride — gear fitting on us',
            'description' => 'Every confirmed test ride this season includes a complimentary helmet and glove fitting at any showroom.',
            'expires_on' => now()->addMonths(2),
        ]);

        /* ---------- Site content — public page sections ---------- */

        foreach (\App\Support\SiteContent::defaults() as $key => $payload) {
            SiteSection::updateOrCreate(['key' => $key], ['payload' => $payload]);
        }

        /* ---------- Showrooms ---------- */

        $showrooms = collect([
            Showroom::create(['city' => 'Dubai', 'country' => 'UAE', 'name' => 'VANTA MOTO — SHEIKH ZAYED FLAGSHIP', 'address' => 'Sheikh Zayed Rd, Al Quoz 1, Dubai', 'phone' => '+971 4 555 0100', 'hours' => 'SAT–THU 9:00–21:00 · FRI 14:00–21:00', 'is_service_center' => true, 'latitude' => 25.1184, 'longitude' => 55.2275, 'open_days' => [0, 1, 2, 3, 4, 6]]),
            Showroom::create(['city' => 'Riyadh', 'country' => 'KSA', 'name' => 'VANTA MOTO — KING FAHD ROAD', 'address' => 'King Fahd Rd, Al Olaya, Riyadh', 'phone' => '+966 11 555 0130', 'hours' => 'SAT–THU 9:00–22:00', 'is_service_center' => true, 'latitude' => 24.7136, 'longitude' => 46.6753, 'open_days' => [0, 1, 2, 3, 4, 6]]),
            Showroom::create(['city' => 'Doha', 'country' => 'Qatar', 'name' => 'VANTA MOTO — WEST BAY', 'address' => 'West Bay, Doha', 'phone' => '+974 4 555 0160', 'hours' => 'SAT–THU 9:00–21:00', 'is_service_center' => false, 'latitude' => 25.3218, 'longitude' => 51.5254, 'open_days' => [0, 1, 2, 3, 4, 5, 6]]),
            Showroom::create(['city' => 'Kuwait City', 'country' => 'Kuwait', 'name' => 'VANTA MOTO — SERVICE PARTNER', 'address' => 'Al Rai, Kuwait City', 'phone' => '+965 2 555 0180', 'hours' => 'SUN–THU 9:00–20:00', 'is_service_center' => true, 'latitude' => 29.3117, 'longitude' => 47.9335, 'open_days' => [0, 1, 2, 3, 4]]),
            Showroom::create(['city' => 'Manama', 'country' => 'Bahrain', 'name' => 'VANTA MOTO — SEEF', 'address' => 'Seef District, Manama', 'phone' => '+973 1 555 0140', 'hours' => 'SAT–THU 9:00–21:00', 'is_service_center' => false, 'latitude' => 26.2364, 'longitude' => 50.5351, 'open_days' => [0, 1, 2, 3, 4, 6]]),
        ]);

        // Each model is stocked in a subset of the group's showrooms.
        Motorcycle::all()->each(
            fn (Motorcycle $m) => $m->showrooms()->sync($showrooms->random(random_int(2, 4))->pluck('id'))
        );

        /* ---------- Roles & users ---------- */

        $adminRole = Role::firstOrCreate(
            ['slug' => 'administrator'],
            ['name' => 'Administrator', 'description' => 'Full access to every section — bypasses all permission checks.', 'is_admin' => true, 'permissions' => []]
        );
        $entryRole = Role::firstOrCreate(
            ['slug' => 'data-entry'],
            ['name' => 'Data Entry', 'description' => 'Manages assigned brands: motorcycles, offers and test-ride requests.', 'permissions' => \App\Support\Permissions::dataEntryDefaults()]
        );

        User::create([
            'name' => 'Group Admin',
            'email' => 'admin@vantamoto.com',
            'password' => 'VantaAdmin2026',
            'role_id' => $adminRole->id,
        ]);

        $entry = User::create([
            'name' => 'Sara Mansour',
            'email' => 'data@vantamoto.com',
            'password' => 'VantaEntry2026',
            'role_id' => $entryRole->id,
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
