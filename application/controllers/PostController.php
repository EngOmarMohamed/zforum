<?php

class PostController extends Zend_Controller_Action {

    public static $postModel;

    public function init() {
        if (empty(PostController::$postModel)) {
            PostController::$postModel = new Application_Model_DbTable_Post();
        }
    }

    public function indexAction() {
        $authorization = Zend_Auth::getInstance();
        if (!$authorization->hasIdentity()) {
            $this->redirect('index');
        }
        $auth_info = $authorization->getIdentity();
        $posts = PostController::$postModel->getPostByUserID($auth_info->id);
        $this->view->posts = $posts;
    }

    public function addAction() {
        // action body
    }

    public function editAction() {
        if ($this->getRequest()->isGet()) {
            $post_id = $this->_request->getParam("post_id");
            $editPost = PostController::$postModel->getPostToEdit($post_id);
            if($editPost) {
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
    }

    public function deleteAction() {
        // action body
    }

}
