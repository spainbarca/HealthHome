<?php

declare(strict_types=1);

namespace phpformbuilder;

use phpformbuilder\Validator\Validator;

class FormExtended extends Form
{

    /**
     * Create a contact form.
     *
     * @return self
     */
    public function createContactForm(): self
    {
        $framework = $this->framework;
        $this->startFieldset('Please fill in this form to contact us', '', 'class=text-center center-align mb-4');
        $this->groupElements('user-name', 'user-first-name');
        $this->setCols(3, 6);
        $this->addHelper('Name', 'user-name');
        $this->addInput('text', 'user-name', '', 'Full Name', 'placeholder=Name, required');
        $this->setCols(0, 3);
        $this->addHelper('First name', 'user-first-name');
        $this->addInput('text', 'user-first-name', '', '', 'placeholder=First Name, required');
        $this->setCols(3, 9);
        $this->addInput('email', 'user-email', '', 'Email', 'placeholder=Email, required');
        $this->addInput('text', 'user-phone', '', 'Phone', 'placeholder=Phone, required');
        $this->addTextarea('message', '', 'Message', 'cols=30, rows=4, required');
        $this->addPlugin('word-character-count', '#message', 'default', array('%maxAuthorized%' => 100));
        $this->centerContent();
        $this->addCheckbox('newsletter', 'Suscribe to Newsletter', '1', 'checked=checked');
        $this->printCheckboxGroup('newsletter', '');
        $this->setCols(0, 12);
        $this->addHcaptcha('321856aa-ff29-4ab6-840a-8db73ca51dbf', 'class=text-center center-align');

        $submitBtnClass = [
            'bs4'                 => 'btn btn-primary',
            'bs5'                 => 'btn btn-primary',
            'bulma'               => 'button is-primary',
            'foundation'          => 'button primary',
            'material'            => 'btn waves-effect waves-light',
            'material-bootstrap'  => 'btn btn-primary waves-effect waves-light',
            'tailwind'            => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center center-align mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
            'uikit'               => 'uk-button uk-button-primary'
        ];

        $this->addBtn('submit', 'submit-btn', '1', 'Send', 'class=' . $submitBtnClass[$framework]);
        $this->endFieldset();

        // Custom radio & checkbox css
        if ($framework !== 'material') {
            $this->addPlugin('nice-check', '#' . $this->formId, 'default', ['skin' => 'red']);
        }

        // jQuery validation
        $this->addPlugin('formvalidation', '#' . $this->formId);

        return $this;
    }

    /* Contact form validation */

    /**
     * Validate the contact form.
     *
     * @param string $formName The name of the form.
     * @return bool Returns true if the form is valid, false otherwise.
     */
    public static function validateContactForm(string $formName): bool
    {
        // create validator & auto-validate required fields
        $validator = static::validate($formName);

        // additional validation
        $validator->maxLength(100)->validate('message');
        $validator->email()->validate('user-email');
        // hcaptcha validation
        $validator->hcaptcha('0xE49dEF7c889f9a19F34C5AEE68D77EB78eB7870d', 'Captcha Error')->validate('h-captcha-response');

        // check for errors

        if ($validator->hasErrors()) {
            $_SESSION['errors'][$formName] = $validator->getAllErrors();

            return false;
        } else {
            return true;
        }
    }

    /* =============================================
        Fields shorcuts and groups for users
    ============================================= */

    /**
     * Add an address field to the form.
     *
     * @param int $i The index of the address field.
     * @return $this
     */
    public function addAddress(int $i = 0): self
    {
        $index = $this->getIndex($i);
        $this->setCols(3, 9, 'md');
        $this->addTextarea('address' . $index, '', 'Address', 'required');
        $this->groupElements('zip_code' . $index, 'city' . $index);
        $this->setCols(3, 4, 'md');
        $this->addInput('text', 'zip_code' . $index, '', 'Zip Code', 'required');
        $this->setCols(2, 3, 'md');
        $this->addInput('text', 'city' . $index, '', 'City', 'required');
        $this->setCols(3, 9, 'md');
        $this->addCountrySelect('country' . $index, 'Country', 'class=no-autoinit, data-width=100%, required');

        return $this;
    }

    /**
     * Add a birth field to the form.
     *
     * @param int $i The index of the birth field.
     * @return $this
     */
    public function addBirth(int $i = 0): self
    {
        $index = $this->getIndex($i);
        $this->setCols(3, 4, 'md');
        $this->groupElements('birth_date' . $index, 'birth_zip_code' . $index);
        $this->addInput('text', 'birth_date' . $index, '', 'Birth Date', 'placeholder=click to open calendar, data-litepick=true, data-select-forward=false, data-dropdown-min-year=1920, data-dropdown-months=true, data-dropdown-years=true');
        $this->setCols(2, 3, 'md');
        $this->addInput('text', 'birth_zip_code' . $index, '', 'Birth Zip Code');
        $this->setCols(3, 4, 'md');
        $this->groupElements('birth_city' . $index, 'birth_country' . $index);
        $this->addInput('text', 'birth_city' . $index, '', 'Birth  City');
        $this->setCols(2, 3, 'md');
        $this->addCountrySelect('birth_country' . $index, 'Birth Country', 'class=no-autoinit, data-width=100%');

        return $this;
    }

    /**
     * Add a civility select field to the form.
     *
     * @param int $i The index of the civility field.
     * @return $this
     */
    public function addCivilitySelect(int $i = 0): self
    {
        $index = $this->getIndex($i);
        $this->addOption('civility' . $index, 'M.', 'M.');
        $this->addOption('civility' . $index, 'M<sup>s</sup>', 'Ms');
        $this->addSelect('civility' . $index, 'Civility', 'required');

        return $this;
    }

    /**
     * Add a contact field to the form.
     *
     * @param int $i The index of the contact field.
     * @return $this
     */
    public function addContact(int $i = 0): self
    {
        $index = $this->getIndex($i);
        $this->groupElements('phone' . $index, 'mobile_phone' . $index);
        $this->setCols(3, 4, 'md');
        $this->addInput('text', 'phone' . $index, '', 'Phone');
        $this->setCols(2, 3, 'md');
        $this->addInput('text', 'mobile_phone' . $index, '', 'Mobile', 'required');
        $this->setCols(3, 9, 'md');
        $this->addInput('email', 'email_professional' . $index, '', 'BuisnessE-mail', 'required');
        $this->addInput('email', 'email_private' . $index, '', 'Personal E-mail');

        return $this;
    }

    /**
     * Add an identity field to the form.
     *
     * @param int $i The index of the identity field.
     * @return $this
     */
    public function addIdentity(int $i = 0): self
    {
        $index = $this->getIndex($i);
        $this->groupElements('civility' . $index, 'name' . $index);
        $this->setCols(3, 2, 'md');
        $this->addCivilitySelect($i);
        $this->setCols(3, 4, 'md');
        $this->addInput('text', 'name' . $index, '', 'Name', 'required');
        $this->setCols(3, 9, 'md');
        $this->startDependentFields('civility' . $index, 'Mrs');
        $this->addInput('text', 'maiden_name' . $index, '', 'Maiden Name');
        $this->endDependentFields();
        $this->groupElements('firstnames' . $index, 'citizenship' . $index);
        $this->setCols(3, 4, 'md');
        $this->addInput('text', 'firstnames' . $index, '', 'Firstnames', 'required');
        $this->setCols(2, 3, 'md');
        $this->addInput('text', 'citizenship' . $index, '', 'Citizenship');

        return $this;
    }

    /* Submit buttons */

    /**
     * Add a back submit button to the form.
     *
     * @return $this
     */
    public function addBackSubmit(): self
    {
        $this->setCols(0, 12);
        $this->addHtml('<p>&nbsp;</p>');
        $this->addBtn('submit', 'back-btn', '1', 'Back', 'class=btn btn-warning button warning', 'submit_group');
        $this->addBtn('submit', 'submit-btn', '1', 'Submit', 'class=btn btn-success button success', 'submit_group');
        $this->printBtnGroup('submit_group');

        return $this;
    }

    /**
     * Add a cancel submit button to the form.
     *
     * @return $this
     */
    public function addCancelSubmit(): self
    {
        $framework = $this->framework;
        $cancelBtnClass = [
            'bs4' => 'btn btn-warning',
            'bs5' => 'btn btn-warning',
            'bulma' => 'button is-warning',
            'foundation' => 'button warning',
            'material' => 'btn orange darken-1 waves-effect waves-light',
            'material-bootstrap' => 'btn btn-warning waves-effect waves-light',
            'tailwind' => 'text-white bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:focus:ring-amber-900',
            'uikit' => 'uk-button uk-button-default'
        ];
        $submitBtnClass = [
            'bs4'                 => 'btn btn-primary',
            'bs5'                 => 'btn btn-primary',
            'bulma'               => 'button is-primary',
            'foundation'          => 'button primary',
            'material'            => 'btn waves-effect waves-light',
            'material-bootstrap'  => 'btn btn-primary waves-effect waves-light',
            'tailwind'            => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center center-align mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
            'uikit'               => 'uk-button uk-button-primary'
        ];
        $this->centerContent();
        $this->addBtn('button', 'cancel-btn', '1', 'Cancel', 'class=' . $cancelBtnClass[$framework], 'submit_group');
        $this->addBtn('submit', 'submit-btn', '1', 'Submit', 'class=' . $submitBtnClass[$framework], 'submit_group');
        $this->printBtnGroup('submit_group');

        return $this;
    }

    /**
     * Get the index value.
     *
     * @param string|int $i The index value.
     * @return string|false The index value with a prefix or false if no index is provided.
     */
    private function getIndex($i)
    {
        if ($i !== '') {
            return '-' . $i;
        }

        return false;
    }
}
