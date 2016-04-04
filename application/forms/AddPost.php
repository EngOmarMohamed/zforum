<?php

class Application_Form_AddPost extends Zend_Form {

    public function init() {
        $title_regex = new Zend_Validate_Regex(array("pattern" => "/^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$/"));
        $title_regex->setMessage("Don't use special charcters like @#$*");

        $content_regex = new Zend_Validate_Regex(array("pattern" => "/^[a-zA-Z\s]*$/"));
        $content_regex->setMessage("Don't use special charcters like @#$*");

        $title = new Zend_Form_Element_Text("title");
        $title->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator($title_regex)
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');

        $content = new Zend_Form_Element_Textarea("content");
        $content->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator($content_regex)
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');

        $submit = new Zend_Form_Element_Button("submit");
        $submit->setAttrib('type', "submit")
                ->setLabel("Add Post")
                ->setAttrib("class", "btn btn-info")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $elements = array($title, $content, $submit);
        $this->setAttrib("id", 'addPostForm')
                ->setAttrib("role", 'form')
                ->removeDecorator('HtmlTag');
        $this->addElements($elements);
    }

}
