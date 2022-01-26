<?php

use yii\bootstrap4\Html;
use common\models\ProductsCategory;
use common\models\ProductsSubcategory;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Products */

$this->title = 'Редактирование продукта: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$products_category = ProductsCategory::find()->all();
$products_category_item = ArrayHelper::map($products_category, 'id','name');
$products_category = ProductsSubcategory::find()->where(['product_category_id'=>'1'])->one();
$products_subcategory = ProductsSubcategory::find()->all();
$products_subcategory_item = ArrayHelper::map($products_subcategory, 'id','name');



?>
<div class="products-update container">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
