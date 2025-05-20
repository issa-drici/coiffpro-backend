<?php

namespace App\Infrastructure\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationModel extends Model
{
    protected $table = 'email_notifications';
    
    protected $fillable = [
        'user_id',
        'type',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
