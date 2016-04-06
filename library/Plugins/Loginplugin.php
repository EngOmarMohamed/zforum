<?php

class Plugins_Loginplugin extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
//        die("===dddd");
        $authorization = Zend_Auth::getInstance();
        $controllers = array(
            "index" => array("change-availability"),
            "login" => array("profile", "logout"),
            "forum" => array("change-permit", "add", "edit", "delete"),
            "post" => array("change-permit", "index", "add", "edit", "delete"),
            "post" => array("index", "add", "edit", "delete"),
            "category" => array("index", "add", "edit", "delete"),
            "user" => array("index", "add", "edit", "delete", "change-type", "change-ban")
        );

        if (!$authorization->hasIdentity() && (in_array($request->getControllerName(), array_keys($controllers)) && in_array($request->getActionName(), $controllers[$request->getControllerName()]))) {
            $request->setControllerName('error');
            $request->setActionName('error');
        } else {
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->auth_info = $authorization->getIdentity();
            if (!empty($view->auth_info)) {
                $sysModel = new Application_Model_DbTable_CloseSystem();
                $userModel = new Application_Model_DbTable_User();
                $info = $authorization->getIdentity();
                $id = $info->id;

//                    die($view->auth_info->type);
                if ($userModel->checkBlockingById($id) || ($info->type == "user" && !$sysModel->checkAvailability())) {
//                    $this->redirect("login/logout");
//                    var_dump($request->isGet());
//                    var_dump($request->getActionName());
//                    die("==0000=");
//                    header('Content-Type: text/html');
//                    header('Location: http://localhost/zforum/public/login/logout');
                    if (in_array($request->getActionName(), array("logout", "index")) || ($request->isGet() && $request->getActionName() == "profile")) {
                        $request->setControllerName('login');
                        $request->setActionName('logout');
                    } else {
                        echo json_encode(array("status" => "system"));
                        exit;
                    }
                } else if (!empty($view->auth_info) && $view->auth_info->type == "user") {
                    $controllers['login'] = array("index", "register");
                    $controllers['post'] = array("change-permit", "delete");
                    $controllers['comment'] = array("index");

                    if (in_array($request->getControllerName(), array_keys($controllers)) && in_array($request->getActionName(), $controllers[$request->getControllerName()])) {
                        if ($request->getControllerName() == "login") {
                            $request->setControllerName('index');
                            $request->setActionName('index');
                        } else {

                            $request->setControllerName('error');
                            $request->setActionName('error');
                        }
                    }
                } else if (!empty($view->auth_info) && $view->auth_info->type != "user") {

                    $controllers_admin = array(
                        "login" => array("index", "register"),
                    );
                    if (in_array($request->getControllerName(), array_keys($controllers_admin)) && in_array($request->getActionName(), $controllers_admin[$request->getControllerName()])) {
                        $request->setControllerName('index');
                        $request->setActionName('index');
                    }
                }
            }
        }
    }

}

