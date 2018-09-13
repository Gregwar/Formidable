<?php
namespace Test\Gregwar\Formidable;

use Gregwar\Formidable\Factory;

/**
 * Testing Formidable factory
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing creating forms using the factory
     */
    public function testFactoryCreation()
    {
        $factory = new Factory;
        $form = $factory->getForm(__DIR__.'/fixtures/context/form.html');

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
        $factory->registerType('testing', '\Gregwar\Formidable\Fields\TextField');

        $form = $factory->getForm(__DIR__.'/fixtures/context/testing.html');
        $html = "$form";
        $this->assertContains('text', $html);
        $this->assertEquals('Hello', $form->test);
    }
}
