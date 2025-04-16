<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientIDsController extends Controller
{


    public function uploadValidID(Request $request)
    {
        $request->validate([
            'valid_id' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048' // Adjust max size as needed
        ]);
       
        if ($request->file('valid_id')->isValid()) {
            $path = $request->file('valid_id')->store('public/Client-Valid-IDs');
            $fileName = basename($path);
           
            // Optional: Save $fileName or $path to the database
           
            return back()->with('success', 'File uploaded successfully')->with('file', $fileName);
        }
       
        return back()->withErrors(['valid_id' => 'File upload failed. Please try again.']);
    }

}