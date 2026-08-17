<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'judge_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isJudge();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || str_contains(strtolower($this->email), 'admin');
    }

    public function isJudge(): bool
    {
        return $this->role === 'judge' || str_contains(strtolower($this->email), 'judge');
    }

    public function getJudgeName(): string
    {
        if (!empty($this->judge_name)) {
            return $this->judge_name;
        }

        if (str_contains(strtolower($this->email), 'judge1') || str_contains(strtolower($this->name), 'judge 1')) {
            return 'Judge 1';
        }
        if (str_contains(strtolower($this->email), 'judge2') || str_contains(strtolower($this->name), 'judge 2')) {
            return 'Judge 2';
        }
        if (str_contains(strtolower($this->email), 'judge3') || str_contains(strtolower($this->name), 'judge 3')) {
            return 'Judge 3';
        }

        return 'Judge 1';
    }
}
