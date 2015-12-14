<?php if (Yii::$app->getSession()->hasFlash('i18n')): ?>
<div class="alert alert-success">
    <?= Yii::$app->getSession()->getFlash('i18n') ?>
</div>
<?php endif; ?>

<?php if (Yii::$app->getSession()->hasFlash('i18n-error')): ?>
<div class="alert alert-danger">
    <?= Yii::$app->getSession()->getFlash('i18n-error') ?>
</div>
<?php endif; ?>