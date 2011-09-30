<?php

use Gregwar\DSD\Form;

/**
 * Tests des contraintes
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class ConstraintsTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Test le rendu d'un champ requis et du test
     */
    public function testRequired()
    {
        $form = $this->getForm('required.html');
        $this->assertContains('required=', "$form");

        $this->assertAccept($form, array(
            'name' => 'jack'
        ));

        $this->assertRefuse($form, array(
            'name' => ''
        ));

        $this->assertAccept($form, array(
            'name' => '0'
        ));
    }

    /**
     * Test d'envoi d'un array sur une valeur simple
     */
    public function testArray()
    {
        $form = $this->getForm('required.html');

        $this->assertRefuse($form, array(
            'name' => array('xyz')
        ));
    }

    /**
     * Test le rendu d'un champ optionel et du test
     */
    public function testOptional()
    {
        $form = $this->getForm('optional.html');
        $this->assertNotContains('required=', "$form");

        $this->assertAccept($form, array(
            'name' => ''
        ));

        $this->assertAccept($form, array(
            'name' => 'Jack'
        ));
    }

    /**
     * Test la longueur maximale
     */
    public function testMaxLength()
    {
        $form = $this->getForm('maxlength.html');
        
        $this->assertContains('maxlength', "$form");

        $this->assertAccept($form, array(
            'nick' => str_repeat('x', 100)
        ));

        $this->assertRefuse($form, array(
            'nick' => str_repeat('x', 101)
        ));
    }

    /**
     * Test la longueur minimale
     */
    public function testMinLength()
    {
        $form = $this->getForm('minlength.html');
        
        $this->assertNotContains('minlength', "$form");

        $this->assertAccept($form, array(
            'nick' => str_repeat('x', 10)
        ));

        $this->assertRefuse($form, array(
            'nick' => str_repeat('x', 9)
        ));
    }

    /**
     * Test de regex=""
     */
    public function testRegex()
    {
        $form = $this->getForm('regex.html');

        $this->assertNotContains('regex', "$form");

        $this->assertAccept($form, array(
            'nick' => 'hello'
        ));

        $this->assertRefuse($form, array(
            'nick' => 'hm hm'
        ));
    }

    /**
     * Test de min="" et max=""
     */
    public function testMinMax()
    {
        $form = $this->getForm('minmax.html');

        $this->assertNotContains('min', "$form");
        $this->assertNotContains('max', "$form");

        $this->assertAccept($form, array(
            'num' => 7
        ));

        $this->assertRefuse($form, array(
            'num' => 3
        ));

        $this->assertRefuse($form, array(
            'num' => 13
        ));
    }

    /**
     * Test de contrainte custom
     */
    public function testCustomConstraint()
    {
        $form = $this->getForm('custom.html');

        $form->addConstraint('name', function($value) {
            if ($value[0] == 'J') {
                return 'Le nom ne doit pas commencer par J';
            }
        });

        $this->assertAccept($form, array(
            'name' => 'Paul'
        ));

        $this->assertRefuse($form, array(
            'name' => 'Jack'
        ));
    }

    /**
     * Test de contrainte custom
     */
    public function testCaptcha()
    {
        $form = $this->getForm('captcha.html');
        $html = "$form";

        $this->assertContains('<img', $html);
        $this->assertContains('code', $html);
        $this->assertContains('type="text"', $html);

        $captchaValue = $form->get('code')->getCaptchaValue();

        $this->assertNotContains($captchaValue, $html);

        $this->assertAccept($form, array(
            'code' => $captchaValue
        ));

        $form = $this->getForm('captcha.html');
        $html = "$form";

        $this->assertRefuse($form, array(
            'code' => 'xxx'
        ));
    }

    /**
     * Test de non-réutilisabilité du CAPTCHA
     */
    public function testCaptchaNotReusable()
    {
        $form = $this->getForm('captcha.html');
        $html = "$form";

        $captchaValue = $form->get('code')->getCaptchaValue();

        $this->assertAccept($form, array(
            'code' => $captchaValue
        ));

        $this->assertRefuse($form, array(
            'code' => $captchaValue
        ));
    }

    /**
     * Test de valeur postée n'étant pas dans un select
     */
    public function testSelectOut()
    {
        $form = $this->getForm('select.html');

        $this->assertAccept($form, array(
            'city' => 'la'
        ));

        $this->assertRefuse($form, array(
            'city' => 'xy'
        ));
    }

    /**
     * Test des multiples
     */
    public function testMultiple()
    {
        $form = $this->getForm('multiple.html');
        $html = "$form";

        $this->assertContains('<script', $html);
        $this->assertContains('<a', $html);

        $this->assertRefuse($form, array(
            'names' => ''
        ));

        $this->assertAccept($form, array(
            'names' => array('a', 'b')
        ));

        $this->assertRefuse($form, array(
            'names' => array(str_repeat('x', 25))
        ));

        $this->assertRefuse($form, array(
            'names' => array(array('a', 'b'))
        ));
    }

    /**
     * Test qu'on ne peut pas changer les readonly
     */
    public function testReadOnly()
    {
        $form = $this->getForm('readonly.html');
        $html = "$form";

        $this->assertContains('Jack', $html);
        $this->assertContains('selected=', $html);

        $this->assertAccept($form, array(
            'nom' => 'Jack',
            'color' => 'g'
        ));

        $this->assertRefuse($form, array(
            'nom' => 'Jack',
            'color' => 'y'
        ));
    }

    /**
     * Teste le reset
     */
    public function testReset()
    {
        $form = $this->getForm('reset.html');

        $this->assertEquals('Jack', $form->name);

        $this->assertAccept($form, array(
            'name' => 'Paul'
        ));

        $this->assertEquals('Paul', $form->name);
        $form->reset();
        $this->assertEquals('Jack', $form->name);
    }

    /**
     * Test du sourcage des <options>
     */
    public function testOptions()
    {
        $form = $this->getForm('options.html');
        $html = "$form";

        $this->assertNotContains('option', $html);
        $this->assertNotContains('pretty', $html);
        $this->assertNotContains('Cat', $html);

        $form->source('animals', array(
            '1' => 'Cat',
            '2' => 'Dog', 
            '3' => 'Zebra'
        ));

        $html = "$form";

        $this->assertContains('option', $html);
        $this->assertContains('pretty', $html);
        $this->assertContains('Cat', $html);

        $this->assertAccept($form, array(
            'animal' => 2
        ));

        $this->assertRefuse($form, array(
            'animal' => 4
        ));
    }

    /**
     * Test des multiradio
     */
    public function testMultiradio()
    {
        $form = $this->getForm('multiradio.html');
        $html = "$form";

        $this->assertNotContains('radio', $html);
        $this->assertNotContains('pretty', $html);
        $this->assertNotContains('Cat', $html);

        $form->source('animals', array(
            '1' => 'Cat',
            '2' => 'Dog',
            '3' => 'Zebra'
        ));

        $html = "$form";

        $this->assertContains('radio', $html);
        $this->assertContains('pretty', $html);
        $this->assertContains('Cat', $html);

        $this->assertAccept($form, array(
            'animal' => '2'
        ));

        $this->assertContains('checked', "$form");

        $this->assertRefuse($form, array(
            'animal' => '4'
        ));

        $this->assertRefuse($form, array(
            'animal' => ''
        ));
    }

    /**
     * Test de multiradio optionnel
     */
    public function testOptionalMultiradio()
    {
        $form = $this->getForm('multiradio_optional.html');

        $form->source('animals', array(
            '1' => 'Cat',
            '2' => 'Dog',
            '3' => 'Zebra'
        ));

        $this->assertAccept($form, array(
            'animal' => '2'
        ));

        $this->assertAccept($form, array(
            'animal' => ''
        ));
    }

    /**
     * Test du multicheckbox
     */
    public function testMultiCheckBox()
    {
        $form = $this->getForm('multicheckbox.html');
        $html = "$form";

        $this->assertNotContains('checkbox', $html);
        $this->assertNotContains('pretty', $html);
        $this->assertNotContains('Cat', $html);

        $form->source('animals', array(
            '1' => 'Cat',
            '2' => 'Dog',
            '3' => 'Zebra'
        ));

        $html = "$form";

        $this->assertContains('checkbox', $html);
        $this->assertContains('pretty', $html);
        $this->assertContains('Cat',$html);

        $this->assertAccept($form, array(
            'animals' => array('1')
        ));

        $this->assertAccept($form, array(
            'animals' => array('1', '3')
        ));

        $this->assertContains('checked', "$form");

        $this->assertAccept($form, array(
            'animals' => array()
        ));

        $this->assertNotContains('checked', "$form");
    }

    /**
     * Test qu'un formulaire accepte les données fournies
     */
    private function assertAccept($form, $data) {
        $_POST = $data;
        $_POST['csrf_token'] = $form->getToken();
        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());
    }

    /**
     * Test qu'un formulaire rejette les données fournies
     */
    private function assertRefuse($form, $data) {
        $_POST = $data;
        $_POST['csrf_token'] = $form->getToken();
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
    }

    private function getForm($file)
    {
        return new Form(__DIR__.'/files/form/'.$file);
    }
}
