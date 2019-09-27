<?php

namespace Varbox\Schema;

use Spatie\SchemaOrg\Schema as SchemaGenerator;

class VideoObject extends Schema
{
    /**
     * Generate the json+ld schema code for type Event.
     *
     * @return string|void
     */
    public function generate()
    {
        return SchemaGenerator::videoObject()
            ->name($this->normalizeValue('name'))
            ->description($this->normalizeValue('description'))
            ->thumbnailUrl($this->normalizeUploadValue('thumbnail_url'))
            ->contentUrl($this->normalizeUploadValue('content_url'))
            ->embedUrl($this->normalizeUrlValue('embed_url'))
            ->uploadDate($this->normalizeDateValue('upload_date'))
            ->toScript();
    }
}
