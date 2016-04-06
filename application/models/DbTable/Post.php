<?php

class Application_Model_DbTable_Post extends Zend_Db_Table_Abstract {

    protected $_name = 'post';

    public function getPostByUserID($id) {
        $row = $this->select()->from($this)->setIntegrityCheck(false)->join('forum', "post.forum_id = forum.id", array("name"))->where("post.user_id=" . $id);
        return $this->fetchAll($row)->toArray();
    }

    public function getPostToEdit($id, $userID, $userType) {
        try {
            $sql = $this->select()->from($this)->where("id=" . $id . " and user_id= " . $userID);
            $obj = $this->fetchRow($sql);
            if (empty($obj) && $userType == "user") {
                return false;
            }
            return $this->fetchRow("id=" . $id)->toArray();
        } catch (Exception $ex) {
            return false;
        }
    }

    public function editPost($e_data) {
        try {
            $obj = $this->fetchRow("id=" . $e_data['id']);
            $authorization = Zend_Auth::getInstance();
            $auth_info = $authorization->getIdentity();
            if (empty($obj) || ($auth_info->type == "user" && $obj->user_id != $auth_info->id)) {
                return false;
            }
            $this->update($e_data, "id=" . $e_data['id']);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function getPostbyid($id) {
        try {
            $obj = $this->fetchRow("id=" . $id);
            if (empty($obj)) {
                return "no_post";
            }
            $sql = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('p' => 'post'), array('id', 'title', 'content', 'allow_replies'))
                    ->join(array('u' => 'users'), 'p.user_id = u.id', array('user_id' => 'id', 'username', 'image', "name"))
                    ->joinLeft(array('c' => 'comment'), 'c.post_id = p.id', array('comment', 'c_id' => 'id'))
                    ->joinLeft(array('uc' => 'users'), 'c.user_id = uc.id', array('c_user_id' => 'id', 'c_name' => 'name', "u_image" => "image"))
                    ->where("p.id = $id");
            $resultSet = $this->fetchAll($sql);
            return $resultSet->toArray();
        } catch (Exception $ex) {
            return "error";
        }
    }

    public function changeAllowReply($id, $allow) {
        try {
            $editRow = $this->fetchRow("id=" . $id);
            if (empty($editRow)) {
                return false;
            }
            $editRow->allow_replies = $allow;
            $editRow->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function changeAllowPost($id, $allow) {
        try {
            $editRow = $this->fetchRow("id=" . $id);
            if (empty($editRow)) {
                return false;
            }
            $editRow->allow_post = $allow;
            $editRow->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deletePost($id) {
        try {
            $obj = $this->fetchRow("id=" . $id);
            if (empty($obj)) {
                return false;
            }
            $this->delete("id=" . $id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addPost($data) {
        try {
            $select = $this->getAdapter()->select();
            $select->from('forum')
                    ->where("id =" . $data['forum_id']);

            $allow = $this->getAdapter()->fetchRow($select);
            if (!$allow['allow_post']) {

                return "disallow";
            }
            $this->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
