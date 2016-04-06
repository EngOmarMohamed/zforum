<?php

class PostController extends Zend_Controller_Action {

    public static $postModel = null;

    public function init() {
        if (empty(PostController::$postModel)) {
            PostController::$postModel = new Application_Model_DbTable_Post();
        }
    }

    public function indexAction() {
        $authorization = Zend_Auth::getInstance();
//        if (!$authorization->hasIdentity()) {
//            $this->redirect('index');
//        }
        $auth_info = $authorization->getIdentity();
        $posts = PostController::$postModel->getPostByUserID($auth_info->id);
        $this->view->posts = $posts;
    }

    public function addAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $post_info = $this->_request->getParams();
                $forum_id = $post_info['forum_id'];
                $post_form = new Application_Form_AddPost();

                if ($post_form->isValid($post_info)) {

                    $data = $post_form->getValues();
                    $data['forum_id'] = $forum_id;

                    $authorization = Zend_Auth::getInstance();
                    $auth_info = $authorization->getIdentity();
                    $data['user_id'] = $auth_info->id;

                    $post = PostController::$postModel->addPost($data);

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
                    foreach ($post_form->getMessages() as $k => $v) {
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
                $post_id = $this->_request->getParam("post_id");

                $authorization = Zend_Auth::getInstance();
                $auth_info = $authorization->getIdentity();

                $editPost = PostController::$postModel->getPostToEdit($post_id, $auth_info->id, $auth_info->type);
                if ($editPost) {
                    unset($editPost["allow_replies"]);
                    unset($editPost["sticky"]);
                    echo json_encode(array("status" => "success", "editRow" => $editPost));
                    exit();
                }
                echo json_encode(array("status" => "failed"));
                exit();
            } else if ($this->getRequest()->isPost()) {
                $request = $this->getRequest();
                $post_info = $this->_request->getParams();

                $post_form = new Application_Form_AddPost();
                $id = $post_info['editID'];
                unset($post_info['id']);

                if ($post_form->isValid($post_info)) {

                    $data = $post_form->getValues();
                    $data['id'] = $id;
                    $post = PostController::$postModel->editPost($data);
                    if ($post) {
                        echo json_encode(array("status" => "success"));
                        exit;
                    } else {
                        echo json_encode(array("status" => "error"));
                        exit;
                    }
                } else {
                    foreach ($post_form->getMessages() as $k => $v) {
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
                $post_id = $this->_request->getParam("post_id");
                $delete = PostController::$postModel->deletePost($post_id);
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

    public function showSingleAction() {
        $id = $this->getRequest()->getParam('id');
        $post = PostController::$postModel->getPostbyid($id);
        if ($post === "no_post") {
            $this->redirect("index");
        } else if ($post === "error") {
            $this->redirect("index");
        } else {
            $this->view->post_id = $post[0]['id'];
            $this->view->index = $post;
        }
    }

    public function changePermitAction() {
        if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
            if ($this->getRequest()->isPost()) {
                $post_id = $this->_request->getParam("post_id");
                $allow = $this->_request->getParam("allow");
                $change = PostController::$postModel->changeAllowReply($post_id, $allow);
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

}
