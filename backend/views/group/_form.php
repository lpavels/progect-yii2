<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Group */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-form">

    <?php
    $two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-4 col-form-label font-weight-bold']];
    $items = [
        1 => '1-2',
        4 => '2-3',
        2 => '3-4',
        5 => '4-5',
        3 => '5-7',
    ];
    $params = [
        'class'=> 'form-control col-4',
    ];
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name', $two_column)->textInput(['maxlength' => true,'class'=> 'form-control col-4']) ?>

    <?= $form->field($model, 'group_age', $two_column)->dropDownList($items, $params) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить группу', ['class' => 'btn btn-sm main-button-3 form-control col-8 mt-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
