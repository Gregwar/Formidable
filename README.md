# Formidable

Formidable is a PHP library to handles forms.

## How does it work?

### Step 1: Write your form in HTML

First, you have to write your code in HTML, for instance:

```html
<!-- forms/example.html -->
<form method="post">
    Enter your name: 
    <input type="text" name="name" /><br />
    <input type="submit" />
</form>
```

### Step 2: Give it to Formidable

In your PHP code, give your form to Formidable:

```php
<?php
session_start();
include('formidable/autoload.php');

// Formidable will parse the form and use it to check integrity
// on the server-side
$form = new Gregwar\Formidable\Form('forms/example.html');

$form->handle(function() {
    echo "Form OK!";
}, function($errors) {
    echo "Errors: <br/>";
    foreach ($errors as $error) {
        echo "$error<br />";
    }
});

echo $form;
```

Simple, right?

### Step 3: Enjoy your form

You can then use the Formidable API to play with your form:

```php
<?php

// Will set the value of the field
$form->name = "Bob";

// Will get the value of the field
$name = $form->name;

// Adds a constraint on the name
$form->addConstraint('name', function($value) {
    if (strlen($value) < 10) {
        return 'Your name should be at least 10 characters!';
    }
});
```

## Types

The following input types are supported:

* `text`
* `number`, see `min` and `max` attributes
* `integer`, see `min` and `max` attributes
* `file`
* `checkbox`
* `radio`
* `hidden`
* `password`
* `captcha`, will automatically generate an image
* `date`, will generate three selects, and return a `DateTime` as data

Moreover, the textareas and select are supported

## Attributes

Note that some attributes are not HTML-valid, like `maxlength`:

```html
    <input type="text" name="name" maxlength="10" />
```

It will not be rendered in the HTML form, but will be used to check integrity.

Here is the list of available attributes:

* `minlength`: the minimum length of the value
* `maxlength`: the maximum length of the value
* `regex`: the regexp that the value should respect
* `min` (for numbers): the minimum value
* `max` (for numbers): the maximum value
* `optional`: tell that the field is not required
* `readonly`: the field is readonly and should not be modifier

## CSRF protection

An additional CSRF token is automatically inserted in the form and checked
when it's submitted. Thus, all your forms will be secured.

!## Languages

The language for the errors can be set with `setLanguage()`:

```php
<?php

// Will set the language to french for errors
$form->setLanguage(new Gregwar\Formidable\French);
```

Check that your language is supported in the `Language` directory, don't hesitate
to participate!
