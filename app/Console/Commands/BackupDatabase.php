<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = storage_path('app/backups');

        // Ensure folder exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = $directory . '/stockbase_db_backup_full_' . now()->format('Y-m-d_H-i-s') . '.sql';

        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $filename
        );

        exec($command);

        $this->info('Backup created: ' . $filename);
    }
}
