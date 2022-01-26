<?php

use common\models\AgeInfo;
use common\models\FeedersCharacters;
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

$show_items = [
    1 => 'Не показывать никому',
    2 => 'Показать всем',
    3 => 'Показать только школам',
    4 => 'Показать только детским садам',
    5 => 'Показать только интернатам',
    6 => 'Показать школам и детским садам',
];

$param3 = ['options' =>[ $model->show_indicator => ['Selected' => true]], 'class'=>'form-control col-11 col-md-3'];


?>
<div class="menus-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin();

    ?>

    <?//= $form->field($model, 'organization_id')->textInput() ?>

    <?= $form->field($model, 'show_indicator')->dropDownList($show_items, $param3) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'age')->dropDownList($Age_items) ?>

    <?= $form->field($model, 'characters')->dropDownList($characters_items) ?>

    <?= $form->field($model, 'cycles')->textInput() ?>

    <?= $form->field($model, 'days1')->checkbox() ?>

    <?= $form->field($model, 'days2')->checkbox() ?>

    <?= $form->field($model, 'days3')->checkbox() ?>

    <?= $form->field($model, 'days4')->checkbox() ?>

    <?= $form->field($model, 'days5')->checkbox() ?>

    <?= $form->field($model, 'days6')->checkbox() ?>

    <?= $form->field($model, 'days7')->checkbox() ?>

    <?= $form->field($model, 'nutrition1')->checkbox() ?>

    <?= $form->field($model, 'nutrition2')->checkbox() ?>

    <?= $form->field($model, 'nutrition3')->checkbox() ?>

    <?= $form->field($model, 'nutrition4')->checkbox() ?>

    <?= $form->field($model, 'nutrition5')->checkbox() ?>

    <?= $form->field($model, 'nutrition6')->checkbox() ?>

    <?= $form->field($model, 'date_start')->textInput(['class'=>'datepicker-here form-control', 'placeholder'=>'ДД.ММ.ГГГГ']) ?>

    <?= $form->field($model, 'date_end')->textInput(['class'=>'datepicker-here form-control', 'placeholder'=>'ДД.ММ.ГГГГ']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
