<?php

use App\Models\ActivityLog;
use App\Models\Motorcycle;
use App\Models\Offer;
use App\Models\Showroom;
use App\Models\SiteSection;
use App\Models\TestRideRequest;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
 * Public API consumed by the VANTA MOTO frontend (vanta-moto).
 * Read-only — only data that is safe to show publicly is exposed.
 */

Route::get('showrooms', function () {
    $showrooms = Showroom::with(['motorcycles' => fn ($q) => $q
            ->where('is_active', true)
            ->with('brand', 'currency', 'images')])
        ->orderBy('country')->orderBy('city')
        ->get();

    return response()->json([
        'data' => $showrooms->map(fn (Showroom $s) => [
            'id' => (string) $s->id,
            'name' => $s->name,
            'city' => $s->city,
            'country' => $s->country,
            'address' => $s->address,
            'phone' => $s->phone,
            'hours' => $s->hours,
            'isServiceCenter' => $s->is_service_center,
            // Weekdays open for test rides (0 = Sun … 6 = Sat); null = every day.
            'openDays' => $s->open_days,
            'location' => ($s->latitude && $s->longitude) ? [
                'lat' => $s->latitude,
                'lng' => $s->longitude,
                'osmUrl' => "https://www.openstreetmap.org/?mlat={$s->latitude}&mlon={$s->longitude}#map=16/{$s->latitude}/{$s->longitude}",
            ] : null,
            'motorcycles' => $s->motorcycles->map(fn ($m) => [
                'id' => (string) $m->id,
                'slug' => $m->slug,
                'name' => $m->name,
                'brand' => $m->brand->name,
                'brandSlug' => $m->brand->slug,
                'price' => $m->price,
                'currency' => $m->currency?->code,
                'image' => $m->coverUrl(),
                'colors' => $m->images->pluck('color_hex')->filter()
                    ->concat(collect($m->colors ?? [])->pluck('hex'))
                    ->filter()->unique()->values(),
            ])->values(),
        ]),
    ]);
})->name('api.showrooms');

Route::get('motorcycles', function () {
    $bikes = Motorcycle::where('is_active', true)
        ->with(['brand', 'currency', 'images', 'showrooms'])
        ->orderBy('brand_id')->orderBy('name')
        ->get();

    return response()->json([
        'data' => $bikes->map(fn (Motorcycle $m) => [
            'id' => $m->slug,
            'slug' => $m->slug,
            'name' => $m->name,
            'nameAr' => $m->name_ar,
            'brandId' => $m->brand->slug,
            'brand' => $m->brand->name,
            'segment' => $m->brand->segment,
            'tagline' => $m->tagline,
            'taglineAr' => $m->tagline_ar,
            'description' => $m->description,
            'descriptionAr' => $m->description_ar,
            'price' => $m->price,
            'monthlyFrom' => $m->monthly_from,
            'currency' => $m->currency?->code,
            'image' => $m->coverUrl(),
            // Gallery colours first (they carry a photo to swap to); model
            // paint JSON fills any gaps as tint-only swatches.
            'colors' => $m->images
                ->map(fn ($i) => $i->color_hex ? [
                    'name' => $i->color_name,
                    'hex' => $i->color_hex,
                    'image' => $i->url,
                ] : null)
                ->filter()
                ->concat(collect($m->colors ?? [])->map(fn ($c) => [
                    'name' => $c['name'] ?? null,
                    'hex' => $c['hex'] ?? null,
                    'image' => null,
                ]))
                ->unique('hex')->values(),
            // Custom spec table wins; the four numeric columns are the
            // fallback for rows that never filled one in.
            'specs' => collect($m->specs ?? [])
                ->map(fn ($s) => ['label' => $s['label'] ?? '', 'value' => $s['value'] ?? ''])
                ->filter(fn ($s) => $s['label'] !== '' && $s['value'] !== '')
                ->values()->all()
                ?: array_values(array_filter([
                    $m->engine_cc ? ['label' => 'ENGINE', 'value' => $m->engine_cc.' CC'] : null,
                    $m->power_hp ? ['label' => 'POWER', 'value' => $m->power_hp.' HP'] : null,
                    $m->weight_kg ? ['label' => 'WEIGHT', 'value' => $m->weight_kg.' KG'] : null,
                    $m->top_speed_kmh ? ['label' => 'TOP SPEED', 'value' => $m->top_speed_kmh.' KM/H'] : null,
                ])),
            'features' => array_values($m->features ?? []),
            'showroomIds' => $m->showrooms->pluck('id')->map(fn ($id) => (string) $id)->values(),
        ]),
    ]);
})->name('api.motorcycles');

Route::get('offers', function () {
    $offers = Offer::active()->with('brand', 'motorcycle')->latest()->get();

    return response()->json([
        'data' => $offers->map(fn (Offer $o) => [
            'id' => (string) $o->id,
            'title' => $o->title,
            'description' => $o->description,
            'badge' => $o->badge,
            'image' => $o->imageUrl(),
            'discountPercent' => $o->discount_percent,
            'scope' => $o->scope->value,
            'target' => $o->targetName(),
            'expiresOn' => $o->expires_on?->toDateString(),
        ]),
    ]);
})->name('api.offers');

Route::get('sections', function () {
    $defs = SiteContent::sections();
    $payloads = SiteSection::allPayloads();

    $data = collect($defs)->map(function (array $def, string $key) use ($payloads) {
        $payload = $payloads[$key] ?? [];
        // Only expose fields declared in the schema; turn image paths into URLs.
        return collect($def['fields'])
            ->mapWithKeys(function (array $field, string $name) use ($payload) {
                $value = $payload[$name] ?? null;
                if ($field[0] === 'image' && $value) {
                    $value = asset('storage/'.$value);
                }
                $out = [$name => $value];
                // Localizable fields always emit their _ar twin (possibly
                // empty) so the frontend can tell "no Arabic yet" apart
                // from "field isn't translatable" — colors, images, phones.
                if (SiteContent::localizable($field[0])) {
                    $out["{$name}_ar"] = $payload["{$name}_ar"] ?? '';
                }
                return $out;
            })
            ->filter(fn ($v, $k) => str_ends_with($k, '_ar') || ($v !== null && $v !== ''))
            ->all();
    })->filter();

    return response()->json(['data' => $data]);
})->name('api.sections');

/*
 * Public test-ride booking. Accepts the CMS id or the slug the frontend
 * uses for both motorcycle and showroom so it survives bundled-fallback ids.
 */
Route::post('test-rides', function (Request $request) {
    $data = $request->validate([
        'motorcycle' => ['required', 'string', 'max:80'],
        'showroom' => ['required', 'string', 'max:120'],
        'date' => ['required', 'date', 'after:today'],
        'name' => ['required', 'string', 'max:140'],
        'phone' => ['required', 'string', 'max:40'],
        'email' => ['nullable', 'email', 'max:140'],
    ]);

    $bike = Motorcycle::where('is_active', true)
        ->where(fn ($q) => $q->where('slug', $data['motorcycle'])
            ->orWhere('id', $data['motorcycle'])
            // bundled seed ids can be a prefix of the real slug
            ->orWhere('slug', 'like', $data['motorcycle'].'-%'))
        ->first();
    abort_unless($bike, 422, 'Unknown motorcycle.');

    $needle = Str::slug($data['showroom']);
    $showroom = Showroom::all()->first(fn (Showroom $s) =>
        (string) $s->id === $data['showroom']
        || Str::slug($s->city) === $needle
        || Str::slug($s->name) === $needle
        || Str::slug("{$s->city}-{$s->name}") === $needle
    );
    abort_unless($showroom, 422, 'Unknown showroom.');

    // The model must actually be on that floor (empty pivot = open to all).
    if ($showroom->motorcycles()->exists()
        && ! $showroom->motorcycles()->where('motorcycles.id', $bike->id)->exists()) {
        return response()->json([
            'message' => 'This model is not available at the selected showroom.',
            'errors' => ['motorcycle' => ['Not available at this showroom']],
        ], 422);
    }

    // Closed weekdays — open_days null means "open every day".
    $openDays = $showroom->open_days;
    if (is_array($openDays) && $openDays !== []
        && ! in_array((int) date('w', strtotime($data['date'])), array_map('intval', $openDays), true)) {
        return response()->json([
            'message' => 'The showroom is closed on that day.',
            'errors' => ['date' => ['Showroom closed on this day']],
        ], 422);
    }

    $ride = TestRideRequest::create([
        'motorcycle_id' => $bike->id,
        'showroom_id' => $showroom->id,
        'name' => $data['name'],
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'],
        'preferred_date' => $data['date'],
        'status' => \App\Enums\TestRideStatus::Pending,
    ]);

    $reference = 'VTR-'.str_pad((string) $ride->id, 5, '0', STR_PAD_LEFT);
    ActivityLog::record('created', $ride, "Test ride request {$reference} (website)");

    return response()->json(['reference' => $reference, 'id' => $ride->id], 201);
})->middleware('throttle:10,1')->name('api.test-rides.store');
