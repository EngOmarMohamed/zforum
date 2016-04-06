<?php

class LoginController extends Zend_Controller_Action {

    public static $userModel = null;
    public static $sysModel = null;

    public function init() {
        if (empty(LoginController::$userModel)) {
            LoginController::$userModel = new Application_Model_DbTable_User();
        }
        if (empty(LoginController::$sysModel)) {
            LoginController::$sysModel = new Application_Model_DbTable_CloseSystem();
        }
    }

    public function indexAction() {
//        $authorization = Zend_Auth::getInstance();
//        if ($authorization->hasIdentity()) {
//            $this->redirect('index');
//        }

        $username = $this->_request->getParam('username');
        $password = $this->_request->getParam('password');
        if ($this->getRequest()->isPost() && $username && $password) {

            if (LoginController::$sysModel->checkAvailability()) {
                if (LoginController::$userModel->checkBlocking($username)) {
                    $this->view->loginError = "This user is blocked or not exist";
                } else {

// get the default db adapter
                    $db = Zend_Db_Table::getDefaultAdapter();

                    $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'username', 'password');
//set the email and password
                    try {
                        $authAdapter->setIdentity($username);
                        $authAdapter->setCredential(md5($password));
                        //authenticate
                        $result = $authAdapter->authenticate();
                        if ($result->isValid()) {

                            $auth = Zend_Auth::getInstance();
                            $storage = $auth->getStorage();
                            $storage->write($authAdapter->getResultRowObject(array('type', 'id', 'name', "image")));

                            $this->redirect('index');
                        } else {
                            $this->view->loginError = "Invalid Username or password";
                        }
                    } catch (Exception $ex) {
                        $this->view->loginError = "Invalid Username or password";
                    }
                }
            } else {
                if (!LoginController::$userModel->checkIfAdmin($username)) {
                    $this->view->loginError = "The System is closed Only admins can log in";
                } elseif (LoginController::$userModel->checkBlocking($username)) {
                    $this->view->loginError = "This user is blocked";
                } else {
                    $db = Zend_Db_Table::getDefaultAdapter();

                    $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'username', 'password');
//set the email and password
                    try {
                        $authAdapter->setIdentity($username);
                        $authAdapter->setCredential(md5($password));
                        //authenticate
                        $result = $authAdapter->authenticate();
                        if ($result->isValid()) {

                            $auth = Zend_Auth::getInstance();
                            $storage = $auth->getStorage();
                            $storage->write($authAdapter->getResultRowObject(array('type', 'id', 'name', "image", "username")));

                            $this->redirect('index');
                        } else {
                            $this->view->loginError = "Invalid Username or password";
                        }
                    } catch (Exception $ex) {
                        $this->view->loginError = "Invalid Username or password";
                    }
                }
            }
        }
        $loginForm = new Application_Form_Login();

        $this->view->loginForm = $loginForm;
    }

    public function logoutAction() {
        $authorization = Zend_Auth::getInstance();
//        if ($authorization->hasIdentity()) {
        $authorization->clearIdentity();
        $this->redirect('index');
//        }
    }

    public function registerAction() {
//        $authorization = Zend_Auth::getInstance();
//        if ($authorization->hasIdentity()) {
//            $this->redirect('index');
//        }

        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            $user_info = $this->_request->getParams();

            $user_form = new Application_Form_AddUser();
            $user_info["image"] = $_FILES["image"];

            if ($user_form->isValid($user_info)) {
                if (!empty($_FILES["image"]["name"])) {
                    $originalFilename = pathinfo($user_form->image->getFileName());
                    $newName = time() . "." . $originalFilename['extension'];
                    $user_form->image->addFilter('Rename', $newName);
                }
                $data = $user_form->getValues();

                $user = LoginController::$userModel->addUser($data);
                if ($user) {
                    include PUBLIC_PATH . '/../library/sendgrid-php/sendgrid-php.php';
                    $sendgrid = new SendGrid("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");
                    $email = new SendGrid\Email();
                    $email
                            ->addTo($data["email"])
                            ->setFrom('admin@zforum.com')
                            ->setSubject('Welcome to Zforum')
                            ->setText('Your Registeration Info')
                            ->setHtml("Dear Mr." . $data['name'] . "<br/>&nbsp;&nbsp;&nbsp;Thanks for registeration.<br/>your username: " . $data['username'] . "<br/>your password: " . $data['password'] . "<br/>your password: " . $data['country']);

                    // get the default db adapter
                    $db = Zend_Db_Table::getDefaultAdapter();

                    $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'username', 'password');
                    //set the email and password
                    try {
                        $sendgrid->send($email);

                        $authAdapter->setIdentity($data['username']);
                        $authAdapter->setCredential(md5($data['password']));
                        //authenticate
                        $result = $authAdapter->authenticate();
                        if ($result->isValid()) {

                            $auth = Zend_Auth::getInstance();
                            $storage = $auth->getStorage();
                            $storage->write($authAdapter->getResultRowObject(array('type', 'id', 'name', "image", "username")));

                            echo json_encode(array("status" => "success"));
                            exit;
                        } else {
                            echo json_encode(array("status" => "errorRedirect"));
                            exit;
                        }
                    } catch (Exception $ex) {
                        echo json_encode(array("status" => "errorRedirect"));
                        exit;
                    }
                } else {
                    echo json_encode(array("status" => "error"));
                    exit;
                }
            } else {
                foreach ($user_form->getMessages() as $k => $v) {
                    foreach ($v as $key => $val) {
                        $errorMessages[$k] = $val;
                    }
                }
                echo json_encode(array("status" => "failed", "errorMessages" => $errorMessages));
                exit;
            }
        }
    }

    public function profileAction() {
        $authorization = Zend_Auth::getInstance();
//        if (!$authorization->hasIdentity()) {
//            $this->redirect('index');
//        }
        if ($this->getRequest()->isGet()) {
            $auth_info = $authorization->getIdentity();

            $editUser = LoginController::$userModel->getSingleUser($auth_info->id);

            unset($editUser["password"]);
            unset($editUser["ban_flag"]);
            unset($editUser["type"]);

            $this->view->editUser = $editUser;
        } else if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            $user_info = $this->_request->getParams();

            $user_form = new Application_Form_AddUser();
            $user_info["image"] = $_FILES["image"];
            $id = $user_info['editID'];
            unset($user_info['id']);

            $user_form->getElement('email')
                    ->removeValidator('Db_NoRecordExists');
//            print_r($id);die("==");
            $user_form->getElement('email')
                    ->addValidator('Db_NoRecordExists', false, array('table' => 'users',
                        'field' => 'email',
                        'exclude' => array('field' => 'id', 'value' => $id)));

            $user_form->getElement('username')
                    ->removeValidator('Db_NoRecordExists');

            $user_form->getElement('username')
                    ->addValidator('Db_NoRecordExists', false, array('table' => 'users',
                        'field' => 'username',
                        'exclude' => array('field' => 'id', 'value' => $id)));
            if (empty($user_info['password'])) {
                $user_form->getElement('password')
                        ->setRequired(false);
                $user_form->getElement('confirm_password')
                        ->setRequired(false);
            }

            if ($user_form->isValid($user_info)) {
                if (!empty($_FILES["image"]["name"])) {
                    $originalFilename = pathinfo($user_form->image->getFileName());
                    $newName = time() . "." . $originalFilename['extension'];
                    $user_form->image->addFilter('Rename', $newName);
                }
                $data = $user_form->getValues();
                $data['id'] = $id;
                $user = LoginController::$userModel->editUser($data);
                if ($user) {
                    echo json_encode(array("status" => "success"));
                    exit;
                } else {
                    echo json_encode(array("status" => "error"));
                    exit;
                }
            } else {
                foreach ($user_form->getMessages() as $k => $v) {
                    foreach ($v as $key => $val) {
                        $errorMessages[$k] = $val;
                    }
                }
                echo json_encode(array("status" => "failed", "errorMessages" => $errorMessages));
                exit;
            }
        }
    }

}
