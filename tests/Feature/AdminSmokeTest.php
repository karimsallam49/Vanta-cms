<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_admin_sees_dashboard_and_all_models(): void
    {
        $admin = User::where('email', 'admin@vantamoto.com')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/motorcycles')
            ->assertOk()
            ->assertSee('VOLTA SPARK 350')
            ->assertSee('SOVEREIGN CROWN GT');
    }

    public function test_data_entry_sees_only_assigned_brands(): void
    {
        $entry = User::where('email', 'data@vantamoto.com')->firstOrFail();

        $response = $this->actingAs($entry)->get('/admin/motorcycles')->assertOk();
        $response->assertSee('VOLTA SPARK 350');
        $response->assertDontSee('SOVEREIGN CROWN GT');
    }

    public function test_data_entry_blocked_from_admin_routes_and_foreign_brand(): void
    {
        $entry = User::where('email', 'data@vantamoto.com')->firstOrFail();
        $foreign = \App\Models\Motorcycle::where('name', 'SOVEREIGN CROWN GT')->firstOrFail();

        $this->actingAs($entry)->get('/admin/users')->assertForbidden();
        $this->actingAs($entry)->get("/admin/motorcycles/{$foreign->id}/edit")->assertForbidden();
    }
}
