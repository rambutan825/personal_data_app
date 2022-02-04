<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuleRow extends Model
{
    use HasFactory;

    protected $table = 'module_rows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id',
        'position',
    ];

    /**
     * Get the module associated with the module.
     *
     * @return BelongsTo
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the module row fields associated with the module.
     *
     * @return HasMany
     */
    public function fields()
    {
        return $this->hasMany(ModuleRowField::class);
    }
}
