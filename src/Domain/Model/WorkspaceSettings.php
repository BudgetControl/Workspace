<?php 
namespace Budgetcontrol\Workspace\Domain\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class WorkspaceSettings extends Model
{
    protected $table = 'workspace_settings';
    protected $fillable = ['workspace_id', 'key', 'value'];

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true),
        );
    }
}