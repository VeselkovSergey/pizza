<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissionCategory, $permission = null, $subPermission = null)
    {
        if (auth()->user()) {
            if (auth()->user()->checkAccess($permissionCategory, $permission, $subPermission)) {
                return $next($request);
            }
        }

        throw new AuthorizationException('Доступ закрыт');
    }
}
