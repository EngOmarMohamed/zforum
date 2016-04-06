<?php

class Application_Model_DbTable_Forum extends Zend_Db_Table_Abstract {

    protected $_name = 'forum';

    public function addForum($data) {
        try {
            $this->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function editForum($e_data) {
        try {
            $obj = $this->fetchRow("id=" . $e_data['id']);
            if (empty($obj)) {
                return false;
            }
            $this->update($e_data, "id=" . $e_data['id']);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function deleteForum($id) {
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

    public function getForumToEdit($id) {
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

    public function getPosts($id) {
        try {
            $obj = $this->fetchRow("id=" . $id);
            if (empty($obj)) {
                return "no_forum";
            }
            $sql = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('p' => 'post'), array('id', 'title', 'forum_id', 'allow_replies'))
                    ->join(array('u' => 'users'), 'p.user_id = u.id', array('user_id' => 'id', 'username'))
                    ->join(array('f' => 'forum'), 'f.id = p.forum_id', array('forum_name' => 'name', "allow_post"))
                    ->where("f.id = $id");

            $resultSet = $this->fetchAll($sql);
            return $resultSet->toArray();
        } catch (Exception $ex) {
            return "error";
        }
    }

}
