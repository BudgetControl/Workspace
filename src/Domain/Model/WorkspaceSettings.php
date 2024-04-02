<?php 
namespace Budgetcontrol\Workspace\Domain\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkspaceSettings extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspace_settings';
    protected $fillable = ['workspace_id', 'key', 'value'];

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true),
        );
    }
}