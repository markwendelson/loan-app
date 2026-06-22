<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

//models
use App\Models\Client;
use App\Models\ClientGroupType;
use App\Models\ClientGroupTagging;

class ClientGroupsController extends Controller
{
    //client group index
    public function index(Request $request)
    {
        if(!auth()->user()->can('create-client-group')) {
            abort(403, 'Unauthorized action.');
        }

        if($request->isMethod('post')) {
            if(isset($request->client_group_tagging) && $request->client_group_tagging) {
                $userId = auth()->user()->id;
            

                try {
                    DB::beginTransaction();

                    $bactchData = [];
                    foreach($request->client_group_tagging as $key => $val) {
                        $batchData[] = [
                            'client_id' => $val['client_id'],
                            'client_group_type_id' => $val['type'],
                            'created_by' => $userId,
                        ];
                    }

                    ClientGroupTagging::upsert(
                        $bactchData,
                        [
                            'client_id'
                        ],
                        [
                            'client_group_type_id',
                            'updated_at' => now()
                        ]
                    );

                    DB::commit();

                    return redirect()->route('client-group.index')->with('success', 'Client group created successfully!');
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->route('client-group.index')->withErrors($e->getMessage());
                }
            }
        }

        //get group person incharge
        $inchargeData = ClientGroupType::whereNotNull('client_id')->pluck('client_id')->toArray();

        //get all client list
        $clients = Client::select(
            'clients.*',
            'client_details.firstname',
            'client_details.middlename',
            'client_details.lastname',
        )->leftJoin('client_details', 'client_details.client_id', '=', 'clients.id')
        ->whereNotIn('clients.id', $inchargeData)
        ->get()->toArray();

        $clientGroupType = ClientGroupType::all()->toArray();

        return view('client.group.index', compact(
            'clients',
            'clientGroupType',
        ));
    }

    
    public function clientGrouptypes()
    {
        //check permission
        if(!auth()->user()->can('view-client-group-type')) {
            abort(403, 'Unauthorized action.');
        }

        $groupTypesData = ClientGroupType::select(
                'client_group_types.*',
                'client_details.firstname',
                'client_details.lastname',
                'client_details.middlename',
            )
            ->leftJoin('client_details', 'client_details.client_id', 'client_group_types.client_id')
            ->paginate(10);

        //get client list
        $clientData = Client::select(
                'clients.*',
                'client_details.firstname',
                'client_details.lastname',
                'client_details.middlename',
            )
            ->leftJoin('client_details', 'client_details.client_id', '=', 'clients.id')
            ->get();

        return view('client.group.group-type.index', compact(
            'groupTypesData',
            'clientData'
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
            'client_id' => [
                'required',
                'exists:clients,id'
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

            ClientGroupType::insert([
                'name' => $request->name,
                'description' => $request->description,
                'client_id' => $request->client_id,
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
            ],
            'client_id' => [
                'required',
                Rule::unique('client_group_types')->ignore($request->input('id'))
            ],
            [
                'client_id.required' => 'Person In-charge is required.',
                'client_id.unique' => 'This Persion is already assigned to another group type.',
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
                'client_id' => $request->client_id,
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

    public function removeClientGrouping($id)
    {
        $response = [];

        try {
            DB::beginTransaction();

            ClientGroupTagging::where('id', $id)->delete();

            DB::commit();
            $response = [
                'status' => 'success',
                'message' => 'Client succesfully removed from group.'
            ];
        }
        catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'An error occurred while removing the client from grouping: ' . $e->getMessage()
            ];
        }
        return response()->json($response);
    }

}
