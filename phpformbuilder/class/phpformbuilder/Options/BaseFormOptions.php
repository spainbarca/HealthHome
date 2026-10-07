<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\Wrappers\FormOptionsWrappers;

class BaseFormOptions implements FormOptionsInterface
{
    protected bool $ajax = false;

    /**
    * @var array<string, bool> $centerContent
    */
    protected array $centerContent = [
        'center' => false,
        'stack'  => false
    ];

    protected bool $deferScripts    = true;
    protected string $loadJsBundle  = '';
    protected bool $useLoadJs       = false;
    protected FormOptionsWrappers $wrappers;

    public function __construct()
    {
        $this->wrappers = new FormOptionsWrappers();
    }

    /**
    * @param string $option
    * @return array<string, bool|string>|bool|string
    * @throws \InvalidArgumentException
    */
    public function get(string $option)
    {
        if (property_exists($this, $option)) {
            return $this->$option;
        }

        throw new \InvalidArgumentException("Option $option does not exist");
    }

    /**
     * Retrieves the properties of the BaseFormOptions class.
     *
     * @return array<string, bool|string> An array containing the properties of the BaseFormOptions class.
     */
    public function getProperties()
    {
        return get_object_vars($this);
    }

    /**
     * Returns a string value.
     *
     * @param string $option The option to be returned.
     * @return string The returned string value.
     * @throws \InvalidArgumentException
     */
    public function getString(string $option): string
    {
        if (property_exists($this, $option)) {
            return $this->$option;
        }

        throw new \InvalidArgumentException("Option $option does not exist");
    }

    /**
     * Returns a boolean value.
     *
     * @param string $option The option to be returned.
     * @return bool The boolean value.
     * @throws \InvalidArgumentException
     */
    public function getBool(string $option): bool
    {
        if (property_exists($this, $option)) {
            return $this->$option;
        }

        throw new \InvalidArgumentException("Option $option does not exist");
    }

    /**
     * Returns an array.
     *
     * @param string $option The option to be returned.
     * @return array<string, bool|string> The output array.
     * @throws \InvalidArgumentException
     */
    public function getArray(string $option): array
    {
        if (property_exists($this, $option)) {
            return $this->$option;
        }

        throw new \InvalidArgumentException("Option $option does not exist");
    }

    /**
    * @param string $option
    * @param array<string, bool>|bool|string $value
    * @throws \InvalidArgumentException
    * @return void
    */
    public function set(string $option, $value): void
    {
        if (property_exists($this, $option)) {
            $this->$option = $value;
        } else {
            throw new \InvalidArgumentException("Option $option does not exist");
        }

    }

    /**
    * Returns the start wrapper HTML code for a form element.
    *
    * @param string $key The key of the form element.
    * @return string The start wrapper HTML code.
    */
    public function getStartWrapper(string $key): string
    {
        return $this->wrappers->getStartWrapper($key);
    }

    /**
    * Returns the end wrapper HTML code for a form element.
    *
    * @param string $key The key of the form element.
    * @return string The end wrapper HTML code.
    */
    public function getEndWrapper(string $key): string
    {
        return $this->wrappers->getEndWrapper($key);
    }

    /**
    * Sets the the wrapper's starting / ending HTML for a specific key.
    *
    * @param string $key The key of the wrapper.
    * @return void
    */
    public function setWrapperStartEndHtml(string $key): void
    {
        if (property_exists($this, $key . 'Wrapper')) {
            $html = $this->getString($key . 'Wrapper');
            $this->wrappers->setStartEndWrappers($key, $html);
        } else {
            throw new \InvalidArgumentException('Option ' . $key . 'Wrapper does not exist');
        }
    }
}
