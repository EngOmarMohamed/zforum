<?php

class Application_Form_AddCategory extends Zend_Form {

    public function init() {
        $exist_cat = new Zend_Validate_Db_NoRecordExists(array('table' => 'category', 'field' => 'category'));
        $exist_cat->setMessage("Category is already exist");
        
        $cat_regex = new Zend_Validate_Regex(array("pattern" => "/^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$/"));
        $cat_regex->setMessage("Don't use special charcters like @#$*");


        $cat = new Zend_Form_Element_Text("category");
        $cat->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator($cat_regex)
                ->addValidator($exist_cat)
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');
        
        $submit = new Zend_Form_Element_Button("submit");
        $submit->setAttrib('type', "submit")
                ->setLabel("Add Category")
                ->setAttrib("class", "btn btn-info")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $elements = array($cat, $submit);
        $this->setAttrib("id", 'addCatForm')
                ->setAttrib("role", 'form')
                ->removeDecorator('HtmlTag');
        $this->addElements($elements);
    }

}
