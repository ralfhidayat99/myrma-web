<?php

namespace App\Console\Commands;

use App\Models\FcmToken;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNotifUnrespondedLemburan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:send-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily emails to all supervisor';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Log::info("Cron job Berhasil di jalankan " . date('Y-m-d H:i:s'));
        FcmToken::create(['token' => 'test']);
    }
}
