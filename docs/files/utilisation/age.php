<?php // age.php
include(__DIR__.'/../../Formidable/Form.php');

$form = new Gregwar\Formidable\Form('forms/age.html');

if ($form->posted()) {
    echo 'Bonjour '.htmlspecialchars($form->prenom).' !';
}

echo $form;
