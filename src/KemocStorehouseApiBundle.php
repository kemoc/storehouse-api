<?php
declare(strict_types=1);

namespace Kemoc\Storehouse\ApiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class KemocStorehouseApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

}
