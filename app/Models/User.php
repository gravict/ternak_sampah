<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nik', 'name', 'email', 'username', 'password',
        'dob', 'gender', 'points', 'balance', 'streak',
        'last_trivia_date', 'profile_photo', 'role', 'admin_branch',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'last_trivia_date' => 'date',
        ];
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function primaryBankAccount()
    {
        return $this->hasOne(BankAccount::class)->where('is_primary', true);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
