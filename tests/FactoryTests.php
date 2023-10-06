<?php

use Gregwar\Formidable\Factory;
use Gregwar\Formidable\Fields\TextField;
use PHPUnit\Framework\TestCase;

/**
 * Testing Formidable factory
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FactoryTests extends TestCase
{
    /**
     * Testing creating forms using the factory
     */
    public function testFactoryCreation()
    {
        $factory = new Factory;
        $form = $factory->getForm(__DIR__.'/files/factory/form.html');

        $html = "$form";
        $this->assertContains('test', $html);
        $this->assertEquals('Hello', $form->test);
    }

    /**
     * Testing adding a customized type
     */
    public function testFactoryCustomType()
    {
        $factory = new Factory;
        $factory->registerType('testing', TextField::class);

        $form = $factory->getForm(__DIR__.'/files/factory/testing.html');
        $html = "$form";
        $this->assertContains('text', $html);
        $this->assertEquals('Hello', $form->test);
    }
}
