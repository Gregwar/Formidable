<?php

use Gregwar\DSD\Parser;
use Gregwar\DSD\ParserException;

/**
 * Tests du parser DSD
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class ParserTests extends \PHPUnit_Framework_TestCase
{
    public function testBasicParse()
    {
        $parser = $this->getParser('basic.html');

        $fields = $parser->getFields();

        $this->assertEquals(1, count($fields));
        $this->assertArrayHasKey('foo', $fields);
    }

    public function testDefaultValues()
    {
        $parser = $this->getParser('values.html');

        $fields = $parser->getFields();

        $this->assertArrayHasKey('message', $fields);
        $this->assertEquals('Hello!', $fields['message']->getValue());

        $this->assertArrayHasKey('gender', $fields);
        $this->assertEquals('1', $fields['gender']->getValue());

        $this->assertArrayHasKey('color', $fields);
        $this->assertEquals('blue', $fields['color']->getValue());

        $this->assertArrayHasKey('checkme', $fields);
        $this->assertEquals('42', $fields['checkme']->getValue());
    }

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
     * @expectedException               Gregwar\DSD\ParserException
     * @expectedExceptionMessage        Le formulaire DSD doit avoir une balise <form>
     */
    public function testFormTagPresence()
    {
        $this->getParser('form_presence.html');
    }

    /**
     * @expectedException               Gregwar\DSD\ParserException
     * @expectedExceptionMessage        <input> non typé
     */
    public function testUntypedInputError()
    {
        $this->getParser('untyped_input.html');
    }

    /**
     * @expectedException               Gregwar\DSD\ParserException
     * @expectedExceptionMessage        <option> en dehors d'un <select>
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
