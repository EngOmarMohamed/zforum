<?php

class UserController extends Zend_Controller_Action {

    public static $userModel;

    public function init() {
        if (empty(UserController::$userModel)) {
            UserController::$userModel = new Application_Model_DbTable_User();
        }
    }

    public function indexAction() {
        $users_info = UserController::$userModel->listUsers();
        $addUserForm = new Application_Form_AddUser();
        $this->view->users = $users_info;

        $this->view->addUserForm = $addUserForm;
    }

    public function addAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
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

                    $user = UserController::$userModel->addUser($data);
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
        } else {
            $this->redirect("error/error");
        }
    }

    public function editAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isGet()) {
                $user_id = $this->_request->getParam("user_id");
                $editUser = UserController::$userModel->getSingleUser($user_id);
                unset($editUser["password"]);
                unset($editUser["ban_flag"]);
                unset($editUser["type"]);
                unset($editUser["image"]);
                echo json_encode(array("status" => "success", "editRow" => $editUser));
                exit();
            } else if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $user_info = $this->_request->getParams();

                $user_form = new Application_Form_AddUser();
                $user_info["image"] = $_FILES["image"];
                $id = $user_info['editID'];
                unset($user_info['id']);

                $user_form->getElement('email')
                        ->removeValidator('Db_NoRecordExists');

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
                    $user = UserController::$userModel->editUser($data);
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
        } else {
            $this->redirect("error/error");
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $user_id = $this->_request->getParam("user_id");
                $delete = UserController::$userModel->deleteUser($user_id);
                if ($delete) {
                    echo json_encode(array("status" => "success"));
                    exit;
                }
                echo json_encode(array("status" => "failed"));
                exit;
            }
        } else {
            $this->redirect("error/error");
        }
    }

    public function changeTypeAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $user_id = $this->_request->getParam("user_id");
                $type = $this->_request->getParam("type");
                $change = UserController::$userModel->changeAdminUser($user_id, $type);
                if ($change) {
                    echo json_encode(array("status" => "success", "type" => $change));
                    exit;
                }
                echo json_encode(array("status" => "failed"));
                exit;
            }
        } else {
            $this->redirect("error/error");
        }
    }

    public function changeBanAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $user_id = $this->_request->getParam("user_id");
                $ban = $this->_request->getParam("ban");
                $change = UserController::$userModel->changeBanUser($user_id, $ban);
                if ($change) {
                    echo json_encode(array("status" => "success", "ban" => $ban));
                    exit;
                }
                echo json_encode(array("status" => "failed"));
                exit;
            }
        } else {
            $this->redirect("error/error");
        }
    }

}
