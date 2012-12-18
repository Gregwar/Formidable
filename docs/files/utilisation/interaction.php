<?php

// Définit la valeur du champ "prenom"
$form->prenom = 'Pierre';

// Obtient la valeur du champ "prenom"
echo $form->prenom;

// Définit des valeurs dans le formulaire
$form->setValues(array(
    'prenom' => 'Jacques';
));
