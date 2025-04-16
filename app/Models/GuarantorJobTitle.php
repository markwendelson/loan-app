<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuarantorJobTitle extends Model {
    use HasFactory;

    protected $table = 'guarantor_job_title';
    protected $fillable = [
        'client_id',
        'job_id',
        'office_name',
        'salary',
        'status',
        'created_at',
        'updated_at'
    ];

    public static function getCondition($query, $params = []) {
        //id
        if(isset($params['id']) && $params['id']){
            if(is_array($params['id'])){
                $query->whereIn('id', $params['id']);
            }else{
                $query->where('id', '=' , $params['id']);
            }
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']){
            if(is_array($params['not_id'])){
                $query->whereNotIn('id', $params['not_id']);
            }else{
                $query->where('id', '<>' , $params['not_id']);
            }
        }

        //client_id
        if(isset($params['client_id']) && $params['client_id']){
            $query->where('client_id', '=', $params['client_id']);
        }

        //job_id
        if(isset($params['job_id']) && $params['job_id']){
            if(is_array($params['job_id'])){
                $query->whereIn('job_id', $params['job_id']);
            }else{
                $query->where('job_id', '=', $params['job_id']);
            }
        }

        //office_name
        if(isset($params['office_name']) && $$params['office_name']){
            $query->where('office_name', '=', $params['office_name']);
        }

        return $query;
    }

    public static function getGuarantorJobtitle($params = []){
        $data = [];

        //default fields
        $fields = [
            'id',
            'client_id',
            'job_id',
            'office_name',
            'salary',
            'status'
        ];

        $fields = isset($params['all_fields']) && $params['all_fields'] ? ['*'] : $fields;

        $query = getGuarantorJobtitle::select($fields);
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
