# Formidable

Formidable is a PHP library to handles forms.

## How does it work?

### Step 1: Write your form in HTML

First, you have to write your code in HTML, for instance:

```html
<!-- forms/example.html -->
<form method="post">
    Enter your name: 
    <input type="text" name="firstname" /><br />
    <input type="submit" />
</form>
```

### Step 2: Give it to Formidable

In your PHP code, give your form to Formidable:

```php
<?php
include('formidable/autoload.php');

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
$form->firstname = "Bob";

// Will get the value of the field
$firstname = $form->firstname;

// Adds a constraint on the firstname
$form->addConstraint('firstname', function($value) {
    if (strlen($value) < 10) {
        return 'Your firstname should be at least 10 characters!';
    }
});
```

