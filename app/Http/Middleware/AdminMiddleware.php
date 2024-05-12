<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->role_id === Role::whereName('admin')->value('id')) {
            return $next($request);
        }

        return response()->json(['error' => 'Only for admins'], 401);
    }
}
