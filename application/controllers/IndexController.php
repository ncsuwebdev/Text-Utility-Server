<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * Documentation page
     */
    public function indexAction()
    {
        $this->view->apiUrl = $this->_getApiUrl();
        
        $bColor = new Ncstate_Brand_Color();
        $this->view->colors = $bColor->getColors();
        
        $text = new Ncstate_Brand_Text();
        $this->view->options = $text->getOptions();
        $this->view->text = $text->getText();
    }
    
    /**
     * Generates the logo with the desired options
     */
    public function apiAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        $this->_helper->layout()->disableLayout();
        
        $text = new Ncstate_Brand_Text();
        
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
        
        $key = md5(serialize($options));
        
        // get the cache object
        $cache = Zend_Registry::get('cache');
        
        // Log the request
        $logger = Zend_Registry::get('logger');
        
        // load image from cache if available
        if (($imageSrc = $cache->load($key)) === false) {
            $text->setText($options['text'])
                 ->setOptions($options)
                 ;
                 
            $image = $text->createImage();
            
            // Capture output buffer of PNG for caching
            ob_start();
            imagepng($image);
            $imageSrc = ob_get_contents();
            ob_end_clean();
            
            $cache->save($imageSrc, $key);
            
            $logger->setEventItem('cachehit', 'no');
        } else {
            $logger->setEventItem('cachehit', 'yes');
        }
        
        $logger->setEventItem('ip', $_SERVER['REMOTE_ADDR']);
        $logger->info(http_build_query($options));
        
        header('Content-type: image/png');
        echo $imageSrc;
        
    }
    
    /**
     * Provides a demo of the API
     */
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
    
    public function validateAction()
    {
        $this->_helper->viewRenderer->setNeverRender();
        $this->_helper->layout()->disableLayout();
        
        $brandColor = new Ncstate_Brand_Color();
        $brandText = new Ncstate_Brand_Text();

        $params = $this->_getAllParams();
        
        $isBold = preg_match('/\*/i', $params['text']);
        $level = 'AA';
        $fontSize = $params['fontSize'];
        $fontColor = $params['fontColor'];
        $backgroundColor = $params['backgroundColor'];
        $text = $params['text'];
        
        try {
            $isValidColorContrast = $brandColor->isValidColorContrast($fontColor, $backgroundColor, $fontSize, $isBold, $level);
            
            $isValidWebsiteHeader = $brandColor->isValidWebsiteHeader($fontColor, $backgroundColor);
            
            $isValidText = $brandText->isValidText($text);
            
        } catch (Exception $e) {
            echo Zend_Json::encode(array('rc' => 0, 'message' => $e->getMessage()));
            return;
        }
        
                
        echo Zend_Json::encode(
            array(
        		'rc' => 1, 
        		'isValidColorContrast' => ($isValidColorContrast) ? 'yes' : 'no',
            	'isValidWebsiteHeader' => ($isValidWebsiteHeader) ? 'yes' : 'no',
                'isValidText'          => ($isValidText) ? 'yes' : 'no',
            )
        );
    }
    
    /**
     * Gets the URL for the API
     */
    protected function _getApiUrl()
    {
        return str_replace($this->view->baseUrl(), '', Zend_Registry::get('siteUrl'))
               . $this->view->url(array(), 'api', true);
    }
}

