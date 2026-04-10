<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

//models
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    
    public function index()
    {
        $documentTypes = DocumentType::getAll()->paginate(10);

        return view('document-type.index', compact(
            'documentTypes'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255|unique:document_types,type_name',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'fail',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        $data = $request->only(['type_name', 'description', 'status']);

        try {
            DocumentType::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Document type saved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving document type: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:255|unique:document_types,type_name,'. $request->id .'',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1'
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'fail',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        $data = $request->only(['type_name', 'description', 'status']);

        try {
            $docTypeData = DocumentType::find($request->id);

            if(!$docTypeData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document Type not found'
                ], 404);
            }

            $docTypeData->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Document type updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving document type: ' . $e->getMessage()
            ], 500);
        }
    }
}
