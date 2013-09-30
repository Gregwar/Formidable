<?php

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    <select name="animal">
        <options source="animals" />
    </select>
    </form>');

$form->source('animals', array('Cat', 'Dog'));
$form->animal = 1;

echo $form;
