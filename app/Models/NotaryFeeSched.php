<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NotaryFeeSched extends Model {

    use HasFactory;
    protected $table = 'notary_schedule';
    protected $fillable = ['id', 'min_amount', 'max_amount', 'notary_fee', 
                           'is_percentage', 'status', 'created_at', 'updated_at'];

       public static function getNotaryFeeSched($params = []) {
        $data = [];
   
        //default fields
        $fields = [
            'id',
            'min_amount',
            'max_amount',
            'notary_fee',     
            'is_percentage',
            'status'
        ];
   
        $query = NotaryFeeSched::select($fields);
        $query = Self::getConditions($query, $params);
   
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

    public static function getConditions($query, $params = []) {
      
        //id
        if(isset($params['id']) && $params['id']){
            $query->whereIn('id', $params['id']);
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']){
            $query->whereNotIn('id', $params['not_id']);
        }

        //min amount
        if(isset($params['min_amount']) && $params['min_amount']){
            $query->where('min_amount', '=', $params['min_amount']);
        }

           //min amount
           if(isset($params['max_amount']) && $params['max_amount']){
            $query->where('max_amount', '=', $params['max_amount']);
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
   

}