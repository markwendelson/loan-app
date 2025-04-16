<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;

    
    protected $table = 'employee_details';
    protected $fillable = ['id', 'employee_id', 'department_id', 'gender',                           
                           'branch_id', 'lname', 'fname', 'mname', 'suffix',                            
                           'civil_status', 'email','birthdate', 'contact',
                           'hire_date','country_id', 'nationality', 'created_at','updated_at',
                           'present_province_id', 'present_city_id', 'present_address',
                           'permanent_province_id', 'permanent_city_id', 'permanent_address',
                           'present_postal_code','permanent_postal_code'];
        
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

    public static function getEmp_Details($params = []) {
        $data = [];

        //default fields   // 'user_type', //disbled for now
        $fields =   ['id, employee_id', 'department_id', 'gender',                           
                    'branch_id', 'lname', 'fname', 'mname', 'suffix',                            
                    'civil_status', 'email','birthdate', 'contact',
                    'hire_date','country_id', 'nationality', 'created_at','updated_at',
                    'present_province_id', 'present_city_id', 'present_address',
                    'permanen_province_id', 'permanent_city_id', 'permanent_address',
                    'present_postal_code','permanent_postal_code'];

        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = EmployeeDetail::select($fields);
        $query = self::getConditions($query, $params);
    
        //is_multiple
        if (isset($params['is_multiple']) && $params['is_multiple']) {
            //return multiple rows as an Eloquent collection
            $data = $query->get(); // This will return an Eloquent collection
            return $data; // Do not call toArray() here
        }
    
        //return single row as an Eloquent model
        $data = $query->first();
        return $data ? $data->toArray() : []; // This will return a single Eloquent model, not an array
    }

    public static function updateDetails(array $criteria, array $data)
    {
        // Find the record based on the criteria
        $record = self::where($criteria)->first();

        if ($record) {
            // Fill the record with new data
            $record->fill($data);

            // Check if any changes were made
            if ($record->isDirty()) {
                $record->save();
            }

            return $record;
        }

        return null; // Return null if no record was found
    }
                           
                           
                           
                           
                           
                           

}
