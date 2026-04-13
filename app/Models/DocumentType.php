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

    public static function getAll()
    {
        return self::paginate(10);
    }

    public static function getAllActive()
    {
        return self::where('status', 1)->get();
    }

    //relation ship
    public function clientDocument()
    {
        return $this->belongsTo(ClientDocument::class, 'id','document_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
