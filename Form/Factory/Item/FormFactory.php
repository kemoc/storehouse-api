<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Factory\Item;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Kemoc\Storehouse\ApiBundle\Form\Data\Item\Create;
use Kemoc\Storehouse\ApiBundle\Form\Type\Item\CreateType;
use Kemoc\Storehouse\ApiBundle\Form\Data\Item\Update;
use Kemoc\Storehouse\ApiBundle\Form\Type\Item\UpdateType;

class FormFactory
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getCreate(Create $data, string $method = Request::METHOD_POST, ?string $name = null): FormInterface
    {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(CreateType::class);

        return $this->formFactory->createNamed($name, CreateType::class, $data, ['method' => $method]);
    }

    public function getUpdate(Update $data, $method = Request::METHOD_PATCH, ?string $name = null): FormInterface
    {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UpdateType::class);

        return $this->formFactory->createNamed($name, UpdateType::class, $data, ['method' => $method]);
    }
}

