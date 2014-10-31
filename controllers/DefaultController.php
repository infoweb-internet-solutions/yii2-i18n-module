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
            Yii::$app->getSession()->setFlash('success', Module::t('Updated'));
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', ['model' => $model]);
        }
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
            ],
        }
        
        return $gridViewColumns;    
    }
}
