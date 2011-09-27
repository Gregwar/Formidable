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
     * Test le rendu d'un champ requis
     */
    public function testRequired()
    {
        $form = $this->getForm('required.html');
        $this->assertContains('required=', "$form");
    }

    /**
     * Test le rendu d'un champ optionel
     */
    public function testOptional()
    {
        $form = $this->getForm('optional.html');
        $this->assertNotContains('required=', "$form");
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
