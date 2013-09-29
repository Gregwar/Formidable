<?php

include(__DIR__.'/../autoload.php');

class Person
{
    protected $name;
    public function getName() { return $this->name; }
    public function setName($name) {
        $this->name = $name;
    }
}

$person = new Person;
$person->setName('Jack');

$form = new Gregwar\Formidable\Form('<form method="post">
    <input type="text" name="name" mapping="name" />
    </form>');

$form->setData($person);

echo $form;
/*
Will output something like:

<form method="post">
    <input required="required" type="text" name="name" value="Jack" />
    <input type="hidden" name="csrf_token" value="aa27f437cc6127c244db14361fd614af51c79aac" />
</form>
*/
