<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    use HasFactory;
    protected $table = 'department';
    protected $fillable = ['id', 'code', 'name', 'status', 'created_at', 'updated_at'];

    public static function getConditions($query, $params = []) {
      
        //id
        if(isset($params['id']) && $params['id']){
           if(is_array($params['id'])){
                $query->whereIn('id', $params['id']);
            }else{
                $query->where('id', '=', $params['id']);
           }
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']){
            if(is_array($params['not_id'])){
                $query->whereNotIn('id', $params['not_id']);
            }else{
                $query->where('id', '!=', $params['not_id']);
            }
        }

        //name
        if(isset($params['name']) && $params['name']){
            $query->where('name', '=', $params['name']);
        }

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

    public static function getDepartments($params = []) {
        $data = [];
        //default fields
        $fields = [
            'id',
            'code',
            'name',
            'status'
        ];

        $query = Department::select($fields);
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
