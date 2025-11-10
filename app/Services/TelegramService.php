<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected string $token;
    protected string $chatId;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }

    public function sendMessage(string $message): bool
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $response = Http::get($url, [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'Markdown', // ✅ fa interpretare * e \n correttamente
        ]);

        return $response->successful();
    }
}
