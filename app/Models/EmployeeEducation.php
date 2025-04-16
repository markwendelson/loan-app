<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;
    protected $table = 'employee_education';
    protected $fillable = [ 'id', 'employee_id', 'school_name', 'date_started', 'date_graduated', 'created_at','updated_at' ];


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
        
        //username
        if(isset($params['username']) && $params['username']){
            $query->where('username', '=', $params['username']);
        }

       return $query;
    }     

    public static function getEducation($params = []) {
        $data = [];

        //default fields   // 'user_type', //disbled for now
        $fields = [
            'id', 
            'employee_id', 
            'school_name',
             'date_started', 
             'date_graduated' 
            ];
            
        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = EmployeeEducation::select($fields);
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


    public static function updateEducation(array $criteria, array $data)
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
