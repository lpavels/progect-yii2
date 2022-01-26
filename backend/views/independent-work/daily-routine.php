<?php

use common\models\SportsSectionName;
use kartik\time\TimePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\DailyRoutine */

$this->title = 'Общая информация об учебном дне';

$two_column = ['options' => ['class' => 'row mt-1'], 'labelOptions' => ['class' => 'col-sm-7 col-form-label pb-0']];
?>

    <div class="daily-routine container w-75">

        <h1 class=""><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>

        <?
        if ($model2['charging'] == 1)
        {
            echo $form->field($model, 'field2', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']);
        }


        if ($model2['walk'] == 1)
        {
            echo $form->field($model, 'field15', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']);
        }

        if ($model2['sleep_day'] == 1)
        {
            echo $form->field($model, 'field17', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']);
        }


        if ($model2['additional_education'] == 1)
        {
            echo $form->field($model, 'field4', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']);
        }

        if ($model2['sports_section'] == 1)
        {
            echo $form->field($model, 'field6', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0'])->label('Укажите продолжительность занятий спортом "' . SportsSectionName::findOne($model2['sports_section1'])->name . '" в минутах');

            if (!empty($model2['sports_section2']))
            {
                echo $form->field($model, 'field7', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0'])->label('Укажите продолжительность занятий спортом "' . SportsSectionName::findOne($model2['sports_section2'])->name . '" в минутах');
            }
        }
        ?>

        <div class="font-weight-bold mt-1 pb-0">Продолжительность нахождения в пути до школы, спортивной секции, кружка (студии) и обратно в минутах</div>
        <?= $form->field($model, 'field8', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']); ?>
        <?= $form->field($model, 'field9', $two_column)->textInput(['type' => 'number', 'class' => 'form-control form-control-sm col-sm-2 pb-0']); ?>

        <div class="font-weight-bold mt-1 pb-0">Характеристика основных временных показателей</div>
        <?= $form->field($model, 'field18', $two_column)->widget(TimePicker::className(), [
            'options' => ['class' => 'form-control form-control-sm col-sm-10 pb-0','value'=>'06:00'],
            'pluginOptions' => [
                'showMeridian' => false,
                'minuteStep' => 15,
                'template' => false,
            ]
        ]); ?>
        <?= $form->field($model, 'field19', $two_column)->widget(TimePicker::className(), [
            'options' => ['class' => 'form-control form-control-sm col-sm-10 pb-0','value'=>'21:00'],
            'pluginOptions' => [
                'showMeridian' => false,
                'minuteStep' => 15,
                'template' => false
            ]
        ]); ?>
        <?= $form->field($model, 'field20', $two_column)->widget(TimePicker::className(), [
            'options' => ['class' => 'form-control form-control-sm col-sm-10 pb-0','value'=>'09:00'],
            'pluginOptions' => [
                'showMeridian' => false,
                'minuteStep' => 15,
                'template' => false
            ]
        ]); ?>
        <?= $form->field($model, 'field21', $two_column)->widget(TimePicker::className(), [
            'options' => ['class' => 'form-control form-control-sm col-sm-10 pb-0','value'=>'13:00'],
            'pluginOptions' => [
                'showMeridian' => false,
                'minuteStep' => 15,
                'template' => false
            ]
        ]); ?>

        <div class="form-group mt-1">
            <?= Html::submitButton('Сохранить информацию об учебном дне и сформировать отчёт', ['class' => 'btn btn-success btn-sm']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php
$js = <<< JS
    $('form').on('beforeSubmit', function(){
        var form = $(this);
        var submit = form.find(':submit');
        submit.html('<span class="fa fa-spin fa-spinner"></span> Пожалуйста, подождите...');
        submit.prop('disabled', true);
    });
JS;
$this->registerJs($js, View::POS_READY);