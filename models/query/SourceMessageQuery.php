<?php

namespace Zelenin\yii\modules\I18n\models\query;

use Yii;
use yii\db\ActiveQuery;
use Zelenin\yii\modules\I18n\models\Message;

class SourceMessageQuery extends ActiveQuery
{
    public function notTranslated($category = '')
    {
        $messageTableName = Message::tableName();
        $query = Message::find()->select($messageTableName . '.id');
        $i = 0;

        foreach ($this->languages($category) as $language) {
            if ($i === 0) {
                $query->andWhere($messageTableName . '.language = :language and ' . $messageTableName . '.translation is not null', [':language' => $language]);
            } else {
                $query->innerJoin($messageTableName . ' t' . $i, 't' . $i . '.id = ' . $messageTableName . '.id and t' . $i . '.language = :language and t' . $i . '.translation is not null', [':language' => $language]);
            }
            $i++;
        }
        $ids = $query->indexBy('id')->all();
        $this->andWhere(['not in', 'id', array_keys($ids)]);
        return $this;
    }

    public function translated($category = '')
    {
        $messageTableName = Message::tableName();
        $query = Message::find()->select($messageTableName . '.id');
        $i = 0;
        
        foreach ($this->languages($category) as $language) {
            if ($i === 0) {
                $query->andWhere($messageTableName . '.language = :language and ' . $messageTableName . '.translation is not null', [':language' => $language]);
            } else {
                $query->innerJoin($messageTableName . ' t' . $i, 't' . $i . '.id = ' . $messageTableName . '.id and t' . $i . '.language = :language and t' . $i . '.translation is not null', [':language' => $language]);
            }
            $i++;
        }
        $ids = $query->indexBy('id')->all();
        $this->andWhere(['in', 'id', array_keys($ids)]);
        return $this;
    }
    
    /**
     * Returns the languages that the provided category should be validated against.
     * The 'frontend' category has to be completely translated.
     * For the other categories, only the application language is necessary.
     * 
     * @param   string  $category       The message category
     * @return  array   $languages      The translation languages
     */
    protected function languages($category = '') {
        // For the frontend all languages have to be translated
        if ($category == 'frontend') {
            $languages = Yii::$app->getI18n()->languages;       
        } else {            
            $languages = [Yii::$app->language];    
        }
        
        return $languages;    
    }
}
