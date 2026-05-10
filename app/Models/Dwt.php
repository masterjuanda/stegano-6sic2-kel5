<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dwt extends Model
{
    use HasFactory;

    protected $table = 'dwt';

    protected $fillable = [
        'user_id',
        'judul',
        'nama_file',
        'gambar',
        'is_encoded',
    ];

    protected $casts = [
        'is_encoded' => 'boolean',
    ];

    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) return null;
        return asset('storage/' . $this->gambar);
    }

    public function deleteGambar(): bool
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return Storage::disk('public')->delete($this->gambar);
        }
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
