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
        'reference_no',
        'transDate',
        'checkNo',
        'last_name',
        'first_name',
        'middle_name',
        'bank_id',
        'checkAmount',
        'charge',
        'net_proceeds',
        'id_type',          
        'id_number',        
        'id_picture_path', 
        'no_service_charge',
    ];

    // Casts for automatic type conversion
    protected $casts = [
        'transDate'     => 'date',
        'checkAmount'   => 'decimal:2',
        'charge'        => 'decimal:2',
        'net_proceeds'  => 'decimal:2',
        'no_service_charge' => 'boolean',
    ];

     // Accessor for full customer name
    public function getCustomerNameAttribute()
    {
        $middleInitial = $this->middle_name ? strtoupper($this->middle_name[0]) . '.' : '';
        return "{$this->last_name}, {$this->first_name} {$middleInitial}";
    }

}