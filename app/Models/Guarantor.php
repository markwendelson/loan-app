<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model {
    use HasFactory;
    
    protected $table = 'guarantor';
    protected $fillable = [
        'client_id', 
        'lname', 
        'fname', 
        'mname',
        'img',
        'gender',
        'birthdate',
        'relation_client', 
        'contact_info', 
        'email',
        'civil_status',
        'nationality',
        'present_province',
        'present_city',
        'present_zipcode',
        'present_address',
        'permanent_province',
        'permanent_city',
        'permanent_zipcode',
        'permanent_address',
        'created_at', 
        'updated_at'
    ];

  
}