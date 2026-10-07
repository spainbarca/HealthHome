<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class TailwindFormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * Constructor for the TailwindFormOptions class.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'inline-flex rounded-md shadow-sm';
        $this->buttonWrapper                = '<div class="grid grid-cols-1 mb-7"></div>';
        $this->checkboxWrapper              = '<div class="grid grid-cols-1 mb-2 whitespace-nowrap"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500;focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';
        $this->elementsErrorClass           = 'is-invalid-input';
        $this->elementsWrapper              = '<div class="grid grid-cols-1 mb-7"></div>';
        $this->formHorizontalClass          = 'form-horizontal';
        $this->formVerticalClass            = '';
        $this->helperWrapper                = '<p class="text-sm text-gray-700"></p>';
        $this->horizontalElementCol         = 'col-span-8 relative';
        $this->horizontalLabelClass         = 'block py-2';
        $this->horizontalLabelCol           = 'col-span-4';
        $this->horizontalLabelWrapper       = true;
        $this->horizontalOffsetCol          = 'col-start-4';
        $this->iconAfterWrapper             = '<div class="icon is-right flex absolute inset-y-0 right-0 items-center pr-3 pointer-events-none;z-10"></div>';
        $this->iconBeforeWrapper            = '<div class="flex absolute icon is-left inset-y-0 left-0 items-center pl-3 pointer-events-none z-10"></div>';
        $this->inlineCheckboxLabelClass     = 'checkbox-inline';
        $this->inlineCheckboxWrapper        = '<span class="flex-initial flex-nowrap items-center mb-4 mr-6"></span>';
        $this->inlineRadioLabelClass        = 'radio-inline inline-block ml-2 text-sm font-medium text-gray-900 dark:text-gray-300';
        $this->inlineRadioWrapper           = '<span class="flex-initial flex-nowrap items-center mb-4 mr-6"></span>';
        $this->openDomReady                 = 'document.addEventListener(\'DOMContentLoaded\', function(event) {';
        $this->radioWrapper                 = '<div class="grid grid-cols-1 mb-2 whitespace-nowrap"></div>';
        $this->requiredMark                 = '<sup class="text-red-400">* </sup>';
        $this->textErrorClass               = 'absolute text-sm text-red-600';
        $this->verticalCheckboxLabelClass   = 'block ml-2';
        $this->verticalLabelClass           = 'block mb-2';
        $this->verticalLabelWrapper         = false;
        $this->verticalRadioLabelClass      = 'block ml-2';
        $this->wrapCheckboxesIntoLabels     = false;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = false;

        if ($layout == 'horizontal') {
            $this->buttonWrapper   = '<div class="grid grid-cols-12 gap-4 mb-7"></div>';
            $this->checkboxWrapper = '<div class="flex items-center my-2 whitespace-nowrap"></div>';
            $this->elementsWrapper = '<div class="grid grid-cols-12 gap-4 mb-7"></div>';
            $this->radioWrapper    = '<div class="flex items-center my-2 whitespace-nowrap"></div>';
        }
    }
}
