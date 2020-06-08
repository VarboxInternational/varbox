<?php

namespace Varbox\Filters;

use Varbox\Contracts\UploadFilterContract;

class UploadFilter extends Filter implements UploadFilterContract
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
                'columns' => 'original_name,full_path',
            ],
            'type' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'type',
            ],
            'size' => [
                'operator' => Filter::OPERATOR_BETWEEN,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'size',
            ],
            'start_date' => [
                'operator' => Filter::OPERATOR_DATE_GREATER_OR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'date',
            ],
            'end_date' => [
                'operator' => Filter::OPERATOR_DATE_SMALLER_OR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'date',
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
        return [
            'size' => function ($modified) {
                foreach (request()->query('size') as $size) {
                    $modified[] = $size * pow(1024, 2);
                }

                return $modified;
            },
        ];
    }
}
