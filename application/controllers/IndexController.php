<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->apiUrl = $this->_getApiUrl();
        
        $bColor = new Ncstate_Brand_Color();
        $this->view->colors = $bColor->getColors();
        
        $logo = new Ncstate_Brand_Logo();
        $this->view->options = $logo->getOptions();
        $this->view->boldText = $logo->getBoldText();
        $this->view->normalText = $logo->getNormalText();
    }
    
    public function apiAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        $this->_helper->layout()->disableLayout();
        
        $univLogo = new Ncstate_Brand_Logo();
        
        $options = array(
            'pathToFonts' => APPLICATION_PATH . '/fonts',
        );
        
        $options = array_merge($this->_getAllParams(), $options);
        
        $form = new Application_Form_LogoGenerator();
        
        if (!$form->isValid($options)) {
            foreach ($form->getMessages() as $key => $e) {
                unset($options[$key]);
            }
        }
        
        $univLogo->setBoldText($options['boldText'])
                 ->setNormalText($options['normalText'])
                 ->setOptions($options)
                 ;
                 
        $image = $univLogo->getImage();
        
        header('Content-type: image/png');
        imagepng($image);
    }
    
    public function demoAction()
    {
        $bColor = new Ncstate_Brand_Color();
        $this->view->colors = Zend_Json::encode($bColor->getColors());
        
        $form = new Application_Form_LogoGenerator();
        $this->view->form = $form;
        
        $this->view->apiUrl = $this->_getApiUrl();
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.colorPicker.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/index/index.js');
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/index/index.css');        
    }
    
    protected function _getApiUrl()
    {
        return str_replace($this->view->baseUrl(), '', Zend_Registry::get('siteUrl'))
               . $this->view->url(array(), 'api', true);
    }
}

