<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailConfirmation;

class CleanupExpiredConfirmations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired email confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = EmailConfirmation::cleanupExpired();
        
        $this->info("Cleaned up {$deletedCount} expired email confirmations.");
        
        return Command::SUCCESS;
    }
}
