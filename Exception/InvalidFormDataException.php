<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Exception;

use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Throwable;

class InvalidFormDataException extends RuntimeException
{
    const DEFAULT_ERROR_MESSAGE = "The data submitted to the form was invalid.";

    /**
     * @var FormInterface
     */
    protected $form;

    public function __construct(FormInterface $form, string $message = self::DEFAULT_ERROR_MESSAGE, $code = 0,
                                Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
        $this->form = $form;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
