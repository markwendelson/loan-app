<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

//models
use App\Models\DatabaseBackup;

class DatabaseController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('view-database-backups')) {
            abort(403, 'Unauthorized action.');
        }

        return view('database-backup.index');
    }

    public function create()
    {
        $response = [];

        $dumpPath = env('DB_DUMP_BINARY_PATH') . '\mysqldump.exe';

        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        
        $backupDirectory = storage_path('app/private/database_backups');

        // Create backup directory if it doesn't exist
        if (!is_dir($backupDirectory)) {
            mkdir($backupDirectory, 0755, true);
        }

        $filename = $database . '_' . date('Y-m-d_H-i-s') . '.sql';
        
        $filePath = 'database_backups/' . $filename;

        $backupFile = $backupDirectory . DIRECTORY_SEPARATOR . $filename;
        $sysBackupFile = url($backupDirectory . '/' . $filename);
        $command = '"' . $dumpPath . '"'
            . ' -h ' . escapeshellarg($host)
            . ' -P ' . escapeshellarg($port)
            . ' -u ' . escapeshellarg($username);

        if (!empty($password)) {
            $command .= ' -p' . escapeshellarg($password);
        }

        $command .= ' ' . escapeshellarg($database)
            . ' > ' . escapeshellarg($backupFile);

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            throw new RuntimeException(
                "Database backup failed.\n" .
                implode("\n", $output)
            );
        }

        if (!file_exists($backupFile)) {
            throw new RuntimeException(
                'Backup command completed but the backup file was not created.'
            );
        }

        if($exitCode === 0) {
            $response = [
                'status' => 'success',
                'message' => 'Database backup created successfully.'
            ];

            try {
                DB::beginTransaction();

                DatabaseBackup::create([
                    'file_name' => $filename,
                    'file_path' => $filePath,
                    'created_by' => auth()->user()->id
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                $response = [
                    'status' => 'error',
                    'message' => 'An error occurred while creating the database backup. Please try again later.'
                ];
            }
        }
        
        return response()->json($response);
    }

    public function download($filename)
    {
        $path = 'database_backups/' . $filename;

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}