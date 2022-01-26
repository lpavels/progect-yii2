<?php

use common\models\AgeInfo;
use common\models\FeedersCharacters;
use common\models\Organization;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Menus */

$this->title = 'Добавление меню';
$this->params['breadcrumbs'][] = ['label' => 'Menuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$Age = AgeInfo::find()->all();
$Age_items = ArrayHelper::map($Age, 'id', 'name');

$characters = FeedersCharacters::find()->all();
$characters_items = ArrayHelper::map($characters, 'id', 'name');

$day_pars = [
    2 => 'Многодневное',
    1 => 'Однодневное',
];

if (Yii::$app->user->can('minobr') || Yii::$app->user->can('rospotrebnadzor_nutrition'))
{
    $organization_id = Yii::$app->user->identity->organization_id;
    $region_id = Organization::findOne($organization_id)->region_id;
    $municipalities = \common\models\Municipality::find()->where(['region_id' => $region_id])->all();
    $municipality_null = array(-1 => 'ОСТАВИТЬ ПУСТЫМ И НЕ ЗАГРУЖАТЬ ОРГАНИЗАЦИЮ');
    $municipality_items = ArrayHelper::map($municipalities, 'id', 'name');
    $municipality_items = ArrayHelper::merge($municipality_null, $municipality_items);

    $organization_items = ['0' => 'ВЫБЕРИТЕ МУНИЦИПАЛЬНЫЙ РАЙОН ИЛИ ОСТАВЬТЕ ПУСТЫМ'];
}
?>

<style>
    .day-container{
        display:flex;
        flex-wrap: wrap;
        justify-content: space-around;
    }
    .nutrition-container{
        display:flex;
        flex-wrap: wrap;
        justify-content: space-around;
        padding: 5px;
    }
    .border-block{
        border:1px solid #ced4da;
        border-radius: 10px;
        margin-bottom: 25px;
        padding: 5px;
    }
</style>
<div class="menus-create container">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin();

    ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

    <?= $form->field($model, 'age')->dropDownList($Age_items) ?>

    <?= $form->field($model, 'characters')->dropDownList($characters_items) ?>

    <?//if(Yii::$app->user->can('rospotrebnadzor_nutrition') ||Yii::$app->user->can('minobr')){?>
        <?= $form->field($model, 'odno_vnogodnev')->dropDownList($day_pars)->label('Опция дней') ?>
    <?//}?>

    <?= $form->field($model, 'cycles')->textInput(['autocomplete' => 'off']) ?>
    <div class="border-block" id="day-indicator">
        <p style="font-size: 18px;"><b>Дни меню <small>(отметьте нужные варианты галочками)</small></b></p>
        <div class="day-container">
            <?= $form->field($model, 'days1')->checkbox() ?>

            <?= $form->field($model, 'days2')->checkbox() ?>

            <?= $form->field($model, 'days3')->checkbox() ?>

            <?= $form->field($model, 'days4')->checkbox() ?>

            <?= $form->field($model, 'days5')->checkbox() ?>

            <?= $form->field($model, 'days6')->checkbox() ?>

            <?= $form->field($model, 'days7')->checkbox() ?>
        </div>
    </div>

    <div class="border-block">
        <p style="font-size: 18px;"><b>Приемы пищи <small>(отметьте нужные варианты галочками)</small></b></p>
        <div class="nutrition-container">
            <?= $form->field($model, 'nutrition1')->checkbox() ?>

            <?= $form->field($model, 'nutrition2')->checkbox() ?>

            <?= $form->field($model, 'nutrition3')->checkbox() ?>

            <?= $form->field($model, 'nutrition4')->checkbox() ?>

            <?= $form->field($model, 'nutrition5')->checkbox() ?>

            <?= $form->field($model, 'nutrition6')->checkbox() ?>
        </div>
    </div>


    <?= $form->field($model, 'date_start')->textInput(['type'=>'date', 'class'=>'form-control', 'autocomplete' => 'off']) ?>

    <?= $form->field($model, 'date_end')->textInput(['type'=>'date', 'class'=>'form-control', 'autocomplete' => 'off']) ?>

    <?if(Yii::$app->user->can('rospotrebnadzor_nutrition') ||Yii::$app->user->can('minobr')){?>
        <div class="border-block mt-5">
            <p class="text-center text-danger">Необязательные поля</p>
            <?= $form->field($model, 'municipality')->dropDownList($municipality_items, [
                'class' => 'form-control text-center',
                'onchange' => '
                      $.get("../menus/orglist?id="+$(this).val(), function(data){
                        $("select#menuform-organization_id").html(data);
                      });'
            ]); ?>
            <?= $form->field($model, 'organization_id')->dropDownList($organization_items) ?>
        </div>
    <?}?>

    <div class="form-group justify-content-center text-center" style="margin-bottom: 135px">
        <?= Html::submitButton('Сохранить', ['class' => 'mt-2 btn main-button-3 col-11 col-md-6']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?
//print_r($data);
$script = <<< JS

/*window.onload = function() {
    $('.field-menusdishes-created_at').hide();
    $('.field-menusdishes-nutrition_id').hide();
    }*/

    
    var field = $('#menuform-odno_vnogodnev');
    field.on('change', function () {
           if (field.val() === "1") {
               console.log('222');
               $('.field-menuform-cycles').hide();
               $('.field-menuform-date_end').hide();
               $('#menuform-cycles').val('1');
               $('#menuform-date_end').val('1');
               $('.field-menuform-date_start label').text('Дата меню');
               $('#day-indicator').hide();
           }
            else if(field.val() === "2") {
               console.log('sss');
              $('#menuform-cycles').val('');
              $('.field-menuform-cycles').show();
              $('.field-menuform-date_end').show();
              $('#menuform-date_end').val('');
              $('#day-indicator').show();
              $('.field-menuform-date_start label').text('Дата начала');
           }
    });
    field.trigger('change');
    
    
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
