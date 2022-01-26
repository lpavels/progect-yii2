<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
//use kartik\date\DatePicker;
//require 'C:\OSPanel\domains\yii2\vendor\kartik-v\yii2-widget-datepicker\src\DatePicker.php';
use common\models\AgeInfo;
use common\models\FeedersCharacters;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Menus */

$this->title = 'Настройка: ' . $menus->name;
$this->params['breadcrumbs'][] = ['label' => 'Menuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $menus->id, 'url' => ['view', 'id' => $menus->id]];

$Age = AgeInfo::find()->all();
$Age_items = ArrayHelper::map($Age, 'id', 'name');

$characters = FeedersCharacters::find()->all();
$characters_items = ArrayHelper::map($characters, 'id', 'name');

$show_items = [
    1 => 'Не показывать никому',
    2 => 'Показать всем',
    3 => 'Показать только школам',
    4 => 'Показать только детским садам',
    5 => 'Показать только интернатам',
    6 => 'Показать только лагерям',
    7 => 'Показать только палаточным лагерям',
    8 => 'Показать школам и детским садам',
    9 => 'Показать интернатам и лагерям',
];

$param1 = ['options' =>[ $menus->feeders_characters_id => ['Selected' => true]], 'class'=>'form-control col-11 col-md-3'];
$param2 = ['options' =>[ $menus->age_info_id => ['Selected' => true]], 'class'=>'form-control col-11 col-md-3'];
$param3 = ['options' =>[ $menus->show_indicator => ['Selected' => true]], 'class'=>'form-control col-11 col-md-3'];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="menus-update">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <br><br>
    <?php $form = ActiveForm::begin(); ?>
    <?php
    $two_column = ['options'=>['class'=>'row justify-content-center mt-3'], 'labelOptions' => ['class' => 'col-11 col-md-3 col-form-label font-weight-bold']];
    ?>
    <?= $form->field($model, 'show_indicator', $two_column)->dropDownList($show_items, $param3) ?>
    <br><br>

    <?= $form->field($model, 'name', $two_column)->textInput(['value' => $menus->name, 'class'=>'form-control col-11 col-md-3']) ?>

    <?= $form->field($model, 'age_info_id', $two_column)->dropDownList($Age_items, $param2) ?>

    <?= $form->field($model, 'feeders_characters_id', $two_column)->dropDownList($characters_items, $param1) ?>

    <?= $form->field($model, 'cycle', $two_column)->textInput(['value' => $menus->cycle, 'class'=>'form-control col-11 col-md-3 mb-3']) ?>

    <?= $form->field($model, 'date_start', $two_column)->textInput([ 'value' => date("d.m.Y", $menus->date_start), 'class'=>'datepicker-here form-control col-11 col-md-3']) ?>

    <?= $form->field($model, 'date_end', $two_column)->textInput([ 'value' => date("d.m.Y", $menus->date_end), 'class'=>'datepicker-here form-control col-11 col-md-3 mb-3']) ?>

    <div class="form-group justify-content-center text-center">
        <?= Html::submitButton('Сохранить', ['class' => 'mt-2 btn main-button-3 col-11 col-md-6']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
