<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Person extends Schema
{
    /**
     * Generate the json+ld schema code for type Person.
     *
     * @return string|void
     */
    public function generate()
    {
        return SchemaGenerator::person()
            ->name($this->normalizeValue('name'))
            ->url($this->normalizeUrlValue('url'))
            ->email($this->normalizeValue('email'))
            ->telephone($this->normalizeValue('phone'))
            ->description($this->normalizeValue('description'))
            ->image($this->normalizeUrlValue('image'))
            ->gender($this->normalizeValue('gender'))
            ->height($this->buildHeight())
            ->weight($this->buildWeight())
            ->jobTitle($this->normalizeValue('job_title'))
            ->worksFor($this->buildWorkPlace())
            ->alumniOf($this->buildCollegeUniversity())
            ->address($this->buildAddress())
            ->birthPlace($this->buildBirthPlace())
            ->sameAs([
                $this->normalizeValue('personal_site'),
                $this->normalizeValue('wikipedia_page'),
                $this->normalizeValue('facebook_profile'),
                $this->normalizeValue('twitter_profile'),
                $this->normalizeValue('linkedin_profile'),
                $this->normalizeValue('google_profile'),
                $this->normalizeValue('youtube_profile'),
                $this->normalizeValue('instagram_profile')
            ])
            ->toScript();
    }

    /**
     * Build height schema.
     *
     * @return \Spatie\SchemaOrg\QuantitativeValue
     */
    protected function buildHeight()
    {
        $height = $this->normalizeValue('height');

        if ($height) {
            return SchemaGenerator::quantitativeValue()
                ->value($height);
        }

        return null;
    }

    /**
     * Build weight schema.
     *
     * @return \Spatie\SchemaOrg\QuantitativeValue
     */
    protected function buildWeight()
    {
        $weight = $this->normalizeValue('weight');

        if ($weight) {
            return SchemaGenerator::quantitativeValue()
                ->value($this->normalizeValue('weight'));
        }

        return null;
    }

    /**
     * Build organization schema.
     *
     * @return \Spatie\SchemaOrg\Organization
     */
    protected function buildWorkPlace()
    {
        $name = $this->normalizeValue('work_place');

        if ($name) {
            return SchemaGenerator::organization()
                ->name($name);
        }

        return null;
    }

    /**
     * Build college/university schema.
     *
     * @return \Spatie\SchemaOrg\CollegeOrUniversity
     */
    protected function buildCollegeUniversity()
    {
        $name = $this->normalizeValue('college_graduated');

        if ($name) {
            return SchemaGenerator::collegeOrUniversity()
                ->name($name);
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
        $locality = $this->normalizeValue('address_locality');
        $region = $this->normalizeValue('address_region');
        $country = $this->normalizeValue('address_country');

        if ($locality || $region || $country) {
            return SchemaGenerator::postalAddress()
                ->addressLocality($locality)
                ->addressRegion($region)
                ->addressCountry($country);
        }

        return null;
    }

    /**
     * Build birthplace schema.
     *
     * @return \Spatie\SchemaOrg\Place
     */
    protected function buildBirthPlace()
    {
        $locality = $this->normalizeValue('birth_locality');
        $region = $this->normalizeValue('birth_region');
        $country = $this->normalizeValue('birth_country');

        if ($locality || $region || $country) {
            return SchemaGenerator::place()->address(
                SchemaGenerator::postalAddress()
                    ->addressLocality($locality)
                    ->addressRegion($region)
                    ->addressCountry($country)
            );
        }

        return null;
    }
}
