<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Varbox\Models\Activity;
use Varbox\Options\ActivityOptions;

trait HasActivity
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\ActivityOptions file.
     *
     * @var ActivityOptions
     */
    protected $activityOptions;

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return ActivityOptions
     */
    abstract public function getActivityOptions(): ActivityOptions;

    /**
     * Flag to manually enable/disable logging activity when a model is tampered with.
     *
     * @var bool
     */
    protected static $shouldLogActivity = true;

    /**
     * Boot the trait.
     *
     * @return void
     * @throws Exception
     */
    public static function bootHasActivity()
    {
        if (config('varbox.varbox-activity.enabled', false) !== true) {
            return;
        }

        static::activityEventsToBeLogged()->each(function ($event) {
            return static::$event(function (Model $model) use ($event) {
                if ($model->shouldLogActivity()) {
                    $model->logActivity($event);
                }

                if ($event == 'deleted') {
                    $model->activity()->forSubject($model)->update([
                        'obsolete' => true,
                    ]);
                }
            });
        });
    }

    /**
     * Model has many activity logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        $activity = config('varbox.varbox-binding.models.activity_model', Activity::class);

        return $this->morphMany($activity, 'subject');
    }

    /**
     * @param string $event
     * @throws Exception
     */
    public function logActivity($event)
    {
        if (!$this->getAttribute('id')) {
            return;
        }

        $this->initActivityOptions();

        $this->activity()->create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'entity_type' => $this->activityOptions->type ?: null,
            'entity_name' => $this->activityOptions->name ?: null,
            'entity_url' => $this->exists ? $this->activityOptions->url : null,
            'event' => $event,
        ]);
    }

    /**
     * Determine if activity should be logged.
     *
     * @return bool
     */
    public function shouldLogActivity()
    {
        return
            config('varbox.varbox-activity.enabled', true) === true &&
            static::$shouldLogActivity === true;
    }

    /**
     * Enable the activity logging for the current request.
     *
     * @return static
     */
    public function doLogActivity()
    {
        self::$shouldLogActivity = true;

        return $this;
    }

    /**
     * Disable the activity logging for the current request.
     *
     * @return static
     */
    public function doNotLogActivity()
    {
        self::$shouldLogActivity = false;

        return $this;
    }

    /**
     * Get the event names that should be recorded.
     * The script will try to record an activity log for each of these events.
     *
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public static function activityEventsToBeLogged()
    {
        $events = collect([
            'created', 'updated', 'deleted'
        ]);

        /*if (collect(class_uses(__CLASS__))->contains(SoftDeletes::class)) {
            $events->push('restored');
        }

        if (collect(class_uses(__CLASS__))->contains(HasDrafts::class)) {
            $events->push('drafted');
        }

        if (collect(class_uses(__CLASS__))->contains(HasRevisions::class)) {
            $events->push('revisioned');
        }

        if (collect(class_uses(__CLASS__))->contains(HasDuplicates::class)) {
            $events->push('duplicated');
        }*/

        return $events;
    }

    /**
     * Both instantiate the activity options as well as validate their contents.
     *
     * @return void
     * @throws Exception
     */
    protected function initActivityOptions()
    {
        if ($this->activityOptions === null) {
            $this->activityOptions = $this->getActivityOptions();
        }

        $this->validateActivityOptions();
    }

    /**
     * Check if mandatory activity options have been properly set from the model.
     * Check if $field has been set.
     *
     * @return void
     * @throws Exception
     */
    protected function validateActivityOptions()
    {
        if (!$this->activityOptions->type) {
            throw new Exception(
                'The model ' . static::class . ' uses the HasActivity trait' . PHP_EOL .
                'You are required to set the entity type that should act as the definition for the model.' . PHP_EOL .
                'You can do this from inside the getActivityOptions() method defined on the model.'
            );
        }

        if (!$this->activityOptions->name) {
            throw new Exception(
                'The model ' . static::class . ' uses the HasActivity trait' . PHP_EOL .
                'You are required to set the name for the logged model instance.' . PHP_EOL .
                'You can do this from inside the getActivityOptions() method defined on the model.'
            );
        }
    }
}
