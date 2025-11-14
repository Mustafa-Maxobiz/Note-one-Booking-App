<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:restore {backup_file : The backup file to restore} {--force : Force restore without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Restore database from backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupFile = $this->argument('backup_file');
        $backupPath = storage_path("app/backups/{$backupFile}");

        // Check if backup file exists
        if (!file_exists($backupPath)) {
            $this->error("Backup file not found: {$backupFile}");
            return 1;
        }

        // Confirm restore operation
        if (!$this->option('force')) {
            if (!$this->confirm('This will replace your current database. Are you sure?')) {
                $this->info('Restore cancelled.');
                return 0;
            }
        }

        try {
            $this->info('Starting database restore...');

            // Get the database file path
            $databasePath = database_path('database.sqlite');
            
            // Create backup of current database
            $currentBackup = database_path('database_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sqlite');
            if (file_exists($databasePath)) {
                copy($databasePath, $currentBackup);
                $this->info("Current database backed up to: {$currentBackup}");
            }

            // Restore from backup
            if (copy($backupPath, $databasePath)) {
                $this->info("Database restored from: {$backupFile}");
                $this->info('Database restore completed successfully!');
                return 0;
            } else {
                $this->error('Failed to restore database');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Restore failed: ' . $e->getMessage());
            return 1;
        }
    }
}
