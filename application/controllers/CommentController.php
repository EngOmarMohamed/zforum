<?php

class CommentController extends Zend_Controller_Action {

    public static $commentModel = null;

    public function init() {
        if (empty(CommentController::$commentModel)) {
            CommentController::$commentModel = new Application_Model_DbTable_Comment();
        }
    }

    public function indexAction() {
        // action body
    }

    public function addAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->isPost()) {
                $comment_info = $this->_request->getParams();

                $post_id = $comment_info['post_id'];

                $comment_form = new Application_Form_AddComment();
                if ($comment_form->isValid($comment_info)) {

                    $data = $comment_form->getValues();
                    $data['post_id'] = $post_id;

                    $authorization = Zend_Auth::getInstance();
                    $auth_info = $authorization->getIdentity();
                    $data['user_id'] = $auth_info->id;

                    $post = CommentController::$commentModel->addComment($data);

                    if ($post === "disallow") {
                        echo json_encode(array("status" => "disallow"));
                        exit;
                    } else if ($post) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($comment_form->getMessages() as $k => $v) {
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
            if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $comment_info = $this->_request->getParams();

                $comment_form = new Application_Form_AddComment();
                $id = $comment_info['id'];
                $post_id = $comment_info['post_id'];
//                unset($comment_info['id']);

                if ($comment_form->isValid($comment_info)) {

                    $data = $comment_form->getValues();
                    $data['id'] = $id;
                    $data['post_id'] = $post_id;
                    $authorization = Zend_Auth::getInstance();
                    $auth_info = $authorization->getIdentity();

                    $comment = CommentController::$commentModel->editComment($post_id, $data, $auth_info->id, $auth_info->type);
                    if ($comment) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($comment_form->getMessages() as $k => $v) {
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
                $comment_id = $this->_request->getParam("comment_id");
                $post_id = $this->_request->getParam("post_id");

                $authorization = Zend_Auth::getInstance();
                $auth_info = $authorization->getIdentity();

                $delete = CommentController::$commentModel->deleteComment($post_id, $comment_id, $auth_info->id, $auth_info->type);
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
