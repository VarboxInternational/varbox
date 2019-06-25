<?php

namespace Varbox\Options;

use Exception;
use Illuminate\Support\Arr;

class ActivityOptions
{
    /**
     * A short model definition name for easy readability.
     * For \App\User -> "user".
     *
     * @var string
     */
    private $type;

    /**
     * A value representing the name of the entity to be logged.
     * This field should return an attribute of the loaded model instance.
     *
     * @var string
     */
    private $name;

    /**
     * A value representing the url of the entity to be logged.
     * This value will be used to link the activity log info to the page of the entity.
     *
     * @var string
     */
    private $url;

    /**
     * The eloquent model events that should trigger an activity being logged.
     * By default (empty) all {after} model events will log activity.
     * created | updated | deleted | restored | drafted(*) | revisioned(*) | duplicated(*)
     *
     * @var array
     */
    private $events = [];

    /**
     * Get the value of a property of this class.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists(static::class, $name)) {
            return $this->{$name};
        }

        throw new Exception(
            'The property "' . $name . '" does not exist in class "' . static::class . '"'
        );
    }

    /**
     * Get a fresh instance of this class.
     *
     * @return ActivityOptions
     */
    public static function instance(): ActivityOptions
    {
        return new static();
    }

    /**
     * Set the $type to work with in the Varbox\Traits\HasActivity trait.
     *
     * @param $type
     * @return ActivityOptions
     */
    public function withEntityType($type): ActivityOptions
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the $name to work with in the Varbox\Traits\HasActivity trait.
     *
     * @param $name
     * @return ActivityOptions
     */
    public function withEntityName($name): ActivityOptions
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the $url to work with in the Varbox\Traits\HasActivity trait.
     *
     * @param $url
     * @return ActivityOptions
     */
    public function withEntityUrl($url): ActivityOptions
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the $logEvents to work with in the Varbox\Traits\HasActivity trait.
     *
     * @param $events
     * @return ActivityOptions
     */
    public function logOnlyFor(...$events): ActivityOptions
    {
        $this->events = Arr::flatten($events);

        return $this;
    }
}
