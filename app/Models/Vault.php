<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vault extends Model
{
    use HasFactory;

    /**
     * Possible vault permissions.
     */
    const PERMISSION_VIEW = 300;
    const PERMISSION_EDIT = 200;
    const PERMISSION_MANAGE = 100;

    /**
     * Possible vault types.
     */
    const TYPE_PERSONAL = 'personal';
    const TYPE_FAMILY = 'family';
    const TYPE_COMMUNITY = 'community';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'type',
        'name',
        'description',
        'default_template_id',
    ];

    /**
     * Get the account associated with the vault.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the template associated with the vault.
     *
     * @return BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'default_template_id');
    }

    /**
     * Get the users associated with the vault.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('permission');
    }
}
