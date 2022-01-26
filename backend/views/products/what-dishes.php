<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\ProductsCategory;
use common\models\ProductsSubcategory;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список блюд, содержащий продукт('.\common\models\Products::findOne($id)->name.')';
$this->params['breadcrumbs'][] = $this->title;


echo Html::a('Вернутся назад', ['index']);
?>
<div class="products-index">

    <h1 class="text-center mb-5"><?= Html::encode($this->title) ?></h1>
    <table class="table_th0 table-hover table-responsive last">
        <thead>
        <tr>
            <th>№</th>
            <th>Название блюда</th>
            <th>Сборник рецептур</th>
            <th>Перейти к редактированию состава</th>
        </tr>
        </thead>
        <tbody>
    <?$count = 0; foreach($dishes_products as $d_product){ $count ++;?>
        <tr>
            <td><?= $count?></td>
            <?$dish = \common\models\Dishes::findOne($d_product->dishes_id)?>
            <td><b><?= $dish->name;?></b></td>
            <td style="width: 850px"><?= \common\models\RecipesCollection::findOne($dish->recipes_collection_id)->name;?></td>
            <td style="width: 50px"><?= Html::a('<span style="color: white" class="glyphicon glyphicon-plus"></span>', ['dishes/addproduct/', 'id' => $d_product->dishes_id], [
                    'class' => 'btn btn-warning text-center',
                ]) ?></td>
        </tr>
    <?}?>
        </tbody>
    </table>
    <?if(empty($dishes_products)){ ?>
        <p style="color:red;"><b>Этот продукт нигде не используется</b></p>
    <?}?>
</div>
