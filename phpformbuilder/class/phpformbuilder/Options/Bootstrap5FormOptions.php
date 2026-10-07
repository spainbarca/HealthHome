<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class Bootstrap5FormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * Constructor for the Bootstrap5FormOptions class.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'btn-group';
        $this->buttonWrapper                = '<div class="mb-3"></div>';
        $this->checkboxWrapper              = '<div class="form-check"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = 'form-control';
        $this->elementsErrorClass           = 'is-invalid';
        $this->elementsWrapper              = '<div class="bs5-form-stacked-element mb-3"></div>';
        $this->formHorizontalClass          = 'form-horizontal';
        $this->formVerticalClass            = '';
        $this->helperWrapper                = '<span class="form-text"></span>';
        $this->horizontalElementCol         = 'col-sm-8 mb-3';
        $this->horizontalLabelClass         = 'col-form-label';
        $this->horizontalLabelCol           = 'col-sm-4';
        $this->horizontalLabelWrapper       = false;
        $this->horizontalOffsetCol          = 'offset-sm-4';
        $this->iconAfterWrapper             = '<div class="input-group-text"></div>';
        $this->iconBeforeWrapper            = '<div class="input-group-text"></div>';
        $this->inlineCheckboxLabelClass     = 'form-check-label';
        $this->inlineCheckboxWrapper        = '<div class="form-check form-check-inline"></div>';
        $this->inlineRadioLabelClass        = 'form-check-label';
        $this->inlineRadioWrapper           = '<div class="form-check form-check-inline"></div>';
        $this->openDomReady                 = 'document.addEventListener(\'DOMContentLoaded\', function(event) {';
        $this->radioWrapper                 = '<div class="form-check"></div>';
        $this->requiredMark                 = '<sup class="text-danger">* </sup>';
        $this->textErrorClass               = 'invalid-feedback w-100 d-block';
        $this->verticalCheckboxLabelClass   = 'form-label';
        $this->verticalLabelClass           = 'form-label';
        $this->verticalLabelWrapper         = false;
        $this->verticalRadioLabelClass      = 'form-label';
        $this->wrapCheckboxesIntoLabels     = false;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = false;

        if ($layout == 'horizontal') {
            $this->buttonWrapper   = '<div class="row"></div>';
            $this->checkboxWrapper = '<div class="form-check"></div>';
            $this->elementsWrapper = '<div class="row"></div>';
            $this->radioWrapper    = '<div class="form-check"></div>';
        }
    }
}
