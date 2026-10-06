<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Register the configured public HTTPS URL as the Telegram webhook';

    public function handle(): int
    {
        $token = config('scheduler.telegram.bot_token');
        $secret = config('scheduler.telegram.webhook_secret');
        $chatId = config('scheduler.telegram.chat_id');
        $url = rtrim((string) config('app.url'), '/').'/telegram/webhook';
        $parts = parse_url($url);

        if (! $token || ! $secret || ! $chatId) {
            $this->error('Set TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID, and TELEGRAM_WEBHOOK_SECRET in .env first.');
            return self::FAILURE;
        }
        if (($parts['scheme'] ?? null) !== 'https' || in_array($parts['host'] ?? '', ['localhost', '127.0.0.1'], true)) {
            $this->error('Set APP_URL to your public HTTPS domain before registering the webhook.');
            return self::FAILURE;
        }

        try {
            $response = Http::timeout(15)->post("https://api.telegram.org/bot{$token}/setWebhook", [
                'url' => $url,
                'secret_token' => $secret,
                'allowed_updates' => ['message'],
            ])->throw();
            if (! $response->json('ok')) throw new \RuntimeException();
        } catch (\Throwable) {
            $this->error('Telegram did not accept the webhook. Check the public HTTPS URL and try again.');
            return self::FAILURE;
        }

        $this->info('Telegram webhook registered successfully.');
        return self::SUCCESS;
    }
}
