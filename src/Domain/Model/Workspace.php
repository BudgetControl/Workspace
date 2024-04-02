<?php

namespace Budgetcontrol\Workspace\Domain\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "workspaces";

    protected $fillable = [
        'updated_at'
    ];

     /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'workspaces_users_mm','workspace_id','workspace_id');
    }

        /**
     * The users that belong to the role.
     */
    public function setting()
    {
        return $this->hasOne(WorkspaceSettings::class, 'workspace_id');
    }

    public function scopeByUuid($query,$uuid)
    {
        return $query->where('uuid', $uuid)->with('setting')->with('users');
    }


}
