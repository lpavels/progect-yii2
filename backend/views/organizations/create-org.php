<?php

use common\models\AuthAssignment;
use common\models\DepartmentEducation;
use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\MaskedInput;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Organization */

$this->title = 'Добавление  организации';

$this->params['breadcrumbs'][] = $this->title;

$p_cat_bd = ArrayHelper::map(FederalDistrict::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

$p_cat_bd2 = ArrayHelper::map(Region::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');


$municipality_arr = Municipality::find()->where(['region_id' => DepartmentEducation::findOne(['key_login_rpn'=>Yii::$app->user->identity->key_login])->region_id])->orderBy(['name' => SORT_ASC])->all();
if (empty($municipality_arr)) $municipality_arr =Municipality::find()->where(['region_id' => DepartmentEducation::findOne(['key_login_ministry_education'=>Yii::$app->user->identity->key_login])->region_id])->orderBy(['name' => SORT_ASC])->all();

$p_cat_bd3 = ArrayHelper::map($municipality_arr, 'id', 'name');

//$p_cat_bd3 = ArrayHelper::map(Municipality::find()->where(['region_id'=>DepartmentEducation::findOne(['key_login_rpn'=>Yii::$app->user->identity->key_login])->region_id])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

$items = [
    '1' => 'Общеобразовательная организация',
    '2' => 'Организация дошкольного образования',
    '3' => 'Организация профессионального образования',
    '4' => 'Организация дополнительного образования',
    '5' => 'Медицинская организация',
    '6' => 'Организация социального обслуживания',
    '8' => 'Физкультурно-спортивная организация',
    '9' => 'Иная'
];

$two_column = ['options' => ['class' => 'row justify-content-center mt-3'], 'labelOptions' => ['class' => 'col-11 col-md-3 col-form-label font-weight-bold']];
?>

    <div class="organization-create-org mt-3">
        <div class="text-center"><h4>Добавление организации</h4></div>

        <?php $form = ActiveForm::begin(); ?>

        <? if (Yii::$app->user->can('RPN'))
        {
            echo $form->field($model, 'municipality_id', $two_column)->dropDownList($p_cat_bd3, ['class' => 'form-control col-11 col-md-4']);
        } ?>

        <?= $form->field($model, 'title', $two_column)->textInput(['class' => 'form-control col-11 col-md-4']) ?>

        <?= $form->field($model, 'short_title', $two_column)->textInput(['class' => 'form-control col-11 col-md-4']) ?>

        <?= $form->field($model, 'address', $two_column)->textInput(['class' => 'form-control col-11 col-md-4']) ?>

        <? //= $form->field($model, 'status', $two_column)->textInput(['value' => '1', 'class' => 'form-control col-11 col-md-4']) ?>

        <? //= $form->field($model, 'federal_district_id', $two_column)->dropDownList($p_cat_bd, ['class' => 'form-control col-11 col-md-4']) ?>

        <? //= $form->field($model, 'region_id', $two_column)->dropDownList($items2, ['class' => 'form-control col-11 col-md-4']) ?>

        <?= $form->field($model, 'type_org', $two_column)->dropDownList($items, ['class' => 'form-control col-11 col-md-4']) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('Сохранить', ['class' => 'btn main-button-3 mt-3 col-7', 'name' => 'signup-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?
$script = <<< JS
$('#organization-federal_district_id').change(function() {
    var value = $('#organization-federal_district_id option:selected').val();
    console.log(value);
    $.ajax({
         url: "../organizations/search2",
              type: "GET",      // тип запроса SearchDistrict
              data: { // действия
                  'id': value
              },
              // Данные пришли
              success: function( data ) {
                  $("#organization-region_id").empty();
                  $("#organization-region_id").append(data);
              }
         })
});

$('#organization-region_id').change(function() {
    var value = $('#organization-region_id option:selected').val();
    console.log(value);
    $.ajax({
         url: "../organizations/search3",
              type: "GET",      // тип запроса SearchDistrict
              data: { // действия
                  'id': value
              },
              // Данные пришли
              success: function( data ) {
                  $("#organization-municipality_id").empty();
                  $("#organization-municipality_id").append(data);
              }
         })
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>