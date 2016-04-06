<?php

class Application_Form_AddComment extends Zend_Form {

    public function init() {
        $content_regex = new Zend_Validate_Regex(array("pattern" => "/^[a-zA-Z\s]*$/"));
        $content_regex->setMessage("Don't use special charcters like @#$*");

        $content = new Zend_Form_Element_Textarea("comment");
        $content->setRequired(true)
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_StripTags())
                ->addValidator($content_regex)
                ->setAttrib("class", "form-control")
                ->setAttrib("required", "required")
                ->removeDecorator('HtmlTag');

        $submit = new Zend_Form_Element_Button("submit");
        $submit->setAttrib('type', "submit")
                ->setLabel("Add Comment")
                ->setAttrib("class", "btn btn-info")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $elements = array($content, $submit);
        $this->setAttrib("id", 'addCommentForm')
                ->setAttrib("role", 'form')
                ->removeDecorator('HtmlTag');
        $this->addElements($elements);
    }

}
