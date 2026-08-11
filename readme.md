# CustomerBirthDate

Add a date of birth input in customers' forms

## Installation

Add it in your main thelia composer.json file

```
composer require thelia/customer-birth-date-module
```

## Usage

This module adds a birthday field via a Thelia event. By default, the birthday is added to database when a customer is created or updated (back office and front office). However, you must still add the following listener to the forms in your front-end theme:

```php
#[AsEventListener(event: TheliaEvents::FORM_AFTER_BUILD.‘.name_of_form’)]
class NameOfForm {

// code

    public function __invoke(TheliaFormEvent $event): void
    {
        BirthdayDateService::apply($event, $options);
    }

}
```

## Access data in template

Loop has been removed since Thelia 3. But you can still access to birthdate throught twig extension :

```twig
{{ getBirthdate(customer.id)}}
```

## Design

This module doesn't have css or js file.
If you want to custom it design, you can add some class or various attr with `$options` like this :

```php
    public function __invoke(TheliaFormEvent $event): void
    {
        BirthdayDateService::apply($event, [
            'attr' => [
                'class' => 'BirthdateField flex gap-2'
            ]
            // rest of options provided by DateType
        ]);
    }
```

