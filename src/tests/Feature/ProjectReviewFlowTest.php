<?php

namespace Tests\Feature;

use App\Jobs\SendProjectRequestTelegramNotificationJob;
use App\Jobs\SendReviewTelegramNotificationJob;
use App\Models\ProjectRequest;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_request_create_stores_attachment_and_dispatches_job(): void
    {
        Storage::fake('public');
        Queue::fake();

        $response = $this->post(route('project-request.store'), [
            'name' => 'Александр',
            'contact' => 'alex@example.com',
            'task' => 'Нужна картина для спальни',
            'attachment' => UploadedFile::fake()->image('reference.jpg'),
        ]);

        $response->assertRedirect(route('project-request'));
        $response->assertSessionHas('project_request_success');

        $projectRequest = ProjectRequest::query()->first();

        $this->assertNotNull($projectRequest);
        $this->assertNotNull($projectRequest->attachment_path);
        Storage::disk('public')->assertExists($projectRequest->attachment_path);

        Queue::assertPushed(
            SendProjectRequestTelegramNotificationJob::class,
            fn (SendProjectRequestTelegramNotificationJob $job) => $job->projectRequestId === $projectRequest->id
        );
    }

    public function test_project_request_rejects_recent_duplicate_submission(): void
    {
        Queue::fake();

        $payload = [
            'name' => 'Александр',
            'contact' => '@alex',
            'task' => 'Нужна картина для кабинета',
        ];

        $this->post(route('project-request.store'), $payload)
            ->assertRedirect(route('project-request'));

        $this->from(route('project-request'))
            ->post(route('project-request.store'), $payload)
            ->assertRedirect(route('project-request'))
            ->assertSessionHasErrors('duplicate_request');

        $this->assertDatabaseCount('project_requests', 1);
        Queue::assertPushed(SendProjectRequestTelegramNotificationJob::class, 1);
    }

    public function test_review_create_saves_unpublished_record_and_dispatches_job(): void
    {
        Storage::fake('public');
        Queue::fake();

        $response = $this->post(route('reviews.store'), [
            'author_name' => 'Мария',
            'author_city' => 'Санкт-Петербург',
            'text' => 'Отличная работа',
            'rating' => 5,
            'image' => UploadedFile::fake()->image('review.jpg'),
        ]);

        $response->assertRedirect(route('reviews'));
        $response->assertSessionHas('review_success');

        $review = Review::query()->first();

        $this->assertNotNull($review);
        $this->assertFalse($review->is_published);
        Storage::disk('public')->assertExists($review->image_path);

        Queue::assertPushed(
            SendReviewTelegramNotificationJob::class,
            fn (SendReviewTelegramNotificationJob $job) => $job->reviewId === $review->id
        );
    }

    public function test_reviews_page_shows_only_published_reviews(): void
    {
        Review::factory()->published()->create([
            'author_name' => 'Публичный отзыв',
            'text' => 'Этот отзыв должен быть виден.',
        ]);

        Review::factory()->create([
            'author_name' => 'Скрытый отзыв',
            'text' => 'Этот отзыв не должен быть виден.',
        ]);

        $response = $this->get(route('reviews'));

        $response->assertOk();
        $response->assertSee('Публичный отзыв');
        $response->assertDontSee('Скрытый отзыв');
    }
}
