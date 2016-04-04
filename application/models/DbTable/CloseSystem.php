<?php

class Application_Model_DbTable_CloseSystem extends Zend_Db_Table_Abstract
{

    protected $_name = 'close_system';

    public function checkAvailability() {
        try {
            $row = $this->fetchRow("id=1")->toArray();
            return $row["available"];
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function changeAvailability($available) {
        try {
            $editRow = $this->fetchRow("id=1");
            $editRow->available = $available;
            $editRow->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

