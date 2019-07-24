<?php

namespace Varbox\Contracts;

interface EmailModelContract
{
    /**
     * @return mixed
     */
    public function getFromAddressAttribute();

    /**
     * @return mixed
     */
    public function getFromNameAttribute();

    /**
     * @return mixed
     */
    public function getSubjectAttribute();

    /**
     * @return mixed
     */
    public function getMessageAttribute();

    /**
     * @return mixed
     */
    public function getReplyToAttribute();

    /**
     * @return mixed
     */
    public function getAttachmentAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

    /**
     * @param array $data
     * @return array
     */
    public function getData(array $data = []);

    /**
     * @return mixed
     */
    public static function getFromAddress();

    /**
     * @return mixed
     */
    public static function getFromName();

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getVariables();

    /**
     * @return mixed
     * @throws \Varbox\Exceptions\EmailException
     */
    public function getView();

    /**
     * @return array
     */
    public static function getTypesForSelect();

    /**
     * @return array
     */
    public static function getVariablesForSelect();

    /**
     * @param int $type
     * @return array
     */
    public static function getEmailVariables($type);

    /**
     * @param string $type
     * @return EmailModelContract
     * @throws \Varbox\Exceptions\EmailException
     */
    public static function findByType($type);
}
