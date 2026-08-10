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

/*      Copyright (c) OpenStudio */
/*      email : dev@thelia.net */
/*      web : http://www.thelia.net */

/*      For the full copyright and license information, please view the LICENSE.txt */
/*      file that was distributed with this source code. */

namespace CustomerBirthDate;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\Install\Database;
use Thelia\Module\BaseModule;

/**
 * Class CustomerBirthDate.
 *
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class CustomerBirthDate extends BaseModule
{
    public function postActivation(?ConnectionInterface $con = null): void
    {
        $database = new Database($con);

        $database->insertSql(null, [__DIR__.'/Config/create.sql']);
    }
}
