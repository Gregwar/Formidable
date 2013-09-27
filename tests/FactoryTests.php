<?php

use Gregwar\Formidable\Factory;

/**
 * Tests du factory de formulaires Formidable
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FactoryTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Test de création de formulaire à l'aide du factory
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
     * Test d'enregistrement d'un type personnalisé
     */
    public function testFactoryCustomType()
    {
        $factory = new Factory;
        $factory->registerType('testing', '\Gregwar\Formidable\Fields\TextField');

        $form = $factory->getForm(__DIR__.'/files/factory/testing.html');
        $html = "$form";
        $this->assertContains('text', $html);
        $this->assertEquals('Hello', $form->test);
    }
}
