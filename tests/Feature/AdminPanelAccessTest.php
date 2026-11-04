<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_and_cms_resources(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/posts')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/questions')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/settings')
            ->assertOk();
    }

    public function test_viewer_can_view_resources_but_cannot_access_create_pages(): void
    {
        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->get('/admin/posts')
            ->assertOk();

        $this->actingAs($viewer)
            ->get('/admin/posts/create')
            ->assertForbidden();
    }
}
