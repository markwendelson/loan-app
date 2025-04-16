<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Employee extends Model 
{
    use HasFactory, Notifiable;

    protected $table = 'employee'; 
    protected $fillable = [
        'id',
        'emp_code',
        'display_name',
        'username', 
        'password', 
        'status', 
        'user_type', 
        'img', 
        'last_login', 
        'created_at', 
        'updated_at'
    ];

    public static function generateEmpCode()
    {
        // Get the last employee's code
        $lastEmployee = self::orderBy('id', 'desc')->first();

        // Set a default employee code if no employees exist
        $newEmpCode = 'E-0001';  

        if ($lastEmployee) {
            // Extract the numeric part of the employee code
            $lastEmpCode = $lastEmployee->emp_code;
            $lastNumber = (int)substr($lastEmpCode, 2); // Assuming emp_code starts with 'E-'
            $newEmpCode = 'E-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);  // Increment the number
        }

        return $newEmpCode;
    }

    public static function getConditions($query, $params) {
        //id
        // if(isset($params['id']) && $params['id']){
        //     $query->whereIn('id', $params['id']);
        // }

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
        // if(isset($params['not_id']) && $params['not_id']){
        //     $query->whereNotIn('id', $params['not_id']);
        // }

        if(isset($params['emp_code']) && $params['emp_code']){
            $query->where('emp_code', '=', $params['emp_code']);
        }

        //username
        if(isset($params['username']) && $params['username']){
            $query->where('username', '=', $params['username']);
        }

        return $query;
    }

    public static function updateRecord(int $id, array $data)
    {
        // Find the employee record
        $employee = self::find($id);

        if ($employee) {
            // Fill the record with new data
            $employee->fill($data);

            // Check if any changes were made
            if ($employee->isDirty()) {
                $employee->save();
            }

            return $employee;
        }

        return null; // Return null if no record was found
    }


    // Static method to update last_login field
    public static function updateLastLogin($userId)
    {
        // Update the last_login field to the current date and time
        return self::where('id', $userId)->update(['last_login' => now()]);
    }
    public static function getEmployee($params = []) {
        $data = [];

        //default fields   // 'user_type', //disbled for now
        $fields = [
            'id',
            'emp_code',
            'display_name',
            'username',
            'password',
            'status',            
            'img',
            'last_login'
        ];

        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = Employee::select($fields);
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

}
