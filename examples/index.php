<?php
session_start(); // required for CSRF
include(__DIR__.'/../autoload.php');
include('person.php');

$form = new Gregwar\Formidable\Form('forms/demoform.html', array());

// Example for setting language to french
// $form->setLanguage(new Gregwar\Formidable\Language\French);

$form->addConstraint('firstname', function($value) {
    if ($value[0] == 'P') {
        return 'The firstname should not begin with a P!';
    }
});

$form->source('animals', array(
    'zebra' => 'Zebra',
    'bonobo' => 'Bonobo'
));

$errors = $form->handle(function() use ($form) {
    $data = $form->getValues();
    var_dump($data);
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
