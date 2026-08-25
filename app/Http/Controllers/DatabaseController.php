<?php

namespace App\Http\Controllers;

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
        try {
            $database = config('database.connections.mysql.database');

            $backupDirectory = storage_path('app/private/database_backups');

            // Create backup directory if it doesn't exist
            if (!is_dir($backupDirectory)) {
                mkdir($backupDirectory, 0755, true);
            }

            $filename = $database . '_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = 'database_backups/' . $filename;
            $backupFile = $backupDirectory . DIRECTORY_SEPARATOR . $filename;

            // Generate the SQL dump using pure PHP/PDO
            $this->generateDump($backupFile);

            if (!file_exists($backupFile) || filesize($backupFile) === 0) {
                throw new \RuntimeException('Backup completed but the file was not created or is empty.');
            }

            DB::beginTransaction();

            DatabaseBackup::create([
                'file_name' => $filename,
                'file_path' => $filePath,
                'created_by' => auth()->user()->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Database backup created successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database backup failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the database backup. Please try again later.',
            ]);
        }
    }

    public function download($filename)
    {
        // Check both old and new backup paths
        $paths = [
            'database_backups/' . $filename,
            'private/database_backups/' . $filename,
        ];

        foreach ($paths as $path) {
            if (Storage::disk('local')->exists($path)) {
                return Storage::disk('local')->download($path);
            }
        }

        abort(404);
    }

    public function restore($filename)
    {
        if (!auth()->user()->can('restore-database-backup')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $fullPath = $this->resolveBackupPath($filename);

            if (!$fullPath) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Backup file not found.',
                ], 404);
            }

            $sql = Storage::disk('local')->get($fullPath);

            if (empty($sql)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Backup file is empty.',
                ]);
            }

            // Disable foreign key checks and execute the SQL
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

            // Split SQL into individual statements and execute
            $statements = array_filter(
                array_map('trim', preg_split('/;\s*\n/', $sql)),
                fn($s) => !empty($s) && !str_starts_with($s, '--')
            );

            foreach ($statements as $statement) {
                // Skip comments and empty lines
                $statement = trim($statement);
                if (empty($statement) || str_starts_with($statement, '--')) {
                    continue;
                }

                DB::unprepared($statement . ';');
            }

            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

            Log::info('Database restored from backup: ' . $filename . ' by user: ' . auth()->user()->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Database restored successfully from backup.',
            ]);

        } catch (\Exception $e) {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Database restore failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while restoring the database. Please try again later.',
            ]);
        }
    }

    /**
     * Resolve the storage path for a backup file.
     */
    private function resolveBackupPath(string $filename): ?string
    {
        $paths = [
            'database_backups/' . $filename,
            'private/database_backups/' . $filename,
        ];

        foreach ($paths as $path) {
            if (Storage::disk('local')->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Generate a MySQL dump file using pure PHP/PDO.
     */
    private function generateDump(string $outputFile): void
    {
        $pdo = DB::connection()->getPdo();
        $database = config('database.connections.mysql.database');

        $handle = fopen($outputFile, 'w');
        if (!$handle) {
            throw new \RuntimeException('Could not open backup file for writing.');
        }

        // Write header
        fwrite($handle, "-- Database Backup\n");
        fwrite($handle, "-- Database: {$database}\n");
        fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Generated by PHP/PDO\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
        fwrite($handle, "SET AUTOCOMMIT=0;\n");
        fwrite($handle, "START TRANSACTION;\n\n");

        // Get all tables
        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Get CREATE TABLE statement
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createSql = $createStmt['Create Table'] ?? $createStmt['Create View'] ?? null;

            if (!$createSql) {
                continue;
            }

            fwrite($handle, "-- Table: {$table}\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createSql . ";\n\n");

            // Skip data for views
            if (isset($createStmt['Create View'])) {
                continue;
            }

            // Get row count to determine if we should dump data
            $countResult = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetch(\PDO::FETCH_COLUMN);

            if ($countResult == 0) {
                fwrite($handle, "-- No data for table {$table}\n\n");
                continue;
            }

            // Fetch and write data in chunks to conserve memory
            $chunkSize = 500;
            $offset = 0;

            while (true) {
                $rows = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}")
                    ->fetchAll(\PDO::FETCH_ASSOC);

                if (empty($rows)) {
                    break;
                }

                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';

                $values = [];
                foreach ($rows as $row) {
                    $escaped = array_map(function ($value) use ($pdo) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return $pdo->quote($value);
                    }, array_values($row));

                    $values[] = '(' . implode(', ', $escaped) . ')';
                }

                fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n");
                fwrite($handle, implode(",\n", $values) . ";\n\n");

                $offset += $chunkSize;

                if (count($rows) < $chunkSize) {
                    break;
                }
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($handle, "COMMIT;\n");

        fclose($handle);
    }
}