<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    // Menentukan kolom mana saja yang boleh diisi
    protected $fillable = ["name", "price", "description", "status"];

    // Menentukan konversi tipe data
    protected function casts(): array
    {
        return [
            "status" => "boolean",
            "price" => "integer",
        ];
    }

    // Membuat relasi ke tabel Subscription (nantinya)
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}