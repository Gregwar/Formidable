<?php

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    Book name:
    <input type="text" name="book_name" />
    <hr />
    <multiple name="authors">
        <fieldset>
            First name: <input name="first_name" /><br />
            Last name: <input name="last_name" /><br />
        </fieldset>
    </multiple>
    <input type="submit" />
    </form>');

$form->handle();
var_dump($form->getValues());

echo $form;
