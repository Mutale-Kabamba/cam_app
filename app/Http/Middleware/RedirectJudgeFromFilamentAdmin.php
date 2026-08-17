<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectJudgeFromFilamentAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // If the user is a judge (and not an admin), route them to the Judge workstation
        if ($user && $user->isJudge() && !$user->isAdmin()) {
            if ($request->routeIs('filament.admin.auth.logout')) {
                return $next($request);
            }
            return redirect()->route('judge.index');
        }

        return $next($request);
    }
}
