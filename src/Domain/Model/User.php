<?php
namespace Budgetcontrol\Workspace\Domain\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspaces_users_mm', 'workspace_id', 'workspace_id');
    }
}