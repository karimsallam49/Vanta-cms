<?php

namespace App\Support;

/**
 * Registry of editable public-website sections.
 *
 * Each key maps to one `site_sections` row (JSON payload). Field types:
 *   text | textarea | lines | image | color
 * 'lines' = one entry per line (stored as newline-separated text).
 */
class SiteContent
{
    private const HEADING = [
        'eyebrow' => ['text', 'Eyebrow'],
        't1' => ['text', 'Title — line 1'],
        't2' => ['text', 'Title — line 2 (accent)'],
        'copy' => ['textarea', 'Intro copy'],
    ];

    public static function sections(): array
    {
        return [
            /* ---------- Homepage ---------- */
            'hero' => [
                'label' => 'Hero', 'group' => 'Homepage',
                'fields' => [
                    'kicker' => ['text', 'Kicker — top left'],
                    'est' => ['text', 'Corner tag — top right'],
                    'l1' => ['text', 'Headline — line 1'],
                    'l2' => ['text', 'Headline — line 2'],
                    'l3' => ['text', 'Headline — line 3 (dot is added)'],
                    'sub' => ['textarea', 'Subtitle'],
                    'image' => ['image', 'Background photo'],
                ],
            ],
            'group' => [
                'label' => 'Group intro', 'group' => 'Homepage',
                'fields' => [
                    'eyebrow' => ['text', 'Eyebrow'],
                    't1' => ['text', 'Title — line 1'],
                    't2' => ['text', 'Title — line 2 (accent)'],
                    'copy' => ['textarea', 'Intro copy'],
                    'image' => ['image', 'Side photo'],
                    'image_caption' => ['text', 'Photo caption'],
                    'stat_years' => ['text', 'Stat — years'],
                    'stat_brands' => ['text', 'Stat — brands'],
                    'stat_showrooms' => ['text', 'Stat — showrooms'],
                    'stat_riders' => ['text', 'Stat — riders'],
                ],
            ],
            'footer' => [
                'label' => 'Footer', 'group' => 'Homepage',
                'fields' => [
                    'tagline' => ['textarea', 'Tagline paragraph'],
                    'feel' => ['text', 'Giant sign-off text'],
                    'phone' => ['text', 'Contact phone'],
                    'email' => ['text', 'Contact e-mail'],
                ],
            ],

            /* ---------- Section headings ---------- */
            'worlds_head' => ['label' => 'Worlds — heading', 'group' => 'Section headings', 'fields' => self::HEADING],
            'models_head' => [
                'label' => 'Motorcycles — heading', 'group' => 'Section headings',
                'fields' => [
                    'eyebrow' => ['text', 'Eyebrow'],
                    'title' => ['text', 'Title'],
                    'copy' => ['textarea', 'Intro copy'],
                ],
            ],
            'offers_head' => ['label' => 'Offers — heading', 'group' => 'Section headings', 'fields' => self::HEADING],
            'testride_head' => [
                'label' => 'Test ride — heading', 'group' => 'Section headings',
                'fields' => self::HEADING + ['cta' => ['text', 'CTA label'], 'image' => ['image', 'Background photo']]
                    + self::cardFields('step1', 'Step 01 — Choose') + self::cardFields('step2', 'Step 02 — Book') + self::cardFields('step3', 'Step 03 — Ride'),
            ],
            'showrooms_head' => ['label' => 'Showrooms — heading', 'group' => 'Section headings', 'fields' => self::HEADING],
            'ownership_head' => [
                'label' => 'Ownership — heading', 'group' => 'Section headings',
                'fields' => self::HEADING + [
                    'hotline' => ['text', 'Hotline label'],
                    'phone' => ['text', 'Hotline number'],
                    'email' => ['text', 'Care e-mail'],
                ],
            ],

            /* ---------- Worlds ---------- */
            'world-urban' => ['label' => 'Urban', 'group' => 'Worlds', 'fields' => self::worldFields()],
            'world-adventure' => ['label' => 'Adventure & Touring', 'group' => 'Worlds', 'fields' => self::worldFields()],
            'world-performance' => ['label' => 'Performance', 'group' => 'Worlds', 'fields' => self::worldFields()],
            'world-premium' => ['label' => 'Premium', 'group' => 'Worlds', 'fields' => self::worldFields()],

            /* ---------- Ownership services ---------- */
            'svc-service' => ['label' => 'Service & maintenance', 'group' => 'Ownership services', 'fields' => self::serviceFields()],
            'svc-parts' => ['label' => 'Parts & accessories', 'group' => 'Ownership services', 'fields' => self::serviceFields()],
            'svc-warranty' => ['label' => 'Warranty support', 'group' => 'Ownership services', 'fields' => self::serviceFields()],
            'svc-care' => ['label' => 'Customer care', 'group' => 'Ownership services', 'fields' => self::serviceFields()],
        ];
    }

    private static function worldFields(): array
    {
        return [
            'name' => ['text', 'Name (URBAN)'],
            'tagline' => ['text', 'Tagline (URBAN MOBILITY)'],
            'title' => ['text', 'Title (OWN THE CITY)'],
            'desc' => ['textarea', 'Description'],
            'stat_value' => ['text', 'Stat value (400 KM)'],
            'stat_label' => ['text', 'Stat label (REAL-WORLD RANGE)'],
            'accent' => ['color', 'Accent colour'],
            'image' => ['image', 'Background photo'],
        ];
    }

    private static function cardFields(string $prefix, string $label): array
    {
        return [
            "{$prefix}_title" => ['text', "{$label} — title"],
            "{$prefix}_copy" => ['textarea', "{$label} — copy"],
        ];
    }

    private static function serviceFields(): array
    {
        return [
            'title' => ['text', 'Title'],
            'desc' => ['textarea', 'Description'],
            'points' => ['lines', 'Bullet points — one per line'],
        ];
    }

    /**
     * Default payloads — seeded so every section shows the same copy the
     * bundled frontend ships with. Editable afterwards from the CMS.
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'kicker' => 'PREMIUM MOTO GROUP — MIDDLE EAST', 'est' => 'EST. 2012',
                'l1' => 'BORN TO', 'l2' => 'BREAK', 'l3' => 'LIMITS',
                'sub' => "Four worlds. Nine showrooms. One obsession. The region's most wanted machines — waiting for your right hand.",
            ],
            'group' => [
                'eyebrow' => 'THE GROUP',
                't1' => "WE DON'T SELL MOTORCYCLES.", 't2' => 'WE DELIVER FEELING.',
                'copy' => "VANTA MOTO is the Middle East's premium multi-brand motorcycle group — four marques, four worlds, one standard: machines that make your pulse part of the drivetrain. From electric city commuters to hand-built flagship tourers, every bike we put on the floor has earned its place.",
                'image_caption' => 'FLAGSHIP FLOOR — DUBAI',
                'stat_years' => '14', 'stat_brands' => '04', 'stat_showrooms' => '09', 'stat_riders' => '38K',
            ],
            'footer' => [
                'tagline' => "The Middle East's premium multi-brand motorcycle group. Urban, Adventure, Performance and Premium machines — engineered for the region, tuned for feeling.",
                'feel' => 'FEEL THE RIDE',
                'phone' => '+971 4 555 0100', 'email' => 'RIDE@VANTAMOTO.COM',
            ],
            'worlds_head' => [
                'eyebrow' => 'FOUR WORLDS', 't1' => 'PICK YOUR', 't2' => 'POISON',
                'copy' => 'Every rider has a world. Ours has four — each with its own machines, its own physics and its own definition of a perfect day.',
            ],
            'models_head' => [
                'eyebrow' => 'THE MACHINES', 'title' => 'FEATURED MOTORCYCLES',
                'copy' => 'Eight machines across four worlds. Pick a segment, pick a weapon, pick a colour — then come feel it in person.',
            ],
            'offers_head' => [
                'eyebrow' => 'OFFERS', 't1' => 'PROMOTIONS &', 't2' => 'FINANCE',
                'copy' => 'Limited-time deals across the range — financing, packs and trade-in bonuses.',
            ],
            'testride_head' => [
                'eyebrow' => 'TEST RIDE', 't1' => 'READING ABOUT IT', 't2' => "ISN'T RIDING IT.",
                'copy' => "Thirty minutes. Your machine, our route, zero sales pressure. If it doesn't move you, we'll show you one that will.",
                'cta' => 'BOOK A TEST RIDE →',
                'step1_title' => 'Choose your machine',
                'step1_copy' => 'Pick any model from our current range — street, cruiser, or adventure.',
                'step2_title' => 'Book a slot',
                'step2_copy' => 'Select a showroom and a time that suits you. Confirmation within 24 hours.',
                'step3_title' => 'Ride with us',
                'step3_copy' => 'Meet our instructors, gear up, and take a guided ride on curated routes.',
            ],
            'showrooms_head' => [
                'eyebrow' => 'FIND US', 't1' => 'SHOWROOMS ACROSS', 't2' => 'THE REGION',
                'copy' => 'Five flagship floors, one standard. Walk in for a coffee, walk out with a test ride.',
            ],
            'ownership_head' => [
                'eyebrow' => 'AFTER-SALES', 't1' => 'OWNERSHIP,', 't2' => 'ELEVATED.',
                'copy' => 'Buying the machine is the easy part. What happens after is where a motorcycle group earns its name — service centers, genuine parts, warranty cover and people who pick up the phone.',
                'hotline' => 'ROADSIDE ASSISTANCE — REGION-WIDE',
                'phone' => '+971 800 555 0123', 'email' => 'CARE@VANTAMOTO.COM',
            ],
            'world-urban' => [
                'name' => 'URBAN', 'tagline' => 'URBAN MOBILITY', 'title' => 'OWN THE CITY',
                'desc' => 'Silent torque, razor handling, zero compromise. Electric and lightweight machines built to slice through traffic and outrun the commute.',
                'stat_value' => '140 KM', 'stat_label' => 'REAL-WORLD RANGE', 'accent' => '#c8ff3d',
            ],
            'world-adventure' => [
                'name' => 'ADVENTURE', 'tagline' => 'ADVENTURE & TOURING', 'title' => 'LEAVE THE MAP',
                'desc' => 'Long-range tanks, desert-ready suspension and luggage systems engineered for the Empty Quarter — and everything beyond it.',
                'stat_value' => '480 KM', 'stat_label' => 'FUEL RANGE', 'accent' => '#e8963c',
            ],
            'world-performance' => [
                'name' => 'PERFORMANCE', 'tagline' => 'PERFORMANCE', 'title' => 'APEX PREDATORS',
                'desc' => 'Track-bred machinery with MotoGP-derived electronics. Cornering ABS, launch control and aerodynamics measured in milliseconds.',
                'stat_value' => '212 HP', 'stat_label' => 'PEAK POWER', 'accent' => '#ff3b30',
            ],
            'world-premium' => [
                'name' => 'PREMIUM', 'tagline' => 'PREMIUM MOTORCYCLES', 'title' => 'BUILT BY HAND',
                'desc' => 'Limited-production machines assembled by master builders. Billet aluminium, hand-stitched leather and paintwork measured in days, not minutes.',
                'stat_value' => '199 UNITS', 'stat_label' => 'ANNUAL PRODUCTION', 'accent' => '#d4af6e',
            ],
            'svc-service' => [
                'title' => 'SERVICE & MAINTENANCE',
                'desc' => 'Factory-trained technicians, genuine tooling and a digital service history that follows your VIN — not your paperwork.',
                'points' => "PICKUP & DELIVERY\nSAME-DAY SERVICE\nDIGITAL HISTORY",
            ],
            'svc-parts' => [
                'title' => 'PARTS & ACCESSORIES',
                'desc' => 'Genuine OEM parts, homologated performance kits and a customization studio. Regional warehouse means 48-hour delivery, not 6-week waits.',
                'points' => "GENUINE OEM\nCUSTOM STUDIO\n48H DELIVERY",
            ],
            'svc-warranty' => [
                'title' => 'WARRANTY SUPPORT',
                'desc' => 'Two years standard, extendable to five. Pan-regional coverage honoured at every VANTA service center — plus roadside assistance that actually answers.',
                'points' => "UP TO 5 YEARS\nPAN-REGIONAL\nROADSIDE ASSIST",
            ],
            'svc-care' => [
                'title' => 'CUSTOMER CARE',
                'desc' => 'A dedicated concierge line, rider community events, and humans who ride the same machines they sell you.',
                'points' => "CONCIERGE LINE\nRIDER EVENTS\n24/7 HOTLINE",
            ],
        ];
    }

    /** Field types that carry an Arabic twin (`{name}_ar` in the payload). */
    public static function localizable(string $type): bool
    {
        return in_array($type, ['text', 'textarea', 'lines'], true);
    }

    /** Validation rules per field type. */
    public static function rulesFor(array $fields): array
    {
        $rules = [];
        foreach ($fields as $name => [$type]) {
            $rules[$name] = match ($type) {
                'text' => ['nullable', 'string', 'max:255'],
                'textarea', 'lines' => ['nullable', 'string', 'max:4000'],
                'image' => ['nullable', 'image', 'max:6144'],
                'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            };
            if (self::localizable($type)) {
                $rules["{$name}_ar"] = $rules[$name];
            }
            if ($type === 'image') {
                $rules["{$name}_remove"] = ['nullable', 'boolean'];
            }
        }

        return $rules;
    }
}
