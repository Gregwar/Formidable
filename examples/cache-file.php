<?php
$_SESSION = array();

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('forms/performances.html', null, true);

echo $form;
