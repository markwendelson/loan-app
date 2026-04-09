<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//models
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    //
    public function index()
    {
        $documentTypes = DocumentType::getAll()->paginate(10);

        return view('document-type.index', compact(
            'documentTypes'
        ));
    }
}
