<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Http\Request;

class SetBranchPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {

            app(PermissionRegistrar::class)
                ->setPermissionsTeamId(
                    auth()->user()->current_branch_id
                );
        }

        return $next($request);
    }
}