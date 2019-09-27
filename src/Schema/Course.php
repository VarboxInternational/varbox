<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\ItemAvailability;
use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Course extends Schema
{
    /**
     * Generate the json+ld schema code for type Event.
     *
     * @return string|void
     */
    public function generate()
    {
        return SchemaGenerator::course()
            ->name($this->normalizeValue('name'))
            ->description($this->normalizeValue('description'))
            ->provider($this->buildProvider())
            ->hasCourseInstance($this->buildCourseInstance())
            ->aggregateRating($this->buildAggregateRating());
    }

    /**
     * Build provider schema.
     *
     * @return \Spatie\SchemaOrg\Organization|null
     */
    protected function buildProvider()
    {
        $name = $this->normalizeValue('provider_name');

        if ($name) {
            return SchemaGenerator::organization()
                ->name($name);
        }

        return null;
    }

    /**
     * Build course instance schema.
     *
     * @return \Spatie\SchemaOrg\CourseInstance
     */
    protected function buildCourseInstance()
    {
        $name = $this->normalizeValue('name');
        $startDate = $this->normalizeDateValue('start_date');

        if ($name && $startDate) {
            return SchemaGenerator::courseInstance()
                ->name($name)
                ->startDate($startDate)
                ->endDate($this->normalizeDateValue('end_date'))
                ->description($this->normalizeValue('description'))
                ->image($this->normalizeUrlValue('image'))
                ->location($this->buildLocation())
                ->offers($this->buildOffers())
                ->performer($this->buildPerformer());
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
     * Build location schema.
     *
     * @return \Spatie\SchemaOrg\Place
     */
    protected function buildLocation()
    {
        $location = $this->normalizeValue('location_name');

        if ($location) {
            return SchemaGenerator::place()
                ->name($location)
                ->address($this->buildAddress());
        }

        return SchemaGenerator::place()
            ->address($this->buildAddress());
    }

    /**
     * Build address schema.
     *
     * @return \Spatie\SchemaOrg\PostalAddress
     */
    protected function buildAddress()
    {
        return SchemaGenerator::postalAddress()
            ->streetAddress($this->normalizeValue('street_address'))
            ->postalCode($this->normalizeValue('postal_code'))
            ->addressLocality($this->normalizeValue('address_locality'))
            ->addressRegion($this->normalizeValue('address_region'))
            ->addressCountry($this->normalizeValue('address_country'));
    }

    /**
     * Build offer schema.
     *
     * @return \Spatie\SchemaOrg\Offer
     */
    protected function buildOffers()
    {
        $url = $this->normalizeUrlValue('url');
        $price = $this->normalizeValue('price');
        $currency = $this->normalizeValue('currency');
        $availability = $this->buildAvailability();
        $valid = $this->normalizeDateValue($this->model->getCreatedAtColumn());

        if ($url || $price || $currency || $availability) {
            return SchemaGenerator::offer()
                ->url($url)
                ->price($price)
                ->availability($availability)
                ->priceCurrency($currency)
                ->validFrom($valid);
        }

        return null;
    }

    /**
     * Build performer schema.
     *
     * @return \Spatie\SchemaOrg\Organization
     */
    protected function buildPerformer()
    {
        $name = $this->normalizeValue('performer_name');

        if ($name) {
            return SchemaGenerator::organization()
                ->name($name);
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
