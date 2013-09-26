<?php // age.php
session_start(); // sans session, CSRF ne marchera pas
include(__DIR__.'/Formidable/Form.php');

$form = new Gregwar\Formidable\Form('forms/age.html');
