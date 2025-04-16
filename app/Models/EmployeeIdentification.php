<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeIdentification extends Model
{
    use HasFactory;

    protected $table = 'employee_identifications';
    protected $fillable = [ 'id', 'employee_id', 'id_type_id', 'id_no', 'created_at', 'updated_at' ];

    public static function getConditions($query, $params) {
    
        //id
        if (isset($params['id']) && $params['id']) {
            // Ensure $params['id'] is an array or wrap it in an array if it's a single value
            $id = is_array($params['id']) ? $params['id'] : [$params['id']];
            $query->whereIn('id', $id);
        }

        //not_id
        if (isset($params['not_id']) && $params['not_id']) {
            // Ensure $params['not_id'] is an array or wrap it in an array if it's a single value
            $notId = is_array($params['not_id']) ? $params['not_id'] : [$params['not_id']];
            $query->whereNotIn('id', $notId);
        }

        if(isset($params['employee_id']) && $params['employee_id']){
            if(is_array($params['employee_id'])){             
                $query->whereIn('employee_id', $params['employee_id']);
            }else{
                $query->where('employee_id', '=', $params['employee_id']);
            } }
        
        // if(isset($params['not_id']) && $params['not_id']){
        //     $query->whereNotIn('id', $params['not_id']);
        // }

        //username
        if(isset($params['username']) && $params['username']){
            $query->where('username', '=', $params['username']);
        }

       return $query;
    }  

    public static function getEmp_Identifications($params = []) {
        $data = [];
        //default fields
        $fields = [
            'id',
            'employee_id',
            'id_type_id',
            'id_no'
        ];

        $query = EmployeeIdentification::select($fields);
        $query = Self::getConditions($query, $params);
      
        //is_multiple
        if(isset($params['is_multiple']) && $params['is_multiple']){
            $data = $query->get();
            return $data->isNotEmpty() ? $data->toArray() : [];
        }else{
            $data = $query->first();
            return $data ? $data->toArray() : [];
        }
    }

}
