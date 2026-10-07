<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class FoundationFormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * Constructor for the FoundationFormOptions class.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'button-group';
        $this->buttonWrapper                = '<div class="grid-x grid-padding-x"></div>';
        $this->checkboxWrapper              = '<div class="foundation-checkbox"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = '';
        $this->elementsErrorClass           = 'is-invalid-input';
        $this->elementsWrapper              = '<div class="grid-x grid-padding-x"></div>';
        $this->formHorizontalClass          = 'form-horizontal';
        $this->formVerticalClass            = '';
        $this->helperWrapper                = '<p class="help-text"></p>';
        $this->horizontalElementCol         = 'small-12 medium-8 cell';
        $this->horizontalLabelClass         = '';
        $this->horizontalLabelCol           = 'small-12 medium-4 cell';
        $this->horizontalLabelWrapper       = true;
        $this->horizontalOffsetCol          = 'medium-offset-4';
        $this->iconAfterWrapper             = '';
        $this->iconBeforeWrapper            = '';
        $this->inlineCheckboxLabelClass     = 'checkbox-inline';
        $this->inlineCheckboxWrapper        = '<div class="checkbox-inline"></div>';
        $this->inlineRadioLabelClass        = 'radio-inline';
        $this->inlineRadioWrapper           = '<div class="radio-inline"></div>';
        $this->openDomReady                 = 'jQuery(document).ready(function($) {';
        $this->radioWrapper                 = '<div class="foundation-radio"></div>';
        $this->requiredMark                 = '<sup style="color:red">* </sup>';
        $this->textErrorClass               = 'form-error is-visible';
        $this->verticalCheckboxLabelClass   = '';
        $this->verticalLabelClass           = 'small-12 cell';
        $this->verticalLabelWrapper         = true;
        $this->verticalRadioLabelClass      = '';
        $this->wrapCheckboxesIntoLabels     = false;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = false;

        if ($layout !== 'horizontal') {
            $this->horizontalElementCol   = 'small-12 cell';
            $this->wrapElementsIntoLabels = true;
        }
    }
}
