# CustomerBirthDate

Add a date of birth input in customers' forms

## Installation

Add it in your main thelia composer.json file

```
composer require thelia/customer-birth-date-module
```

## Usage

This module adds a birthday field via a Thelia event. The field is added automatically
to the back office customer create and update forms, and the submitted value is saved
when a customer is created or updated (back office and front office).

To display the field on a front office form, add a listener in your theme that attaches
it to the form of your choice:

```php
use CustomerBirthDate\Service\BirthdayDateService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;

#[AsEventListener(event: TheliaEvents::FORM_AFTER_BUILD.'.name_of_form')]
class AddBirthDateToCustomerForm
{
    public function __invoke(TheliaFormEvent $event): void
    {
        BirthdayDateService::apply($event);
    }
}
```

## Access data in template

Loops were removed in Thelia 3. You can access the birth date through the Twig extension:

```twig
{{ customer_birthdate(customer.id) }}
```

## Design

This module doesn't ship any CSS or JS file.
To customize the field, pass any `DateType` option (classes, attributes, ...) to `apply()`:

```php
    public function __invoke(TheliaFormEvent $event): void
    {
        BirthdayDateService::apply($event, [
            'attr' => [
                'class' => 'BirthdateField flex gap-2',
            ],
            // any other option supported by DateType
        ]);
    }
```
