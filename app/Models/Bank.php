<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model {
    use HasFactory;

    protected $table = 'bank';
    protected $fillable = ['id', 'bank_code', 'name', 'status', 'created_at', 'updated_at'];

    public static function getConditions($query, $params) {

        //id
        if(isset($params['id']) && $params['id']){
            $query->whereIn('id', $params['id']);
        }

        //order
        if(isset($params['order']) && $params['order']){
            $query->orderBy($params['order']['field'], $params['order']['direction']);
        }

        //status
        if(isset($params['status']) && $params['status']){
            $query->where('status', '=', $params['status']);
        }

        return $query;
    }

    public static function getBanks($params) {
        $data = [];

        //default fields
        $fields = [
            'id',
            'bank_code',
            'name',
            'status'
        ];

        $query = Bank::select($fields);
        $query = Self::getConditions($query, $params);

        if(isset($params['is_multiple']) && $params['is_multiple']){
            //return multiple rows
            $data = $query->get();
            return  $data->isNotEmpty() ? $data->toArray() : [];
        }

        //retun single row
        $data = $query->first();
        return $data ? $data->first->toArray() : [];
    }

}
