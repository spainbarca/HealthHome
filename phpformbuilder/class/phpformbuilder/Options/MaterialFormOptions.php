<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class MaterialFormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * Constructor for the MaterialFormOptions class.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'btn-group';
        $this->buttonWrapper                = '<div class="form-group"></div>';
        $this->checkboxWrapper              = '<div class="checkbox"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = 'form-control';
        $this->elementsErrorClass           = 'is-invalid';
        $this->elementsWrapper              = '<div class="row form-group"></div>';
        $this->formHorizontalClass          = 'form-horizontal';
        $this->formVerticalClass            = '';
        $this->helperWrapper                = '<small class="form-text text-muted"></small>';
        $this->horizontalElementCol         = 'input-field col s8';
        $this->horizontalLabelClass         = '';
        $this->horizontalLabelCol           = 'col s4';
        $this->horizontalLabelWrapper       = false;
        $this->horizontalOffsetCol          = 'offset-s4';
        $this->iconAfterWrapper             = '';
        $this->iconBeforeWrapper            = '';
        $this->inlineCheckboxLabelClass     = 'checkbox-inline';
        $this->inlineCheckboxWrapper        = '';
        $this->inlineRadioLabelClass        = 'radio-inline';
        $this->inlineRadioWrapper           = '';
        $this->openDomReady                 = 'document.addEventListener(\'DOMContentLoaded\', function(event) {';
        $this->radioWrapper                 = '<div class="radio"></div>';
        $this->requiredMark                 = '<sup class="text-danger">* </sup>';
        $this->textErrorClass               = 'invalid-feedback red-text text-darken-2';
        $this->verticalCheckboxLabelClass   = '';
        $this->verticalLabelClass           = '';
        $this->verticalLabelWrapper         = false;
        $this->verticalRadioLabelClass      = '';
        $this->wrapCheckboxesIntoLabels     = true;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = true;

        if ($layout !== 'horizontal') {
            $this->elementsWrapper = '<div class="row"><div class="input-field col s12"></div></div>';
        }
    }
}
