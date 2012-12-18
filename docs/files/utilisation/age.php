<?php // age.php
include(__DIR__.'/../../DSD/Form.php');

$form = new Gregwar\DSD\Form('forms/age.html');

if ($form->posted()) {
    echo 'Bonjour '.htmlspecialchars($form->prenom).' !';
}

echo $form;
