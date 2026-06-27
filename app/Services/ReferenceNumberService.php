<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

//model
use App\Models\Branch;
use App\Models\ReferenceNumberCounter;

class ReferenceNumberService 
{
    public function generateReferenceNumber($model, $branch): string
{
    try {
        DB::beginTransaction();

        $branchCode = Branch::select('code')->where('id', $branch)->first();

        if (!$branchCode) {
            throw new RuntimeException('Branch not found.');
        }

        $prefix = strtoupper($branchCode->code);

        $counter = ReferenceNumberCounter::where('model', $model)
            ->where('branch_id', $branch)
            ->lockForUpdate()
            ->first();

        if (!$counter) {
            $counter = ReferenceNumberCounter::create([
                'model' => $model,
                'branch_id' => $branch,
                'current_number' => 0,
                'prefix' => $prefix,
            ]);
        }

        $number = $counter->current_number + 1;

        $counter->update([
            'current_number' => $number,
        ]);

        DB::commit();

        return sprintf('%s-%08d', $counter->prefix, $number);
    } catch (\Throwable $e) {
        DB::rollBack();

        // IMPORTANT: always return or rethrow
        throw $e;
    }
}
}