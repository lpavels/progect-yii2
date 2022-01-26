<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\DishesCategory;
use common\models\RecipesCollection;
use common\models\CulinaryProcessing;

/* @var $this yii\web\View */
/* @var $model common\models\Dishes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dishes-form">

    <?
    $dishes_category = DishesCategory::find()->all();
    $dishes_category_items = ArrayHelper::map($dishes_category,'id','name');
    $recipes_collection = RecipesCollection::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->all();
    $recipes_collection_items = ArrayHelper::map($recipes_collection,'id','name');
    $culinary_processing = CulinaryProcessing::find()->all();
    $culinary_processing_items = ArrayHelper::map($culinary_processing,'id','name');
    ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['autocomplete' => 'off']) ?>

    <?= $form->field($model, 'dishes_category_id')->dropDownList($dishes_category_items) ?>

    <?= $form->field($model, 'number_of_dish')->textInput(['autocomplete' => 'off']) ?>

    <?= $form->field($model, 'recipes_collection_id')->dropDownList($recipes_collection_items)->label('Собственное имя сборника(отображаемое в программе)') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->label('Технология приготовления') ?>

    <?= $form->field($model, 'culinary_processing_id')->dropDownList($culinary_processing_items) ?>

    <?= $form->field($model, 'yield')->textInput() ?>

    <?= $form->field($model, 'dishes_characters')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 'number_of_dish')->textInput(['autocomplete' => 'off']) ?>

    <?= $form->field($model, 'techmup_number')->textInput(['autocomplete' => 'off']) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Сохранить', ['class' => 'btn main-button-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
