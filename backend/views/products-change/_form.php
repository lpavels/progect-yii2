<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ProductsChange */
/* @var $form yii\widgets\ActiveForm */

$products = \common\models\Products::find()->orderBy(['name'=> SORT_ASC])->all();
$products = ArrayHelper::map($products, 'id', 'name');
?>

<div class="products-change-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'products_id')->dropDownList($products) ?>

    <?= $form->field($model, 'change_products_id')->dropDownList($products) ?>

    <?//= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
