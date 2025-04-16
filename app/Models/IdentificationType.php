<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentificationType extends Model
{
    use HasFactory;

    protected $table = 'identification_type';
    protected $fillable = [ 'id', 'name', 'is_primary', 'status', 'created_at', 'updated_at' ];

    public static function getConditions($query, $params = []) {
      
        //id
        if(isset($params['id']) && $params['id']){
            $query->whereIn('id', $params['id']);
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']){
            $query->whereNotIn('id', $params['not_id']);
        }
        //name
        if(isset($params['name']) && $params['name']){
            if(is_array($params['province_id'])){
                $query->whereIn('name', $params['name']);
            }else{
                $query->where('name', '=', $params['name']);
            } }

       if(isset($params['employee_id']) && $params['employee_id']){
           if(is_array($params['employee_id'])){
               $query->whereIn('employee_id', $params['employee_id']);
           }else{
               $query->where('employee_id', '=', $params['employee_id']);
           } }

        //order
        if(isset($params['order']) && $params['order']){
            $query->orderBy($params['order']['fields'], $params['order']['direction']);
        }

        //status
        if(isset($params['status'])){
            $query->where('status', '=', $params['status']);
        }

        return $query;
    }


    public static function getIdentifications($params = []) {
        $data = [];
        //default fields
        $fields = [
            'id',
            'name',
            'is_primary',
            'status'
        ];

        $query = IdentificationType::select($fields);
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
