<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\Session;


/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $username
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property string|null $auth
 * @property int $theme_id
 * @property int $two_factor_enabled
 * @property int $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $password_expired
 * @property string|null $ldap_guid
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User where2faEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User where2faSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLdapGuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordExpired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereThemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'theme_id',
        'two_factor_enabled',
        'two_factor_secret',
        'auth',
        'email_verified_at',
        'ldap_guid',
        'password_expired',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
    }

    /**
     * Required by LdapRecord to store LDAP GUID.
     */
    public function getLdapGuidColumn(): string
    {
        return 'ldap_guid';
    }

    /**
     * Required by LdapRecord to match users by email/username.
     */
    public function getLdapDomainColumn(): string
    {
        return 'email';
    }

    public function setLdapGuid($guid): void
    {
        $this->attributes[$this->getLdapGuidColumn()] = $guid;
    }

    public function setLdapDomain($domain): void
    {
        $this->attributes[$this->getLdapDomainColumn()] = $domain;
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            // Only set 'auth' to 'ldap' if being created via LDAP sync
            if (auth()->guard('ldap')->check()) {
                $user->auth = 'ldap';
            }
        });
    }

    static public function twoFactorCheck($email)
    {
        // Fetch your config values from DB
        $config = GeneralModel::config();

        $user_data = User::where('email', $email)->first();

        $twoFactorEnabledGlobally = (int) $config['two_factor_enabled'] ?? 0;
        $twoFactorEnforcedGlobally = (int) $config['two_factor_enforced'] ?? 0;
        $userTwoFactorEnabled = (int) $user_data->two_factor_enabled;

        $shouldRequire2FA = false;
        if ($twoFactorEnabledGlobally == 1) {
            if ($twoFactorEnforcedGlobally == 1) {
                $shouldRequire2FA = true;
            } elseif ($userTwoFactorEnabled == 1) {
                $shouldRequire2FA = true;
            }
        }

        if ($shouldRequire2FA) {
            if (!$user_data->two_factor_secret) {
                // User needs to setup 2FA first - deny login or redirect to setup page
                Session::put('login.id', $user_data->id);
                // return redirect()->route('two-factor.setup');
                return 'setup';
            }

            
            // Store user ID in session to be retrieved on 2FA challenge
            Session::put('login.id', $user_data->id);

            // Do NOT log user in yet, redirect to Fortify 2FA challenge route
            // return redirect()->route('two-factor.challenge');
            return 'challenge';
        }

        return 'skip';
    }
}
