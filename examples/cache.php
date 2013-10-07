<?php
$_SESSION = array();

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    What\'s your name? <input type="text" name="nom" /><br />
    <input type="submit" />
    </form>', null, true);

$form->setValue('nom', 'Jack');

echo $form;
