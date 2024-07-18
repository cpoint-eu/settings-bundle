<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle;

use CreativePoint\SettingsBundle\DependencyInjection\Compiler\PersistenceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CreativePointSettingsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(pass: new PersistenceCompilerPass());
    }
}
