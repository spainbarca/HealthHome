<?php

namespace phpformbuilder\Options;

trait RequiredOptionsTrait
{
    /*  bs4_options, bs5_options, bulma_options, foundation_options, material_options, tailwind_options, uikit_options :
    *   wrappers and classes styled with Bootstrap 4/5, Bulma, foundation XY Grid (Foundation > 6.3), Material Design, Tailwind, UIkit
    *   each option can be individually updated with $form->setOptions();
    *
     * Available options:
     * - elementsWrapper: string - The wrapper HTML element for form elements.
     * - formHorizontalClass: string - The CSS class for horizontal form layout.
     * - formVerticalClass: string - The CSS class for vertical form layout.
     * - checkboxWrapper: string - The wrapper HTML element for checkboxes.
     * - radioWrapper: string - The wrapper HTML element for radio buttons.
     * - helperWrapper: string - The wrapper HTML element for helper text.
     * - buttonWrapper: string - The wrapper HTML element for buttons.
     * - wrapElementsIntoLabels: bool - Whether to wrap form elements into labels.
     * - wrapCheckboxesIntoLabels: bool - Whether to wrap checkboxes into labels.
     * - wrapRadiobtnsIntoLabels: bool - Whether to wrap radio buttons into labels.
     * - elementsClass: string - The CSS class for form elements.
     * - wrapperErrorClass: string - The CSS class for error wrapper.
     * - elementsErrorClass: string - The CSS class for error elements.
     * - textErrorClass: string - The CSS class for error text.
     * - verticalLabelWrapper: bool - Whether to use vertical label wrapper.
     * - verticalLabelClass: string - The CSS class for vertical labels.
     * - verticalCheckboxLabelClass: string - The CSS class for vertical checkbox labels.
     * - verticalRadioLabelClass: string - The CSS class for vertical radio button labels.
     * - horizontalLabelWrapper: bool - Whether to use horizontal label wrapper.
     * - horizontalLabelClass: string - The CSS class for horizontal labels.
     * - horizontalLabelCol: string - The CSS class for horizontal label column.
     * - horizontalOffsetCol: string - The CSS class for horizontal offset column.
     * - horizontalElementCol: string - The CSS class for horizontal element column.
     * - inlineCheckboxLabelClass: string - The CSS class for inline checkbox labels.
     * - inlineRadioLabelClass: string - The CSS class for inline radio button labels.
     * - inlineCheckboxWrapper: string - The wrapper HTML element for inline checkboxes.
     * - inlineRadioWrapper: string - The wrapper HTML element for inline radio buttons.
     * - iconBeforeWrapper: string - The wrapper HTML element for icons before form elements.
     * - iconAfterWrapper: string - The wrapper HTML element for icons after form elements.
     * - btnGroupClass: string - The CSS class for button groups.
     * - requiredMark: string - The HTML for the required mark.
     * - openDomReady: string - The JavaScript code to open DOM ready function.
     * - closeDomReady: string - The JavaScript code to close DOM ready function.
    */

    protected string $elementsWrapper;
    protected string $formHorizontalClass;
    protected string $formVerticalClass;
    protected string $checkboxWrapper;
    protected string $radioWrapper;
    protected string $helperWrapper;
    protected string $buttonWrapper;
    protected bool $wrapElementsIntoLabels;
    protected bool $wrapCheckboxesIntoLabels;
    protected bool $wrapRadiobtnsIntoLabels;
    protected string $elementsClass;
    protected string $wrapperErrorClass;
    protected string $elementsErrorClass;
    protected string $textErrorClass;
    protected bool $verticalLabelWrapper;
    protected string $verticalLabelClass;
    protected string $verticalCheckboxLabelClass;
    protected string $verticalRadioLabelClass;
    protected bool $horizontalLabelWrapper;
    protected string $horizontalLabelClass;
    protected string $horizontalLabelCol;
    protected string $horizontalOffsetCol;
    protected string $horizontalElementCol;
    protected string $inlineCheckboxLabelClass;
    protected string $inlineRadioLabelClass;
    protected string $inlineCheckboxWrapper;
    protected string $inlineRadioWrapper;
    protected string $iconBeforeWrapper;
    protected string $iconAfterWrapper;
    protected string $btnGroupClass;
    protected string $requiredMark;
    protected string $openDomReady;
    protected string $closeDomReady;
}
