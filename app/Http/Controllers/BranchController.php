<?php

namespace App\Http\Controllers;
use App\Models\Branch;

use Illuminate\Http\Request;

class BranchController extends Controller
{
        public function index() {

            $branches = Branch::orderBy('name', 'asc')->get();

            // Pass the data to a view
            return view('branch.index', compact('branches'));
        }

        public function store(Request $request)
        {
            // Validate the incoming request data
            $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'contact_info' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
    
            // Create a new branch instance
            Branch::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
                'contact_info' => $request->input('contact_info'),
                'address' => $request->input('address'),
                'status' => $request->input('status'),
            ]);
    
            // Redirect back with success message
            return redirect()->route('branch.index')->with('success', 'Branch created successfully!');
        }

        public function destroy($id)
        {
            // Find the branch by its ID
            $branch = Branch::findOrFail($id);
    
            // Delete the branch
            $branch->delete();
    
            // Redirect to the branches list with a success message
            return redirect()->route('branch.index')->with('success', 'Branch deleted successfully.');
        }


        public function update(Request $request, $id)
        {
            // Validate the incoming request data
            $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'contact_info' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
    
            // Find the branch by ID
            $branch = Branch::findOrFail($id);
    
            // Update branch fields
            $branch->code = $request->input('code');
            $branch->name = $request->input('name');
            $branch->contact_info = $request->input('contact_info');
            $branch->address = $request->input('address');
            $branch->status = $request->input('status');
            $branch->updated_at = now();
    
            // Save the changes
            $branch->save();
    
            // Redirect back with success message
            return redirect()->route('branch.index')->with('success', 'Branch updated successfully!');
        }

}