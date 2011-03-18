<?php
/**
 * Class to generate the URL for a logo to be sent to the API
 * 
 * @author jfaustin
 *
 */
class Application_Form_LogoGenerator extends Zend_Form
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->setAttrib('id', 'logoGenerator')
	         ->setDecorators(array(
	                 'FormElements',
	                 array('HtmlTag'),
	                 'Form',
	         ));
	         
	    $logo = new Ncstate_Brand_Logo();
	    
	    $defaults = $logo->getOptions();
	    
	    $color = new Ncstate_Brand_Color();
	    $colors = $color->getColors();
	    
	    
        // Email address field
        $text = $this->createElement('textarea', 'text', array('label' => 'Text:'));
        $text->addValidator('StringLength', false, array(0, 256));
        $text->addFilter('StripTags');
        $text->setAttrib('maxlength', 256);
        $text->setAttrib('rows', 3);
        $text->setAttrib('cols', 35);
        $text->setValue('*NC STATE* UNIVERSITY');
        $text->setDescription('Use an asterisk * around a phrase to bold it.');
        
        $fontSize = $this->createElement('text', 'fontSize', array('label' => 'Font Size:'));
        $fontSize->addValidator('Int');
        $fontSize->addValidator('Between', false, array(10, 256));
        
        $lineSpacing = $this->createElement('text', 'lineSpacing', array('label' => 'Line Spacing:'));
        $lineSpacing->addValidator('Between', false, array(-100, 100));
        
        $fontColor = $this->createElement('hidden', 'fontColor');
        $fontColorSelector = $this->createElement('text', 'fontColorSelector', array('label' => 'Font Color:'));
        $fontColor->addValidator('InArray', false, array(array_keys($colors)));
        
        $leftTextOffset = $this->createElement('text', 'leftTextOffset', array('label' => 'Left offset of Text:'));
        $leftTextOffset->addValidator('Int');
        
        $baselineTextOffset = $this->createElement('text', 'baselineTextOffset', array('label' => 'Baseline offset of Text:'));
        $baselineTextOffset->addValidator('Int');
        
        $backgroundColor = $this->createElement('hidden', 'backgroundColor');
        $backgroundColorSelector = $this->createElement('text', 'backgroundColorSelector', array('label' => 'Background Color:'));
        $backgroundColor->addValidator('InArray', false, array(array_keys($colors)));
        
        $transparent = $this->createElement('checkbox', 'transparent', array('label' => 'Set Background Color to Transparent?'));
        $transparent->addValidator('Int');
        $transparent->addValidator('Between', false, array(0,1));
        
        $width = $this->createElement('text', 'width', array('label' => 'Image Width:'));
        $width->addValidator('Int');
        $width->addValidator('Between', false, array(1,2000));
        
        $height = $this->createElement('text', 'height', array('label' => 'Image Height:'));
        $height->addValidator('Int');
        $height->addValidator('Between', false, array(1,2000));
        
        $submit = $this->createElement('submit', 'submit', array('label' => 'Get Logo'));
        $submit->setDecorators(
            array(
                array('ViewHelper', array('helper' => 'formSubmit'))
            )
        );
		$reset = $this->createElement('button', 'resetButton', array('label' => 'Reset'));
        $reset->setDecorators(
            array(
                array('ViewHelper', array('helper' => 'formButton'))
            )
        );
        
        $this->addElements(array($text, $fontSize, $lineSpacing, $fontColorSelector, $leftTextOffset, $baselineTextOffset, $backgroundColorSelector, $transparent, $width, $height, $submit, $reset, $fontColor, $backgroundColor));
        
        $elements = $this->getElements();
        foreach ($elements as $e) {
            if (isset($defaults[$e->getName()])) {
                $e->setValue($defaults[$e->getName()]);
            }
        }
        
        $fontColorSelector->setValue('#' . $colors[$fontColor->getValue()]);
        $backgroundColorSelector->setValue('#' . $colors[$backgroundColor->getValue()]);
        return $this;
        
    }
}