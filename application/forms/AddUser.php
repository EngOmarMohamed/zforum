<?php

class Application_Form_AddUser extends Zend_Form {

    public function init() {
        $exist_user = new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'username'));
        $exist_user->setMessage("Username is already exist");

        $exist_email = new Zend_Validate_Db_NoRecordExists(array('table' => 'users', 'field' => 'email'));
        $exist_email->setMessage("Email is already exist");

        $valid_email = new Zend_Validate_EmailAddress();
        $valid_email->setMessage("Invalid Email");

        $str_length = new Zend_Validate_StringLength(array("min" => 8, "max" => 20));
        $str_length->setMessage("Password should be min 8 max 20 characters");

        $name = new Zend_Form_Element_Text("name");
        $name->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator(new Zend_Validate_Alnum())
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');

        $uname = new Zend_Form_Element_Text("username");
        $uname->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator($exist_user)
                ->addValidator(new Zend_Validate_Alnum())
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');

        $password = new Zend_Form_Element_Password("password");
        $password->setRequired(true)
                ->addValidator($str_length)
                ->setAttrib("class", "form-control")
                ->setAttrib("id", "user_password")
                ->removeDecorator('HtmlTag');

        $co_password = new Zend_Form_Element_Password("confirm_password");
        $co_password->setRequired(true)
                ->addValidator($str_length)
                ->addValidator(new Zend_Validate_Identical('password'))
                ->setAttrib("class", "form-control")
                ->setAttrib("id", "user_co_password")
                ->removeDecorator('HtmlTag');

        $email = new Zend_Form_Element_Text("email");
        $email->setRequired(true)
                ->addValidator($valid_email)
                ->addValidator($exist_email)
                ->setAttrib("class", "form-control")
                ->setAttrib("type", "email")
                ->setAttrib("required", "required");

        $country = new Zend_Form_Element_Select("country");
        $country->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator(new Zend_Validate_InArray(array("Egypt" => "Egypt", "UK" => "UK", "USA" => "USA")))
                ->setAttrib("class", "form-control")
                ->setOptions(array('multiOptions' => array("Egypt" => "Egypt", "UK" => "UK", "USA" => "USA")));

        $gender = new Zend_Form_Element_Radio("gender");
        $gender->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator(new Zend_Validate_InArray(array("M", "F")))
                ->setAttrib("required", "required")
                ->setOptions(array('multiOptions' => array(
                        'F' => 'Female',
                        'M' => 'Male',
        )));

        $signature = new Zend_Form_Element_Text("signature");
        $signature->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator(new Zend_Validate_Alnum())
                ->setAttrib("class", "form-control");

//                print_r(PUBLIC_PATH."/uploads/user/");die("===");
        $user_photo = new Zend_Form_Element_File("image");
        $user_photo->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator(new Zend_Validate_File_Count(array("min" => 0, "max" => 1)))
                ->addValidator(new Zend_Validate_File_Size(array("min" => 1, "max" => 2097152)))
                ->setDestination(PUBLIC_PATH . "/uploads/user")
                ->addValidator(new Zend_Validate_File_IsImage());

        $submit = new Zend_Form_Element_Button("submit");
        $submit->setAttrib('type', "submit")
                ->setLabel("Add User")
                ->setAttrib("class", "btn btn-info")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $elements = array($uname, $password, $co_password, $email, $country, $gender, $signature, $submit, $user_photo, $name);
        $this->setAttrib("id", 'addUserForm')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAttrib("role", 'form')
                ->removeDecorator('HtmlTag');
        $this->addElements($elements);
    }

}
