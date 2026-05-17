<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


//models
use App\Models\CountryCode;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request) {

        if(!auth()->user()->can('view-vendors')) {
            abort(403);
        }
        
        $vendorData = Vendor::select(
                'vendors.*',
                'country_codes.country_name'
            )
            ->leftJoin('country_codes', 'country_codes.id', '=', 'vendors.country_id')
            ->paginate(10);
        $countryCode = CountryCode::getCountryCodes([
            'is_multiple' => 1,
        ]);

        return view('vendors.index', compact(
            'vendorData',
            'countryCode'
        ));
    }

    public function store(Request $request)
    {
        $response = []; //response holder

        $inputData = $request->except('vendor_id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:vendors,name,',
            'tin' => 'required|string|unique:vendors,tin',
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'error',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        if(isset($inputData) && $inputData) {
            $inputData['created_by'] = Auth::user()->id;

            $result = Vendor::insert($request->all());

            if(isset($result) && $result) {
                $response = [
                    'status' => 'success',
                    'message' => 'Successfully added new vendor.'
                ];
            }else{
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to add new vendor, Please try again later!'
                ];
            }
        }

        return response()->json($response);
    }

    public function update(Request $request)
    {
        $response = []; //handles response data
        
        $inputData = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vendors', 'name')->ignore($inputData['vendor_id']),
            ],
            'tin' => [
                'required',
                'string',
                Rule::unique('vendors', 'tin')->ignore($inputData['vendor_id']),
            ],
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'error',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        $result = Vendor::findOrFail($inputData['vendor_id'])
            ->update($inputData);

        if($result) {
            $response = [
                'status' => 'success',
                'message' => 'Vendor information successfully updated!'
            ];
        }else{
            $response = [
                'status' => 'error',
                'message' => 'Unable to update vendor information, please try again later!'
            ];
        }

        return response()->json($response);
    }

    public function getVendorData(Request $request)
    {
        $response = [];

        $vendorData = Vendor::select(
                'vendors.*',
                'country_codes.country_name'
            )
            ->where('vendors.id', $request['vendor_id'])
            ->leftJoin('country_codes', 'country_codes.id', '=', 'vendors.country_id')
            ->first()
            ->toArray();

        if(!empty($vendorData) && is_array($vendorData)) {
            //adjust vendor type
            $vendorData['vendor_type'] = config('constants.vendor_types')[$vendorData['vendor_type']];

            $response = [
                'status' => 'success',
                'message' => 'Vendor data successfully fetched!',
                'data' => $vendorData
            ];
        }else{
            $response = [
                'status' => 'error',
                'message' => 'Vendor not found!',
                'data' => []
            ];
        }
        return response()->json($response);
    }
}
