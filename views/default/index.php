<?php
/**
 * @var View $this
 * @var SourceMessageSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;
use Zelenin\yii\modules\I18n\models\search\SourceMessageSearch;
use Zelenin\yii\modules\I18n\Module;
use kartik\grid\GridView;

$this->title = Module::t('Translations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <?php // Title ?>
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php // Flash messages ?>
    <?php echo $this->render('_flash_messages'); ?>
    
    <?php // Gridview ?>
    <?php echo GridView::widget([
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'columns' => $gridViewColumns,
        'responsive' => true,
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => 88],
        'hover' => true,
        'pjax' => true,
        'export' => false,
    ]); ?>
</div>
