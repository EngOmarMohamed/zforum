<?php

class Application_Form_AddForum extends Zend_Form {

    public function init() {
        $exist_forum = new Zend_Validate_Db_NoRecordExists(array('table' => 'forum', 'field' => 'name'));
        $exist_forum->setMessage("Forum is already exist");

        $forum_regex = new Zend_Validate_Regex(array("pattern" => "/^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$/"));
        $forum_regex->setMessage("Don't use special charcters like @#$*");


        $forum = new Zend_Form_Element_Text("name");
        $forum->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator($forum_regex)
                ->addValidator($exist_forum)
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');

        $submit = new Zend_Form_Element_Button("submit");
        $submit->setAttrib('type', "submit")
                ->setLabel("Add Forum")
                ->setAttrib("class", "btn btn-info")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $elements = array($forum, $submit);
        $this->setAttrib("id", 'addForumForm')
                ->setAttrib("role", 'form')
                ->removeDecorator('HtmlTag');
        $this->addElements($elements);
    }

}
