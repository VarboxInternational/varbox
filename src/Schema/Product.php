<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\ItemAvailability;
use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Product extends Schema
{
    /**
     * Generate the json+ld schema code for type Product.
     *
     * @return string
     */
    public function generate()
    {
        return SchemaGenerator::product()
            ->name($this->normalizeValue('name'))
            ->description($this->normalizeValue('description'))
            ->image($this->normalizeUploadValue('image'))
            ->sku($this->normalizeValue('sku'))
            ->mpn($this->normalizeValue('isbn'))
            ->brand($this->buildBrand())
            ->offers($this->buildOffers())
            ->review($this->buildReview())
            ->aggregateRating($this->buildAggregateRating())
            ->toScript();
    }

    /**
     * Build brand schema.
     *
     * @return \Spatie\SchemaOrg\Brand
     */
    protected function buildBrand()
    {
        return SchemaGenerator::brand()
            ->name($this->normalizeValue('brand'));
    }

    /**
     * Build offer schema.
     *
     * @return \Spatie\SchemaOrg\Offer
     */
    protected function buildOffers()
    {
        $price = $this->normalizeUrlValue('url');

        if ($price) {
            return SchemaGenerator::offer()
                ->url($price)
                ->price((float)$this->normalizeValue('price'))
                ->availability($this->buildAvailability())
                ->priceCurrency($this->normalizeValue('currency'))
                ->priceValidUntil(now()->addMonth()->format('Y-m-d'));
        }

        return null;
    }

    /**
     * Build review schema.
     *
     * @return \Spatie\SchemaOrg\Review
     */
    protected function buildReview()
    {
        $rating = $this->normalizeValue('rating');

        if ($rating) {
            return SchemaGenerator::review()
                ->author(SchemaGenerator::person())
                ->reviewRating(SchemaGenerator::rating()->ratingValue($rating));
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

    /**
     * Build availability schema.
     *
     * @return string
     */
    protected function buildAvailability()
    {
        switch ($this->normalizeValue('stock')) {
            case 1:
                return ItemAvailability::InStock;
                break;
            case 2:
                return ItemAvailability::OutOfStock;
                break;
            case 3:
                return ItemAvailability::SoldOut;
                break;
            case 4:
                return ItemAvailability::LimitedAvailability;
                break;
            case 5:
                return ItemAvailability::InStoreOnly;
                break;
            case 6:
                return ItemAvailability::OnlineOnly;
                break;
            case 7:
                return ItemAvailability::PreOrder;
                break;
            case 8:
                return ItemAvailability::PreSale;
                break;
            case 9:
                return ItemAvailability::Discontinued;
                break;
        }
    }
}
