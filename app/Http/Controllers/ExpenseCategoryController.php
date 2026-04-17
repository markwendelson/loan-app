<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


//models
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    //
    public function index()
    {
        $categories = ExpenseCategory::paginate(10);

        return view('expenses.category.index', compact(
            'categories'
        ));
    }
}
