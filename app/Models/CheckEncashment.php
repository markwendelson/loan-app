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
        'last_name',
        'first_name',
        'middle_name',
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

     // Accessor for full customer name
    public function getCustomerNameAttribute()
    {
        $middleInitial = $this->middle_name ? strtoupper($this->middle_name[0]) . '.' : '';
        return "{$this->last_name}, {$this->first_name} {$middleInitial}";
    }

    // Example relationship (if you later link to a Customer table)
//     public function customer()
//     {
//         return $this->belongsTo(Customer::class);
//     }

}