<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CustomerBirthDate;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Core\Install\Database;
use Thelia\Module\BaseModule;

/**
 * Class CustomerBirthDate.
 *
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class CustomerBirthDate extends BaseModule
{
    public const string DOMAIN_NAME = 'customerbirthdate';

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([__DIR__.'/I18n/*', __DIR__.'/Model/*'])
            ->autowire(true)
            ->autoconfigure(true);
    }

    public static function loadConfiguration(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->loadFromExtension('twig', [
            'form_themes' => ['@CustomerBirthDateModule/form/birthday_theme.html.twig'],
        ]);
    }

    public function postActivation(?ConnectionInterface $con = null): void
    {
        $database = new Database($con);

        $database->insertSql(null, [__DIR__.'/Config/create.sql']);
    }
}
