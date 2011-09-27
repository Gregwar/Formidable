<?php

use Gregwar\DSD\Form;

/**
 * Tests des formulaires DSD
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FormTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Test que toString renvoie bien le getHtml
     */
    public function testToString()
    {
        $form = $this->getForm('test.html');

        $this->assertEquals((string)$form, $form->getHtml());
    }

    /**
     * Test que l'enctype passe en multipart sur des file
     */
    public function testEnctype()
    {
        $form = $this->getForm('enctype_normal.html');
        $this->assertFalse(strpos("$form", 'enctype='));

        $form = $this->getForm('enctype_file.html');
        $this->assertContains('enctype=', "$form");
    }

    /**
     * Test l'obtention de valeurs par défaut
     */
    public function testGetValues()
    {
        $form = $this->getForm('values.html');

        $this->assertEquals('Hello with spaces and "!', $form->message);
        $this->assertEquals('1', $form->gender);
        $this->assertEquals('blue', $form->color);
        $this->assertEquals('42', $form->getValue('checkme'));
        $this->assertEquals('Hello world, i\'m a long message', $form->area);
    }

    /**
     * Test la définition de valeurs
     */
    public function testSetValues()
    {
        $form = $this->getForm('test.html');
        $form->message = 'Setting a value';
        $form->choices = 1;
        $form->checkme = 1;

        $html = "$form";
        $this->assertContains('Setting a value', $html);
        $this->assertContains('selected=', $html);
        $this->assertContains('checked=', $html);
    }

    /**
     * Test la définition avec plusieurs valeurs
     */
    public function testSetMultipleValues()
    {
        $form = $this->getForm('test.html');

        $form->setValues(array(
            'message' => 'something',
            'choices' => '1',
            'color' => 'blue'
        ));

        $this->assertEquals('something', $form->message);
        $this->assertEquals('1', $form->choices);
        $this->assertEquals('blue', $form->color);
    }

    /**
     * Test que le jeton CSRF calculé est bien le même en le calculant
     * deux fois de suite
     */
    public function testCsrfTokenGeneration()
    {
        $form1 = $this->getForm('empty.html');
        $form2 = $this->getForm('empty.html');

        $this->assertEquals($form1->getToken(), $form2->getToken());
    }

    /**
     * Teste que posted() retourne bien vrai quand le jeton CSRF est dans
     * la requête
     */
    public function testCsrfTokenCheck()
    {
        $form = $this->getForm('empty.html');

        $_POST = array();
        $this->assertFalse($form->posted());

        $_POST = array('csrf_token' => $form->getToken());
        $this->assertTrue($form->posted());
    }

    /**
     * Test le rendu d'un champ requis et du test
     */
    public function testRequired()
    {
        $form = $this->getForm('required.html');
        $this->assertContains('required=', "$form");

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'name' => ''
        );

        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());

        $_POST['name'] = 'jack';
        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());
    }

    /**
     * Test le rendu d'un champ optionel et du test
     */
    public function testOptional()
    {
        $form = $this->getForm('optional.html');
        $this->assertNotContains('required=', "$form");

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'name' => ''
        );

        $form->posted();
        $this->assertEmpty($form->check());

        $_POST['name'] = 'jack';
        $form->posted();
        $this->assertEmpty($form->check());
    }

    /**
     * Test la longueur maximale
     */
    public function testMaxLength()
    {
        $form = $this->getForm('maxlength.html');

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'nick' => str_repeat('x', 100)
        );

        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $_POST['nick'] = str_repeat('x', 101);
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
    }

    /**
     * Test la longueur minimale
     */
    public function testMinLength()
    {
        $form = $this->getForm('minlength.html');

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'nick' => str_repeat('x', 10)
        );

        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $_POST['nick'] = str_repeat('x', 9);
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
    }

    /**
     * Test que la sortie redonnée à DSD redonne la même sortie
     */
    public function testOutIn()
    {
        $form = $this->getForm('out_in.html');
        $html = $form->getHtml();
        $otherForm = new Form($html);
        $otherHtml = $otherForm->getHtml();

        $this->assertEquals($html, $otherHtml);
    }

    private function getForm($file)
    {
        return new Form(__DIR__.'/files/form/'.$file);
    }
}
