<?php

use Gregwar\Formidable\Form;
use Gregwar\Formidable\PostIndicator;

/**
 * Testing Formidable forms
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class FormTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Testing that toString() give the same thing as getHtml()
     */
    public function testToString()
    {
        $form = $this->getForm('test.html');

        $this->assertEquals((string)$form, $form->getHtml());
    }

    /**
     * Testing that Formidable guess if it should interpret the contents
     */
    public function testGuessPathOrContent()
    {
        $form = new Form('<form>
            <input type="text" name="foo" value="bar" />
        </form>');

        $this->assertEquals('bar', $form->foo);
    }

    /**
     * Testing that the enctype becomes multipart when there is file
     */
    public function testEnctype()
    {
        $form = $this->getForm('enctype_normal.html');
        $this->assertFalse(strpos("$form", 'enctype='));

        $form = $this->getForm('enctype_file.html');
        $this->assertContains('enctype=', "$form");
    }

    /**
     * Testing attributes handling
     */
    public function testAttributes()
    {
        $form = $this->getForm('attributes.html');
        
        $this->assertEquals('red rounded', $form->getAttribute('name', 'class'));
        $this->assertEquals('Your name', $form->getAttribute('name', 'title'));

        $form->setAttribute('name', 'title', 'Outside attribute');
        $this->assertContains('title="Outside attribute"', "$form");
    }

    /**
     * Testing default values
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
     * Testing defining values
     */
    public function testSetValues()
    {
        $form = $this->getForm('test.html');
        $form->message = 'Setting a value';
        $form->choices = '1';
        $form->checkme = '1';

        $html = "$form";
        $this->assertContains('value="Setting a value"', $html);
        $this->assertContains('selected=', $html);
        $this->assertContains('checked=', $html);
    }

    /**
     * Testing definition with multiple values
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
     * Testing that the CSRF token is t he same if computer twice on
     * the same file, and different for another file with a different form name
     */
    public function testCsrfTokenGeneration()
    {
        $form1 = $this->getForm('empty.html');
        $form2 = $this->getForm('empty.html');

        $this->assertNotEquals('', $form1->getToken());
        $this->assertEquals($form1->getToken(), $form2->getToken());

        $form3 = $this->getForm('empty2.html');
        $this->assertNotEquals($form1->getToken(), $form3->getToken());
    }

    /**
     * Testing that the generated secret is different
     */
    public function testCsrfSecretGeneration()
    {
        $form1 = $this->getForm('empty.html');
        $token1 = $form1->getToken();
        
        $form2 = $this->getForm('empty.html');
        $token2 = $form2->getToken();

        // Deleting session variales
        foreach ($_SESSION as $key => $v) {
            unset($_SESSION[$key]);
        }

        $form3 = $this->getForm('empty.html');
        $token3 = $form3->getToken();

        // Assert that the token is in the form
        $this->assertContains($token1, "$form1");

        // Tokens from two forms sharing the session should be equals
        $this->assertEquals($token1, $token2);

        // Tokens from two forms with the session destroyed should be different
        $this->assertNotEquals($token1, $token3);
    }

    /**
     * Testing a post indicator without session
     */
    public function testPostIndicator()
    {

        unset($_SESSION);
        $form1 = $this->getForm('post-indicator.html');
        $token1 = $form1->getToken();
        
        unset($_SESSION);
        $form2 = $this->getForm('post-indicator.html');
        $token2 = $form2->getToken();

        $this->assertEquals($token1, $token2);
        $this->assertContains($token1, "$form1");
        $this->assertContains($token1, "$form2");
    }

    /**
     * Testing posted() returns true when the csrd token is in the request
     */
    public function testCsrfTokenCheck()
    {
        $form = $this->getForm('empty.html');

        $_POST = array();
        $this->assertFalse($form->posted());

        $_POST = array(PostIndicator::$fieldName => $form->getToken());
        $this->assertTrue($form->posted());
    }

    /**
     * Testing the filling of the values in the form using post
     */
    public function testPostValue()
    {
        $form = $this->getForm('post-values.html');

        $_POST = array(
            PostIndicator::$fieldName => $form->getToken(),
            'message' => 'Hello with spaces and "!',
            'gender' => '1',
            'color' => 'blue',
            'checkme' => '42',
            'area' => 'Hello world, i\'m a long message'
        );

        $this->assertTrue($form->posted());

        $this->assertEquals('Hello with spaces and "!', $form->message);
        $this->assertEquals('1', $form->gender);
        $this->assertEquals('blue', $form->color);
        $this->assertEquals('42', $form->getValue('checkme'));
        $this->assertEquals('Hello world, i\'m a long message', $form->area);

        $this->assertContains('checked=', "$form");
        $this->assertContains('selected=', "$form");
        $this->assertContains('Hello with spaces and &quot;!', "$form");
        $this->assertContains('Hello world, i\'m a long message', "$form");
    }

    /**
     * Testing that Formidable output given back to Formidable gives the same output
     */
    public function testOutIn()
    {
        $form = $this->getForm('out_in.html');
        $html = $form->getHtml();
        $otherForm = new Form($html);
        $otherHtml = $otherForm->getHtml();

        $this->assertEquals($html, $otherHtml);
    }

    /**
     * Testing running the PHP interpreter on the form
     */
    public function testPHPInterpreter()
    {
        $form = $this->getForm('custom.php', array('label' => 'Hello world!'));
        $html = $form->getHtml();

        $this->assertNotContains('php', $html);
        $this->assertContains('Hello world!', $html);
    }

    /**
     * Testing that accessing a non-existent field raise an exception
     *
     * @expectedException              \InvalidArgumentException
     */
    public function testAccessingNotExistingField()
    {
        $form = $this->getForm('basic.html');

        $form->getField('titi');
    }

    public function testPlaceholder()
    {
        $form = $this->getForm('placeholder.html');

        $html = "$form";
        $this->assertNotContains('something', $html);

        $form->setPlaceholder('something', 'hello!');
        
        $html = "$form";
        $this->assertContains('hello!', $html);
    }

    /**
     * Testing caching a form
     */
    public function testCache()
    {
        $cache = new Gregwar\Cache\Cache;
        $cache->setCacheDirectory($this->getCacheDirectory());

        $form = $this->getForm('basic.html', null, $cache);
        $html = "$form";
        $this->assertContains('toto', $html);
        $this->assertEquals(false, $form->isCached);

        $form = $this->getForm('basic.html', null, $cache);
        $html = "$form";
        $this->assertContains('toto', $html);
        $this->assertEquals(true, $form->isCached);
    }

    private function getForm($file, $vars = array(), $cache = false)
    {
        return new Form(__DIR__.'/files/form/'.$file, $vars, $cache);
    }

    public function setup()
    {
        $_SESSION = array();
    }

    public function getCacheDirectory()
    {
        return __DIR__ . '/cache';
    }

    public function teardown()
    {
        $cacheDir = $this->getCacheDirectory();
        `rm -rf $cacheDir`;
    }
}
