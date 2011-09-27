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

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'name' => ''
        );

        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());

        $_POST['name'] = 'jack';
        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $_POST['name'] = '0';
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
        
        $this->assertNotContains('minlength', "$form");

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
     * Test de regex=""
     */
    public function testRegex()
    {
        $form = $this->getForm('regex.html');

        $this->assertNotContains('regex', "$form");

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'nick' => 'hello'
        );

        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $_POST['nick'] = 'he he';
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
    }

    /**
     * Test de min="" et max=""
     */
    public function testMinMax()
    {
        $form = $this->getForm('minmax.html');

        $this->assertNotContains('min', "$form");
        $this->assertNotContains('max', "$form");

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'num' => '7'
        );

        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $_POST['num'] = '3';
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());

        $_POST['num'] = '13';
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
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

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'name' => 'Paul'
        );

        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $_POST['name'] = 'Jack';
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
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

        $_POST = array(
            'csrf_token' => $form->getToken(),
            'code' => $form->get('code')->getCaptchaValue()
        );

        $this->assertTrue($form->posted());
        $this->assertEmpty($form->check());

        $form = $this->getForm('captcha.html');
        $html = "$form";
        $_POST['code'] = 'xdz';
        $this->assertTrue($form->posted());
        $this->assertNotEmpty($form->check());
    }

    private function getForm($file)
    {
        return new Form(__DIR__.'/files/form/'.$file);
    }
}
