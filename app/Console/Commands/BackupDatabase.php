<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:backup {--compress : Compress the backup file}';

    /**
     * The console command description.
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$timestamp}.sqlite";
        $backupPath = "backups/{$filename}";

        try {
            // Get the database file path
            $databasePath = database_path('database.sqlite');
            
            if (!file_exists($databasePath)) {
                $this->error('Database file not found at: ' . $databasePath);
                return 1;
            }

            // Create backup directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Copy database file
            $backupFilePath = storage_path("app/{$backupPath}");
            if (copy($databasePath, $backupFilePath)) {
                $this->info("Database backup created: {$backupPath}");
                
                // Compress if requested
                if ($this->option('compress')) {
                    $this->compressBackup($backupFilePath);
                }

                // Clean old backups (keep last 7 days)
                $this->cleanOldBackups();
                
                $this->info('Database backup completed successfully!');
                return 0;
            } else {
                $this->error('Failed to create database backup');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Compress the backup file.
     */
    private function compressBackup($filePath)
    {
        $this->info('Compressing backup...');
        
        $compressedPath = $filePath . '.gz';
        
        if (function_exists('gzopen')) {
            $fp_in = fopen($filePath, 'rb');
            $fp_out = gzopen($compressedPath, 'wb9');
            
            while (!feof($fp_in)) {
                gzwrite($fp_out, fread($fp_in, 1024 * 512));
            }
            
            fclose($fp_in);
            gzclose($fp_out);
            
            // Remove original file
            unlink($filePath);
            
            $this->info("Compressed backup created: {$compressedPath}");
        } else {
            $this->warn('Gzip compression not available');
        }
    }

    /**
     * Clean old backup files.
     */
    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/backup_*.sqlite*');
        $cutoffDate = Carbon::now()->subDays(7);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffDate->timestamp) {
                unlink($file);
                $this->info('Removed old backup: ' . basename($file));
            }
        }
    }
}
