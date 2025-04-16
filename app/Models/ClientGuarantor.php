<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientGuarantor extends Model {
    use HasFactory;

    protected $table  = 'client_guarantor';
    protected $fillable = [
        'id',
        'client_id',
        'created_at',
        'updated_at'
    ];

    public static function getCondition($query, $params = []) {
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
                $query->where('id', '<>', $params['not_id']);
            }
        }

        return $query;
    }

    public static function getClientGuarantor($params = []) {
        $data = [];

        //default fields
        $fields = [
            'id',
            'client_id',
            'guarantor_id'
        ];

        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = ClientGuarantor::select($fields);
        $query = self::getCondition($query, $params);

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
