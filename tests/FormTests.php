<?php

use Gregwar\DSD\Form;

/**
 * Tests des formulaires DSD
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FormTests extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $form = $this->getForm('test.html');

        $this->assertEquals((string)$form, $form->getHtml());
    }

    public function testEnctype()
    {
        $form = $this->getForm('enctype_normal.html');
        $this->assertFalse(strpos("$form", 'enctype='));

        $form = $this->getForm('enctype_file.html');
        $this->assertTrue(false !== strpos("$form", 'enctype='));
    }

    public function testGetValues()
    {
        $form = $this->getForm('values.html');

        $this->assertEquals('Hello with spaces and "!', $form->message);
        $this->assertEquals('1', $form->gender);
        $this->assertEquals('blue', $form->color);
        $this->assertEquals('42', $form->getValue('checkme'));
        $this->assertEquals('Hello world, i\'m a long message', $form->area);
    }

    public function testSetValues()
    {
        $form = $this->getForm('test.html');
        $form->message = 'Setting a value';
        $form->choices = 1;

        $this->assertTrue(false !== strpos("$form", 'Setting a value'));
        $this->assertTrue(false !== strpos("$form", 'selected='));
    }

    public function testSetMultipleValues()
    {
        $form = $this->getForm('test.html');
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
