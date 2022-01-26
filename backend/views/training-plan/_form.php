<?php

use common\models\Group;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingPlan */
/* @var $form yii\widgets\ActiveForm */

$groups_null = array('' => 'Выберите группу ...');
$groups = Group::find()->where(['user_id'=>Yii::$app->user->identity->id])->all();
$groups_items = ArrayHelper::map($groups, 'id', 'name');
$groups_items = ArrayHelper::merge($groups_null,$groups_items);
$params = [
    'class'=> 'form-control col-2',
];
$two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-4 col-form-label font-weight-bold']];
?>

<div class="training-plan-form">

    <?php $form = ActiveForm::begin();
    ?>

    <?= $form->field($model, 'group_id', $two_column)->dropDownList($groups_items, $params) ?>
    
    <?= $form->field($model, 'field1', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field2', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field3', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field4', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field5', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field6', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field7', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field8', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field9', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field10', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field11', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field12', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>

    <?= $form->field($model, 'field13', $two_column)->textInput(['class'=>'form-control col-2', 'type'=>'date']) ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success mt-3 form-control col-6']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
