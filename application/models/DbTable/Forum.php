<?php

class Application_Model_DbTable_Forum extends Zend_Db_Table_Abstract
{

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
//            $this->fetchRow("id=" . $id)->toArray();
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

}

