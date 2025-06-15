<?php

namespace App\Infrastructure\Models;

use App\Infrastructure\Models\SalonModel;
use App\Models\Barber;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QueueClientModel extends Model
{
    use HasUuids;

    protected $table = 'queue_clients';
    protected $fillable = [
        'id',
        'client_id',
        'salon_id',
        'barber_id',
        'status',
        'amountToPay',
        'notes',
        'ticket_number'
    ];

    protected $casts = [
        'amountToPay' => 'decimal:2',
        'ticket_number' => 'integer',
        'estimatedTime' => 'datetime',
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

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class, 'barber_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceModel::class, 'queue_client_services', 'queue_client_id', 'service_id')
            ->withTimestamps();
    }
}
