<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['user_id', 'file_name', 'file_path', 'mime_type', 'file_size', 'alt_text', 'disk'])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    protected $attributes = [
        'disk' => 'public',
    ];

    protected static function booted(): void
    {
        static::creating(function (Media $media): void {
            $media->user_id ??= auth()->id();
        });

        static::saving(function (Media $media): void {
            if (! $media->isDirty('file_path')) {
                return;
            }

            $storage = Storage::disk($media->disk);

            if ($storage->exists($media->file_path)) {
                $media->mime_type = $storage->mimeType($media->file_path) ?: null;
                $media->file_size = $storage->size($media->file_path);
            }

            $media->file_name ??= basename($media->file_path);

            $originalPath = $media->getOriginal('file_path');

            if ($media->exists && $originalPath && $originalPath !== $media->file_path) {
                Storage::disk($media->getOriginal('disk') ?? $media->disk)->delete($originalPath);
            }
        });

        static::deleted(function (Media $media): void {
            Storage::disk($media->disk)->delete($media->file_path);
        });
    }

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => Storage::disk($this->disk)->url($this->file_path));
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }
}
