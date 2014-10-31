<?php

namespace Zelenin\yii\modules\I18n\controllers;

use Yii;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Zelenin\yii\modules\I18n\models\search\SourceMessageSearch;
use Zelenin\yii\modules\I18n\models\SourceMessage;
use Zelenin\yii\modules\I18n\Module;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new SourceMessageSearch;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'gridViewColumns' => $this->getGridViewColumns($searchModel, $dataProvider)
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        /** @var SourceMessage $model */
        $model = $this->findModel($id);
        $model->initMessages();

        if (Model::loadMultiple($model->messages, Yii::$app->getRequest()->post()) && Model::validateMultiple($model->messages)) {
            $model->saveMessages();
            Yii::$app->getSession()->setFlash('i18n', Module::t('Updated'));
            
            // Take appropriate action based on the pushed button
            if (isset($post['close'])) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }
        
        return $this->render('update', ['model' => $model]);
    }
    
    /**
     * Deletes an existing Publication model.
     * If deletion is successful, the browser will be redirected to the 'index' publication.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        
        // Set flash message
        Yii::$app->getSession()->setFlash('i18n', Yii::t('app', 'The item has been deleted'));

        return $this->redirect(['index']);
    }

    /**
     * @param array|integer $id
     * @return SourceMessage|SourceMessage[]
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $query = SourceMessage::find()->where('id = :id', [':id' => $id]);
        $models = is_array($id)
            ? $query->all()
            : $query->one();
        if (!empty($models)) {
            return $models;
        } else {
            throw new NotFoundHttpException(Module::t('The requested page does not exist'));
        }
    }
    
    /**
     * Returns the columns that are used in the gridview
     * 
     * @return  array
     */
    protected function getGridViewColumns($searchModel, $dataProvider)
    {
        // Build the gridview columns
        $gridViewColumns = [];
        
        // Add id column
        if (Yii::$app->user->can('Superadmin')) {
            $gridViewColumns[] = [
                'attribute' => 'id',
                'value' => function ($model, $index, $dataColumn) {
                    return $model->id;
                },
                'filter' => false
            ];            
        }
        
        // Add message column
        $gridViewColumns[] = [
            'attribute' => 'message',
            'format' => 'raw',
            'value' => function ($model, $index, $widget) {
                return Html::a($model->message, ['update', 'id' => $model->id], ['data' => ['pjax' => 0]]);
            }
        ];
        
        // Add category column
        if (Yii::$app->user->can('Superadmin')) {
            $gridViewColumns[] = [
                'attribute' => 'category',
                'value' => function ($model, $index, $dataColumn) {
                    return $model->category;
                },
                'filter' => ArrayHelper::map($searchModel::getCategories(), 'category', 'category')
            ];            
        }
        
        // Add status column
        $gridViewColumns[] = [
            'attribute' => 'status',
            'value' => function ($model, $index, $widget) {
                return '';
            },
            'filter' => Html::dropDownList($searchModel->formName() . '[status]', $searchModel->status, $searchModel->getStatus(), [
                'class' => 'form-control',
                'prompt' => ''
            ])
        ];
        
        // Add action column
        if (Yii::$app->user->can('Superadmin')) {
            $gridViewColumns[] = [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'updateOptions' => ['title' => Yii::t('app', 'Update'), 'data-toggle' => 'tooltip'],
                'deleteOptions' => ['title' => Yii::t('app', 'Delete'), 'data-toggle' => 'tooltip'],
                'width' => '120px',
            ];
        }
        
        return $gridViewColumns;    
    }
}
