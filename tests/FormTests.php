<?php

use Gregwar\Formidable\Form;

/**
 * Tests des formulaires Formidable
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
     * Test que Formidable devine bien s'il doit inclure ou interpreter le contenu
     */
    public function testGuessPathOrContent()
    {
        $form = new Form('<form>
            <input type="text" name="foo" value="bar" />
        </form>');

        $this->assertEquals('bar', $form->foo);
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
     * Teste la manipulation des attributs
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
        $form->choices = '1';
        $form->checkme = '1';

        $html = "$form";
        $this->assertContains('value="Setting a value"', $html);
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

        $this->assertNotEquals('', $form1->getToken());
        $this->assertEquals($form1->getToken(), $form2->getToken());

        $form3 = $this->getForm('empty2.html');
        $this->assertNotEquals($form1->getToken(), $form3->getToken());
    }

    /**
     * Test que le secret généré est bien différent
     */
    public function testCsrfSecretGeneration()
    {
        $form1 = $this->getForm('empty.html');
        $token1 = $form1->getToken();
        $_SESSION['formidable_secret'] = null;
        $form2 = $this->getForm('empty.html');
        $token2 = $form2->getToken();

        $this->assertNotEquals($token1, $token2);
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

    /**
     * Test du bon pré-remplissage des valeurs à l'aide de POST()
     */
    public function testPostValue()
    {
        $form = $this->getForm('post-values.html');

        $_POST = array(
            'csrf_token' => $form->getToken(),
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
     * Test que la sortie redonnée à Formidable redonne la même sortie
     */
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
