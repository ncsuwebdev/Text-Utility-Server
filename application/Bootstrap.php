<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Fallback to ZF 1.5x loader
     */
    protected function _initAutoload()
    {
        require_once 'Zend/Loader/Autoloader.php';
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->setFallbackAutoloader(true);
    }

    /**
     * Sets up the base URL and site URL vars
     */
    protected function _initUrl()
    {
        $baseUrl = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/public/index.php'));
        
        $zcf = Zend_Controller_Front::getInstance();
        
        $zcf->setBaseUrl($baseUrl);
        
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = substr(
            strtolower($_SERVER["SERVER_PROTOCOL"]),
            0,
            strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")
        ) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
        $url = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $baseUrl;
        
        Zend_Registry::set('siteUrl', $url);
    }
    
    /**
     * Sets up configuration options for the application
     */
    protected function _initOptions()
    {
        $options = $this->getOptions();
        
        if (isset($options['options'])) {
            foreach ($options['options'] as $key => $value) {
                Zend_Registry::set('option-' . $key, $value);
            }
        }
    }
    
    /**
     * Initializes the cache
     */
    protected function _initCache()
    {
        $frontendOptions = array(
            'lifetime'                => null, // cache lifetime is forever
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => Zend_Registry::get('option-cacheDir'),
        );
        
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        $cache->setOption('caching', (boolean)Zend_Registry::get('option-cacheOn'));
        
        Zend_Registry::set('cache', $cache);
    }
    
    /**
     * Initializes the logger
     */
    protected function _initLog()
    {
        $logger = new Zend_Log();
           
        if (Zend_Registry::get('option-logOn')) {
            $writer = new Zend_Log_Writer_Stream(realpath(Zend_Registry::get('option-logDir') . '/' . Zend_Registry::get('option-logFile')));
            
            $format = '%timestamp%, %ip%, %cachehit%, %message%' . PHP_EOL;
            $formatter = new Zend_Log_Formatter_Simple($format);
          
            $writer->setFormatter($formatter);
        } else {
            $writer = new Zend_Log_Writer_Null;
        }
        
        $logger->addWriter($writer);
        
        Zend_Registry::set('logger', $logger);
    }
}