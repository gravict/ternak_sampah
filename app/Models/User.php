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
        'dob', 'gender', 'points', 'kontribusi', 'level', 'balance', 'streak',
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

    public function kontribusiUntukNaikLevel(): int
    {
        if ($this->level >= 20) return 0;
        return $this->level * 100;
    }

    public static function totalKontribusiSampaiLevel(int $targetLevel): int
    {
        return (($targetLevel * ($targetLevel - 1)) / 2) * 100;
    }

    public function kontribusiProgress(): float
    {
        if ($this->level >= 20) return 100;
        $needed = $this->kontribusiUntukNaikLevel();
        $baseForCurrentLevel = self::totalKontribusiSampaiLevel($this->level);
        $progressInLevel = $this->kontribusi - $baseForCurrentLevel;
        return min(100, max(0, ($progressInLevel / $needed) * 100));
    }

    public function tambahKontribusi(int $jumlah): array
    {
        $this->kontribusi += $jumlah;
        $leveledUp = false;
        $oldLevel = $this->level;

        while ($this->level < 20) {
            $threshold = self::totalKontribusiSampaiLevel($this->level + 1);
            if ($this->kontribusi >= $threshold) {
                $this->level++;
                $leveledUp = true;
            } else {
                break;
            }
        }

        $this->save();

        return [
            'leveled_up' => $leveledUp,
            'old_level' => $oldLevel,
            'new_level' => $this->level,
            'kontribusi_earned' => $jumlah,
        ];
    }

    public function tahapPohon(): array
    {
        $labelAndEmoji = match(true) {
            $this->level <= 2  => ['label' => 'Biji',           'emoji' => '🫘'],
            $this->level <= 4  => ['label' => 'Tunas',          'emoji' => '🌱'],
            $this->level <= 7  => ['label' => 'Kecambah',       'emoji' => '🌿'],
            $this->level <= 10 => ['label' => 'Pohon Kecil',    'emoji' => '🪴'],
            $this->level <= 13 => ['label' => 'Pohon Sedang',   'emoji' => '🌲'],
            $this->level <= 17 => ['label' => 'Pohon Besar',    'emoji' => '🌳'],
            default            => ['label' => 'Pohon Rindang',  'emoji' => '🎄'],
        };

        return [
            'image' => 'stage-' . $this->level . '.png',
            'label' => $labelAndEmoji['label'],
            'stage' => $this->level,
            'emoji' => $labelAndEmoji['emoji'],
        ];
    }
}
