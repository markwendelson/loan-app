<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model {
    use HasFactory;

    protected $table = 'country_codes';

    public static function getConditions($query, $params = []) {

        //id
        if(isset($params['id']) && $params['id']){
            $query->whereIn('id', $params['id']);
        }

        //order
        if(isset($params['order']) && $params['order']){
            $query->orderBy($params['order']['field'], $params['order']['direction']);
        }

        return $query;
    }

    public static function getCountryCodes($params = []){
        $data = [];

        //default fields
        $fields = [
            'id',
            'country_code',
            'country_name',
            'nationality',
            'status'
        ];

        $query = CountryCode::select($fields);

        $query = Self::getConditions($query, $params);

        //is_multiple
        if(isset($params['is_multiple']) && $params['is_multiple']){
            //return multiple row
            $data = $query->get();
            return $data->isNotEmpty() ? $data->toArray() : [];
        }

        //return sigle row
        $data = $query->first();
        return $data ? $data->toArray() : [];


    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
