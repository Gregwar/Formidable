<?php
session_start(); // required for CSRF
include(__DIR__.'/../Form.php');

include('person.php');

$form = new Gregwar\DSD\Form('forms/demoform.html');

$form->addConstraint('prenom', function($value) {
    if ($value[0] == 'P') {
	return 'Le prénom ne doit pas commencer par un P !';
    }
});

$form->source('animaux', array(
    '3' => 'Zèbre',
    '4' => 'Bonobo'
));

$errors = $form->handle(function($datas) {
    print_r($datas);
});

?>
<!DOCTYPE html>
<html>
    <meta charset="utf-8" />
    <head>
        <title>DSD Demo</title>
    </head> 
    <body>
        <h1>DSD Demo</h1>
        <?php if ($errors) { ?>
            <div style="color:red">
                <h2>Erreurs de validation</h1>
                <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo $error; ?></li>
                <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php echo $form; ?>
    </body>
</html>
