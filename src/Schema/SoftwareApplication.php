<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class SoftwareApplication extends Schema
{
    /**
     * Generate the json+ld schema code for type Event.
     *
     * @return string|void
     */
    public function generate()
    {
        $method = $this->schema->fields['type'] ?? null;

        if (!array_key_exists($method, $this->schema->softwareApplicationSchemaTypes())) {
            return;
        }

        return SchemaGenerator::{$method}()
            ->name($this->normalizeValue('name'))
            ->image($this->normalizeUploadValue('image'))
            ->applicationCategory($this->normalizeValue('category'))
            ->operatingSystem($this->normalizeValue('operating_system'))
            ->offers($this->buildOffers())
            ->aggregateRating($this->buildAggregateRating())
            ->toScript();
    }

    /**
     * Build offer schema.
     *
     * @return \Spatie\SchemaOrg\Offer
     */
    protected function buildOffers()
    {
        $price = $this->normalizeValue('price');
        $currency = $this->normalizeValue('currency');

        if ($price || $currency) {
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
