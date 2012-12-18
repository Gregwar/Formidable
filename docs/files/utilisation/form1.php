<?php // age.php
session_start(); // sans session, CSRF ne marchera pas
include(__DIR__.'/DSD/Form.php');

$form = new Gregwar\DSD\Form('forms/age.html');
