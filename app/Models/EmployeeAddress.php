<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAddress extends Model
{
    use HasFactory;

    protected $table = 'employee_address';
    protected $fillable = ['id', 'employee_id', 'present_province_id', 'present_city_id',
                             'present_address',  'permanent_province_id', 'permanent_city_id', 
                             'permanent_address', 'present_postal_code', 'permanent_postal_code', 
                             'country_id', 'created_at', 'updated_at'];
                            
    public static function getConditions($query, $params) {
    
        return $query;

    }

    public static function getEmpAddress($params = []) {
        $data = [];
        //default fiels
        $fields = [
            'id',
            'employee_id',
            'present_province_id',
            'present_city_id',            
            'present_address',
            'permanen_province_id',
            'permanent_city_id',
            'permanent_address',                            
            'present_postal_code',
            'permanent_postal_code'         
        ];

        $query = Client::select($fields);

        $query = self::getConditions($query, $params);

        //is_multiple
        if(isset($params['is_multiple']) && $params['is_multiple']){
            //return multiple row
            $data = $query->get();
            return $data->isNotEmpty() ? $data->toArray() : [];
        }

        //return single row
        $data = $query->firstS();
        return $data ? $data->toArray() : [];

    } 
}
