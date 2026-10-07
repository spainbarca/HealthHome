<?php

declare(strict_types=1);

namespace phpformbuilder\traits;

use phpformbuilder\HtmlFormatter\HtmlFormatter;

trait Utilities
{

    /**
     * shortcut to prepend or append any adon to an input
     * @param string $inputName the name of target input
     * @param string $addonHtml  addon html code
     * @param string $pos        before | after
     * @return self
     */
    public function addAddon(string $inputName, string $addonHtml, string $pos): self
    {
        $hasButton = preg_match('`<button`i', $addonHtml);

        if ($this->framework == 'bs5' || $this->framework == 'bs4' || $this->framework == 'foundation') {
            $this->addInputWrapper('<div class="input-group has-addon-' . $pos . '"></div>', $inputName);
        } elseif ($this->framework == 'bulma') {
            $this->addInputWrapper('<div class="field has-addons"></div>', $inputName);
        } elseif ($this->framework == 'tailwind') {
            $this->addInputWrapper('<div class="flex addon-' . $pos . '"></div>', $inputName);
        } elseif ($this->framework == 'uikit') {
            if (!$hasButton) {
                $this->addInputWrapper('<div class="uk-flex uk-flex-wrap uk-position-relative uk-width-1-1"></div>', $inputName);
            } else {
                $this->addInputWrapper('<div class="uk-flex uk-position-relative"></div>', $inputName);
            }
        }

        if ($this->framework == 'bs4') {
            if ($pos == 'before') {
                $inputGroupAddonClass = 'input-group-prepend';
            } else {
                $inputGroupAddonClass = 'input-group-append';
            }
            $inputGroupAddonClass .= ' phpfb-addon-' . $pos;
            if (!$hasButton) {
                $addonHtml = '<span class="input-group-text">' . $addonHtml . '</span>';
            }
            $this->addHtml('<div class="' . $inputGroupAddonClass . '">' . $addonHtml . '</div>', $inputName, $pos);
        } elseif ($this->framework == 'bs5') {
            if (!$hasButton) {
                $this->addHtml('<div class="input-group-text phpfb-addon-' . $pos . '">' . $addonHtml . '</div>', $inputName, $pos);
            } else {
                $addonHtml = $this->addClass('input-group-btn phpfb-addon-' . $pos, $addonHtml);
                $this->addHtml($addonHtml, $inputName, $pos);
            }
        } elseif ($this->framework == 'bulma') {
            if (!$hasButton) {
                $this->addHtml('<p class="control addon-control phpfb-addon-' . $pos . '"><a class="button is-static">' . $addonHtml . '</a></p>', $inputName, $pos);
            } else {
                $this->addHtml('<p class="control addon-control phpfb-addon-' . $pos . '">' . $addonHtml . '</p>', $inputName, $pos);
            }
        } elseif ($this->framework == 'foundation') {
            if (!$hasButton) {
                $this->addHtml('<span class="input-group-label phpfb-addon-' . $pos . '">' . $addonHtml . '</span>', $inputName, $pos);
            } else {
                $this->addHtml('<div class="input-group-button phpfb-addon-' . $pos . '">' . $addonHtml . '</div>', $inputName, $pos);
            }
        } elseif ($this->framework == 'material') {
            $clazz = 'addon-' . $pos . ' phpfb-addon-' . $pos;
            if (!$hasButton) {
                $this->addHtml('<span class="input-group-text ' . $clazz . '">' . $addonHtml . '</span>', $inputName, $pos);
            } else {
                $addonHtml = $this->addClass($clazz, $addonHtml);
                $this->addHtml($addonHtml, $inputName, $pos);
            }
        } elseif ($this->framework == 'tailwind') {
            $rounded = 'l';
            $border = 'r';
            if ($pos === 'after') {
                $rounded = 'r';
                $border = 'l';
            }
            $clazz = 'phpfb-addon-' . $pos;
            if (!$hasButton) {
                $this->addHtml('<span class="phpfb-addon-' . $pos . ' inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 rounded-' . $rounded . '-md border border-' . $border . '-0 border-gray-300 dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">' . $addonHtml . '</span>', $inputName, $pos);
            } else {
                $addonHtml = $this->addClass($clazz, $addonHtml);
                $this->addHtml($addonHtml, $inputName, $pos);
            }
        } elseif ($this->framework == 'uikit') {
            $clazz = 'uk-text-nowrap addon-' . $pos;
            if ($pos === 'after') {
                $clazz .= ' uk-form-icon-flip';
            }
            $clazz .= ' phpfb-addon-' . $pos;
            if (!$hasButton) {
                $this->addHtml('<span class="uk-form-icon ' . $clazz . '">' . $addonHtml . '</span>', $inputName, $pos);
            } else {
                $addonHtml = $this->addClass($clazz, $addonHtml);
                $this->addHtml($addonHtml, $inputName, $pos);
            }
        } else {
            $addonHtml = $this->addClass('phpfb-addon-' . $pos, $addonHtml);
            $this->addHtml($addonHtml, $inputName, $pos);
        }
        $this->fieldsWithAddons[$inputName] = $pos;

        return $this;
    }

    /**
     * add a HTML heading
     *
     * @param  string $html         the heading content text or HTML
     * @param  string $tagName     (Optional) the heading tag name (h1, h2, ...)
     * @param  string $attr         (Optional) the heading attributes
     * @return void
     */
    public function addHeading(string $html, string $tagName = 'h4', string $attr = ''): void
    {
        $headingAttr = '';
        if (!empty($attr)) {
            $headingAttr = ' ' . $this->getAttributes($attr);
        }
        if (!$this->hasAccordion) {
            $this->html .= '<' . $tagName . $headingAttr . '>' . $html . '</' . $tagName . '>';
        } else {
            $this->html .= '<' . $tagName . $headingAttr . '><span class="js-badger-accordion-header">' . $html . '</span></' . $tagName . '>';
        }
    }

    /**
     * Shortcut to add element helper text
     *
     * @param string $helperText    The helper text or HTML to add.
     * @param string $elementName   the helper text will be inserted just after the element.
     * @return self
     */
    public function addHelper(string $helperText, string $elementName): self
    {
        if (!isset($this->htmlElementContent[$elementName])) {
            $this->htmlElementContent[$elementName] = [
                'before' => [],
                'after' => []
            ];
        }
        // add an id to be refered by the element 'aria-describedby' attribute
        $hsw = preg_replace('`>$`', ' id="' . $elementName . '-helper">', $this->options->getStartWrapper('helper'));
        $this->htmlElementContent[$elementName]['after'][] = $hsw . $helperText . $this->options->getEndWrapper('helper');
        $this->fieldsWithHelpers[] = $elementName;

        return $this;
    }

    /**
     * Adds HTML code at any place of the form
     *
     * @param string $html         The html code to add.
     * @param string $elementName (Optional) If not empty, the html code will be inserted
     *                             just before or after the element.
     * @param string $pos          (Optional) If $elementName is not empty, defines the position
     *                             of the inserted html code.
     *                             Values can be 'before' or 'after'.
     * @return self
     */
    public function addHtml(string $html, string $elementName = '', string $pos = 'after'): self
    {
        if (!empty($elementName)) {
            $this->htmlElementContent[$elementName][$pos][] = $html;
        } else {
            $this->html .= $html;
        }

        return $this;
    }

    /**
     * shortcut to prepend or append icon to an input
     *
     * @param string $inputName the name of target input
     * @param string $iconHtml  icon html code
     * @param string $pos        before | after
     * @return self
     */
    public function addIcon(string $inputName, string $iconHtml, string $pos): self
    {
        $iconHtml = $this->addClass('phpfb-addon-' . $pos, $iconHtml);
        if ($this->framework == 'bs5' || $this->framework == 'bs4' || $this->framework == 'foundation') {
            $this->addInputWrapper('<div class="input-group has-addon-' . $pos . '"></div>', $inputName);
        } elseif ($this->framework == 'material') {
            $class = 'prefix' . ' icon-' . $pos;
            $iconHtml = $this->addClass($class, $iconHtml);
        } elseif ($this->framework == 'uikit') {
            $this->addInputWrapper('<div class="uk-flex uk-flex-wrap uk-position-relative uk-width-1-1"></div>', $inputName);
            if ($pos === 'after') {
                $iconHtml = $this->addClass('uk-form-icon-flip', $iconHtml);
            }
        }
        $startWrapper = $this->options->getStartWrapper('iconBefore');
        $endWrapper = $this->options->getEndWrapper('iconBefore');
        if ($pos === 'after') {
            $startWrapper = $this->options->getStartWrapper('iconAfter');
            $endWrapper = $this->options->getEndWrapper('iconAfter');
        }
        $this->addHtml($startWrapper . $iconHtml . $endWrapper, $inputName, $pos);

        $this->fieldsWithIcons[$inputName] = $pos;

        return $this;
    }

    /**
     * Wraps the element with HTML code.
     *
     * @param string $html         The HTML code to wrap the element with.
     *                             The HTML tag must be opened and closed.
     *                             Example : <div class="my-class"></div>
     * @param string $elementName The form element to wrap.
     * @return self
     */
    public function addInputWrapper(string $html, string $elementName): self
    {
        $this->inputWrapper[$elementName] = $html;

        return $this;
    }

    /**
     * @param bool $center
     * @param bool $stack
     * @return self
     */
    public function centerContent(bool $center = true, bool $stack = false): self
    {
        $this->setOptions(['centerContent' => ['center' => $center, 'stack' => $stack]]);

        return $this;
    }

    /**
     * set html output linebreaks and indent
     * @param  string $html
     * @return string clean html
     */
    public function cleanHtmlOutput(string $html): string
    {
        $cleaningObject = new HtmlFormatter();

        // set linebreaks & indent
        $html = $cleaningObject->indent($html);

        // remove empty lines
        // $html = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $html);

        return $html;
    }

    /**
     * End a HTML column
     * @return self
     */
    public function endCol(): self
    {
        return $this->endDiv();
    }

    /**
     * End a HTML div element
     *
     * @return self
     */
    public function endDiv(): self
    {
        $this->addHtml('</div>');

        return $this;
    }

    /**
     * End a HTML row
     * @return self
     */
    public function endRow(): self
    {
        return $this->endDiv();
    }

    /**
     * Ends a fieldset tag.
     * @return self
     */
    public function endFieldset(): self
    {
        if ($this->hasAccordion) {
            $this->html .= '</div>';
        }
        $this->html .= '</fieldset>';

        return $this;
    }

    /**
     * Allows to group inputs in the same wrapper
     *
     * Arguments can be :
     *     -    a single array with fieldnames to group
     *     OR
     *     -    fieldnames given as strings
     *
     * @param array<string>|string $input1 The name of the first input of the group
     *                             OR
     *                             array including all fieldnames
     *
     * @param string $input2 The name of the second input of the group
     * @param string $input3 [optional] The name of the third input of the group
     * @param string $input4 [optional] The name of the fourth input of the group
     * @param string $input5 [optional] The name of the fifth input of the group
     * @param string $input6 [optional] The name of the sixth input of the group
     * @param string $input7 [optional] The name of the seventh input of the group
     * @param string $input8 [optional] The name of the eighth input of the group
     * @param string $input9 [optional] The name of the ninth input of the group
     * @param string $input10 [optional] The name of the tenth input of the group
     * @param string $input11 [optional] The name of the eleventh input of the group
     * @param string $input12 [optional] The name of the twelfth input of the group
     * @return self
     */
    public function groupElements($input1, string $input2 = '', string $input3 = '', string $input4 = '', string $input5 = '', string $input6 = '', string $input7 = '', string $input8 = '', string $input9 = '', string $input10 = '', string $input11 = '', string $input12 = ''): self
    {
        $group = [];

        if (is_array($input1)) {
            // if array given
            for ($i = 1; $i <= count($input1); $i++) {
                $group['input_' . $i] = $input1[$i - 1];
            }
        } else {
            $args = func_get_args();
            // if strings given
            for ($i = 0; $i < func_num_args(); $i++) {
                $input = $args[$i];
                if (!empty($input)) {
                    $group['input_' . ($i + 1)] = $input;
                }
            }
        }
        $this->inputGrouped[] = $group;

        return $this;
    }

    /**
     * Shortcut for labels & cols options
     *
     * @param int $labelColNumber number of columns for label
     * @param int $fieldColNumber number of columns for fields
     * @param string $breakpoint Bootstrap's breakpoints : xs | sm | md |lg
     * @return self
     */
    public function setCols(int $labelColNumber, int $fieldColNumber, string $breakpoint = 'sm'): self
    {
        $options = [];
        if ($this->framework == 'bs5' || $this->framework == 'bs4' || $this->framework == 'material' && $this->layout == 'horizontal') {
            $labelCol          = '';
            $labelOffsetCol   = '';
            $elementCol        = '';
            if (!empty($fieldColNumber) && $fieldColNumber > 0) {
                $elementCol = 'col-' . $breakpoint . '-' . $fieldColNumber;
            }
            if ($this->framework == 'bs5' || $this->framework == 'bs4') {
                // Bootstrap
                if (!empty($labelColNumber) && $labelColNumber > 0) {
                    $labelCol        = 'col-' . $breakpoint . '-' . $labelColNumber;
                    // $labelOffsetCol = 'offset-' . $breakpoint . '-' . $labelColNumber;
                }

                // if no breakpoint (ie: col-6)
                $labelCol = str_replace('--', '-', $labelCol);
                // $labelOffsetCol = str_replace('--', '-', $labelOffsetCol);
                $elementCol = str_replace('--', '-', $elementCol);

                // if negative, => "col" without number (auto-width)
                if ($labelColNumber < 0) {
                    $labelCol = 'col';
                    if (!empty($breakpoint)) {
                        $labelCol .= '-' . $breakpoint;
                    }
                }
                if ($fieldColNumber < 0) {
                    $elementCol = 'col';
                    if (!empty($breakpoint)) {
                        $elementCol .= '-' . $breakpoint;
                    }
                }

                $marginBottom = ' mb-3';

                $options = [
                    'horizontalLabelCol'   => $labelCol,
                    'horizontalOffsetCol'  => $labelOffsetCol,
                    'horizontalElementCol' => $elementCol . $marginBottom
                ];
            } elseif ($this->framework == 'material') {
                // Material Design

                // translate from bs4 breakpoints to material
                $mdBreakpoints = [
                    'xs' => 's',
                    'sm' => 'm',
                    'md' => 'l',
                    'lg' => 'xl'
                ];
                $breakpoint = $mdBreakpoints[$breakpoint];
                $elementCol = 'col ' . $breakpoint . $fieldColNumber;

                // set full with to the lower breakpoint
                $lowerCol = '';
                if ($breakpoint == 'xl') {
                    $lowerCol = ' l12';
                } elseif ($breakpoint == 'l') {
                    $lowerCol = ' m12';
                } elseif ($breakpoint == 'm') {
                    $lowerCol = ' s12';
                }
                $elementCol .= $lowerCol;
                if (!empty($labelColNumber) && $labelColNumber > 0) {
                    // Normal input with label in front
                    // elementsWrapper with 2 col divs inside for label & field

                    $labelCol = 'col ' . $breakpoint . $labelColNumber . $lowerCol;
                    $labelOffsetCol = 'offset-' . $breakpoint . $labelColNumber;
                    $options = [
                        'horizontalLabelCol'   => $labelCol,
                        'horizontalOffsetCol'  => $labelOffsetCol,
                        'horizontalElementCol' => 'input-field ' . $elementCol
                    ];
                } else {
                    // Material input-field with label inside
                    // elementsWrapper with row + col class, label & field directly inside
                    $options = [
                        'horizontalLabelCol'   => '',
                        'horizontalOffsetCol'  => '',
                        'horizontalElementCol' => 'input-field ' . $elementCol
                    ];
                }
            }
        } elseif ($this->framework == 'bulma' && $this->layout == 'horizontal') {
            $labelCol         = 'column';
            $labelOffsetCol  = '';
            $elementCol       = 'column';
            $bulmaBreakpoints = [
                'xs' => ' is-mobile',
                'sm' => ' is-tablet',
                'md' => ' is-desktop',
                'lg' => ' is-widescreen'
            ];
            $breakpoint = $bulmaBreakpoints[$breakpoint];
            if (!empty($labelColNumber) && $labelColNumber > 0) {
                $labelCol .= ' is-' . $labelColNumber . $breakpoint;
                $labelOffsetCol = 'is-offset-' . $labelColNumber;
            }
            if (!empty($fieldColNumber) && $fieldColNumber > 0) {
                $elementCol .= ' is-' . $fieldColNumber . $breakpoint;
            }
            $options = [
                'horizontalLabelCol'   => $labelCol,
                'horizontalOffsetCol'  => $labelOffsetCol,
                'horizontalElementCol' => $elementCol
            ];
        } elseif ($this->framework == 'foundation') {
            $labelCol   = '';
            $labelOffsetCol  = '';
            $elementCol = '';
            $foundationBreakpoints = [
                'xs' => 'small',
                'sm' => 'medium',
                'md' => 'medium',
                'lg' => 'large'
            ];
            $breakpoint = $foundationBreakpoints[$breakpoint];
            if ($this->layout == 'horizontal') {
                if (!empty($labelColNumber) && $labelColNumber > 0) {
                    if ($breakpoint !== 'small') {
                        $labelCol = 'small-12 ';
                    }
                    $labelCol        .= $breakpoint . '-' . $labelColNumber . ' cell';
                    $labelOffsetCol = $breakpoint . '-offset-' . $labelColNumber;
                }
                if (!empty($fieldColNumber) && $fieldColNumber > 0) {
                    if ($breakpoint !== 'small') {
                        $elementCol = 'small-12 ';
                    }
                    $elementCol .= $breakpoint . '-' . $fieldColNumber . ' cell';
                }
                $options = [
                    'horizontalLabelCol'   => $labelCol,
                    'horizontalOffsetCol'  => $labelOffsetCol,
                    'horizontalElementCol' => $elementCol
                ];
            } elseif ($this->layout == 'vertical') {
                if (!empty($labelColNumber) && $labelColNumber > 0) {
                    $labelCol        = $breakpoint . '-' . $labelColNumber . ' cell';
                }
                $options = ['verticalLabelClass' => $labelCol];
            }
        } elseif ($this->framework == 'tailwind' && $this->layout == 'horizontal') {
            $labelCol          = '';
            $labelOffsetCol  = '';
            $elementCol        = '';
            if (!empty($labelColNumber) && $labelColNumber > 0) {
                $labelCol        = 'col-span-' . $labelColNumber;
                $labelOffsetCol = 'col-start-' . ($labelColNumber + 1);
            }
            if (!empty($fieldColNumber) && $fieldColNumber > 0) {
                $elementCol = 'col-span-' . $fieldColNumber;
            }

            if (!empty($breakpoint)) {
                $tailwindBreakpoints = [
                    'xs' => '',
                    'sm' => 'sm',
                    'md' => 'md',
                    'lg' => 'lg'
                ];
                $breakpoint = $tailwindBreakpoints[$breakpoint];
                $labelCol = 'col-span-12 ' . $breakpoint . ':' . $labelCol;
                if (!empty($labelColNumber) && $labelColNumber > 0) {
                    $labelOffsetCol = $breakpoint . ':' . $labelOffsetCol;
                }
                $elementCol = 'col-span-12 ' . $breakpoint . ':' . $elementCol;
            }
            $options = [
                'horizontalLabelCol'   => $labelCol,
                'horizontalOffsetCol'  => $labelOffsetCol,
                'horizontalElementCol' => $elementCol . ' relative'
            ];
        } elseif ($this->framework == 'uikit' && $this->layout == 'horizontal') {
            $labelCol          = '';
            $elementCol        = '';
            $uikitCols = [
                1 => 'n/a',
                2 => '1-6',
                3 => '1-4',
                4 => '1-3',
                5 => 'n/a',
                6 => '1-2',
                7 => 'n/a',
                8 => '2-3',
                9 => '3-4',
                10 => '5-6',
                11 => 'n/a',
                12 => '1-1'
            ];
            if (!empty($labelColNumber) && $labelColNumber > 0) {
                if ($uikitCols[$labelColNumber] === 'n/a') {
                    $this->buildErrorMsg('<code>$form->setCols(' . $labelColNumber . ', ' . $fieldColNumber . ');</code><br>The UIkit grid is designed from simple fractions and cannot create columns of ' . $labelColNumber . '/12.<br>');
                }
                $labelCol        = 'uk-width-' . $uikitCols[$labelColNumber];
            }
            if (!empty($fieldColNumber) && $fieldColNumber > 0) {
                if ($uikitCols[$fieldColNumber] === 'n/a') {
                    $this->buildErrorMsg('<code>$form->setCols(' . $labelColNumber . ', ' . $fieldColNumber . ');</code><br>The UIkit grid is designed from simple fractions and cannot create columns of ' . $fieldColNumber . '/12.<br>');
                }
                $elementCol = 'uk-width-' . $uikitCols[$fieldColNumber];
            }

            if (!empty($breakpoint)) {
                $uikitBreakpoints = [
                    'xs' => '',
                    'sm' => 's',
                    'md' => 'm',
                    'lg' => 'l'
                ];
                $breakpoint = $uikitBreakpoints[$breakpoint];
                $labelCol = 'uk-width-1-1 ' . $labelCol . '@' . $breakpoint;
                $elementCol = 'uk-width-1-1 ' . $elementCol . '@' . $breakpoint;
            }
            $options = [
                'horizontalLabelCol'   => $labelCol,
                'horizontalOffsetCol'  => '',
                'horizontalElementCol' => $elementCol . ' relative'
            ];
        }
        $this->setOptions($options);
        $this->options->setWrapperStartEndHtml('elements');

        return $this;
    }

    /**
     * Start a column HTML div
     *
     * @param int $colNumber - the number of columns between 1 and 12
     * @param string $breakpoint - xs, sm, md or lg
     * @param string $additionalClass
     * @param string $id
     * @return self
     */
    public function startCol(int $colNumber, string $breakpoint = 'sm', string $additionalClass = '', string $id = ''): self
    {
        $colClazz = '';
        if ($this->framework === 'bs4' || $this->framework === 'bs5') {
            $marginBottom = ' mb-3';
            $colClazz = str_replace('--', '-', 'col-' . $breakpoint . '-' . $colNumber) . $marginBottom;
        } elseif ($this->framework === 'bulma') {
            $colClazz = 'column';
            $bulmaBreakpoints = [
                'xs' => ' is-mobile',
                'sm' => ' is-tablet',
                'md' => ' is-desktop',
                'lg' => ' is-widescreen'
            ];
            $breakpoint = $bulmaBreakpoints[$breakpoint];
            $colClazz .= ' is-' . $colNumber . $breakpoint;
        } elseif ($this->framework === 'foundation') {
            $foundationBreakpoints = [
                'xs' => 'small',
                'sm' => 'medium',
                'md' => 'medium',
                'lg' => 'large'
            ];
            $breakpoint = $foundationBreakpoints[$breakpoint];
            if ($breakpoint !== 'small') {
                $colClazz = 'small-12 ';
            }
            $colClazz        .= $breakpoint . '-' . $colNumber . ' cell';
        } elseif ($this->framework === 'material') {
            $mdBreakpoints = [
                'xs' => 's',
                'sm' => 'm',
                'md' => 'l',
                'lg' => 'xl'
            ];
            $breakpoint = $mdBreakpoints[$breakpoint];
            $colClazz = 'col ' . $breakpoint . $colNumber;

            // set full with to the lower breakpoint
            $lowerCol = '';
            if ($breakpoint == 'xl') {
                $lowerCol = ' l12';
            } elseif ($breakpoint == 'l') {
                $lowerCol = ' m12';
            } elseif ($breakpoint == 'm') {
                $lowerCol = ' s12';
            }
            $colClazz .= $lowerCol;
        } elseif ($this->framework === 'tailwind') {
            $colClazz = 'col-span-' . $colNumber;
            $tailwindBreakpoints = [
                'xs' => '',
                'sm' => 'sm',
                'md' => 'md',
                'lg' => 'lg'
            ];
            $breakpoint = $tailwindBreakpoints[$breakpoint];
            $colClazz = 'col-span-12 ' . $breakpoint . ':' . $colClazz;
        } elseif ($this->framework === 'uikit') {
            $uikitCols = [
                1 => 'n/a',
                2 => '1-6',
                3 => '1-4',
                4 => '1-3',
                5 => 'n/a',
                6 => '1-2',
                7 => 'n/a',
                8 => '2-3',
                9 => '3-4',
                10 => '5-6',
                11 => 'n/a',
                12 => '1-1'
            ];
            if ($uikitCols[$colNumber] === 'n/a') {
                $this->buildErrorMsg('<code>$form->startCol(' . $colNumber . ');</code><br>The UIkit grid is designed from simple fractions and cannot create columns of ' . $colNumber . '/12.<br>');
            }
            $colClazz = 'uk-width-' . $uikitCols[$colNumber];
            if (!empty($breakpoint)) {
                $uikitBreakpoints = [
                    'xs' => '',
                    'sm' => 's',
                    'md' => 'm',
                    'lg' => 'l'
                ];
                $breakpoint = $uikitBreakpoints[$breakpoint];
                $colClazz = 'uk-width-1-1 ' . $colClazz . '@' . $breakpoint;
            }
        }

        if (!empty($additionalClass)) {
            $colClazz .= ' ' . $additionalClass;
        }
        $idHtml = '';
        if (!empty($id)) {
            $idHtml = ' id="' . $id . '"';
        }
        $this->addHtml('<div class="' . \trim($colClazz) . '"' . $idHtml . '>');

        return $this;
    }

    /**
     *
     * Start a HTML div element
     * @param string $class
     * @param string $id
     * @return self
     */
    public function startDiv(string $class = '', string $id = ''): self
    {
        if (!empty($class)) {
            $class = ' class="' . $class . '"';
        }
        if (!empty($id)) {
            $id = ' id="' . $id . '"';
        }
        $this->addHtml('<div' . $id . $class . '>');

        return $this;
    }

    /**
     * Starts a fieldset tag.
     *
     * @param string $legend (Optional) Legend of the fieldset.
     * @param string $fieldsetAttr (Optional) Fieldset attributes.
     * @param string $legendAttr (Optional) Legend attributes.
     * @return self
     */
    public function startFieldset(string $legend = '', string $fieldsetAttr = '', string $legendAttr = ''): self
    {
        $fieldsetAttr = $this->getAttributes($fieldsetAttr);
        if ($this->hasAccordion) {
            $fieldsetAttr = $this->addClass('badger-accordion__panel js-badger-accordion-panel -ba-is-hidden', $fieldsetAttr);
        }
        if (!empty($fieldsetAttr)) {
            $fieldsetAttr = ' ' . $fieldsetAttr;
        }
        if (!empty($legendAttr)) {
            $legendAttr = ' ' . $this->getAttributes($legendAttr);
        }
        $this->html .= '<fieldset' . $fieldsetAttr . '>';
        if (!empty($legend)) {
            $this->html .= '<legend' . $legendAttr . '>' . $legend . '</legend>';
        }
        if ($this->hasAccordion) {
            $this->html .= '<div class="js-badger-accordion-panel-inner">';
        }

        return $this;
    }

    /**
     * Start a HTML row
     *
     * @param string $additionalClass
     * @param string $id
     * @return self
     */
    public function startRow(string $additionalClass = '', string $id = ''): self
    {
        $clazz = '';
        $fwClazz = [
            'bs4'         => 'row',
            'bs5'         => 'row',
            'bulma'       => 'columns',
            'foundation'  => 'grid-x grid-margin-x',
            'material'    => 'row',
            'tailwind'    => 'grid grid-cols-12 gap-4',
            'uikit'       => 'uk-grid'
        ];
        if (\array_key_exists($this->framework, $fwClazz)) {
            $clazz = $fwClazz[$this->framework];
        }
        if (!empty($additionalClass)) {
            $clazz .= ' ' . $additionalClass;
        }
        $idHtml = '';
        if (!empty($id)) {
            $idHtml = ' id="' . $id . '"';
        }
        $this->addHtml('<div class="' . \trim($clazz) . '"' . $idHtml . '>');

        return $this;
    }
}
