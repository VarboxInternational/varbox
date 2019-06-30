<?php

namespace Varbox\Filters;

class AddressFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:address,country.name,country.code,state.name,state.code,city.name',
            'country' => 'operator:=|condition:or|columns:country_id',
            'start_date' => 'operator:date >=|condition:or|columns:created_at',
            'end_date' => 'operator:date <=|condition:or|columns:created_at',
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
