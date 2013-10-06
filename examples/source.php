<?php

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    <select name="animal">
        <options source="animals" />
    </select>
    </form>');

$form->source('animals', array(
    'cat' => 'Cat',
    'dog' => 'Dog'
));

$form->setValue('animal', 'dog');

echo $form;
