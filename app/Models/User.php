<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'google_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // get user role pelamar
    public static function getUserPelamar()
    {
        $users = User::role('pelamar')->get();

        return $users;
    }

    // get count user pelemar
    public static function getCountUserPelamar()
    {
        $countUsers = User::role('pelamar')->count();

        return $countUsers;
    }

    // relasi dengan tabel personal pelamar
    public function personalPelamar()
    {
        return $this->hasOne(PersonalPelamar::class);
    }

    // relasi dengan table personal company
    public function personalCompany()
    {
        return $this->hasOne(PersonalCompany::class);
    }

    //relasi dengan tabel curriculum vitae user
    public function curriculumVitaeUser()
    {
        return $this->hasMany(CurriculumVitaeUser::class);
    }

    //relasi dengan tabel applicant
    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
