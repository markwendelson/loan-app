<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model {
    use HasFactory;
    protected $table = 'job_title';
    protected $fillable = ['id', 'title', 'description', 'status', 'created_at', 'updated_at'];

    public static function getConditions($query, $params) {

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

        //status
        if(isset($params['status']) && $params['status']){
            $query->where('status', '=', $params['status']);
        }

        //is_deleted
        if(isset($params['is_deleted']) && $params['is_deleted']){
            $query->where('is_deleted', '=', $params['is_deleted']);
        }

        //Title
        if(isset($params['title']) && $params['title']){
            if(is_array($params['title'])){
                $query->whereIn('title', $params['title']);
            }else{
                $query->where('title', '=', $params['title']);
            } }
    

        //order
        if(isset($params['order']) && $params['order']){
            $query->orderBy($params['order']['field'], $params['order']['direction']);
        }

        return $query; 
    }

    public static function getJobTitles($params = []) {
        $data = [];

        //default fields
        $fields = [
            'id',
            'title',
            'description',
            'status'
        ];

        $query = JobTitle::select($fields);
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

}
