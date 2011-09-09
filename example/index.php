<?php
session_start(); // required for CSRF
include(__DIR__.'/../Form.php');

$form = new Gregwar\DSD\Form('forms/demoform.html');
$errors = array();

if ($form->posted()) {
    $errors = $form->check();
}

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
                <h2>Error in validation</h1>
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
