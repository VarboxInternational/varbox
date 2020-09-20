<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Varbox\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Varbox\Contracts\EmailModelContract;
use Varbox\Exceptions\EmailException;
use Varbox\Options\ActivityOptions;
use Varbox\Options\DuplicateOptions;
use Varbox\Options\RevisionOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasRevisions;
use Varbox\Traits\HasUploads;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsDraftable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Email extends Model implements EmailModelContract
{
    use HasFactory;
    use HasUploads;
    use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use IsDraftable;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsCsvExportable;

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
     * Get the from address of an email instance.
     *
     * @return string
     */
    public function getFromAddressAttribute()
    {
        return $this->data['from_email'] ?? config('mail.from.address');
    }

    /**
     * Get the from name of an email instance.
     *
     * @return string
     */
    public function getFromNameAttribute()
    {
        return $this->data['from_name'] ?? config('mail.from.name');
    }

    /**
     * Get the subject of an email instance.
     *
     * @return string
     */
    public function getSubjectAttribute()
    {
        return $this->data['subject'] ?? null;
    }

    /**
     * Get the message of an email instance.
     *
     * @return string
     */
    public function getMessageAttribute()
    {
        return $this->data['message'] ?? null;
    }

    /**
     * Get the reply to address of an email instance.
     *
     * @return string
     */
    public function getReplyToAttribute()
    {
        return $this->data['reply_to'] ?? config('mail.from.address');
    }

    /**
     * Get the subject of an email instance.
     *
     * @return string
     */
    public function getAttachmentAttribute()
    {
        return $this->data['attachment'] ?? null;
    }

    /**
     * Get the corresponding view from the types config property, for a loaded email instance.
     *
     * @return string
     */
    public function getViewAttribute()
    {
        $types = (array)config('varbox.emails.types', []);

        if (!isset($types[$this->type]['view'])) {
            throw EmailException::viewNotFound();
        }

        return $types[$this->type]['view'];
    }

    /**
     * Get the corresponding body variables for a email type.
     *
     * @return array
     */
    public function getVariablesAttribute()
    {
        $types = (array)config('varbox.emails.types', []);
        $vars = (array)config('varbox.emails.variables', []);
        $variables = [];

        if (!isset($types[$this->type]['variables']) || empty($types[$this->type]['variables'])) {
            return [];
        }

        foreach ($types[$this->type]['variables'] as $variable) {
            if (isset($vars[$variable])) {
                $variables[$variable] = $vars[$variable];
            }
        }

        return $variables;
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
     * @return RevisionOptions
     */
    public function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(30);
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('name')
            ->excludeColumns('type')
            ->excludeRelations('revisions', 'activity');
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

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Type', 'Published', 'Created At', 'Last Modified At',
        ];
    }

    /**
     * Get the values for a row in the csv.
     *
     * @return array
     */
    public function toCsvArray()
    {
        return [
            $this->name,
            $this->type,
            $this->isDrafted() ? 'No' : 'Yes',
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
