<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

//models
use App\Models\CountryCode;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request) {

        if(!auth()->user()->can('view-vendors')) {
            abort(403);
        }
        
        $vendorData = Vendor::paginate(10);
        $countryCode = CountryCode::getCountryCodes([
            'is_multiple' => 1,
        ]);

        return view('vendors.index', compact(
            'vendorData',
            'countryCode'
        ));
    }
}
