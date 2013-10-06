<?php

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    <custom source="something" />
    </form>');

$form->source('something', 'Hello world!');

echo $form;
