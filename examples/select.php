<?php

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">
    <select name="colour">
        <option value="blue">Blue</option>
        <option selected value="red">Red</option>
        <option value="green">Green</option>
    </select>
    </form>');

echo $form->getValue('colour') . "\n";
// red

// Sets the color to blue
$form->setValue('colour', 'blue');

echo $form;
/* Will display:
<form method="post">
    <select name="colour" >
        <option selected="selected" value="blue">Blue</option>
        <option value="red">Red</option>
        <option value="green">Green</option>
    </select>
    <input type="hidden" name="csrf_token" value="d293dc38017381b6086ff1a856c1e8fe43738c60" />
</form>
*/
