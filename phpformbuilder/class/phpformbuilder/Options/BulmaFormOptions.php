<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class BulmaFormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * Constructor for the BulmaFormOptions class.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'field btn-group has-addons';
        $this->buttonWrapper                = '<div class="field"></div>';
        $this->checkboxWrapper              = '<div class="control"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = '';
        $this->elementsErrorClass           = 'is-danger';
        $this->elementsWrapper              = '<div class="field"></div>';
        $this->formHorizontalClass          = '';
        $this->formVerticalClass            = '';
        $this->helperWrapper                = '<span class="help"></span>';
        $this->horizontalElementCol         = 'column';
        $this->horizontalLabelClass         = 'label';
        $this->horizontalLabelCol           = 'column is-4';
        $this->horizontalLabelWrapper       = true;
        $this->horizontalOffsetCol          = 'is-offset-4';
        $this->iconAfterWrapper             = '<span class="icon is-small is-right"></span>';
        $this->iconBeforeWrapper            = '<span class="icon is-small is-left"></span>';
        $this->inlineCheckboxLabelClass     = 'checkbox';
        $this->inlineCheckboxWrapper        = '<div class="control"></div>';
        $this->inlineRadioLabelClass        = 'radio';
        $this->inlineRadioWrapper           = '<div class="control"></div>';
        $this->openDomReady                 = 'document.addEventListener(\'DOMContentLoaded\', function(event) {';
        $this->radioWrapper                 = '<div class="control"></div>';
        $this->requiredMark                 = '<sup class="has-text-danger">* </sup>';
        $this->textErrorClass               = 'help is-danger';
        $this->verticalCheckboxLabelClass   = 'checkbox';
        $this->verticalLabelClass           = 'label';
        $this->verticalLabelWrapper         = false;
        $this->verticalRadioLabelClass      = 'radio';
        $this->wrapCheckboxesIntoLabels     = true;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = true;

        if ($layout == 'horizontal') {
            $this->buttonWrapper   = '<div class="field is-horizontal"></div>';
            $this->elementsWrapper = '<div class="field is-horizontal"></div>';
        }
    }
}
