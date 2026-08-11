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

namespace CustomerBirthDate\Twig;

use CustomerBirthDate\Service\BirthdayDateService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CustomerBirthdateExtension extends AbstractExtension
{
    public function __construct(
        private readonly BirthdayDateService $birthdayService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('customer_birthdate', [$this, 'getBirthdate']),
        ];
    }

    public function getBirthdate(int $id): string
    {
        return $this->birthdayService->getBirthdateByCustomer($id);
    }
}
