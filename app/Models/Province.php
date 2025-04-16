<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model {
    use HasFactory;

    protected $table = 'provinces';
    protected $fillable = ['id', 'name', 'status', 'created_at', 'updated_at'];

    public static function getConditions($query, $params = []) {

        //id
        if(isset($params['id']) && $params['id']){
            $query->whereIn('id', $params['id']);
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']){
            $query->whereNotIn('id', $params['not_id']);
        }

        //status
        if(isset($params['status']) && $params['status']){
            $query->where('status', '=', $params['status']);
        }

        return $query; 
    }

    public static function getProvinces($params = []) {
        $data = [];

        //default fields
        $fields = [
            'id',
            'name',
            'status'
        ];

        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = Province::select($fields);
        $query = self::getConditions($query, $params);

        //is_multiple
        if(isset($params['is_multiple']) && $params['is_multiple']){
            //return multiple row
            $data = $query->get();
            return $data->isNotEmpty() ? $data->toArray() : [];
        }

        //return single row
        $data = $query->first();
        return $data ? $data->toArray() : [];
    }

}
