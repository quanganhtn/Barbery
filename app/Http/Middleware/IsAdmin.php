<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        if (!auth()->check()) abort(403, 'Bạn chưa đăng nhập');

        // Voyager dùng role() relation, name = admin/user...
        $roleName = optional(auth()->user()->role)->name;

        if ($roleName !== 'admin') abort(403, 'Không có quyền');

        return $next($request);
    }

}
