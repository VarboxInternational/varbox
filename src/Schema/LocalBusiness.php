<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class LocalBusiness extends Schema
{
    /**
     * Generate the json+ld schema code for type Event.
     *
     * @return string|void
     */
    public function generate()
    {
        $method = $this->schema->fields['type'] ?? null;

        if (!array_key_exists($method, $this->schema->localBusinessSchemaTypes())) {
            return;
        }

        return SchemaGenerator::{$method}()
            ->name($this->normalizeValue('name'))
            ->description($this->normalizeValue('description'))
            ->telephone($this->normalizeValue('telephone'))
            ->image($this->normalizeUploadValue('image'))
            ->url($this->normalizeUrlValue('url'))
            ->priceRange($this->normalizeValue('price_range'))
            ->address($this->buildAddress())
            ->openingHoursSpecification($this->buildOpeningHours())
            ->geo($this->buildGeo())
            ->aggregateRating($this->buildAggregateRating())
            ->toScript();
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
            ->addressCountry($this->normalizeValue('address_country'));
    }

    /**
     * Build opening hours schema.
     *
     * @return array
     */
    protected function buildOpeningHours()
    {
        $openingHours = [];

        $mondayOpens = $this->normalizeValue('monday_opens');
        $mondayCloses = $this->normalizeValue('monday_closes');

        $tuesdayOpens = $this->normalizeValue('tuesday_opens');
        $tuesdayCloses = $this->normalizeValue('tuesday_closes');

        $wednesdayOpens = $this->normalizeValue('wednesday_opens');
        $wednesdayCloses = $this->normalizeValue('wednesday_closes');

        $thursdayOpens = $this->normalizeValue('thursday_opens');
        $thursdayCloses = $this->normalizeValue('thursday_closes');

        $fridayOpens = $this->normalizeValue('friday_opens');
        $fridayCloses = $this->normalizeValue('friday_closes');

        $saturdayOpens = $this->normalizeValue('saturday_opens');
        $saturdayCloses = $this->normalizeValue('saturday_closes');

        $sundayOpens = $this->normalizeValue('sunday_opens');
        $sundayCloses = $this->normalizeValue('sunday_closes');

        if ($mondayOpens || $mondayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Monday')->opens($mondayOpens)->closes($mondayCloses);
        }

        if ($tuesdayOpens || $tuesdayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Tuesday')->opens($tuesdayOpens)->closes($tuesdayCloses);
        }

        if ($wednesdayOpens || $wednesdayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Wednesday')->opens($wednesdayOpens)->closes($wednesdayCloses);
        }

        if ($thursdayOpens || $thursdayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Thursday')->opens($thursdayOpens)->closes($thursdayCloses);
        }

        if ($fridayOpens || $fridayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Friday')->opens($fridayOpens)->closes($fridayCloses);
        }

        if ($saturdayOpens || $saturdayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Saturday')->opens($saturdayOpens)->closes($saturdayCloses);
        }

        if ($sundayOpens || $sundayCloses) {
            $openingHours[] = SchemaGenerator::openingHoursSpecification()
                ->dayOfWeek('Sunday')->opens($sundayOpens)->closes($sundayCloses);
        }

        return $openingHours;
    }

    /**
     * Build geo schema.
     *
     * @return \Spatie\SchemaOrg\GeoCoordinates
     */
    protected function buildGeo()
    {
        $latitude = $this->normalizeValue('latitude');
        $longitude = $this->normalizeValue('longitude');

        if ($latitude && $longitude) {
            return SchemaGenerator::geoCoordinates()
                ->latitude($this->normalizeValue('latitude'))
                ->longitude($this->normalizeValue('longitude'));
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
