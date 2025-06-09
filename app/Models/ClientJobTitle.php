<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientJobTitle extends Model {
    use HasFactory;

    protected $table = 'client_job_title';
    protected $fillable = [
        'client_id', 
        'job_id', 
        'office_name', 
        'salary', 
        'status',
        'created_at',
        'updated_at'
    ];

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function job()
    {
        return $this->belongsTo(JobTitle::class);
    }
}
