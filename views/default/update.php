<?php
/**
 * @var View $this
 * @var SourceMessage $model
 */

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\url;
use Zelenin\yii\modules\I18n\models\SourceMessage;
use Zelenin\yii\modules\I18n\Module;

$this->title = Module::t('Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('Translations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="message-update">
    <div class="message-form">

        <?php // Flash messages ?>
        <?php echo $this->render('_flash_messages'); ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Module::t('Source message') ?>
                <?php if (Yii::$app->user->can('Superadmin')) : ?> <strong>(<?php echo $model->category; ?>)</strong><?php endif; ?>
            </div>
            <div class="panel-body"><?= Html::encode($model->message) ?></div>
        </div>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <?php foreach ($model->messages as $language => $message) : ?>
            <?php if (in_array($language, array_keys(Yii::$app->params['languages']))): ?>
            <?= $form->field($model->messages[$language], '[' . $language . ']translation', ['options' => ['class' => 'form-group col-sm-6']])->textArea(['rows' => 5])->label(Yii::$app->params['languages'][$language]) ?>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="form-group buttons">
            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::submitButton(Yii::t('app', 'Update & close'), ['class' => 'btn btn-default', 'name' => 'close']) ?>
            <?= Html::a(Yii::t('app', 'Close'), Url::previous(), ['class' => 'btn btn-danger']) ?>
        </div>
        <?php $form::end(); ?>
    </div>
</div>
