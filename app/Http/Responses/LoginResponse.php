<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements Responsable
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->Estado === 'inactivo') {
            Auth::logout();
            session()->flash('error', 'Tu usuario está inactivo. Contacta con el administrador.');
            return redirect()->to(url('admin/login'));
        }

        if ($user->hasRole('Admin')) {
            return redirect()->intended(url('admin'));
        }

        if ($user->hasRole('Contable')) {
            return redirect()->intended(url('contable'));
        }

        if ($user->hasRole('General')) {
            return redirect()->intended(url('general'));
        }
        
        Auth::logout();
        session()->flash('error', 'Tu cuenta no tiene un rol válido.');
        return redirect()->to(url('admin/login'));
    }
}
