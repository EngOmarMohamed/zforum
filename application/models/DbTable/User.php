<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract {

    protected $_name = 'users';

    public function listUsers() {
        $users = $this->select()->order("id");
        $usersRows = $this->fetchAll($users)->toArray();
        return $usersRows;
    }

    public function addUser($data) {
        try {
            unset($data["confirm_password"]);
            $data['password'] = md5($data['password']);
            $this->insert($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function editUser($e_data) {
        try {
            $editRow = $this->fetchRow("id=" . $e_data['id']);
            $editRow->name = $e_data['name'];
            $editRow->username = $e_data['username'];
            $editRow->country = $e_data['country'];
            $editRow->email = $e_data['email'];
            if (!empty($e_data['password'])) {
                $editRow->password = md5($e_data['password']);
            }
            $editRow->gender = $e_data['gender'];
            if (!empty($e_data['image'])) {
                if (!empty($editRow->image)) {
                    unlink(PUBLIC_PATH . "/uploads/user/" . $editRow->image);
                }
                $editRow->image = $e_data['image'];
            }
            $editRow->signature = $e_data['signature'];
            $editRow->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteUser($id) {
        try {
            $sigle_user = $this->getSingleUser($id);
            $authorization = Zend_Auth::getInstance();
            $auth_info = $authorization->getIdentity();
            if ($sigle_user['type'] == "root" || $id == $auth_info->id) {
                return false;
            }
            $this->delete("id=" . $id);
            if (!empty($sigle_user['image'])) {
                unlink(PUBLIC_PATH . "/uploads/user/" . $sigle_user['image']);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSingleUser($id) {
        $userRow = $this->fetchRow("id=" . $id)->toArray();
        return $userRow;
    }

    public function changeAdminUser($id, $type) {
        try {
            $sigle_user = $this->getSingleUser($id);
            if ($sigle_user['type'] == "root") {
                return false;
            }
            if ($type == 1) {
                $type = "admin";
            } elseif ($type == 0) {
                $type = "user";
            } else {
                return false;
            }
            $editRow = $this->fetchRow("id=" . $id);
            $editRow->type = $type;
            $editRow->save();
            return $type;
        } catch (Exception $e) {
            return false;
        }
    }

    public function changeBanUser($id, $ban) {
        try {
            $sigle_user = $this->getSingleUser($id);
            if ($sigle_user['type'] == "root") {
                return false;
            }
            $editRow = $this->fetchRow("id=" . $id);
            $editRow->ban_flag = $ban;
            $editRow->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkBlocking($uname) {
        try {
            $userRow = $this->fetchRow("username = '" . $uname . "'");
            if (!empty($userRow)) {
                $userRow = $userRow->toArray();
            } else {
                return true;
            }

            if ($userRow["ban_flag"] == 1) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkIfAdmin($uname) {
        try {
            $userRow = $this->fetchRow("username = '" . $uname . "'")->toArray();

            if ($userRow["type"] == "admin" || $userRow["type"] == "root") {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
