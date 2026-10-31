<?php

namespace Database\Seeders;

use App\Models\Motorcycle;
use App\Models\SiteSection;
use App\Support\SiteContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Backfills the catalogue with the rich copy the bundled frontend ships
 * with — six-row spec tables, feature tags, the full paint palette and one
 * gallery photo per colour — plus restores the hero section to defaults.
 *
 * Safe to re-run: rows are updated, not duplicated.
 */
class EnrichCatalogSeeder extends Seeder
{
    // slug => [name, price, monthly, tagline, description, specs, features,
    //          colors(photo,name,hex in display order), engine_cc, power_hp, weight_kg, top_speed_kmh]
    private const BIKES = [
        'volta-spark-350' => [
            'name' => 'VOLTA SPARK 350', 'price' => 6400, 'monthly' => 139,
            'tagline' => 'The city, electrified.',
            'description' => 'A 350-class electric commuter with instant torque, a swappable battery and a chassis tuned for gap-shot agility.',
            'specs' => [
                ['MOTOR', 'PERMANENT MAGNET ELECTRIC'], ['POWER', '35 KW / 47 HP'], ['TORQUE', '105 NM'],
                ['RANGE', '140 KM'], ['WEIGHT', '168 KG'], ['0–100', '4.1 S'],
            ],
            'features' => ['SWAPPABLE BATTERY', 'REGEN BRAKING', 'APP CONNECTIVITY'],
            'colors' => [
                ['photo-1591637333184-19aa84b3e01f', 'VOLT YELLOW', '#c8ff3d'],
                ['photo-1626847037657-fd3622613ce3', 'ASPHALT', '#2a2a2e'],
                ['photo-1547549082-6bc09f2049ae', 'SIGNAL WHITE', '#e8e6e0'],
            ],
            'cc' => null, 'hp' => 47, 'kg' => 168, 'top' => null,
        ],
        'volta-grid-125' => [
            'name' => 'VOLTA GRID 125', 'price' => 3900, 'monthly' => 89,
            'tagline' => 'Every street. Every day.',
            'description' => 'The entry point to electric riding — featherweight, effortless and cheap to run. Perfect for first bikes and fleet duty.',
            'specs' => [
                ['MOTOR', 'HUB ELECTRIC'], ['POWER', '11 KW / 15 HP'], ['TORQUE', '62 NM'],
                ['RANGE', '95 KM'], ['WEIGHT', '121 KG'], ['0–60', '5.8 S'],
            ],
            'features' => ['REMOVABLE BATTERY', 'ECO / SPORT MODES', 'USB-C CHARGING'],
            'colors' => [
                ['photo-1626847037657-fd3622613ce3', 'MIDNIGHT', '#101014'],
                ['photo-1531327431456-837da4b1d562', 'VOLT YELLOW', '#c8ff3d'],
            ],
            'cc' => null, 'hp' => 15, 'kg' => 121, 'top' => null,
        ],
        'nomad-atlas-900' => [
            'name' => 'NOMAD ATLAS 900', 'price' => 16800, 'monthly' => 349,
            'tagline' => 'The horizon is a suggestion.',
            'description' => 'A 900cc twin built for transcontinental abuse — 30-litre tank, semi-active suspension and a cockpit designed for 12-hour days.',
            'specs' => [
                ['ENGINE', '889 CC PARALLEL TWIN'], ['POWER', '105 HP'], ['TORQUE', '92 NM'],
                ['TANK', '30 L / 480 KM RANGE'], ['WEIGHT', '228 KG WET'], ['SEAT', '855 MM'],
            ],
            'features' => ['SEMI-ACTIVE SUSPENSION', 'TUBELESS SPOKES', 'HEATED GRIPS'],
            'colors' => [
                ['photo-1547549082-6bc09f2049ae', 'DESERT SAND', '#c9a15e'],
                ['photo-1609630875171-b1321377ee65', 'SAHARA ORANGE', '#e8963c'],
                ['photo-1591768793355-74d04bb6608f', 'BASALT', '#33333a'],
            ],
            'cc' => 889, 'hp' => 105, 'kg' => 228, 'top' => null,
        ],
        'nomad-dune-450' => [
            'name' => 'NOMAD DUNE 450', 'price' => 9800, 'monthly' => 219,
            'tagline' => 'Light enough to get lost.',
            'description' => 'A rally-raid inspired single that treats corrugated tracks like smooth tarmac. Road-legal, race-ready.',
            'specs' => [
                ['ENGINE', '450 CC SINGLE'], ['POWER', '52 HP'], ['TORQUE', '44 NM'],
                ['TANK', '16 L / 320 KM'], ['WEIGHT', '152 KG WET'], ['SEAT', '890 MM'],
            ],
            'features' => ['RALLY TOWER NAV', 'LONG-TRAVEL SUSPENSION', 'SKID PLATE'],
            'colors' => [
                ['photo-1531327431456-837da4b1d562', 'RALLY WHITE', '#e8e6e0'],
                ['photo-1622185135505-2d795003994a', 'SAHARA ORANGE', '#e8963c'],
            ],
            'cc' => 450, 'hp' => 52, 'kg' => 152, 'top' => null,
        ],
        'kaizen-rk-1' => [
            'name' => 'KAIZEN RK-1', 'price' => 28900, 'monthly' => 589,
            'tagline' => 'Born on the grid.',
            'description' => 'Our flagship superbike — MotoGP winglets, a 212 HP inline-four and an electronics suite that reads the corner before you do.',
            'specs' => [
                ['ENGINE', '998 CC INLINE-4'], ['POWER', '212 HP @ 13,500'], ['TORQUE', '113 NM'],
                ['WEIGHT', '199 KG WET'], ['0–100', '3.0 S'], ['TOP SPEED', '299 KM/H'],
            ],
            'features' => ['AERO WINGLETS', '6-AXIS IMU', 'LAUNCH CONTROL'],
            'colors' => [
                ['photo-1622185135505-2d795003994a', 'RACE RED', '#e10600'],
                ['photo-1449426468159-d96dbf08f19f', 'PHANTOM BLACK', '#0d0d10'],
            ],
            'cc' => 998, 'hp' => 212, 'kg' => 199, 'top' => 299,
        ],
        'kaizen-storm-1000r' => [
            'name' => 'KAIZEN STORM 1000R', 'price' => 21400, 'monthly' => 439,
            'tagline' => 'Superbike DNA, street manners.',
            'description' => 'A naked hyperbike with the RK-1 heart, detuned for the street and wrapped in aggressive minimal bodywork.',
            'specs' => [
                ['ENGINE', '998 CC INLINE-4'], ['POWER', '165 HP'], ['TORQUE', '104 NM'],
                ['WEIGHT', '193 KG WET'], ['0–100', '3.3 S'], ['TOP SPEED', '260 KM/H'],
            ],
            'features' => ['QUICKSHIFTER', 'CORNERING ABS', 'TFT DASH'],
            'colors' => [
                ['photo-1614165936126-2ed18e471b3b', 'STEALTH GREY', '#3a3a40'],
                ['photo-1591370874773-6702e8f12fd8', 'RACE RED', '#e10600'],
            ],
            'cc' => 998, 'hp' => 165, 'kg' => 193, 'top' => 260,
        ],
        'sovereign-regent' => [
            'name' => 'SOVEREIGN REGENT', 'price' => 42000, 'monthly' => 849,
            'tagline' => 'One of one hundred ninety-nine.',
            'description' => 'Hand-assembled around a torque-rich 1200 twin. Every Regent carries its builder’s signature inside the crankcase.',
            'specs' => [
                ['ENGINE', '1200 CC PARALLEL TWIN'], ['POWER', '98 HP'], ['TORQUE', '118 NM'],
                ['WEIGHT', '215 KG'], ['FRAME', 'HAND-TIGGED STEEL'], ['PRODUCTION', '199 UNITS / YEAR'],
            ],
            'features' => ['HAND-STITCHED SADDLE', 'BILLET CONTROLS', 'NUMBERED PLAQUE'],
            'colors' => [
                ['photo-1609630875171-b1321377ee65', 'OXFORD GREEN', '#1e3a2a'],
                ['photo-1562979314-bee7453e911c', 'CHAMPAGNE', '#d4af6e'],
                ['photo-1601758228041-f3b2795255f1', 'MIDNIGHT', '#101014'],
            ],
            'cc' => 1200, 'hp' => 98, 'kg' => 215, 'top' => null,
        ],
        'sovereign-crown-gt' => [
            'name' => 'SOVEREIGN CROWN GT', 'price' => 54500, 'monthly' => 1099,
            'tagline' => 'Grand touring, elevated.',
            'description' => 'A bespoke GT with adaptive aero, a champagne-grade finish and a ride-by-wire system calibrated over 40,000 road kilometres.',
            'specs' => [
                ['ENGINE', '1300 CC PARALLEL TWIN'], ['POWER', '140 HP'], ['TORQUE', '132 NM'],
                ['WEIGHT', '242 KG'], ['RANGE', '420 KM'], ['SEAT', '810 MM'],
            ],
            'features' => ['ADAPTIVE AERO', 'MASSAGE SADDLE', 'BESPOKE LUGGAGE'],
            'colors' => [
                ['photo-1591768793355-74d04bb6608f', 'CHAMPAGNE', '#d4af6e'],
                ['photo-1517846693594-1567da72af75', 'OBSIDIAN', '#15151a'],
            ],
            'cc' => 1300, 'hp' => 140, 'kg' => 242, 'top' => null,
        ],
    ];

    public function run(): void
    {
        foreach (self::BIKES as $slug => $b) {
            $moto = Motorcycle::where('slug', $slug)->first();
            if (! $moto) {
                continue;
            }

            $moto->update([
                'price' => $b['price'],
                'monthly_from' => $b['monthly'],
                'tagline' => $b['tagline'],
                'description' => $b['description'],
                'engine_cc' => $b['cc'],
                'power_hp' => $b['hp'],
                'weight_kg' => $b['kg'],
                'top_speed_kmh' => $b['top'],
                'specs' => collect($b['specs'])->map(fn ($s) => ['label' => $s[0], 'value' => $s[1]])->all(),
                'features' => $b['features'],
                'colors' => collect($b['colors'])->map(fn ($c) => ['name' => $c[1], 'hex' => $c[2]])->all(),
            ]);

            // One gallery photo per paint — uploaded files are kept, only the
            // colour metadata is relabelled; missing colours get a new photo.
            $images = $moto->images()->orderBy('sort_order')->get();
            foreach ($b['colors'] as $i => [$photo, $name, $hex]) {
                if (isset($images[$i])) {
                    $images[$i]->update(['color_name' => $name, 'color_hex' => $hex]);
                } else {
                    $moto->images()->create([
                        'path' => "https://images.unsplash.com/{$photo}?q=75&w=1600&auto=format&fit=crop",
                        'color_name' => $name,
                        'color_hex' => $hex,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        /* ---------- Hero — restore the shipped copy, keep any uploaded photo ---------- */

        $hero = SiteSection::firstWhere('key', 'hero');
        $payload = SiteContent::defaults()['hero'];
        $stored = $hero?->payload['image'] ?? null;
        if ($stored && Storage::disk('public')->exists($stored)) {
            $payload['image'] = $stored;
        }
        SiteSection::updateOrCreate(['key' => 'hero'], ['payload' => $payload]);
    }
}
