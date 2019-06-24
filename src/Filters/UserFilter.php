<?php

namespace Varbox\Filters;

class UserFilter extends Filter
{
    /**
     * Get the main where condition between entire request fields.
     *
     * @return string
     */
    public function morph()
    {
        return 'and';
    }

    /**
     * Get the filters that apply to the request.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'search' => 'operator:like|condition:or|columns:email,first_name,last_name',
            'active' => 'operator:=|condition:or|columns:active',
            'role' => 'operator:=|condition:or|columns:roles.role_id',
            'start_date' => 'operator:date >=|condition:or|columns:users.created_at',
            'end_date' => 'operator:date <=|condition:or|columns:users.created_at',
        ];
    }

    /**
     * Get the modified value of a request filter field.
     *
     * @return array
     */
    public function modifiers()
    {
        return [];
    }
}
