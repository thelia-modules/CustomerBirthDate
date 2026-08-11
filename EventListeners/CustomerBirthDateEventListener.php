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

namespace CustomerBirthDate\EventListeners;

use CustomerBirthDate\Model\CustomerBirthDate;
use CustomerBirthDate\Model\CustomerBirthDateQuery;
use CustomerBirthDate\Service\BirthdayDateService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Address\AddressCreateOrUpdateEvent;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateMinimalEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Form\CustomerCreateForm;
use Thelia\Form\CustomerUpdateForm;
use Thelia\Model\Customer;

/**
 * Class CustomerBirthDateEventListener.
 *
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class CustomerBirthDateEventListener implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            // Add field on backOffice form
            TheliaEvents::FORM_AFTER_BUILD.'.'.CustomerCreateForm::getName() => ['addBirthdateInput', 100],
            TheliaEvents::FORM_AFTER_BUILD.'.'.CustomerUpdateForm::getName() => ['addBirthdateInput', 100],
            TheliaEvents::CUSTOMER_CREATEACCOUNT => ['saveBirthDate', 100],

            // save birthdate
            TheliaEvents::CREATE_CUSTOMER_MINIMAL => ['saveBirthDate', 100],
            TheliaEvents::ADDRESS_CREATE => ['saveBirthDate', 100],
            TheliaEvents::CUSTOMER_UPDATEPROFILE => ['saveBirthDate', 100],
            TheliaEvents::CUSTOMER_UPDATEACCOUNT => ['saveBirthDate', 100],
        ];
    }

    public function saveBirthDate(CustomerCreateOrUpdateMinimalEvent|AddressCreateOrUpdateEvent|CustomerCreateOrUpdateEvent $event): void
    {
        // CustomerCreateOrUpdateMinimalEvent::getCustomer() is the only one that may return null.
        $customer = $event->getCustomer();

        if (!$customer instanceof Customer) {
            return;
        }

        $birthDate = $this->readSubmittedBirthDate();

        if (null === $birthDate) {
            return;
        }

        $this->persistBirthDate($customer, $birthDate);
    }

    private function persistBirthDate(Customer $customer, \DateTime $birthDate): void
    {
        $customerBirthDate = CustomerBirthDateQuery::create()->findPk($customer->getId())
            ?? (new CustomerBirthDate())->setId($customer->getId());

        $customerBirthDate
            ->setBirthDate($birthDate)
            ->save();
    }

    private function readSubmittedBirthDate(): ?\DateTime
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }

        $submitted = $this->findBirthDateValue($request->request->all());

        return null === $submitted ? null : $this->parseBirthDate($submitted);
    }

    private function findBirthDateValue(array $payload): array|string|null
    {
        foreach ($payload as $key => $value) {
            if (BirthdayDateService::BIRTHDATE_INPUT_NAME === $key && (\is_array($value) || \is_string($value))) {
                return $value;
            }

            if (\is_array($value) && null !== $found = $this->findBirthDateValue($value)) {
                return $found;
            }
        }

        return null;
    }

    private function parseBirthDate(array|string $value): ?\DateTime
    {
        if (\is_array($value)) {
            foreach (['year', 'month', 'day'] as $part) {
                if (!isset($value[$part]) || !is_numeric($value[$part])) {
                    return null;
                }
            }

            if (!checkdate((int) $value['month'], (int) $value['day'], (int) $value['year'])) {
                return null;
            }

            $value = \sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day']);
        }

        // The column is a DATE: "!" resets the time part instead of taking "now".
        $date = \DateTime::createFromFormat('!Y-m-d', $value) ?: null;

        // Same bounds as the form constraints: the value is read from the raw request,
        // so it must be re-validated here.
        if (null === $date || $date > new \DateTime('today') || $date < new \DateTime('today -120 years')) {
            return null;
        }

        return $date;
    }

    public function addBirthdateInput(TheliaFormEvent $event): void
    {
        BirthdayDateService::apply($event);
    }
}
