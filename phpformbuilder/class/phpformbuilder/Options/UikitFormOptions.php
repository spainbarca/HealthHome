<?php

namespace phpformbuilder\Options;

use phpformbuilder\Options\BaseFormOptions;

class UikitFormOptions extends BaseFormOptions implements FormOptionsInterface
{
    use RequiredOptionsTrait;

    /**
     * UikitFormOptions constructor.
     *
     * @param string $layout The layout option for the form. 'horizontal' or 'vertical'.
     */
    public function __construct(string $layout)
    {
        parent::__construct();
        $this->btnGroupClass                = 'uk-button-group';
        $this->buttonWrapper                = '<div class="uk-form-stacked-element uk-margin"></div>';
        $this->checkboxWrapper              = '<div class="uk-form-stacked-element uk-margin"></div>';
        $this->closeDomReady                = '});';
        $this->elementsClass                = '';
        $this->elementsErrorClass           = 'uk-form-danger';
        $this->elementsWrapper              = '<div class="uk-form-stacked-element uk-margin"></div>';
        $this->formHorizontalClass          = 'form-horizontal';
        $this->formVerticalClass            = 'uk-form-stacked';
        $this->helperWrapper                = '<p class="uk-text-muted uk-margin-remove"></p>';
        $this->horizontalElementCol         = 'uk-form-controls uk-width-2-3@s';
        $this->horizontalLabelClass         = 'uk-form-label';
        $this->horizontalLabelCol           = 'uk-width-1-3@s';
        $this->horizontalLabelWrapper       = false;
        $this->horizontalOffsetCol          = '';
        $this->iconAfterWrapper             = '';
        $this->iconBeforeWrapper            = '';
        $this->inlineCheckboxLabelClass     = 'uk-form-label';
        $this->inlineCheckboxWrapper        = '<span class="uk-flex-inline uk-flex-middle uk-margin-right uk-margin-small-bottom uk-text-nowrap"></span>';
        $this->inlineRadioLabelClass        = 'uk-form-label';
        $this->inlineRadioWrapper           = '<span class="uk-flex-inline uk-flex-middle uk-margin-right uk-margin-small-bottom uk-text-nowrap"></span>';
        $this->openDomReady                 = 'document.addEventListener(\'DOMContentLoaded\', function(event) {';
        $this->radioWrapper                 = '<div class="uk-form-stacked-element uk-margin"></div>';
        $this->requiredMark                 = '<sup class="uk-text-danger">* </sup>';
        $this->textErrorClass               = 'uk-text-danger uk-margin-remove-top';
        $this->verticalCheckboxLabelClass   = 'uk-form-label';
        $this->verticalLabelClass           = 'uk-form-label';
        $this->verticalLabelWrapper         = false;
        $this->verticalRadioLabelClass      = 'uk-form-label';
        $this->wrapCheckboxesIntoLabels     = false;
        $this->wrapElementsIntoLabels       = false;
        $this->wrapperErrorClass            = '';
        $this->wrapRadiobtnsIntoLabels      = false;

        if ($layout == 'horizontal') {
            $this->buttonWrapper   = '<div class="uk-grid uk-flex-right"></div>';
            $this->checkboxWrapper = '<div class="uk-flex mb-2"></div>';
            $this->elementsWrapper = '<div class="uk-grid uk-flex-right uk-width-1-1"></div>';
            $this->radioWrapper    = '<div class="uk-flex mb-2"></div>';
        }
    }
}
