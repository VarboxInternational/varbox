<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Review extends Schema
{
    /**
     * Generate the json+ld schema code for type Review.
     *
     * @return string
     */
    public function generate()
    {
        return SchemaGenerator::review()
            ->author($this->buildAuthor())
            ->itemReviewed($this->buildItemReviewed())
            ->reviewRating($this->buildRating())
            ->toScript();
    }

    /**
     * Build item reviewed schema.
     *
     * @return \Spatie\SchemaOrg\Product
     */
    protected function buildItemReviewed()
    {
        return SchemaGenerator::product()
            ->name($this->normalizeValue('name'))
            ->description($this->normalizeValue('description'))
            ->image($this->normalizeUploadValue('image'));
    }

    /**
     * Build author schema.
     *
     * @return \Spatie\SchemaOrg\Person
     */
    protected function buildAuthor()
    {
        return SchemaGenerator::person();
    }

    /**
     * Build rating schema.
     *
     * @return \Spatie\SchemaOrg\Rating
     */
    protected function buildRating()
    {
        return SchemaGenerator::rating()
            ->ratingValue($this->normalizeValue('rating'));
    }
}
