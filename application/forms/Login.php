<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $uname = new Zend_Form_Element_Text("username");
        $uname->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->setAttrib("placeholder", "Username")
                ->setAttrib("class", "input")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag')
                ->removeDecorator('Label');;

        $password = new Zend_Form_Element_Password("password");
        $password->setRequired(true)
                ->addValidator(new Zend_Validate_StringLength(array(6, 20)))
                ->setAttrib("placeholder", "Password")
                ->setAttrib("class", "input")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag')
                ->removeDecorator('Label');

        $submit = new Zend_Form_Element_Button("submit");
        $submit->setAttrib("class", "login-button")
                ->setAttrib('type',"submit")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $elements = array($uname, $password, $submit);
        $this->setAttrib("class",'form')
                ->removeDecorator('HtmlTag');
        $this->addElements($elements);
    }


}

