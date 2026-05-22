<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'start_date',
        'end_date',
        'status',
    ];

    // Relasi ke Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke Service
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}