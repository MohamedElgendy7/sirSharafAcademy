<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_permissions_page_marks_enabled_permissions_as_checked()
    {
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Permission::create([
            'user_id' => $admin->id,
            'can_view_students_current' => true,
            'can_view_courses' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.permissions.index'))
            ->assertOk()
            ->assertSee('name="can_view_students_current"', false)
            ->assertSee('checked', false);
    }
}
