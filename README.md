# Formidable

[![Build status](https://travis-ci.org/Gregwar/Formidable.svg?branch=master)](https://travis-ci.org/Gregwar/Formidable)

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

// Adds a constraint on the whole form
$form->addConstraint(function($form) {
    if ($form->getValue('pass1') != $form->getValue('pass2')) {
        return 'The passwords are different';
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

Want a CAPTCHA to secure your form?

```html
<input type="captcha" name="code" />
```

This will generate an image and an input field on the client-side, and use
session on the server-side to check that the code is correct.

Note that this will use the dependency with [Gregwar/Captcha](https://github.com/Gregwar/Captcha/)
library (you will have to install dependencies using composer).

## Types

The following input types are supported:

* `input` tags, with types:
    * `text`
    * `number` or `numeric`, see `min` and `max` attributes
    * `int` or `integer`, see `min` and `max` attributes
    * `file`
    * `checkbox`
    * `radio`
    * `hidden`
    * `password`
    * `captcha`, will automatically generate an image
    * `date`, will generate three selects, and return a `DateTime` as data
    * `multiradio` and `multicheckbox` (see the source section)
* `textarea`
* `select`

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
* `required`: tell that the field is required
* `readonly`: the field is readonly and should not be modifier
* `value`: the default value for the field
* `min-entries`: specify the minimum number of
  entries that you should provide for a multiple (see below)
* `max-entries`: specify the maximum number of
  entries that you can provide for a multiple (see below)
* `entries`: specify both minimum and maximum number of entries
  for a multiple (see below)

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
* `setPlaceholder($name, $value)`: sets a placeholder value (see below)
* `addConstraint($field, $callback)`: adds a custom constraint on a field, the 
  `callback` will be called with the field value and should return false if no
  problem, or an error string. If you just pass a closure to it, the closure will
  be called with the form passed as argument and can then do some tests involving
  multiple fields or form information.
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

## Creating form from string

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

echo $form->getValue('colour') . "\n";
// red

// Sets the color to blue
$form->setValue('colour', 'blue');

echo $form;
/* Will display:
<form method="post">
    <select name="colour" >
        <option selected="selected" value="blue">Blue</option>
        <option value="red">Red</option>
        <option value="green">Green</option>
    </select>
    <input type="hidden" name="posted_token" value="d293dc38017381b6086ff1a856c1e8fe43738c60" />
</form>
*/
```

## Mapping

You can also use `mapping` attribute to populate your form or to get back the form data in an array or
in an object, for instance:

```php
<?php

class Person
{
    protected $name;
    public function getName() { return $this->name; }
    public function setName($name) {
        $this->name = $name;
    }
}

$person = new Person;
$person->setName('Jack');

$form = new Gregwar\Formidable\Form('<form method="post">
    <input type="text" name="name" mapping="name" />
    </form>');

$form->setData($person);

echo $form;
/*
Will output something like:

<form method="post">
    <input required="required" type="text" name="name" value="Jack" />
    <input type="hidden" name="posted_token" value="aa27f437cc6127c244db14361fd614af51c79aac" />
</form>
*/
```

Note that the mapping uses the [Symfony PropertyAccessor](https://github.com/symfony/PropertyAccess),
you can then use accessor as in the example above to populate properties.

You can use:

* `getData($entity = array())`: populate and return entity with data populated
* `setData($entity)`: populate the form with the entity attributes

## Creating multiple sub-forms

You can add multiple sub-forms to a page using the `<multiple>` tag:

```html
<form method="post">
    Film name: <input type="text" name="film_name" mapping="name" />

    <h2>Actors</h2>
    <multiple name="actors" mapping="actors">
        First name: <input name="first_name" mapping="firstName" /><br />
        Last name: <input name="last_name" mapping="lastName" /><br />
    </multiple>
    <input type="submit" />
</form>
```

With this, the `<multiple>` can be used exactly like a field, but it will
contains an array of elements.

Some JS will be injected in the page and allow you to add/remove some 
elements.

You can use `min-entries` and `max-entries` constraint to set limits on the
number of entries in a multiple.

If you specify the same value for `min-entries` and `max-entries`, or specify
a value for `entries` (which is actually an alias to do it), the number ofr inputs
will be fixed and no javascript will be required.

## Adding dynamic data into the form

In some case, you'll want to add custom data into the form, there is two way to do this.

### First way: using the placeholders

The `{{ something }}` syntax allow you to simply inject data from the code, like this:

```php
<?php

$form = new Gregwar\Formidable\Form('<form method="post">
    Hello {{ name }}!
    </form>');

$form->setPlaceholder('name', 'Bob');

echo $form;
```

In the example above, the `{{ name }}` will be rendered as `Bob`.

Note that placeholders may be used anyway excepted in the `<form>` and input tags:

```php
<?php

$form = new Gregwar\Formidable\Form('<form method="post">
    <span style="color:{{ color }}">Hello</span>
    </form>');

$form->setPlaceholder('color', 'red');

echo $form;
```

### Second way: using PHP form

You can also write your form using PHP, like a template, for instance:

```php
<form>
    <?php echo $label; ?>: <input type="text" name="name" />
    <input type="submit" />
</form>
```

And then instanciate your form passing the template variables as a second argument:

```php
<?php

$form = new Gregwar\Formidable\Form('the-above-form.php', array('label' => 'Your name'));
```

The `$label` will be interpreted using PHP.

## Caching

For performances reasons, you may want to cache the parsed forms.

To do this, simply pass `true` as the third argument of the constructor:

```php
<?php

/**
 * Parsed data for the form will be serialized and stored in a cache file,
 * if you use this form often, this will offer you better performances.
 */
$form = new Gregwar\Formidable\Form('form.html', null, true);

```

This will use the [Gregwar/Cache](http://github.com/Gregwar/Cache) system, you will need to get
the composer dependencies of this repository or install it manually. By default, cache files
will be wrote in the `cache` directory from where the script is run.

Try to run the `performances.php` script in the `examples/` directory, this will give you an 
example of performance gain with cache.

You can also pass an instance of `Gregwar\Cache\Cache` as the third parameter, which will allow you
to set the cache directory.

## License

`Gregwar\Formidable` is under MIT License, have a look at the `LICENSE` file for more information.
