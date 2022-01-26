<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MenusDishes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menus-dishes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'menu_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'days_id')->hiddenInput()->label(false)?>

    <?= $form->field($model, 'nutrition_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'dishes_id')->textInput() ?>

    <?= $form->field($model, 'yield')->textInput() ?>

    <?//= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
