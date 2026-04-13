<?php

namespace App\Services\Telegram;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TelegramBotService
{
    public function sendText(string $message, array $options = []): array
    {
        return $this->request('sendMessage', array_merge([
            'chat_id' => (string) config('services.telegram.chat_id'),
            'text' => $message,
            'disable_web_page_preview' => true,
        ], $options));
    }

    public function sendPhoto(string $path, ?string $caption = null, array $options = []): array
    {
        return $this->request(
            'sendPhoto',
            array_merge([
                'chat_id' => (string) config('services.telegram.chat_id'),
                'caption' => $caption,
            ], $options),
            [[
                'name' => 'photo',
                'path' => $path,
            ]],
        );
    }

    public function sendDocument(string $path, ?string $caption = null, array $options = []): array
    {
        return $this->request(
            'sendDocument',
            array_merge([
                'chat_id' => (string) config('services.telegram.chat_id'),
                'caption' => $caption,
            ], $options),
            [[
                'name' => 'document',
                'path' => $path,
            ]],
        );
    }

    public function editMessageText(string|int $chatId, int $messageId, string $text, array $options = []): array
    {
        return $this->request('editMessageText', array_merge([
            'chat_id' => (string) $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'disable_web_page_preview' => true,
        ], $options));
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, array $options = []): array
    {
        return $this->request('answerCallbackQuery', array_merge([
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ], $options));
    }

    private function request(string $method, array $payload = [], array $files = []): array
    {
        $token = (string) config('services.telegram.bot_token');
        $baseUrl = rtrim((string) config('services.telegram.base_url', 'https://api.telegram.org'), '/');

        if ($token === '' || (($payload['chat_id'] ?? '') === '' && $method !== 'answerCallbackQuery')) {
            Log::warning('Telegram notification skipped because credentials are incomplete.', [
                'method' => $method,
            ]);

            return [];
        }

        try {
            $request = Http::timeout(15)->retry(2, 500);

            if ($files !== []) {
                foreach ($files as $file) {
                    $path = $file['path'];

                    if (!is_file($path)) {
                        throw new RuntimeException("Telegram attachment file not found: {$path}");
                    }

                    $request = $request->attach(
                        $file['name'],
                        fopen($path, 'r'),
                        basename($path),
                        ['Content-Type' => mime_content_type($path) ?: 'application/octet-stream'],
                    );
                }

                $payload = $this->prepareMultipartPayload($payload);
            }

            $response = $request->post("{$baseUrl}/bot{$token}/{$method}", $payload);

            if ($response->failed()) {
                Log::error('Telegram Bot API request failed.', [
                    'method' => $method,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $response->throw();
            }

            $decodedPayload = $response->json();

            if (!is_array($decodedPayload) || !($decodedPayload['ok'] ?? false)) {
                Log::error('Telegram Bot API returned an invalid payload.', [
                    'method' => $method,
                    'payload' => $decodedPayload,
                ]);

                throw new RuntimeException('Telegram Bot API returned an invalid payload.');
            }

            return is_array($decodedPayload['result'] ?? null) ? $decodedPayload['result'] : [];
        } catch (RequestException $exception) {
            Log::error('Telegram notification request exception.', [
                'method' => $method,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Telegram notification sending failed.', [
                'method' => $method,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function prepareMultipartPayload(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            }
        }

        return array_filter($payload, static fn (mixed $value): bool => $value !== null);
    }
}
