<?php

class Application_Model_DbTable_Comment extends Zend_Db_Table_Abstract {

    protected $_name = 'comment';

    public function addComment($data) {
        try {
            $select = $this->getAdapter()->select();
            $select->from('post')
                    ->where("id =" . $data['post_id']);

            $allow = $this->getAdapter()->fetchRow($select);
            if (!$allow['allow_replies']) {
                return "disallow";
            }
            $this->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteComment($post_id, $id, $userID, $userType) {
        try {
            $sql = $this->select()->from($this)->where("id=" . $id);
            $obj = $this->fetchRow($sql);
            if (empty($obj)) {
                return false;
            }

            $sql_ = $this->select()->from($this)->where("id=" . $id);
            $obj_ = $this->fetchRow($sql_);

            if ($obj_->user_id != $userID && $userType == "user") {
                return false;
            }

            if ($obj_->post_id != $post_id) {
                return false;
            }
            $this->delete("id=" . $id);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function editComment($post_id, $e_data, $userID, $userType) {
        try {
            $sql = $this->select()->from($this)->where("id=" . $e_data['id']);
            $obj = $this->fetchRow($sql);
            if (empty($obj)) {
                return false;
            }

            $sql_ = $this->select()->from($this)->where("id=" . $e_data['id']);
            $obj_ = $this->fetchRow($sql_);

            if ($obj_->user_id != $userID && $userType == "user") {
                return false;
            }

            if ($obj_->post_id != $post_id) {
                return false;
            }
            $this->update($e_data, "id=" . $e_data['id']);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

}
