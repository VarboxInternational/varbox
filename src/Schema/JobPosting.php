<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class JobPosting extends Schema
{
    /**
     * Generate the json+ld schema code for type Event.
     *
     * @return string|void
     */
    public function generate()
    {
        return SchemaGenerator::jobPosting()
            ->title($this->normalizeValue('title'))
            ->description($this->normalizeValue('description'))
            ->employmentType($this->normalizeValue('employment_type'))
            ->datePosted($this->normalizeDateValue('date_posted'))
            ->validThrough($this->normalizeDateValue('valid_through'))
            ->hiringOrganization($this->buildHiringOrganization())
            ->baseSalary($this->buildBaseSalary())
            ->jobLocation($this->buildJobLocation())
            ->toScript();
    }

    /**
     * Build hiring organization schema.
     *
     * @return \Spatie\SchemaOrg\Organization
     */
    protected function buildHiringOrganization()
    {
        return SchemaGenerator::organization()
            ->name($this->normalizeValue('organization_name'))
            ->logo($this->normalizeUrlValue('organization_logo'));
    }

    /**
     * Build base salary schema.
     *
     * @return \Spatie\SchemaOrg\MonetaryAmount
     */
    protected function buildBaseSalary()
    {
        $salary = $this->normalizeUrlValue('base_salary');
        $currency = $this->normalizeUrlValue('salary_currency');
        $unit = $this->normalizeUrlValue('salary_unit');

        if ($salary || $currency || $unit) {
            return SchemaGenerator::monetaryAmount()
                ->value(SchemaGenerator::quantitativeValue()->value($salary)->unitText($unit))
                ->currency($currency);
        }

        return null;
    }

    /**
     * Build job location schema.
     *
     * @return \Spatie\SchemaOrg\Place
     */
    protected function buildJobLocation()
    {
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
}
