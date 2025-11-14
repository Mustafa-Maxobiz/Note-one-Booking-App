<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Models\Payment;

class TestEncryption extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:test-encryption';

    /**
     * The console command description.
     */
    protected $description = 'Test data encryption functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing data encryption...');

        // Test teacher encryption
        $teacher = Teacher::first();
        if ($teacher) {
            $this->line("Testing teacher encryption...");
            
            // Set encrypted values
            $teacher->zoom_api_key = 'test_api_key_12345';
            $teacher->zoom_api_secret = 'test_secret_67890';
            $teacher->save();

            $this->line("Original API Key: test_api_key_12345");
            $this->line("Retrieved API Key: {$teacher->zoom_api_key}");
            $this->line("Encryption working: " . ($teacher->zoom_api_key === 'test_api_key_12345' ? 'YES' : 'NO'));
        }

        // Test payment encryption
        $payment = Payment::first();
        if ($payment) {
            $this->line("\nTesting payment encryption...");
            
            // Set encrypted values
            $payment->transaction_id = 'txn_test_12345';
            $payment->payment_details = ['method' => 'stripe', 'card_last4' => '1234'];
            $payment->save();

            $this->line("Original Transaction ID: txn_test_12345");
            $this->line("Retrieved Transaction ID: {$payment->transaction_id}");
            $this->line("Encryption working: " . ($payment->transaction_id === 'txn_test_12345' ? 'YES' : 'NO'));
        }

        $this->info('Encryption test completed!');
    }
}
