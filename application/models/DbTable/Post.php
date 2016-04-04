<?php

class Application_Model_DbTable_Post extends Zend_Db_Table_Abstract {

    protected $_name = 'post';

    public function getPostByUserID($id) {
        $row = $this->select()->from($this)->setIntegrityCheck(false)->join('forum', "post.forum_id = forum.id", array("name"))->where("post.user_id=" . $id);
        return $this->fetchAll($row)->toArray();
    }

    public function getPostToEdit($id) {
        try {
            $obj = $this->fetchRow("id=" . $id);
            if (empty($obj)) {
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
            if (empty($obj)) {
                return false;
            }
//            $this->fetchRow("id=" . $id)->toArray();
            $this->update($e_data, "id=" . $e_data['id']);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

}
