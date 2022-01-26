<?php

use common\models\Group;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\KidsQ */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kids-q-form">

    <?php

    $groups_null = array('' => 'Выберите группу ...');
    $groups = Group::find()->where(['user_id'=>Yii::$app->user->identity->id])->all();
    $groups_items = ArrayHelper::map($groups, 'id', 'name');
    $groups_items = ArrayHelper::merge($groups_null,$groups_items);

    $two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-4 col-form-label font-weight-bold']];
    $params = [
        'class'=> 'form-control col-4',
    ];
    $params2 = [
        0 =>  'женский',
        1 =>  'мужской',
    ];

    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group_id', $two_column)->dropDownList($groups_items, $params) ?>
    <?= $form->field($model, 'sex', $two_column)->dropDownList($params2, $params) ?>

    <?= $form->field($model, 'lastname', $two_column)->textInput(['maxlength' => true,'class'=> 'form-control col-4']) ?>

    <?= $form->field($model, 'name', $two_column)->textInput(['maxlength' => true,'class'=> 'form-control col-4']) ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-sm main-button-3 col-8 mt-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
