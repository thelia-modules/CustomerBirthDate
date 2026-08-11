# 2.0.0

- Thelia 3 compatibility (Symfony 7, PHP 8.3+)
- The field is now added with a Symfony `BirthdayType`-like form type rendered by a Twig form theme, instead of Smarty templates and jQuery snippets
- The `customerbirthdate` loop is replaced by the `customer_birthdate(id)` Twig function
- Front office themes must attach the field to their forms via `BirthdayDateService::apply()` (see readme)
- Submitted values are validated: real calendar date, not in the future, at most 120 years in the past

# 1.2.1

- Fix translation in the BackOffice and frontOffice

# 1.2

- Change foreign key name
