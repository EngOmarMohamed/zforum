<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initLogin() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Plugins_Loginplugin());
    }

    protected function _initPlaceholders() {
        $this->bootstrap('View');
        $view = $this->getResource('View');
//        $view = $layout->getView('layout');
//        $path = realpath(APPLICATION_PATH . '/../public/');
//        $baseURL = $view->baseUrl();
        
//        print_r($baseURL);die("===");

        $view->doctype('XHTML1_STRICT');
        //Meta
        $view->headMeta()->appendName('keywords', 'framework, PHP')->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        // Set the initial title and separator:
        $view->headTitle('ZForum')
                ->setSeparator(' :: ');
        // Set the initial stylesheet:
        $view->headLink()->prependStylesheet($view->baseUrl()."/static/css/bootstrap.min.css");
        $view->headLink()->appendStylesheet($view->baseUrl()."/static/css/style.css");
        $view->headLink()->appendStylesheet($view->baseUrl()."/static/css/style1.css");
        $view->headLink()->appendStylesheet($view->baseUrl()."/static/font-awesome/css/font-awesome.min.css");
        $view->headLink()->appendStylesheet($view->baseUrl()."/static/css/form-elements.css");
        
        // Set the initial JS to load:
        $view->headScript()->prependFile($view->baseUrl()."/static/js/jquery-1.11.2.js");
        $view->headScript()->appendFile($view->baseUrl()."/static/js/bootstrap.min.js");
        $view->headScript()->appendFile($view->baseUrl()."/static/js/jquery.backstretch.min.js");
        $view->headScript()->appendFile($view->baseUrl()."/static/js/retina-1.1.0.min.js");
        $view->headScript()->appendFile($view->baseUrl()."/static/js/scripts.js");
        $view->headScript()->appendFile($view->baseUrl()."/static/js/index.js");

    }


}

