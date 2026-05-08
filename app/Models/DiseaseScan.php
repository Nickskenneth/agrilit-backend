<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiseaseScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'commodity',
        'result_label',
        'result_label_id',
        'confidence_score',
        'latitude',
        'longitude',
        'location_name',
        'synced',
        'scanned_at',
        'notes',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'latitude'         => 'float',
        'longitude'        => 'float',
        'synced'           => 'boolean',
        'scanned_at'       => 'datetime',
    ];

    // =====================
    // SCOPES
    // =====================

    // Data yang belum di-sync (untuk monitoring admin)
    public function scopeUnsynced($query)
    {
        return $query->where('synced', false);
    }

    // Filter berdasarkan komoditas
    public function scopeForCommodity($query, string $commodity)
    {
        return $query->where('commodity', $commodity);
    }

    // Data yang punya koordinat (untuk peta)
    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    // Filter berdasarkan area bounding box (untuk peta Malang)
    public function scopeInBounds($query, $latMin, $latMax, $lngMin, $lngMax)
    {
        return $query->whereBetween('latitude', [$latMin, $latMax])
            ->whereBetween('longitude', [$lngMin, $lngMax]);
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        return asset('storage/' . $this->image_path);
    }

    // Format confidence score jadi persentase, misal: 0.9312 -> "93.1%"
    public function getConfidencePercentAttribute(): string
    {
        if (!$this->confidence_score) return 'N/A';
        return round($this->confidence_score * 100, 1) . '%';
    }
}
