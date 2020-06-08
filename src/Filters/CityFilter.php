<?php

namespace Varbox\Filters;

use Varbox\Contracts\CityFilterContract;

class CityFilter extends Filter implements CityFilterContract
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
            'search' => [
                'operator' => Filter::OPERATOR_LIKE,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'name,country.name',
            ],
            'country' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'country_id',
            ],
            'state' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'state_id',
            ],
            'start_date' => [
                'operator' => Filter::OPERATOR_DATE_GREATER_OR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'created_at',
            ],
            'end_date' => [
                'operator' => Filter::OPERATOR_DATE_SMALLER_OR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'created_at',
            ],
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
