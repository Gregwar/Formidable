<?php

class Book
{
    public $name;
    public $authors;
}

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    Book name:
    <input type="text" name="book_name" mapping="name" />
    <hr />
    <multiple name="authors" mapping="authors" min-entries="2">
        <fieldset>
            First name: <input name="first_name" mapping="firstName" /><br />
            Last name: <input name="last_name" mapping="lastName" /><br />
            Age: <input type="int" name="age" min="7" optional />
        </fieldset>
    </multiple>
    <input type="submit" />
    </form>');
$form->setLanguage(new Gregwar\Formidable\Language\French);

$form->handle(function() use ($form) {
    var_dump($form->getData(new Book));
}, function($errors) {
    echo "Errors:<br />";
    foreach ($errors as $error) {
        echo "* $error<br/>";
    }
});

echo $form;
