<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ButtonHelperContract
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @param string|null $class
     * @param array $attributes
     * @return $this
     */
    public function action($text, $url, $icon = null, $class = null, $attributes = []);

    /**
     * @param string $text
     * @param string $url
     * @param string|null $icon
     * @param string|null $class
     * @param string|null $confirm
     * @param array $attributes
     * @return $this
     */
    public function submit($text, $url, $icon = null, $class = null, $confirm = null, $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function addRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function editRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function deleteRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function cancelAction($url, array $attributes = []);

    /**
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function updateAction(array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function continueAction($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function goBack($url, array $attributes = []);

    /**
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function filterRecords(array $attributes = []);

    /**
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function clearFilters(array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function viewRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function downloadFile($url, array $attributes = []);

    /**
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveRecord(array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveElsewhere($url, array $attributes = []);

    /**
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAndStay(array $attributes = []);

    /**
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAndNew(array $attributes = []);

    /**
     * @param string $routeName
     * @param array $routeParameters
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAndContinue($routeName, array $routeParameters = [], array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveAsDraft($url, array $attributes = []);

    /**
     * @param string $url
     * @param string $approvalUrl
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function saveForApproval($url, $approvalUrl, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function publishDraft($url, array $attributes = []);

    /**
     * @param string $url
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function publishLimboDraft($url, Model $model, array $attributes = []);

    /**
     * @param string $url
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function deleteLimboDraft($url, Model $model, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function rollbackRevision($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function duplicateRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function previewRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function restoreRecord($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function deletedRecords($url, array $attributes = []);

    /**
     * @param string $url
     * @param array $attributes
     * @return \Illuminate\View\View
     */
    public function draftedRecords($url, array $attributes = []);
}
