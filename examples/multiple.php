<?php

class Film
{
    public $name;
    public $actors;
}

include(__DIR__.'/../autoload.php');

$form = new Gregwar\Formidable\Form('<form method="post">

    <h2>Film</h2>
    Film name:
    <input type="text" name="film_name" mapping="name" />
    <hr />

    <h2>Actors</h2>
    <multiple name="actors" mapping="actors" min-entries="2">
        <fieldset>
            First name: <input name="first_name" mapping="firstName" /><br />
            Last name: <input name="last_name" mapping="lastName" /><br />
            Age: <input type="int" name="age" min="7" mapping="age" optional />
            Gender:
            <select mapping="gender" name="gender">
                <option value="m">Male</option>
                <option value="f">Female</option>
            </select>
        </fieldset>
    </multiple>
    <input type="submit" />
    </form>', array(), true);

$form->handle(function() use ($form) {
    var_dump($form->getData(new Film));
}, function($errors) {
    echo "Errors:<br />";
    foreach ($errors as $error) {
        echo "* $error<br/>";
    }
});

echo $form;
