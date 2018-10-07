<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\Form\Data\Item;

//use Symfony\Component\Validator\Constraints as Assert;
use Kemoc\Storehouse\ApiBundle\Entity\Item as ItemEntity;
//use Symfony\Component\Translation\TranslatorInterface;

interface UpdateInterface extends CreateInterface
{

    public function getId(): int;

}

