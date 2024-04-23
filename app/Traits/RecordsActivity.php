<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait RecordsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;

        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    /**
     * Fetch all model events that require activity recording.
     *
     * @return array
     */
    protected static function getActivitiesToRecord(): array
    {
        return ['created'];
    }

    /**
     * Record new activity for the model.
     *
     * @param string $event
     */
    protected function recordActivity($event): void
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event)
        ]);
    }

    /**
     * Fetch the activity relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Determine the activity type.
     *
     * @param  string $event
     * @return string
     */
    protected function getActivityType($event): string
    {
        $type = strtolower(class_basename($this));

        return "{$event}_{$type}";
    }
}
