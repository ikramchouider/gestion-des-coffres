<?php
// src/Exception/InvalidRegistrationDataException.php
namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRegistrationDataException extends HttpException
{
    private $errors;

    public function __construct(ConstraintViolationListInterface $errors)
    {
        $this->errors = $errors;
        parent::__construct(422, 'Invalid registration data');
    }

    public function getErrors(): array
    {
        $errorMessages = [];
        foreach ($this->errors as $error) {
            $errorMessages[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        return $errorMessages;
    }
}