<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientModel extends Model
{
    use HasUuids;

    protected $table = 'clients';
    protected $fillable = [
        'firstName',
        'lastName',
        'phoneNumber',
        'email',
        'salon_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(SalonModel::class, 'salon_id');
    }

    public function queueClients(): HasMany
    {
        return $this->hasMany(QueueClientModel::class, 'client_id');
    }
}
