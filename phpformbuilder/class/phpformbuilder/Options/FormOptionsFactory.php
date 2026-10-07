<?php

namespace phpformbuilder\Options;

use InvalidArgumentException;

class FormOptionsFactory
{
    /**
     * @param  string $framework bs4 | bs5 | bulma | foundation | material | tailwind | uikit | default
     * @param  string $layout    horizontal | vertical
     * @return \phpformbuilder\Options\FormOptionsInterface
     * @throws \InvalidArgumentException
     */
    public static function create(string $framework, string $layout): \phpformbuilder\Options\FormOptionsInterface
    {
        if ($framework === 'bs4') {
            return new Bootstrap4FormOptions($layout);
        } elseif ($framework === 'bs5') {
            return new Bootstrap5FormOptions($layout);
        } elseif ($framework === 'bulma') {
            return new BulmaFormOptions($layout);
        } elseif ($framework === 'foundation') {
            return new FoundationFormOptions($layout);
        } elseif ($framework === 'material') {
            return new MaterialFormOptions($layout);
        } elseif ($framework === 'tailwind') {
            return new TailwindFormOptions($layout);
        } elseif ($framework === 'uikit') {
            return new UikitFormOptions($layout);
        }

        throw new InvalidArgumentException("Unsupported framework: $framework");
    }
}
