<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

//models
use App\Models\DatabaseBackup;

class DatabaseController extends Controller
{
    public function index()
    {
        return view('database-backup.index');
    }

    public function create()
    {
        $response = [];
                $exitCode = Artisan::call('backup:run', [
            '--only-db' => true,
        ]);

        dump($exitCode);
        dump(Artisan::output());
        dd("harhar");
        try {
            // Your code here

            DB::beginTransaction();

            //set backup name
            $backupName='DBBACKUP_'.date('YmdHis').'.sql';

            Artisan::call('backup:run', [
                '--only-db' => true,
            ]);

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Database backup created successfully!'
            ];
        } catch (\Exception $e) {
            // Handle exception
            DB::rollback();

            Log::error('Database backup failed', [
                'message' => $e->getMessage(),
            ]);

            $response = [
                'status' => 'error',
                'message' => 'An error occurred while creating the database backup.'
            ];
        }

        return response()->json($response);
    }
}