<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequestRequest;
use App\Jobs\SendProjectRequestTelegramNotificationJob;
use App\Models\ProjectRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProjectRequestController extends Controller
{
    public function create(): View
    {
        return view('pages.project-request', [
            'pageKey' => 'project-request',
            'seoTitle' => __('site.meta.project_request.title'),
            'seoDescription' => __('site.meta.project_request.description'),
        ]);
    }

    public function store(StoreProjectRequestRequest $request): RedirectResponse
    {
        $payload = [
            'name' => $request->string('name')->squish()->toString(),
            'contact' => $request->string('contact')->squish()->toString(),
            'task' => $request->string('task')->squish()->toString(),
        ];

        $duplicateExists = ProjectRequest::query()
            ->where('name', $payload['name'])
            ->where('contact', $payload['contact'])
            ->where('task', $payload['task'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($duplicateExists) {
            throw ValidationException::withMessages([
                'duplicate_request' => __('site.messages.duplicate_request'),
            ]);
        }

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('project-requests', 'public')
            : null;

        $projectRequest = ProjectRequest::create([
            'name' => $payload['name'],
            'contact' => $payload['contact'],
            'task' => $payload['task'],
            'attachment_path' => $attachmentPath,
        ]);

        SendProjectRequestTelegramNotificationJob::dispatch($projectRequest->id);

        return redirect()
            ->route('project-request')
            ->with('project_request_success', __('site.messages.project_request_sent'));
    }
}
