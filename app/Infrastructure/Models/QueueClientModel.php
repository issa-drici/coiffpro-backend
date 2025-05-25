<?php

namespace App\Infrastructure\Models;

use App\Infrastructure\Models\SalonModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QueueClientModel extends Model
{
    use HasUuids;

    protected $table = 'queue_clients';
    protected $fillable = [
        'client_id',
        'status',
        'estimatedTime',
        'amountToPay',
        'salon_id'
    ];

    protected $casts = [
        'estimatedTime' => 'datetime',
        'amountToPay' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    public function salon(): BelongsTo
    {
        return $this->belongsTo(SalonModel::class, 'salon_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceModel::class, 'queue_client_services', 'queue_client_id', 'service_id')
            ->withTimestamps();
    }
}
