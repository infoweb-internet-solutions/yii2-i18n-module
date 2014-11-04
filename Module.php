<?php

namespace Zelenin\yii\modules\I18n;

use Yii;
use yii\i18n\MissingTranslationEvent;
use Zelenin\yii\modules\I18n\models\SourceMessage;
use Zelenin\yii\modules\I18n\models\Message;

class Module extends \yii\base\Module
{
    public $pageSize = 50;

    public static function module()
    {
        return static::getInstance();
    }

    public static function t($message, $params = [], $language = null)
    {
        return Yii::t('zelenin/modules/i18n', $message, $params, $language);
    }

    /**
     * @param MissingTranslationEvent $event
     */
    public static function missingTranslation(MissingTranslationEvent $event)
    {
        $sourceMessage = SourceMessage::find()
            ->where('category = :category and message = binary :message', [
                ':category' => $event->category,
                ':message' => $event->message
            ])
            ->with('messages')
            ->one();

        if (!$sourceMessage) {
            $sourceMessage = new SourceMessage;
            $sourceMessage->setAttributes([
                'category' => $event->category,
                'message' => $event->message
            ], false);
            $sourceMessage->save(false);
        }
        
        // Category is 'frontend' and the language is the main language
        if ($event->category == 'frontend' && $event->language == Yii::$app->language) {
            // Use the event message as the translation
            $message = new Message;
            $message->setAttributes([
                'id'            => $sourceMessage->id,
                'language'      => $event->language,
                'translation'   => $event->message    
            ]);
            $message->save();          
        }
        
        $sourceMessage->initMessages();
        $sourceMessage->saveMessages();
    }
}
