<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Sop extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sops';

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'commodity',
        'description',
        'duration_days',
        'monthly_calendar',
        'inputs_needed',
        'thumbnail',
        'is_published',
    ];

    protected $casts = [
        // Cast JSON otomatis ke array PHP saat diakses
        'monthly_calendar' => 'array',
        'inputs_needed'    => 'array',
        'is_published'     => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sop) {
            if (empty($sop->slug)) {
                $sop->slug = Str::slug($sop->title);
            }
        });
    }

    // =====================
    // SCOPES
    // =====================

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForCommodity($query, string $commodity)
    {
        return $query->where('commodity', $commodity);
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) return null;
        return asset('storage/' . $this->thumbnail);
    }
}
