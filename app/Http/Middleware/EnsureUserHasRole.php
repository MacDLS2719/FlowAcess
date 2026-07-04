<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return $this->redirectToLogin($role);
        }

        $user = Auth::user(); 
        if ($user->Estado === 'inactivo') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Tu usuario está inactivo. Contacta con el administrador.',
            ]);
        }

        if (!$user->hasRole($role)) {
            return $this->redirectToUserPanel($user);
        }
        return $next($request);
    }

    private function redirectToLogin(string $role)
    {
        $panelId = match ($role) {
            'Admin' => 'admin',
            'Contable' => 'contable',
            'General' => 'general',
            default => 'admin',
        };

        return redirect()->to(url("{$panelId}/login"));
    }

    private function redirectToUserPanel($user)
    {
        if ($user->hasRole('Admin')) {
            return redirect()->to(url('admin'));
        }

        if ($user->hasRole('Contable')) {
            return redirect()->to(url('contable'));
        }

        if ($user->hasRole('General')) {
            return redirect()->to(url('general'));
        }
        
        Auth::logout();
        session()->flash('error', 'Tu cuenta no tiene un rol válido.');
        return redirect()->to(url('admin/login'));
    }
}