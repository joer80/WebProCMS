<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\RoleCast;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleCast::class,
            'must_change_password' => 'boolean',
        ];
    }

    public function isAtLeast(Role $role): bool
    {
        return $this->role->isAtLeast($role);
    }

    /**
     * Returns the effective Role for display/preview purposes.
     * Super users can simulate a lower role via session; all others always get their real role.
     */
    public function effectiveRole(): Role
    {
        $preview = session('preview_role');

        if ($preview && $this->role === Role::Super) {
            $role = collect(Role::cases())->firstWhere('name', ucfirst($preview));

            if ($role && $this->role->value > $role->value) {
                return $role;
            }
        }

        return $this->role;
    }

    /**
     * True when a Super user is actively simulating a lower role.
     */
    public function isPreviewingRole(): bool
    {
        return $this->role === Role::Super && $this->effectiveRole() !== $this->role;
    }

    /**
     * isAtLeast() using the effective (possibly simulated) role.
     * Use this in views and middleware so previewing a lower role is fully reflected.
     */
    public function previewIsAtLeast(Role $role): bool
    {
        return $this->effectiveRole()->isAtLeast($role);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
