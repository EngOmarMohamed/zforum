<?php

class CategoryController extends Zend_Controller_Action {

    public static $catModel;
    public static $sysModel;
    public static $userModel;

    public function init() {
        
        if (empty(CategoryController::$catModel)) {
            CategoryController::$catModel = new Application_Model_DbTable_Category();
        }
    }

    public function indexAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) !== 'xmlhttprequest') {
            $this->redirect("error/error");
        }
    }

    public function deleteAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $cat_id = $this->_request->getParam("cat_id");
                $delete = CategoryController::$catModel->deleteCat($cat_id);
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

    public function addAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $cat_info = $this->_request->getParams();

                $cat_form = new Application_Form_AddCategory();

                if ($cat_form->isValid($cat_info)) {

                    $data = $cat_form->getValues();

                    $cat = CategoryController::$catModel->addCat($data);
                    if ($cat) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($cat_form->getMessages() as $k => $v) {
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
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isGet()) {
                $cat_id = $this->_request->getParam("cat_id");
                $editCat = CategoryController::$catModel->getSingleCat($cat_id);
                if ($editCat) {
                    echo json_encode(array("status" => "success", "editRow" => $editCat));
                    exit();
                }
                echo json_encode(array("status" => "failed"));
                exit();
            } else if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $cat_info = $this->_request->getParams();

                $id = $cat_info['editID'];

                $cat_form = new Application_Form_AddCategory();

                $cat_form->getElement('category')
                        ->removeValidator('Db_NoRecordExists');

                $cat_form->getElement('category')
                        ->addValidator('Db_NoRecordExists', false, array('table' => 'category',
                            'field' => 'category',
                            'exclude' => array('field' => 'id', 'value' => $id)));

                if ($cat_form->isValid($cat_info)) {

                    $data = $cat_form->getValues();
                    $data['id'] = $id;
                    $cat = CategoryController::$catModel->editCat($data);
                    if ($cat) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($cat_form->getMessages() as $k => $v) {
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

}
