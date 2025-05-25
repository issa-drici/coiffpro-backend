<?php

namespace App\Infrastructure\Models;

use App\Infrastructure\Models\SalonModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceModel extends Model
{
    use HasUuids;

    protected $table = 'services';
    protected $fillable = [
        'name',
        'price',
        'duration',
        'description',
        'category',
        'salon_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(SalonModel::class, 'salon_id');
    }

    public function queueClients(): BelongsToMany
    {
        return $this->belongsToMany(QueueClientModel::class, 'queue_client_services', 'service_id', 'queue_client_id')
            ->withTimestamps();
    }
}
