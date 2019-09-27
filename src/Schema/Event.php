<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\ItemAvailability;
use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Event extends Schema
{
    /**
     * Generate the json+ld schema code for type Event.
     *
     * @return string|void
     */
    public function generate()
    {
        $method = $this->schema->fields['type'] ?? null;

        if (!array_key_exists($method, $this->schema->eventSchemaTypes())) {
            return;
        }

        return SchemaGenerator::{$method}()
            ->name($this->normalizeValue('name'))
            ->description($this->normalizeValue('description'))
            ->image($this->normalizeUploadValue('image'))
            ->startDate($this->normalizeDateValue('start_date'))
            ->endDate($this->normalizeDateValue('end_date'))
            ->location($this->buildLocation())
            ->offers($this->buildOffers())
            ->performer($this->buildPerformer())
            ->toScript();
    }

    /**
     * Build location schema.
     *
     * @return \Spatie\SchemaOrg\Place
     */
    protected function buildLocation()
    {
        return SchemaGenerator::place()
            ->name($this->normalizeValue('location_name'))
            ->address($this->buildAddress());
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
     * @return \Spatie\SchemaOrg\Person
     */
    protected function buildPerformer()
    {
        $name = $this->normalizeValue('performer_name');

        if ($name) {
            return SchemaGenerator::person()
                ->name($this->normalizeValue('performer_name'));
        }

        return null;
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
