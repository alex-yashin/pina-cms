<?php


namespace PinaCMS\Controls;

use PinaMedia\Media;
use Pina\Controls\LinkedListItem;
use Pina\Controls\RecordTrait;
use Pina\Html;

class FeedRecordRow extends LinkedListItem
{
    use RecordTrait;

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

        $second = Html::tag('i', implode(' • ', array_filter($data)));
        if ($text) {
            $second .= ' — ' . $text;
        }
        $content[] = Html::nest('.short',  $second);

        return $icon . implode('', array_filter($content));
    }
}