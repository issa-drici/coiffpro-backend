<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Barber;

class SalonModel extends Model
{
    protected $table = 'salons';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'address',
        'postal_code',
        'city',
        'city_slug',
        'name_slug',
        'type_slug',
        'phone',
        'logo_id',
        'social_links',
        'google_info'
    ];

    protected $casts = [
        'social_links' => 'array',
        'google_info' => 'array'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function logo()
    {
        return $this->belongsTo(FileModel::class, 'logo_id');
    }

    /**
     * Relation avec les barbers
     */
    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }
}
