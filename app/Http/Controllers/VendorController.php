<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


//models
use App\Models\CountryCode;
use App\Models\User;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request) {

        $filterData = $request->all();

        $user = auth()->user();

        if(!$user->can('view-vendors')) {
            abort(403);
        }

        //main query
        $countryCode = CountryCode::getCountryCodes([
            'is_multiple' => 1,
        ]);

        return view('vendors.index', compact(
            'user',
            'countryCode',
        ));
    }

    public function store(Request $request)
    {
        $response = []; //response holder

        $inputData = $request->except('vendor_id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:vendors,name,',
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
            ->withTrashed()
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

    public function getVendorsList(Request $request) 
    {
        $response = [];

        $vendorsData = Vendor::all()->toArray();

        if(isset($vendorsData) && !empty($vendorsData)) {
            $response = [
                'status' => 'success',
                'message' => 'Vendors list successfully updated!',
                'data' => $vendorsData
            ];
        }else{
            $response = [
                'status' => 'error',
                'message' => 'No vendors found!',
                'data' => []
            ];
        }

        return response()->json($response);
    }
    

    public function destroy(Request $request)
    {
        $inputData = $request->all();
        $response = []; //reponse holder

        if(!auth()->user()->can('delete-vendors')) {
            $response = [
                'status' => 'error',
                'message' => 'You do not have permission to delete vendors. Please contact your administrator if you believe this is an error.'
            ];
        }

        if(isset($inputData['vendor_id']) && $inputData['vendor_id']) {
            $vendor = Vendor::find($inputData['vendor_id']);

            if($vendor) {
                $result = $vendor->delete();

                if($result) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Vendor has been successfully deleted'
                    ];
                }else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Failed to delete vendor,  Please try again.'
                    ];
                }
            }
        }else{
            $response = [
                'status' => 'error',
                'message' => 'Please select an item to delete before proceeding.'
            ];
        }

        return response()->json($response);

    }
}
