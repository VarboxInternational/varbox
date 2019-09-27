<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Book extends Schema
{
    /**
     * Generate the json+ld schema code for type Book.
     *
     * @return string|void
     */
    public function generate()
    {
        return SchemaGenerator::book()
            ->name($this->normalizeValue('name'))
            ->url($this->normalizeUrlValue('url'))
            ->description($this->normalizeValue('description'))
            ->image($this->normalizeUrlValue('image'))
            ->genre($this->normalizeValue('genre'))
            ->numberOfPages($this->normalizeValue('page_number'))
            ->author($this->buildAuthor())
            ->publisher($this->buildPublisher())
            ->offers($this->buildOffer())
            ->aggregateRating($this->buildAggregateRating())
            ->toScript();
    }

    /**
     * Build author schema.
     *
     * @return \Spatie\SchemaOrg\Person
     */
    protected function buildAuthor()
    {
        $name = $this->normalizeValue('author_name');

        if ($name) {
            return SchemaGenerator::person()
                ->name($name);
        }

        return null;
    }

    /**
     * Build publisher schema.
     *
     * @return \Spatie\SchemaOrg\Organization
     */
    protected function buildPublisher()
    {
        $name = $this->normalizeValue('publisher_name');

        if ($name) {
            return SchemaGenerator::organization()
                ->name($name);
        }

        return null;
    }

    /**
     * Build offer schema.
     *
     * @return \Spatie\SchemaOrg\Offer
     */
    protected function buildOffer()
    {
        $price = $this->normalizeValue('price');
        $currency = $this->normalizeValue('currency');

        if ($price && $currency) {
            return SchemaGenerator::offer()
                ->price($price)
                ->priceCurrency($currency);
        }

        return null;
    }

    /**
     * Build aggregate rating schema.
     *
     * @return \Spatie\SchemaOrg\AggregateRating
     */
    protected function buildAggregateRating()
    {
        $rating = $this->normalizeValue('rating');
        $count = $this->normalizeValue('review_count');

        if ($rating && $count) {
            return SchemaGenerator::aggregateRating()
                ->ratingValue($rating)
                ->reviewCount($count);
        }

        return null;
    }
}
