<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingActual */
/* @var $form yii\widgets\ActiveForm */

$yes_no_items = [
    '' => 'Выбрать',
    1 => 'Да'
]
?>

<div class="training-actual-form container">

    <?php $form = ActiveForm::begin(); ?>

    <?//= $form->field($model, 'kids_id')->textInput() ?>

    <?//= $form->field($model, 'field1')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field2')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field3')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field4')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field5')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field6')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field7')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field8')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field9')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field10')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field11')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field12')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'field13')->dropDownList($yes_no_items) ?>

    <?//= $form->field($model, 'field14')->dropDownList($yes_no_items) ?>

    <?//= $form->field($model, 'creat_at')->textInput() ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Сохранить', ['class' => 'btn main-button-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
