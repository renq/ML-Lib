<?php

use ML\Forms\Element\Text;
use ML\Forms\Form;

require_once __DIR__ . '/../../ML/loader.php';


class FormsFormTest extends PHPUnit_Framework_TestCase {
	
	
    public function testAutoload() {
        $form = new Form();
        $this->assertContains('<form', (string)$form, 'No <form> tag');
        
        $form->setAttribute('method', 'post');
        $this->assertContains('method="post"', (string)$form, 'Method should be post');
        $this->assertEquals('post', $form->getAttribute('method'));
        
        $form->setAttribute('method', 'get');
        $this->assertContains('method="get"', (string)$form, 'Method should be get');
        $this->assertEquals('get', $form->getAttribute('method'));
    }
    

    public function testAddElementsAutoload() {
        $text = new Text('text-element');
        $text->setLabel('Element')->setValue('some text');
        $this->assertEquals('some text', $text->getValue());
        $this->assertEquals('some text', $text->getAttribute('value'));
        $this->assertEquals('text-element', $text->getName());
        $this->assertEquals('text-element', $text->getAttribute('name'));
        
        $form = new Form();
        $form->addElement($text);
        
        $this->assertContains('input', (string)$form);
        $this->assertContains('name="text-element"', (string)$form);
        $this->assertContains('value="some text"', (string)$form);
    }
    
    
    public function testSetGetFormName() {
        $form = new Form('form-name');
        $this->assertEquals('form-name', $form->getName());
    }
    
        
    
}

