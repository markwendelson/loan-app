<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckEncashment extends Model
{
    use HasFactory;

    protected $table = 'check_encashment';

    // Primary key
    protected $primaryKey = 'id';

    protected $fillable = [
        'transDate',
        'checkNo',
        'bankName',
        'checkAmount',
        'charge',
        'net_proceeds',
        'id_type',          
        'id_number',        
        'id_picture_path', 
    ];

    // Casts for automatic type conversion
    protected $casts = [
        'transDate'     => 'date',
        'checkAmount'   => 'decimal:2',
        'charge'        => 'decimal:2',
        'net_proceeds'  => 'decimal:2',
    ];

    // Example relationship (if you later link to a Customer table)
//     public function customer()
//     {
//         return $this->belongsTo(Customer::class);
//     }

}