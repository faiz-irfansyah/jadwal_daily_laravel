<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramDiscoverChatId extends Command
{
    protected $signature = 'telegram:discover-chat-id';
    protected $description = 'Read pending Telegram updates and show chat IDs without displaying the bot token';

    public function handle(): int
    {
        $token = config('scheduler.telegram.bot_token');
        if (! $token) {
            $this->error('Set TELEGRAM_BOT_TOKEN in .env first.');
            return self::FAILURE;
        }

        try {
            $response = Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getUpdates")->throw();
            if (! $response->json('ok')) throw new \RuntimeException();
        } catch (\Throwable) {
            $this->error('Telegram could not return updates. Send /start to your bot and ensure a webhook is not already set.');
            return self::FAILURE;
        }

        $chats = collect($response->json('result', []))
            ->map(fn (array $update) => data_get($update, 'message.chat'))
            ->filter(fn ($chat) => is_array($chat) && isset($chat['id']))
            ->unique('id')
            ->values();

        if ($chats->isEmpty()) {
            $this->warn('No incoming messages found. Open your bot in Telegram, send /start, then run this command again.');
            return self::SUCCESS;
        }

        $this->table(['Chat ID', 'Name', 'Username'], $chats->map(fn (array $chat) => [
            (string) $chat['id'], $chat['first_name'] ?? ($chat['title'] ?? ''), $chat['username'] ?? '',
        ])->all());
        $this->line('Copy the ID for your private chat into TELEGRAM_CHAT_ID in .env.');

        return self::SUCCESS;
    }
}
