<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('This is a test email from Lakbay Calamba. If you receive this, your email configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Lakbay Calamba');
            });
            
            $this->info("Test email sent successfully to {$email}!");
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
        }
    }
}
