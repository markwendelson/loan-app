<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//declare models used
use App\Models\City; 

class CityController extends Controller {

    public function getCitiesHtmlOptions(Request $request) {
        $data = [];

        $inputData = $request->post();

          // Validate if 'province_id' is present
        if(empty($inputData['province_id'])){
            return response()->json([
                'has_error' => 1,
                'message' => 'Province ID is required'
            ]);
        }


        $citiesData = City::getCities([
            'province_id' => [$inputData['province_id']],
            'status' => 1,
            'is_multiple' => 1
        ]);

        if(!$citiesData){
            return response()->json($data);
        }

        $html = '';
        if(isset($citiesData) && $citiesData){
            foreach($citiesData as $key => $val){
                $html = $html . '<option value = "'. $val['id'] .'">'. $val['name'] .'</option>';
            }
        }

        //generate response data
        $data = [
            'has_error' => 0,
            'html' => $html
        ];

        return response()->json($data);
    }
}
