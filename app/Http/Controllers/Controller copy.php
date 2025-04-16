<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

//declare models used
use App\Models\MenuCategory;
use App\Models\Menu;

class Controller extends BaseController {
    use AuthorizesRequests, ValidatesRequests;

    protected $menus = [];

}
