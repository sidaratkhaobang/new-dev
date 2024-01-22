<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\PrimaryUuid;

use App\Models\Traits\Creator;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];


    public $incrementing = false;
    protected $keyType = 'string';

    public $sortable = ['name', 'username', 'email'];
    public $sortableAs = ['department_name', 'section_name'];


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

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s, $request) {
                    $q2->where('users.username', 'like', '%' . $s . '%');
                    $q2->orWhere('users.name', 'like', '%' . $s . '%');
                    $q2->orWhere('users.email', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->department_id)) {
                $q->where('users.department_id', $request->department_id);
            }
            if (!empty($request->section_id)) {
                $q->where('users.section_id', $request->section_id);
            }
            if (!empty($request->branch_id)) {
                $q->where('users.branch_id', $request->branch_id);
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(UserDepartment::class, 'user_department_id', 'id');
    }

    public function departmentUser()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'notification_user.' . $this->id;
    }
}
