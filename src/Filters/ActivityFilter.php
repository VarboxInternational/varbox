<?php

namespace Varbox\Filters;

use Varbox\Contracts\ActivityFilterContract;

class ActivityFilter extends Filter implements ActivityFilterContract
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
            'user' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'user_id',
            ],
            'entity' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'entity_type',
            ],
            'event' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'event',
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
