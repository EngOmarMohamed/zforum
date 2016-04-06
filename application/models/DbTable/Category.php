<?php

class Application_Model_DbTable_Category extends Zend_Db_Table_Abstract {

    protected $_name = 'category';

    public function getCat() {
        $sql = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('g' => 'category'), array('id', 'category'))
                ->joinLeft(array('f' => 'forum'), 'f.category_id = g.id', array('name', "id as forum_id", "allow_post"))
//                ->where('g.id = f.category_id')
        ;
        $resultSet = $this->fetchAll($sql)->toArray();
//        print_r($resultSet);die("==");
        return $resultSet;
    }

    public function addCat($data) {
        try {
            $this->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSingleCat($id) {
        $userRow = $this->fetchRow("id=" . $id);
        if (!empty($userRow)) {
            return $userRow->toArray();
        }
        return false;
    }

    public function editCat($e_data) {
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

    public function deleteCat($id) {
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

}
