<?
session_start();
include("DSDCaptcha.php");

$c = new DSDCaptcha();
$_SESSION["DSD_Captcha"] = $c->value;
$c->display();
