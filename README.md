# Formidable

Formidable is a PHP library to handles forms. It parses an HTML
form and allow you to manipulate it from your PHP code, and then
render it.

## How does it work?

### Step 1: Download & install Formidable

Via composer:

```json
{
    "require": {
        "gregwar/formidable": "dev-master"
    }
}
```

Or with a clone of the repository:

```bash
git clone https://github.com/Gregwar/Formidable.git
```

Or downloading it:

* [Download .zip](https://github.com/Gregwar/Formidable/archive/master.zip)
* [Download .tar.gz](https://github.com/Gregwar/Formidable/archive/master.tar.gz)

### Step 2: Write your form in HTML

First, you have to write your code in HTML, for instance:

```html
<!-- forms/example.html -->
<form method="post">
    Enter your name: 
    <input type="text" name="name" /><br />
    <input type="submit" />
</form>
```

### Step 3: Give it to Formidable

In your PHP code, give your form to Formidable:

```php
<?php
session_start();
include('vendor/autoload.php');

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

### Step 4: Enjoy the magic

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

You can also try to change your form and add constraint directly in
the HTML code:

```html
<input type="text" name="name" minlength="10" />
```

This will force the text to be at least 10 characters long when the
server-side constraints will be checked.

Want a CAPTCHA to secure your form? No problem:

```html
<input type="captcha" name="code" />
```

This will generate an image and an input field on the client-side, and use
session on the server-side to check that the code is correct.

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
* `multiradio` and `multicheckbox` (see the source section)

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
* `value`: the default value for the field

## API

You can call these method on your `$form` object:

* `posted()`: return true if the form was posted
* `check()`: check the form and return an array of validity errors
* `handle($callback, $errorCallback)`, this shortcut method call posted
  and check(), and will call `$callback` if the form is valid, `$errorCallback`
  else
* `setAttribute($field, $attr, $value)`: sets an extra attribute on a field
* `getAttribute($field, $attr)`: gets an extra attribute on a field
* `source($source, $values)`: feed a source (see the "Source" section)
* `addConstraint($field, $callback)`: adds a custom constraint on a field, the 
  `callback` will be called with the field value and should return false if no
  problem, or an error string 
* `setValue($field, $value)`: set the value of a field
* `getValue($field)`: gets the value of a field
* `setValues(array $values)`: set the values for some fields
* `getValues()`: get the values of all fields

## CSRF protection

An additional CSRF token is automatically inserted in the form and checked
when it's submitted. Thus, all your forms will be secured.

The presence and validity of CSRF token is used to check that a form was 
posted when calling `posted` method (it's used internally in `handle`)

If you specify the `name` attribute in the `form`, the CSRF token will be 
different for this specific form, this will allow Formidable to make the
difference of which form is submitted if there is multiple form on the same
page.

## Languages

The language for the errors can be set with `setLanguage()`:

```php
<?php

// Will set the language to french for errors
$form->setLanguage(new Gregwar\Formidable\French);
```

Check that your language is supported in the `Language` directory, don't hesitate
to participate!

## Source

You can use the sourcing system to populate dynamically a `select`, a `multiradio` or
a `multicheckbox`:

```html
<input type="multicheckbox" name="colours" source="colours" />
```

Then populate it with `source`:

```php
<?php
$form->source('colours', array('red', 'yellow', 'blue'));
```

This will be rendered by some checkboxes.

You can do it this way with `select`:

```html
<select name="colour">
    <options source="colours" />
    <option value="other">Other</option>
</select>
```

And then source it with the same method

## Creating form

You can create form from a file or from a string, this will be detected automatically:

```php
<?php
$form = new Gregwar\Formidable\Form('<form method="post">
    <select name="colour">
        <option value="blue">Blue</option>
        <option selected value="red">Red</option>
        <option value="green">Green</option>
    </select>
    </form>');

echo $form->colour, "\n";
// red

// Sets the color to blue
$form->colour = 'blue';

echo $form;
/* Will display:
<form method="post">
    <select name="colour" >
        <option selected="selected" value="blue">Blue</option>
        <option value="red">Red</option>
        <option value="green">Green</option>
    </select>
    <input type="hidden" name="csrf_token" value="d293dc38017381b6086ff1a856c1e8fe43738c60" />
</form>
*/
```
