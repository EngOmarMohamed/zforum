<?php

class ForumController extends Zend_Controller_Action {

    public static $forumModel;

    public function init() {
        if (empty(ForumController::$forumModel)) {
            ForumController::$forumModel = new Application_Model_DbTable_Forum();
        }
    }

    public function indexAction() {
        $id = $this->getRequest()->getParam('id');
        $forum = ForumController::$forumModel->getPosts($id);
        if ($forum === "no_forum") {
            $this->redirect("index");
        } else if ($forum === "error") {
            $this->redirect("index");
        } else {
            $this->view->allow_post = ForumController::$forumModel->getForumToEdit($id)['allow_post'];
            $this->view->post_forum_id = ForumController::$forumModel->getForumToEdit($id)['id'];
            $this->view->index = $forum;
        }
    }

    public function addAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $forum_info = $this->_request->getParams();
                $cat_id = $forum_info['category_id'];
                $forum_form = new Application_Form_AddForum();

                if ($forum_form->isValid($forum_info)) {

                    $data = $forum_form->getValues();
                    $data['category_id'] = $cat_id;

                    $forum = ForumController::$forumModel->addForum($data);
                    if ($forum) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($forum_form->getMessages() as $k => $v) {
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
                $forum_id = $this->_request->getParam("forum_id");
                $editForum = ForumController::$forumModel->getForumToEdit($forum_id);
                if ($editForum) {
                    unset($editForum["allow_replies"]);
                    echo json_encode(array("status" => "success", "editRow" => $editForum));
                    exit();
                }
                echo json_encode(array("status" => "failed"));
                exit();
            } else if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $forum_info = $this->_request->getParams();
                $id = $forum_info['f_editID'];
                $forum_form = new Application_Form_AddForum();

                $forum_form->getElement('name')
                        ->removeValidator('Db_NoRecordExists');

                $forum_form->getElement('name')
                        ->addValidator('Db_NoRecordExists', false, array('table' => 'forum',
                            'field' => 'name',
                            'exclude' => array('field' => 'id', 'value' => $id)));

                if ($forum_form->isValid($forum_info)) {

                    $data = $forum_form->getValues();
                    $data['id'] = $id;
                    $forum = ForumController::$forumModel->editForum($data);
                    if ($forum) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($forum_form->getMessages() as $k => $v) {
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

    public function changePermitAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $forum_id = $this->_request->getParam("forum_id");
                $allow = $this->_request->getParam("allow");
                $change = ForumController::$forumModel->changeAllowPost($forum_id, $allow);
                if ($change) {
                    echo json_encode(array("status" => "success", "allow" => $allow));
                    exit;
                }
                echo json_encode(array("status" => "failed"));
                exit;
            }
        } else {
            $this->redirect("error/error");
        }
    }

public function deleteAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $forum_id = $this->_request->getParam("forum_id");
                $delete = ForumController::$forumModel->deleteForum($forum_id);
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

}
