<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Painting;
use App\Models\ProjectRequest;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dashboard_and_resource_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $category = Category::factory()->create();
        $painting = Painting::factory()->for($category)->create();
        $order = Order::factory()->create();
        $projectRequest = ProjectRequest::factory()->create();
        $review = Review::factory()->create();

        $this->actingAs($admin)
            ->get(route('filament.admin.pages.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.categories.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.paintings.edit', $painting))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.orders.edit', $order))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.project-requests.view', $projectRequest))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.reviews.edit', $review))
            ->assertOk();
    }
}
