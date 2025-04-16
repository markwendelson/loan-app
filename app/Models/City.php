<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model {
    use HasFactory;

    protected $table = 'cities';
    protected $fillable = ['id', 'province_id', 'name', 'status', 'created_at', 'updated_at'];

    public static function getConditions($query, $params = []) {

        //id
        if(isset($params['id']) && $params['id']) {
            if(is_array($params['id'])){
                $query->whereIn('id', $params['id']);
            }else{
                $query->where('id', '=', $params['id']);
            }      
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']){
            $query->whereNotIn('id', $params['not_id']);
        }

        //province_id               
        if(isset($params['province_id']) && $params['province_id']){
        if(is_array($params['province_id'])){
			$query->whereIn('province_id', $params['province_id']);
		}else{
			$query->where('province_id', '=', $params['province_id']);
		} }
	  
        //status
        if(isset($params['status']) && $params['status']){
            $query->where('status', '=', $params['status']);
        }
        
      return $query;
           
    }

    public static function getCities($params = []) {
        $data = [];

        //default fields
        $fields = [
            'id',
            'province_id',
            'name',
            'status'
        ];

        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = City::select($fields);
        
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
    

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
