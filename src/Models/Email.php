<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Varbox\Options\ActivityOptions;
use Varbox\Options\DuplicateOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasUploads;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\EmailModelContract;
use Varbox\Exceptions\EmailException;

class Email extends Model implements EmailModelContract
{
    use HasUploads;
    //use HasDrafts;
    //use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    //use IsSoftDeletable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'data',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'drafted_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        /*static::drafting(function (EmailModelContract $email) {
            if (!$email->isDraftingEnabled()) {
                return false;
            }
        });

        static::revisioning(function (EmailModelContract $email) {
            if (!$email->isRevisioningEnabled()) {
                return false;
            }
        });

        static::duplicating(function (EmailModelContract $email) {
            if (!$email->isDuplicatingEnabled()) {
                return false;
            }
        });

        static::restoring(function (EmailModelContract $email) {
            if (!$email->isSoftDeletingEnabled()) {
                return false;
            }
        });

        static::deleted(function (EmailModelContract $email) {
            if ($email->forceDeleting === false && !$email->isSoftDeletingEnabled()) {
                $email->forceDelete();
            }
        });*/
    }

    /**
     * Get the from address of an email instance.
     *
     * @return mixed
     */
    public function getFromAddressAttribute()
    {
        return $this->data['from_email'] ?? config('mail.from.address');
    }

    /**
     * Get the from name of an email instance.
     *
     * @return mixed
     */
    public function getFromNameAttribute()
    {
        return $this->data['from_name'] ?? config('mail.from.name');
    }

    /**
     * Get the reply to address of an email instance.
     *
     * @return mixed
     */
    public function getReplyToAttribute()
    {
        return $this->data['reply_to'] ?? config('mail.from.address');
    }

    /**
     * Get the subject of an email instance.
     *
     * @return mixed
     */
    public function getSubjectAttribute()
    {
        return $this->data['subject'] ?? null;
    }

    /**
     * Get the message of an email instance.
     *
     * @return mixed
     */
    public function getMessageAttribute()
    {
        return $this->data['message'] ?? null;
    }

    /**
     * Get the subject of an email instance.
     *
     * @return mixed
     */
    public function getAttachmentAttribute()
    {
        return $this->data['attachment'] ?? null;
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Get the corresponding data for a loaded email instance.
     * Also, at the email data, append the additional provided data from this method.
     *
     * @param array $data
     * @return array
     */
    public function getData(array $data = [])
    {
        return array_merge((array)$this->data ?? [], $data);
    }

    /**
     * Get the from email setting option.
     *
     * @return mixed
     */
    public static function getFromAddress()
    {
        return config('mail.from.address');
    }

    /**
     * Get the from name setting option.
     *
     * @return mixed
     */
    public static function getFromName()
    {
        return config('mail.from.name');
    }

    /**
     * Get all email types defined inside the "config/varbox/emails.php" file.
     *
     * @return array
     */
    public static function getTypes()
    {
        return (array)config('varbox.emails.types', []);
    }

    /**
     * Get all email types defined inside the "config/varbox/emails.php" file.
     *
     * @return array
     */
    public static function getVariables()
    {
        return (array)config('varbox.emails.variables', []);
    }

    /**
     * Get the corresponding view from the types config property, for a loaded email instance.
     *
     * @return mixed
     * @throws EmailException
     */
    public function getView()
    {
        $types = static::getTypes();

        if (!isset($types[$this->type]['view'])) {
            throw EmailException::viewNotFound();
        }

        return $types[$this->type]['view'];
    }

    /**
     * Get the formatted email types for a select.
     * Final format will be: [type => title-cased type].
     *
     * @return array
     */
    public static function getTypesForSelect()
    {
        $types = [];

        foreach (array_keys(static::getTypes()) as $type) {
            $types[$type] = Str::title(str_replace(['_', '-', '.'], ' ', $type));
        }

        return $types;
    }

    /**
     * Get the formatted email variables for a select.
     * Final format will be: [variable => title-cased variable].
     *
     * @return array
     */
    public static function getVariablesForSelect()
    {
        $variables = [];

        foreach (array_keys(static::getVariables()) as $variable) {
            $variables[$variable] = title_case(str_replace(['_', '-', '.'], ' ', $variable));
        }

        return $variables;
    }

    /**
     * Get the corresponding body variables for a email type.
     *
     * @param int $type
     * @return array
     */
    public static function getEmailVariables($type)
    {
        $types = static::getTypes();
        $vars = static::getVariables();
        $variables = [];

        if (!isset($types[$type]['variables']) || empty($types[$type]['variables'])) {
            return [];
        }

        foreach ($types[$type]['variables'] as $variable) {
            if (isset($vars[$variable])) {
                $variables[$variable] = $vars[$variable];
            }
        }

        return $variables;
    }

    /**
     * Return the email corresponding to the provided type.
     *
     * @param string $type
     * @return EmailModelContract
     */
    public static function findByType($type)
    {
        try {
            return app(EmailModelContract::class)->whereType($type)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw EmailException::emailNotFound($type);
        }
    }

    /**
     * Get the specific upload config parts for this model.
     *
     * @return array
     */
    public function getUploadConfig()
    {
        return [];
    }

    /**
     * @return DraftOptions
     */
    /*public function getDraftOptions()
    {
        return DraftOptions::instance();
    }*/

    /**
     * @return RevisionOptions
     */
    /*public function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(100);
    }*/

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('name');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('email')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.emails.edit', $this->getKey()));
    }
}
