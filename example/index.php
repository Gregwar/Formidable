<?php
session_start(); // required for CSRF
include(__DIR__.'/../autoload.php');
include('person.php');

$form = new Gregwar\Formidable\Form('forms/demoform.html');

// $form->setLanguage(new Gregwar\Formidable\Language\French);

$form->addConstraint('firstname', function($value) {
    if ($value[0] == 'P') {
        return 'The firstname should not begin with a P!';
    }
});

$form->source('animals', array(
    '3' => 'Zebra',
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
        <title>Formidable Demo</title>
    </head> 
    <body>
        <h1>Formidable Demo</h1>
        <?php if ($errors) { ?>
            <div style="color:red">
                <h2>Validations error</h1>
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
