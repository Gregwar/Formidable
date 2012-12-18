<?php

/**
 * Définit les valeurs des champs en utilisant les
 * attributs mapping
 */
$form->setDatas(array(
    'prenom' => 'Pierre',
    'age' => 34
));

$datas = $form->getDatas();
/**
 * Retourne :
 * array(
 *   'prenom' => 'Pierre',
 *   'age' => 34
 * );
 */

class Person {
    public $prenom;
    public $age;
}

$jacques = new Person;
$jacques->prenom = 'Jacques';
$jacques->age = 50;

/**
 * Définit les valeurs des champs en utilisant les 
 * propriétés de l'instance passée
 */
$form->setDatas($jacques);

/**
 * Retourne une instance de personne en ayant
 * peuplé les champs correctement en utilisant
 * le mapping
 */
$person = $form->getDatas(new Person);
