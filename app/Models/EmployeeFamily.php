<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFamily extends Model
{
    use HasFactory;

    protected $table = 'employee_family';
    protected $fillable = [ 'id', 'employee_id', 'lname', 'fname', 'mname', 'birthdate', 'relation', 'created_at','updated_at' ];

    public static function getConditions($query, $params) {
                 
        if (isset($params['id']) && $params['id']) {
            // Ensure $params['id'] is an array or wrap it in an array if it's a single value
            $id = is_array($params['id']) ? $params['id'] : [$params['id']];
            $query->whereIn('id', $id);
        }

        if(isset($params['employee_id']) && $params['employee_id']){
            if(is_array($params['employee_id'])){             
                $query->whereIn('employee_id', $params['employee_id']);
            }else{
                $query->where('employee_id', '=', $params['employee_id']);
            } }

        //not_id
        if (isset($params['not_id']) && $params['not_id']) {
            // Ensure $params['not_id'] is an array or wrap it in an array if it's a single value
            $notId = is_array($params['not_id']) ? $params['not_id'] : [$params['not_id']];
            $query->whereNotIn('id', $notId);
        }
      
        //username
        if(isset($params['username']) && $params['username']){
            $query->where('username', '=', $params['username']);
        }

        return $query;
    }     

    public static function getEmpFamily($params = []) {
        $data = [];

        //default fields   // 'user_type', //disbled for now
        $fields = [ 'id', 'employee_id', 'lname', 'fname', 'mname', 'birthdate', 'relation' ];
            
        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = EmployeeFamily::select($fields);
        $query = self::getConditions($query, $params);
    
        //is_multiple
        if (isset($params['is_multiple']) && $params['is_multiple']) {
            //return multiple rows as an Eloquent collection
            $data = $query->get(); // This will return an Eloquent collection
            return $data->isNotEmpty() ? $data->toArray() : []; // Do not call toArray() here
        }
    
        //return single row as an Eloquent model
        $data = $query->first();
        return $data ? $data->toArray() : []; // This will return a single Eloquent model, not an array
    }

}
