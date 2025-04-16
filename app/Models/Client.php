<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use

class Client extends Model {
    use HasFactory;

    protected $table = 'client';
    protected $fillable = ['client_code', 'username', 'password', 'api_key', 'email', 'img', 'created_at', 'updated_at'];

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

        //username
        if(isset($params['username']) && $params['username']){
            $query->where('username','=', $params['username']);
        }

        //api_key
        if(isset($params['api_key']) && $params['api_key']){
            $query->where('api_key','=', $params['api_key']);
        }

        //status
        if(isset($params['status']) && $params['status']){
            $query->where('status', '=', $params['status']);
        }

        return $query;
    }

    public static function getClients() {
        $data = [];

        //default fiels
        $fields = [
            'id',
            'client_code',
            'username',
            'password',
            'api_key'
        ];

        $query = Client::select($fields);

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

    public function clientDetails() {
        return $this->hasOne(ClientDetails::class, 'client_id');
    }

    public function clientJobTitle() {
        return $this->hasMany(ClientJobTitle::class, 'client_id'); // FK is 'client_id' in client_job_titles table
    }

}
