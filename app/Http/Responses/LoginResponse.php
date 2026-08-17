<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Livewire\Features\SupportRedirects\Redirector
     */
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = auth()->user();

        if ($user && $user->isJudge() && !$user->isAdmin()) {
            return redirect()->intended(\App\Filament\Pages\JudgeWorkstation::getUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}
