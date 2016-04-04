<?php

class Plugins_Loginplugin extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $authorization = Zend_Auth::getInstance();

        if (!$authorization->hasIdentity() && $request->getControllerName() != "login") {
            $request->setControllerName('index');
            $request->setActionName('index');
        } else {
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->auth_info = $authorization->getIdentity();
            if (!empty($view->auth_info) && $view->auth_info->type == "user") {
                if ($request->getControllerName() == "user") {
                    $request->setControllerName('index');
                    $request->setActionName('index');
                }
            }
        }
    }

}
