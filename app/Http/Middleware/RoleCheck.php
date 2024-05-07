<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Memeriksa apakah pengguna telah melakukan autentikasi
        if (!Auth::check()) {
            Alert::toast('Error!!!', 'warning');
            return redirect('/login');
        }

        $user = Auth::user();

        // Memeriksa apakah pengguna memiliki peran yang ditetapkan
        if (!$user->role) {
            Alert::toast('Error!!!', 'warning');
            return redirect('/login');
        }

        // Memeriksa apakah peran pengguna sesuai dengan yang diharapkan
        if ($user->role !== $role) {
            Alert::toast('Error!!!', 'warning');
            return redirect('/login');
        }

        return $next($request);
    }
}
