<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

//models
use App\Models\ClientGroupType;

class ClientGroupsController extends Controller
{
    //client group index
    public function index()
    {
        return view('client.group.index');
    }

    
    public function clientGrouptypes()
    {
        //check permission
        if(!auth()->user()->can('view-client-group-type')) {
            abort(403, 'Unauthorized action.');
        }

        $groupTypesData = ClientGroupType::paginate(10);

        return view('client.group.group-type.index', compact(
            'groupTypesData'
        ));
    }

    public function storeClientGroupTypes(Request $request)
    {
        $response = [];

        //check permission
        if(!auth()->user()->can('create-client-group-type')) {
            abort(403, 'Unauthorized action.');
        }

        //validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:client_group_types,name,',
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'error',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        try {
            DB::beginTransaction();

            ClientGroupType::insert([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'created_by' => auth()->user()->id
            ]);

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Client group type created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollback();

            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getGroupType(Request $request)
    {

        $response = [];

        if(isset($request->client_group_type_id) && $request->client_group_type_id) {
            $response = [
                'status' => 'error',
                'message' => ''
            ];   
        }
        //get client group type data
        try {
            $groupType = ClientGroupType::where('id', $request->client_group_type_id)->get()->first()->toArray();
            if (!$groupType) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Client group type not found.'
                ], 404);
            }

            $response = [
                'status' => 'success',
                'data' => $groupType
            ];
        } catch (\Exception $e) {

            $response = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function putClientGroupTypes(Request $request)
    {
        $response = [];

        //validation
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('client_group_types')->ignore($request->input('id')),
            ]
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'error',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        try {

            DB::beginTransaction();

            ClientGroupType::where('id', $request->input('id'))->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status
            ]);

            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Client group type updated successfully.'
            ];

        } catch (\Exeception $e) {
            $response = [
                'status' => 'error',
                'message' => 'An unexpected error occurred while updating the client group type.'
            ];
        }

        return response()->json($response);
    }

}
