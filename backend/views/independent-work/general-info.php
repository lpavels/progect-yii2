<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\Kids */

if (Yii::$app->user->can('kid') || Yii::$app->user->can('school1011') || Yii::$app->user->can('school14') || Yii::$app->user->can('school511') || Yii::$app->user->can('school56') || Yii::$app->user->can('school59') || Yii::$app->user->can('school79') || Yii::$app->user->can('student')) $this->title = 'Общая информация';
else $this->title = 'Общая информация о ребёнке';

$two_column = ['options' => ['class' => 'row mt-1'], 'labelOptions' => ['class' => 'col-sm-7 col-form-label pb-0']];
?>

    <div class="general-info container">

        <h1 class=""><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sex', $two_column)->dropDownList($sex, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'height', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'mass', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'age', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <? if ($type_org == 1)
        {
            ?>
            <?= $form->field($model, 'class', $two_column)->dropDownList($num_class, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>
            <?
        } ?>

        <?= $form->field($model, 'days_id', $two_column)->dropDownList($m_days, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'charging', $two_column)->dropDownList($ar_YoN, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'additional_education', $two_column)->dropDownList($ar_YoN, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'sports_section', $two_column)->dropDownList($ar_YoN, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'sports_section1', $two_column)->dropDownList($sport_sel_name, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'sports_section2', $two_column)->dropDownList($sport_sel_name, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'walk', $two_column)->dropDownList($ar_YoN, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'sleep_day', $two_column)->dropDownList($ar_YoN, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'use_telephone', $two_column)->dropDownList($ar_YoN, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <?= $form->field($model, 'food_intake', $two_column)->dropDownList($num_eat, ['class' => 'form-control form-control-sm col-sm-2 pb-0']) ?>

        <div class="form-group mt-1">
            <?= Html::submitButton('Сохранить общую информацию', ['class' => 'btn btn-success btn-sm']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php
$sports_section = $model['sports_section'];
if (empty($sports_section)) $sports_section = 0;
$js = <<< JS
    $('form').on('beforeSubmit', function(){
        var form = $(this);
        var submit = form.find(':submit');
        submit.html('<span class="fa fa-spin fa-spinner"></span> Пожалуйста, подождите...');
        submit.prop('disabled', true);
    });

    sports_section = $sports_section;
    kidsSportSelect = $('#kids-sports_section');
    kidsSportSelect1 = $('.field-kids-sports_section1');
    kidsSportSelect2 = $('.field-kids-sports_section2');
    
    if (sports_section !== 1)
    {
        kidsSportSelect1.hide();
        kidsSportSelect2.hide();
    }
    
    kidsSportSelect.on('change',function() {
        if (kidsSportSelect.val() === '1')
        {
            kidsSportSelect1.show();
            kidsSportSelect2.show();
        }
        else 
        {
            kidsSportSelect1.hide();
            kidsSportSelect2.hide();
        }
    })

JS;
$this->registerJs($js, View::POS_READY);