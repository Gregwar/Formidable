<?php

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    How old are you? <input type="int" name="age" min="18" max="130" />
    </form>');

echo $form;
