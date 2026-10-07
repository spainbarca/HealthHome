<?php

namespace phpformbuilder\Options\Wrappers;

use phpformbuilder\traits\StaticFunctions;

class FormOptionsWrappers
{
    use StaticFunctions;

    protected string $checkboxStart;
    protected string $checkboxEnd;
    protected string $elementsStart;
    protected string $elementsEnd;
    protected string $helperStart;
    protected string $helperEnd;
    protected string $iconAfterStart;
    protected string $iconAfterEnd;
    protected string $iconBeforeStart;
    protected string $iconBeforeEnd;
    protected string $inlineCheckboxStart;
    protected string $inlineCheckboxEnd;
    protected string $inlineRadioStart;
    protected string $inlineRadioEnd;
    protected string $radioStart;
    protected string $radioEnd;
    protected string $buttonStart;
    protected string $buttonEnd;

    /**
     * Get the start wrapper HTML code for a form element.
     *
     * @param string $key The key of the form element.
     * @return string The start wrapper HTML code.
     */
    public function getStartWrapper(string $key): string
    {
        return $this->{$key . 'Start'};
    }

    /**
     * Get the end wrapper HTML code for a form element.
     *
     * @param string $key The key of the form element.
     * @return string The end wrapper HTML code.
     */
    public function getEndWrapper(string $key): string
    {
        return $this->{$key . 'End'};
    }

    /**
     * Set a key-value pair in the form options HTML wrappers.
     *
     * @param string $key The key to set. E.g.: 'checkbox' for checkboxStart and checkboxEnd.
     * @param string $html The HTML value to extract the starting / ending wrapper.
     * @throws \InvalidArgumentException
     * @return void
     */
    public function setStartEndWrappers(string $key, string $html): void
    {
        if (!\property_exists($this, $key . 'Start') || !\property_exists($this, $key . 'End')) {
            throw new \InvalidArgumentException("Option $key does not exist");
        }
        $this->{$key . 'Start'} = static::buildElementWrapper($html, 'start');
        $this->{$key . 'End'}   = static::buildElementWrapper($html, 'end');
    }
}
