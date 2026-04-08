<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    
    protected $table = 'document_types';
    protected $fillable = [
        'type_name',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];

    public static function getAllActive()
    {
        return self::where('status', 1)->get();
    }


}
