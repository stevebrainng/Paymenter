<?php

namespace App\Models;

use App\Models\Traits\HasPlans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory, HasPlans;

    protected $guarded = [];

    protected $auditInclude = [
        'name',
        'description',
        'category_id',
        'enabled',
    ];

    /**
     * Get the category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the configurable options of the product.
     */
    public function configOptions(): HasManyThrough
    {
        return $this->hasManyThrough(ConfigOption::class, ConfigOptionProduct::class, 'product_id', 'id', 'id', 'config_option_id')->where('hidden', false)->orderBy('sort', 'asc')->orderBy('id', 'desc');
    }

    /**
     * Get the extension of the product.
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get all services using this product.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the settings of the product.
     */
    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    /**
     * Get all available products upgrades
     */
    public function upgrades()
    {
        return $this->belongsToMany(Product::class, 'product_upgrades', 'product_id', 'upgrade_id');
    }

    /**
     * Gets all upgradable config options for the product.
     */
    public function upgradableConfigOptions(): HasManyThrough
    {
        return $this->hasManyThrough(ConfigOption::class, ConfigOptionProduct::class, 'product_id', 'id', 'id', 'config_option_id')->where('hidden', false)->where('upgradable', true)->orderBy('sort', 'asc')->orderBy('id', 'desc');
    }
}
