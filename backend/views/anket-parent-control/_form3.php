<?php

use common\models\AnketParentControl;
use common\models\Menus;
use common\models\Organization;
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



$model_menus = new Menus();
$organization_id = Yii::$app->user->identity->organization_id;

$region_id = Organization::findOne($organization_id)->region_id;

$municipalities = \common\models\Municipality::find()->where(['region_id' => $region_id])->all();
$municipality_null = array(0 => 'Все муниципальные округа ...');
$municipality_items = ArrayHelper::map($municipalities, 'id', 'name');
$municipality_items = ArrayHelper::merge($municipality_null, $municipality_items);
$organization = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id])->all();


$organization_null = array('' => 'Все организации ...');
$organization_items = ArrayHelper::map($organization, 'id', 'title');
$organization_items = ArrayHelper::merge($organization_null, $organization_items);


?>

<div class="anket-parent-control-form container mt-5">


    <p style="color: #0ea1a8">Всего мероприятий проведено: <? $count = AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => 3])->count(); echo $count?></p>
    <?if($count != 0){?>
        <p style="color: #0ea1a8">Дата последнего мероприятия: <?= date('d.m.Y', AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => 3])->orderBy('id desc')->one()->date);?></p>
    <?}?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput(['type'=>'date','class'=>'form-control', 'autocomplete' => 'off'])->label('Дата проведения мероприятия общественного контроля') ?>

    <?= $form->field($model_menus, 'parent_id')->dropDownList($municipality_items, [
        'class' => 'form-control text-center',
        'onchange' => '
                  $.get("../menus/orglist2?id="+$(this).val(), function(data){
                    $("select#anketparentcontrol-organization_id").html(data);
                  });'
    ])->label('Муниципальный округ'); ?>

    <?= $form->field($model, 'organization_id')->dropDownList($organization_items, [
        'class' => 'form-control text-center',
    ])->label('Образовательное учреждение'); ?>

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
