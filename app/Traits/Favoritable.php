<?php

namespace App\Traits;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Favoritable
{
    /**
     * Boot the trait.
     */
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * A reply can be favorited.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * Favorite the current reply.
     *
     * @return Model|null
     */
    public function favorite(): ?Model
    {
        if (! $this->isFavorited()) {
            return $this->favorites()->create(['user_id' => auth()->id()]);
        }

        return null;
    }

    /**
     * Unfavorite the current reply.
     *
     * @return void
     */
    public function unfavorite(): void
    {
        $this->favorites()->where('user_id', auth()->id())->get()->each->delete();
    }

    /**
     * Determine if the current reply has been favorited.
     *
     * @return bool
     */
    public function isFavorited(): bool
    {
        return (bool) $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * Fetch the favorited status as a property.
     *
     * @return bool
     */
    public function getIsFavoritedAttribute(): bool
    {
        return $this->isFavorited();
    }

    /**
     * Get the number of favorites for the reply.
     *
     * @return int
     */
    public function getFavoritesCountAttribute(): int
    {
        return $this->favorites->count();
    }
}
