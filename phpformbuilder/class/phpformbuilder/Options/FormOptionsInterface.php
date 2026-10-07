<?php

namespace phpformbuilder\Options;

interface FormOptionsInterface
{
    /**
     * Retrieves the value of a specific option.
     *
     * @param string $option The name of the option to retrieve.
     * @return mixed The value of the specified option.
     */
    public function get(string $option);

    /**
     * Retrieves the properties of the form options.
     *
     * @return mixed The properties of the form options.
     */
    public function getProperties();

    public function getString(string $option): string;
    public function getBool(string $option): bool;

    /**
     * Retrieves an array for a given option name.
     *
     * @param string $option The name of the option.
     * @return array<string, bool|string> The value of the option.
     */
    public function getArray(string $option): array;


    /**
     * Sets the value of a specific option.
     *
     * @param string $option The name of the option.
     * @param mixed $value The value to set for the option.
     * @return void
     */
    public function set(string $option, $value): void;
    public function getStartWrapper(string $key): string;
    public function getEndWrapper(string $key): string;
    public function setWrapperStartEndHtml(string $key): void;
}
