<?php

use Gregwar\Formidable\Parser;
use Gregwar\Formidable\ParserException;

/**
 * Testing Formidable parser
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class ParserTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Testing the returns of a basic parse
     */
    public function testBasicParse()
    {
        $parser = $this->getParser('basic.html');

        $fields = $parser->getFields();

        $this->assertEquals(1, count($fields));
        $this->assertArrayHasKey('foo', $fields);

        $this->assertEquals('foo', $fields['foo']->getName());
    }

    /**
     * Testing field types
     */
    public function testTypes()
    {
        $parser = $this->getParser('types.html');

        $fields = $parser->getFields();

        $this->assertEquals(9, count($fields));

        $this->assertInstanceOf('Gregwar\Formidable\Fields\TextField', $fields['name']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\EmailField', $fields['email']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\PasswordField', $fields['pass']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\FileField', $fields['picture']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\HiddenField', $fields['cache']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\IntField', $fields['age']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\Select', $fields['choices']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\Radios', $fields['radio']);
        $this->assertInstanceOf('Gregwar\Formidable\Fields\Textarea', $fields['area']);
    }

    /**
     * Testing that attributes access works
     */
    public function testAttributes()
    {
        $parser = $this->getParser('attributes.html');

        $fields = $parser->getFields();

        $this->assertTrue($fields['name']->hasAttribute('class'));
        $this->assertTrue($fields['name']->hasAttribute('title'));

        $this->assertEquals('red rounded', $fields['name']->getAttribute('class'));
        $this->assertEquals('Your name', $fields['name']->getAttribute('title'));
    }

    /**
     * Testing default values getting
     */
    public function testDefaultValues()
    {
        $parser = $this->getParser('values.html');

        $fields = $parser->getFields();

        $this->assertArrayHasKey('message', $fields);
        $this->assertEquals('Hello with spaces and "!', $fields['message']->getValue());

        $this->assertArrayHasKey('gender', $fields);
        $this->assertEquals('1', $fields['gender']->getValue());

        $this->assertArrayHasKey('color', $fields);
        $this->assertEquals('blue', $fields['color']->getValue());

        $this->assertArrayHasKey('checkme', $fields);
        $this->assertEquals('42', $fields['checkme']->getValue());

        $this->assertArrayHasKey('area', $fields);
        $this->assertEquals('Hello world, i\'m a long message', $fields['area']->getValue());
    }

    /**
     * Testing that the form header works
     */
    public function testHead()
    {
        $parser = $this->getParser('head.html');

        $head = $parser->getHead();
        $this->assertNotNull($head);

        $this->assertTrue($head->has('action'));
        $this->assertTrue($head->has('action'));
        $this->assertFalse($head->has('enctype'));

        $this->assertEquals('post', $head->get('method'));
        $this->assertEquals('/post.php', $head->get('action'));

        $head->set('method', 'get');

        $this->assertEquals('get', $head->get('method'));
    }

    /**
     * A form without <form> value is not correct
     *
     * @expectedException               Gregwar\Formidable\ParserException
     * @expectedExceptionMessage        The Formidable form should have a <form> tag
     */
    public function testFormTagPresence()
    {
        $this->getParser('form_presence.html');
    }

    /**
     * Testing that untyped input should become a field of fallback type
     */
    public function testUntypedInputFallback()
    {
        $parser = $this->getParser('untyped_input.html');
        $fields = $parser->getFields();

        $this->assertInstanceOf('Gregwar\Formidable\Fields\TextField', $fields['test']);
    }

    /**
     * Testing that an option out of select raise an error
     *
     * @expectedException               Gregwar\Formidable\ParserException
     * @expectedExceptionMessage        <option> should always be in a <select>
     */
    public function testOptionOutOfSelectException()
    {
        $this->getParser('option_out_of_select.html');
    }

    private function getParser($file)
    {
        return new Parser(file_get_contents(__DIR__.'/files/parser/'.$file));
    }
}
