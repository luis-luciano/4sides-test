<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'seg_usuario';
    protected $primaryKey = "idUsuario";


    /**
     * The column name of the password field using during authentication.
     *
     * @var string
     */
    protected $authPasswordName = 'usuario_password';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_nombre',
        'usuario_email',
        'usuario_password',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'usuario_password',
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
            'usuario_ultima_conexion' => 'datetime',
            'usuario_password' => 'hashed',
        ];
    }

    public function getProfileAttribute()
    {
        return  !empty($this->attributes['profile_image']) ? Storage::disk('public')->url($this->attributes['profile_image']) : asset('assets/image/foto-perfil.png');
    }

    /**
     * Get the e-mail through usuario_email.
     * Used By RoutesNotifications Trait
     * @param mixed $key
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->attributes['usuario_email'];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['usuario_email'] = $value;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['usuario_password'] = bcrypt($value);
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->usuario_email;
    }

    public function setStatusAttribute(StatusUser $statusUser)
    {
        $this->attributes['usuario_estado'] = $statusUser->value;
    }

    public function getStatusAttribute()
    {
        return StatusUser::from($this->attributes['usuario_estado']);
    }

    public function getIsAdminDescriptionAttribute()
    {
        return trans($this->attributes['is_admin'] ? 'Si' : 'No');
    }

    public function getLastConnectedAttribute()
    {
        return $this->usuario_ultima_conexion ? $this->usuario_ultima_conexion->diffForHumans() : 'Nunca';
    }

    public function setIsConnectedAttribute(bool $isConnected)
    {
        $this->attributes['usuario_conectado'] = $isConnected;
    }

    public function getIsConnectedAttribute()
    {
        return $this->attributes['usuario_conectado'] ?? false;
    }

    public function getUserConnectedAttribute()
    {
        return $this->attributes['usuario_conectado'] ?? false ? 'Conectado' : 'Desconectado';
    }

    public function saveDataOfLastLogin()
    {
        $this->timestamps = false;
        $this->isConnected = false;
        $this->usuario_ultima_conexion = now();
        $this->save();
    }
}
