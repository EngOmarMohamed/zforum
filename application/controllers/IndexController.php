<?php

class IndexController extends Zend_Controller_Action {

    public static $sysModel = null;
    public static $catModel = null;

    public function init() {
        if (empty(IndexController::$sysModel)) {
            IndexController::$sysModel = new Application_Model_DbTable_CloseSystem();
        }
        if (empty(IndexController::$catModel)) {
            IndexController::$catModel = new Application_Model_DbTable_Category();
        }
    }

    public function indexAction() {
        if (IndexController::$sysModel->checkAvailability()) {
            $this->view->checkAvailabiliy = true;
        } else {
            $this->view->checkAvailabiliy = false;
        }
        $cat_id = IndexController::$catModel->getCat();
        $this->view->index = $cat_id;

        $addCatForm = new Application_Form_AddCategory();
        $this->view->addCatForm = $addCatForm;

        $addForumForm = new Application_Form_AddForum();
        $this->view->addForumForm = $addForumForm;
    }

    public function changeAvailabilityAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $available = $this->_request->getParam("available");
                $change = IndexController::$sysModel->changeAvailability($available);
                if ($change) {
                    echo json_encode(array("status" => "success", "available" => $available));
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
