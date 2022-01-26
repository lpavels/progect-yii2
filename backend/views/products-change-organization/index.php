<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Замена продуктов в меню';
$this->params['breadcrumbs'][] = $this->title;



$products_items = \common\models\ProductsChange::find()->all();
foreach ($products_items as $p_item){
    $products_ids[] = $p_item->products_id;
    $products_change_ids[] = $p_item->change_products_id;
}

    $products_change = \common\models\Products::find()->where(['id' => $products_change_ids])->all();
    $products_change = ArrayHelper::map($products_change, 'id', 'name');


$products_null = array('' => 'Выберите продукт');
$products= \common\models\Products::find()->where(['id' => $products_ids])->orderBy(['name'=> SORT_ASC])->all();
$products = ArrayHelper::map($products, 'id', 'name');
$products = ArrayHelper::merge($products_null,$products);

?>
<div class="products-change-organization-index">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="container mb-30 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?= $form->field($model, 'products_id')->dropDownList($products, [
                        'onchange' => '
                        $.get("../products-change-organization/productslist?id="+$(this).val(), function(data){
                        console.log(data);
                        $("select#productschangeorganization-change_products_id").html(data);
                        });']);
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'change_products_id')->dropDownList($products_change)->label('Продукт, на который нужно выполнить замену'); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Заменить', ['name'=>'identificator', 'value' => 'view', 'class' => 'btn main-button-3 mb-3 beforeload']) ?>
                <button class="btn main-button-3 load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Посмотреть...
                </button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'organization_id',
            [
                'attribute' => 'products_id',
                'value' => function($model){
                    return \common\models\Products::findOne($model->products_id)->name;
                },

                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
            ],
            [
                'header' => 'Продукт на который выполнена замена',
                'attribute' => 'change_products_id',
                'value' => function($model){
                    return \common\models\Products::findOne($model->change_products_id)->name;
                },

                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
            ],
            //'created_at',

            [
                'header' => 'Отмена',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'action-column text-center'],
                'buttons' => [

                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Редактировать'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm main-button-edit'
                        ]);
                    },

                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"> Отменить</span>', $url, [
                            'title' => Yii::t('yii', 'Удалить'),
                            'data-toggle' => 'tooltip',
                            'class' => 'btn btn-sm main-button-delete mt-1',
                            'data' => ['confirm' => 'Вы уверены что хотите удалить?'],
                        ]);
                    },
                ],
            ]
        ],
    ]); ?>


</div>
<style>
    .grid-view{
        max-width: 1000px!important;
        margin: 0 auto;
    }
</style>