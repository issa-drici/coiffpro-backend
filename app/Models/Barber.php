<?php

namespace App\Models;

use App\Infrastructure\Models\QueueClientModel;
use App\Infrastructure\Models\SalonModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barber extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'salon_id',
        'bio',
        'is_active',
        'is_active_changed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_active_changed_at' => 'datetime',
        ];
    }

    /**
     * Boot method pour mettre à jour automatiquement is_active_changed_at
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($barber) {
            if ($barber->isDirty('is_active')) {
                $barber->is_active_changed_at = now();
            }
        });
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le salon
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(SalonModel::class, 'salon_id');
    }

    /**
     * Relation avec les clients en file d'attente
     */
    public function queueClients(): HasMany
    {
        return $this->hasMany(QueueClientModel::class);
    }
}
