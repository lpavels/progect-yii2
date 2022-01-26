<?php

use common\models\AnketParentControl;
use common\models\Menus;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AnketParentControl */
/* @var $form yii\widgets\ActiveForm */

//$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
//$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');

$yes_no_items = [
     '' => 'Выберите вариант ответа',
    '1' => "Да",
    '0' => "Нет",
];
$yes_no_chast_items = [
    '' => 'Выберите вариант ответа',
    '1' => "Да",
    '0' => "Нет",
    '3' => "Частично",
];


$smena_item = [
    '' => 'Выберите смену',
    1 => '1',
    2 => '2',
];

$peremena_item = [
    '' => 'Выберите перемену',
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6',
    7 => '7',
    8 => '8',
];

?>

<div class="anket-parent-control-form container mt-5">

        <p style="color: #0ea1a8">Всего мероприятий проведено: <? $count = \common\models\AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => 1])->count(); echo $count?></p>
    <?if($count != 0){?>
        <p style="color: #0ea1a8">Дата последнего мероприятия: <?= date('d.m.Y', AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => 1])->orderBy('id desc')->one()->date);?></p>
    <?}?>

    <?php $form = ActiveForm::begin(); ?>

    <?//= $form->field($model, 'organization_id')->textInput() ?>

    <?= $form->field($model, 'date')->textInput(['type'=>'date','class'=>'form-control', 'autocomplete' => 'off']) ?>

    <?= $form->field($model, 'name')->textInput(['autocomplete' => 'off']) ?>

    <?= $form->field($model, 'smena')->dropDownList($smena_item) ?>

    <?= $form->field($model, 'peremena')->dropDownList($peremena_item) ?>

    <?= $form->field($model, 'question1')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question2')->dropDownList($yes_no_chast_items) ?>

    <?= $form->field($model, 'question3')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question5')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question4')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question6')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question7')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question8')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question9')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question10')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question11')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question12')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question13')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'question14')->dropDownList($yes_no_items) ?>

    <?= $form->field($model, 'count')->textInput(['autocomplete' => 'off']) ?>

    <?= $form->field($model, 'masa_porcii')->textInput(['autocomplete' => 'off']) ?>

    <p class="text-danger">Если число не является целым, то разделителем целой и дробной части является точка. (Например:2.85)</p>
    <?= $form->field($model, 'masa_othodov')->textInput(['autocomplete' => 'off']) ?>

    <?//= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group text-center">
            <?= Html::submitButton('Сохранить', ['class' => 'btn main-button-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
