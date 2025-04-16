<?php
// ClientDetails.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDetails extends Model {
    use HasFactory;

    protected $table = 'client_details';
    protected $fillable = [
        'client_id', 
        'fname', 
        'mname', 
        'lname', 
        'suffix', 
        'birthdate',
        'nationality',
        'gender',
        'civil_status',
        'created_at',
        'updated_at'
    ];

    public static function getConditions($query, $params = []) {

        //id
        if(isset($params['id']) && $params['id']) {
            $query->whereIn('id', $params['id']);
        }

        //not_id
        if(isset($params['not_id']) && $params['not_id']) {
            $query->whereNotIn('id', $params['not_id']);
        }

        return $query; 
    }


    public static function getClientDetails($params = []) {
        $data = [];

        //default fields
        $fields = [
            'id',
            'client_id',
            'fname',
            'mname',
            'lname',
            'suffix',
            'birthdate',
            'nationality',
            'civil_status'
        ];

        //all_fields
        if(isset($params['all_fields']) && $params['all_fields']) {
            $fields = ['*'];
        }

        $query = ClientDetal::select($fields);

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

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }

}