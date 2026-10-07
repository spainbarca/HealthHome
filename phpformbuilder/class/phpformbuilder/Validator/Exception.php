<?php
namespace phpformbuilder\Validator;

class Exception extends \Exception
{
    /**
     * Class Exception
     *
     * This class represents an exception thrown by the Validator class in the PHPFormBuilder library.
     */

    /**
     * @var array<string> $_errors An array containing the validation errors.
     */
    protected $_errors = [];

    /**
     * Exception constructor.
     *
     * @param string $message The exception message.
     * @param mixed[] $errors An array of errors.
     */
    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message);
        $this->_errors = $errors;
    }

    /**
     * Get the errors associated with the exception.
     *
     * @return mixed[] An array of errors.
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }
}
