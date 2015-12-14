<?php

namespace Zelenin\yii\modules\I18n\components;

use yii\base\InvalidConfigException;
use yii\i18n\DbMessageSource;

class I18N extends \yii\i18n\I18N
{
    /** @var string */
    public $sourceMessageTable = '{{%source_message}}';
    /** @var string */
    public $messageTable = '{{%message}}';
    /** @var array */
    public $languages;
    /** @var array */
    public $missingTranslationHandler = ['Zelenin\yii\modules\I18n\Module', 'missingTranslation'];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->languages) {
            throw new InvalidConfigException('You should configure i18n component [language]');
        }

        if (!isset($this->translations['*'])) {
            $this->translations['*'] = [
                'class' => DbMessageSource::className(),
                'sourceMessageTable' => $this->sourceMessageTable,
                'messageTable' => $this->messageTable,
                'on missingTranslation' => $this->missingTranslationHandler
            ];
        }
        if (!isset($this->translations['app']) && !isset($this->translations['app*'])) {
            $this->translations['app'] = [
                'class' => DbMessageSource::className(),
                'sourceMessageTable' => $this->sourceMessageTable,
                'messageTable' => $this->messageTable,
                'on missingTranslation' => $this->missingTranslationHandler
            ];
        }
        parent::init();
    }

    /**
     * Hack to suppress error message
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $language
     * @return string
     */
    public function translate($category, $message, $params, $language){
        if($language=='en' && $category=='yii'){
            $language = 'en-US';
        }
        return parent::translate($category, $message, $params, $language);
    }
}
