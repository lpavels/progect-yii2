<?php

use yii\bootstrap4\Html;
use common\models\ProductsCategory;
use common\models\ProductsSubcategory;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Products */

$this->title = 'Добавление продукта';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="products-create container">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin();

    $products_category = ProductsCategory::find()->all();
    $products_category_item = ArrayHelper::map($products_category, 'id','name');
    /*находим самую первую категорию, для того чтобы вычислить ее подкатегории*/
    $products_category = ProductsCategory::find()->one();
    $products_subcategory = ProductsSubcategory::find()->where(['product_category_id' => $products_category->id])->all();
    $products_subcategory_item = ArrayHelper::map($products_subcategory, 'id','name');

    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?/*= $form->field($model, 'products_category_id')->dropDownList($products_category_item) */?>



    <?= $form->field($model, 'products_category_id')->dropDownList($products_category_item,
        [
            'onchange'=>'
                $.get( "../products-subcategory/search?id='.'"+$(this).val(), function(data) {
                $("select#products-products_subcategory_id").html(data);
                 //console.log("я грут");
                });'
        ]);
    ?>
    <?= $form->field($model, 'products_subcategory_id')->dropDownList($products_subcategory_item) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'water')->textInput() ?>

    <?= $form->field($model, 'protein')->textInput() ?>

    <?= $form->field($model, 'fat')->textInput() ?>

    <?= $form->field($model, 'carbohydrates_total')->textInput() ?>

    <?= $form->field($model, 'carbohydrates_saccharide')->textInput() ?>

    <?= $form->field($model, 'carbohydrates_starch')->textInput() ?>

    <?= $form->field($model, 'carbohydrates_lactose')->textInput() ?>

    <?= $form->field($model, 'carbohydrates_sacchorose')->textInput() ?>

    <?= $form->field($model, 'carbohydrates_cellulose')->textInput() ?>

    <?= $form->field($model, 'dust_total')->textInput() ?>

    <?= $form->field($model, 'dust_nacl')->textInput() ?>

    <?= $form->field($model, 'apple_acid')->textInput() ?>

    <?= $form->field($model, 'na')->textInput() ?>

    <?= $form->field($model, 'k')->textInput() ?>

    <?= $form->field($model, 'ca')->textInput() ?>

    <?= $form->field($model, 'mg')->textInput() ?>

    <?= $form->field($model, 'p')->textInput() ?>

    <?= $form->field($model, 'fe')->textInput() ?>

    <?= $form->field($model, 'i')->textInput() ?>

    <?= $form->field($model, 'se')->textInput() ?>

    <?= $form->field($model, 'f')->textInput() ?>

    <?= $form->field($model, 'vitamin_a')->textInput() ?>

    <?= $form->field($model, 'vitamin_b_carotene')->textInput() ?>

    <?= $form->field($model, 'vitamin_b1')->textInput() ?>

    <?= $form->field($model, 'vitamin_b2')->textInput() ?>

    <?= $form->field($model, 'vitamin_pp')->textInput() ?>

    <?= $form->field($model, 'vitamin_c')->textInput() ?>

    <?= $form->field($model, 'vitamin_d')->textInput() ?>

    <?= $form->field($model, 'energy_kkal')->textInput() ?>

    <?= $form->field($model, 'energy_kdj')->textInput() ?>

    <?= $form->field($model, 'salt')->textInput() ?>

    <?= $form->field($model, 'sahar')->textInput() ?>

    <?//= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
