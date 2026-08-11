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

namespace CustomerBirthDate\Service;

use CustomerBirthDate\CustomerBirthDate;
use CustomerBirthDate\Form\Type\BirthdayType;
use CustomerBirthDate\Model\CustomerBirthDateQuery;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\Translation\Translator;

final class BirthdayDateService
{
    public const string BIRTHDATE_INPUT_NAME = 'birthday_date';

    public static function apply(TheliaFormEvent $event, mixed $options = []): void
    {
        if ($event->getForm()->getFormBuilder()->has(self::BIRTHDATE_INPUT_NAME)) {
            return;
        }

        $data = $event->getForm()->getFormBuilder()->getData();

        $birthDate = null;

        if (!empty($data['id'])) {
            $birthDate = CustomerBirthDateQuery::create()
                ->findOneById($data['id']);
        }

        $event->getForm()->getFormBuilder()->add(
            self::BIRTHDATE_INPUT_NAME,
            BirthdayType::class,
            array_merge(
                $options,
                [
                    'label' => Translator::getInstance()->trans('Date of birth', [], CustomerBirthDate::DOMAIN_NAME),
                    'required' => true,
                    // 'required' only sets the HTML5 attribute: without NotBlank an empty or
                    // future date is dropped server-side and the form still validates.
                    // 'input' => 'string' keeps the value a Y-m-d string, which the comparison
                    // validator compares as a string — lexicographic order matches chronological
                    // order on a zero-padded ISO date.
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThanOrEqual(date('Y-m-d', strtotime('-120 years'))),
                        new LessThanOrEqual(date('Y-m-d')),
                    ],
                    'widget' => 'choice',
                    'years' => range(date('Y') - 90, date('Y')),
                    'input' => 'string',
                    'data' => $birthDate?->getBirthDate('Y-m-d'),
                ]
            )
        );
    }

    public function getBirthdateByCustomer(int $id): string
    {
        $birthDate = CustomerBirthDateQuery::create()->findPk($id);

        return $birthDate?->getBirthDate('Y-m-d') ?? '';
    }
}
