<?php

namespace App\Models\Property;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Configuration\EnumOption;

class Property extends Model
{
    protected $fillable = [
        'name',
        'address',
        'status',
        'owner_id',
        'tenant_id',
        'property_type_id'
    ];

    /**
     * Relación: Propiedad - Dueño (owner_id)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relación: Propiedad - Inquilino (tenant_id)
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Relación: Propiedad - Tipo de propiedad (property_type_id)
     */
    public function propertyType()
    {
        return $this->belongsTo(EnumOption::class, 'property_type_id');
    }
}
