<?php

namespace Varbox\Contracts;

interface EmailModelContract
{
    /**
     * @return string
     */
    public function getFromAddressAttribute();

    /**
     * @return string
     */
    public function getFromNameAttribute();

    /**
     * @return string
     */
    public function getSubjectAttribute();

    /**
     * @return string
     */
    public function getMessageAttribute();

    /**
     * @return string
     */
    public function getReplyToAttribute();

    /**
     * @return string
     */
    public function getAttachmentAttribute();

    /**
     * @return string
     * @throws \Varbox\Exceptions\EmailException
     */
    public function getViewAttribute();

    /**
     * Get the corresponding body variables for a email type.
     *
     * @return array
     */
    public function getVariablesAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

    /**
     * @param string $type
     * @return EmailModelContract
     * @throws \Varbox\Exceptions\EmailException
     */
    public static function findByType($type);
}
