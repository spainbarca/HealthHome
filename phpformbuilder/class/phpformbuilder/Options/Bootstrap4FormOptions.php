<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class Bootstrap4FormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * Constructor for Bootstrap4FormOptions class.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'btn-group';
        $this->buttonWrapper                = '<div class="form-group"></div>';
        $this->checkboxWrapper              = '<div class="form-check"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = 'form-control';
        $this->elementsErrorClass           = 'is-invalid';
        $this->elementsWrapper              = '<div class="form-group"></div>';
        $this->formHorizontalClass          = 'form-horizontal';
        $this->formVerticalClass            = '';
        $this->helperWrapper                = '<small class="form-text text-muted"></small>';
        $this->horizontalElementCol         = 'col-sm-8 mb-3';
        $this->horizontalLabelClass         = 'col-form-label';
        $this->horizontalLabelCol           = 'col-sm-4';
        $this->horizontalLabelWrapper       = false;
        $this->horizontalOffsetCol          = 'offset-sm-4';
        $this->iconAfterWrapper             = '<div class="input-group-append"><span class="input-group-text"></span></div>';
        $this->iconBeforeWrapper            = '<div class="input-group-prepend"><span class="input-group-text"></span></div>';
        $this->inlineCheckboxLabelClass     = 'form-check-label';
        $this->inlineCheckboxWrapper        = '<div class="form-check form-check-inline"></div>';
        $this->inlineRadioLabelClass        = 'form-check-label';
        $this->inlineRadioWrapper           = '<div class="form-check form-check-inline"></div>';
        $this->openDomReady                 = 'jQuery(document).ready(function($) {';
        $this->radioWrapper                 = '<div class="form-check"></div>';
        $this->requiredMark                 = '<sup class="text-danger">* </sup>';
        $this->textErrorClass               = 'invalid-feedback w-100 d-block';
        $this->verticalCheckboxLabelClass   = 'form-check-label';
        $this->verticalLabelClass           = 'form-control-label';
        $this->verticalLabelWrapper         = false;
        $this->verticalRadioLabelClass      = 'form-check-label';
        $this->wrapCheckboxesIntoLabels     = false;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = false;

        if ($layout == 'horizontal') {
            $this->checkboxWrapper = '<div class="form-check"></div>';
            $this->elementsWrapper = '<div class="form-group row"></div>';
            $this->radioWrapper    = '<div class="form-check"></div>';
            $this->buttonWrapper   = '<div class="form-group row"></div>';
        }
    }
}
