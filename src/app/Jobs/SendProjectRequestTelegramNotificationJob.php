<?php

namespace App\Jobs;

use App\Models\ProjectRequest;
use App\Services\Telegram\TelegramBotService;
use App\Support\LocalMedia;
use App\Support\Telegram\TelegramMessageFormatter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendProjectRequestTelegramNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $projectRequestId,
    ) {
    }

    public function handle(TelegramBotService $telegram): void
    {
        $projectRequest = ProjectRequest::query()->find($this->projectRequestId);

        if (!$projectRequest) {
            return;
        }

        $attachmentPath = LocalMedia::resolvePath($projectRequest->attachment_path);

        if ($attachmentPath) {
            if (LocalMedia::isImage($projectRequest->attachment_path)) {
                $telegram->sendPhoto($attachmentPath, TelegramMessageFormatter::projectAttachmentCaption($projectRequest));
            } else {
                $telegram->sendDocument($attachmentPath, TelegramMessageFormatter::projectAttachmentCaption($projectRequest));
            }
        }

        $telegram->sendText(TelegramMessageFormatter::projectRequestText($projectRequest));

        $fullText = TelegramMessageFormatter::projectRequestFullText($projectRequest);

        if ($fullText) {
            $telegram->sendText($fullText);
        }
    }
}
