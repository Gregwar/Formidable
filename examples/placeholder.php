<?php

include(__DIR__ . '/../vendor/autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    <span style="color:{{ color }}">Hello</span>
    </form>');

$form->setPlaceholder('color', 'red');
echo $form;
