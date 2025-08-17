<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SubscriberCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:subscriber-command';
    protected $signature = 'redis:subscriber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'listen to redis message';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $channel = "laravel-events";

        Redis::subscribe([$channel], function ($message) {
            $data = json_decode($message, true);
            Log::info("message from node : " . json_encode($data));
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $source = isset($data['source']) ? $data['source'] : 'unknown';
                $msg = isset($data['message']) ? $data['message'] : $message;
                if(strtoupper($source) != "LARAVEL")
                Message::create(
                    [
                        'message' => $msg,
                        'source' => $source,
                    ]
                );

                echo "[" . strtoupper($source) . "] " . $msg . "\n";
            } else {
                echo "[RAW] " . $message . "\n";
            }
        });

        return Command::SUCCESS;
    }
}
