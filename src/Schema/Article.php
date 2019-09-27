<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class Article extends Schema
{
    /**
     * Generate the json+ld schema code for type Article.
     *
     * @return string|void
     */
    public function generate()
    {
        $method = $this->schema->fields['type'] ?? null;

        if (!array_key_exists($method, $this->schema->articleSchemaTypes())) {
            return;
        }

        return SchemaGenerator::{$method}()
            ->headline($this->normalizeValue('headline'))
            ->description($this->normalizeValue('description'))
            ->image($this->normalizeUploadValue('image'))
            ->datePublished($this->normalizeDateValue('date_published'))
            ->dateModified($this->normalizeDateValue('date_modified'))
            ->mainEntityOfPage($this->buildMainEntity())
            ->author($this->buildAuthor())
            ->publisher($this->buildPublisher())
            ->toScript();
    }

    /**
     * Build main entity schema.
     *
     * @return \Spatie\SchemaOrg\WebPage
     */
    protected function buildMainEntity()
    {
        return SchemaGenerator::webPage()
            ->name(config('app.name'));
    }

    /**
     * Build author schema.
     *
     * @return \Spatie\SchemaOrg\Person
     */
    protected function buildAuthor()
    {
        return SchemaGenerator::person()
            ->name($this->normalizeValue('author_name'));
    }

    /**
     * Build publisher schema.
     *
     * @return \Spatie\SchemaOrg\Organization
     */
    protected function buildPublisher()
    {
        return SchemaGenerator::organization()
            ->name(config('app.name'))
            ->logo(SchemaGenerator::imageObject()->url($this->normalizeUploadValue('publisher_logo')));
    }
}
