<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Factory\Product;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Kemoc\Storehouse\ApiBundle\Form\Data\Product\Create;
use Kemoc\Storehouse\ApiBundle\Form\Type\Product\CreateType;
use Kemoc\Storehouse\ApiBundle\Form\Data\Product\Update;
use Kemoc\Storehouse\ApiBundle\Form\Type\Product\UpdateType;

class FormFactory
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getCreate(Create $data, bool $CSRFProtection = true, string $method = Request::METHOD_POST,
                              ?string $name = null
    ): FormInterface
    {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(CreateType::class);

        return $this->formFactory->createNamed($name, CreateType::class, $data, [
                'method' => $method,
                'csrf_protection' => $CSRFProtection
            ]
        );
    }

    public function getUpdate(Update $data, bool $CSRFProtection = true, $method = Request::METHOD_PATCH,
                              ?string $name = null
    ): FormInterface
    {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UpdateType::class);

        return $this->formFactory->createNamed($name, UpdateType::class, $data, [
            'method' => $method,
            'csrf_protection' => $CSRFProtection
        ]);
    }
}

