<?php

namespace PinaCMS\Types;

use Pina\App;
use Pina\Controls\Control;
use Pina\Types\TextType;

class HTMLType extends TextType
{
    protected function makeInput()
    {
        /** @var Control $input */
        $input = parent::makeInput();
        $input->addClass('init-ckeditor');
        App::assets()->addScript('/vendor/ckeditor/ckeditor.js');
        App::assets()->addScript('/editor.js');
        App::assets()->addCssContent('<style>.ck-editor__editable_inline { min-height: 400px; }</style>');
        return $input;
    }
}
