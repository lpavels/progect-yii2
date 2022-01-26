<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products Changes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="products-change-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'products_id',
                'value' => function($model){
                    return \common\models\Products::findOne($model->products_id)->name;
                },

                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
            ],
            [
                'attribute' => 'change_products_id',
                'value' => function($model){
                    return \common\models\Products::findOne($model->change_products_id)->name;
                },

                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
            ],
            //'created_at',

            [
                'header' => 'Удалить',
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
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
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
