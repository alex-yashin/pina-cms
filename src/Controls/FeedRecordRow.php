<?php


namespace PinaCMS\Controls;

use PinaMedia\Media;
use Pina\Controls\LinkedListItem;
use Pina\Controls\RecordTrait;
use Pina\Html;

class FeedRecordRow extends LinkedListItem
{
    use RecordTrait;

    protected $ignore = [];

    public function setIgnore(array $fields)
    {
        $this->ignore = $fields;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function drawInner()
    {
        $data = $this->record->getTextData();

        $title = $data['title'];
        unset($data['title']);

        $text = mb_substr(strip_tags($data['text']), 0, 500);
        unset($data['text']);

        $enabled = $this->record->getData()['enabled'] ?? 'Y';
        if ($enabled == 'N') {
            $this->addClass('disabled');
        }
        unset($data['enabled']);

        $icon = '';
        if (!empty($data['media_storage']) && !empty($data['media_path'])) {
            $mediaUrl = Media::getUrl($data['media_storage'], $data['media_path']);
            $icon = Html::img($mediaUrl, ['class' => 'image']);
        }

        $content = [];
        $content[] = Html::tag('header', $title);

        $tags = implode(' • ', array_filter(array_diff_key($data, array_flip($this->ignore))));
        $second = $tags ? Html::tag('i', $tags) : '';
        if ($text && mb_strlen($text) > 10) {
            $second .= ' — ' . $text;
        }
        $content[] = Html::nest('.short',  $second);

        return $icon . implode('', array_filter($content));
    }
}