<?php

namespace PinaCMS\Types;

use Pina\App;
use Pina\Controls\Control;
use Pina\ResourceManagerInterface;
use Pina\StaticResource\Script;
use Pina\StaticResource\Style;
use Pina\Types\TextType;

class HTMLType extends TextType
{
    protected function makeInput()
    {
        /** @var Control $input */
        $input = parent::makeInput();
        $input->addClass('init-ckeditor');
        $this->resources()->append((new Script())->setSrc('/vendor/ckeditor/ckeditor.js'));
        $this->resources()->append((new Script())->setSrc('/editor.js'));
        $this->resources()->append((new Style())->setContent('<style>.ck-editor__editable_inline { min-height: 400px; }</style>'));
        return $input;
    }

    /**
     *
     * @return ResourceManagerInterface
     */
    protected function resources()
    {
        return App::container()->get(ResourceManagerInterface::class);
    }

}
