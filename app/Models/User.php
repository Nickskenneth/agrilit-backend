<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'region',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
    ];

    // =====================
    // ROLE HELPER METHODS
    // =====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPakar(): bool
    {
        return $this->role === 'pakar';
    }

    public function isPetani(): bool
    {
        return $this->role === 'petani';
    }

    public function isExpert(): bool
    {
        // Admin dan Pakar keduanya bisa menjawab di forum
        return in_array($this->role, ['admin', 'pakar']);
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function sops()
    {
        return $this->hasMany(Sop::class, 'author_id');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function diseaseScans()
    {
        return $this->hasMany(DiseaseScan::class);
    }

    public function articleReads()
    {
        return $this->hasMany(ArticleRead::class);
    }
}
