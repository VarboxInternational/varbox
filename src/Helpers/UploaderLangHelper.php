<?php

namespace Varbox\Helpers;

use Illuminate\Support\Str;
use Varbox\Contracts\UploaderLangHelperContract;

class UploaderLangHelper extends UploaderHelper implements UploaderLangHelperContract
{
    /**
     * Set or get the name of an uploader instance.
     *
     * @param string|null $field
     * @return $this|string
     */
    public function field($field = null)
    {
        if ($field === null) {
            return str_replace('[' . app()->getLocale() . ']', '', $this->field);
        }

        if (Str::is('*[*]*', $field)) {
            $this->field = explode('[', $field, 2)[0] . '[' . app()->getLocale() . '][' . explode('[', $field, 2)[1];
        } else {
            $this->field = $field . '[' . app()->getLocale() . ']';
        }

        return $this;
    }

    /**
     * Check if the uploader instance already has a current upload and set it.
     *
     * @return $this
     */
    protected function generateCurrent()
    {
        if (!$this->model->exists) {
            return $this;
        }

        $upload = uploaded($this->model->getTranslation(
            $this->field(), app()->getLocale(), false
        ));

        $this->current = $upload->exists() ? $upload : null;

        return $this;
    }
}
