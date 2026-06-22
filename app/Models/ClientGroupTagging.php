<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientGroupTagging extends Model
{
    protected $table = 'client_group_tagging';
    protected $fillable = [
        'client_id',
        'client_group_type_id',
        'created_by',
        'created_at',
        'updated_at',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
