<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\ProductsCategory;
use common\models\ProductsSubcategory;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Продукты';
$this->params['breadcrumbs'][] = $this->title;

$p_cat = array('' => 'Все ...');
$p_cat_bd = ArrayHelper::map(ProductsCategory::find()->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$p_cat = ArrayHelper::merge($p_cat,$p_cat_bd);


$sub_categories = array('' => 'Все ...');
$sub_categories_bd = ArrayHelper::map(ProductsSubcategory::find()->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$sub_categories = ArrayHelper::merge($sub_categories,$sub_categories_bd);


?>
<div class="products-index">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавление продукта', ['create'], ['class' => 'btn main-button-3']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'menus-table table-responsive'],
        'tableOptions' => [
            'class' => 'table table-bordered table-responsive'
        ],
        'rowOptions' => ['class' => 'grid_table_tr'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_table_th'],
            ],
            [
                'attribute' => 'name',
                'value' => 'name',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
            ],
            //'products_category_id',
            [
                'attribute' => 'products_category_id',
                'value' => function ($model) {
                    return $model->get_category($model->products_category_id)->name;
                },
                'filter' => $p_cat,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
            ],
            //'products_subcategory_id',
            [
                'attribute' => 'products_subcategory_id',
                'value' => function ($model) {
                    return $model->get_subcategory($model->products_subcategory_id)->name;
                },
                'filter' => $sub_categories,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
            ],
            [
                'attribute' => 'sort',
                'value' => 'sort',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle'],
            ],
            [
                'attribute' => 'Аллерген',
                'value' => function ($model) {
                    return $model->get_allergen($model->id);
                },
                'filter' => $p_cat,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
            ],
            [
                'attribute' => 'Соль/Сахар',
                'value' => function ($model) {
                    return $model->salt.'/'.$model->sahar;
                },
                //'filter' => $p_cat,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
            ],
            //'water',
            //'protein',
            //'fat',
            //'carbohydrates_total',
            //'carbohydrates_saccharide',
            //'carbohydrates_starch',
            //'carbohydrates_lactose',
            //'carbohydrates_sacchorose',
            //'carbohydrates_cellulose',
            //'dust_total',
            //'dust_nacl',
            //'apple_acid',
            //'na',
            //'k',
            //'ca',
            //'mg',
            //'p',
            //'fe',
            //'i',
            //'se',
            //'f',
            //'vitamin_a',
            //'vitamin_b_carotene',
            //'vitamin_b1',
            //'vitamin_b2',
            //'vitamin_pp',
            //'vitamin_c',
            //'vitamin_d',
            //'energy_kkal',
            //'energy_kdj',
            //'created_at',

            [
                'header' => 'Настройки продукта',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{what-dishes} {double} {view} {update} {products-allergen/add-allergen} {delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [

                    'what-dishes' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-search"></span>', $url, [
                            'title' => Yii::t('yii', 'Посмотреть список блюд, в которые входит этот продукт'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-secondary'
                        ]);
                    },

                    'double' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-cutlery"></span>', $url, [
                            'title' => Yii::t('yii', 'Создать промышленное блюдо на основе продукта'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-secondary'
                        ]);
                    },

                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('yii', 'Просмотр'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-success'
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Редактировать'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-primary'
                        ]);
                    },
                    'products-allergen/add-allergen' => function ($url, $model, $key) {
                        return Html::a('<b>A</b>', $url, [
                            'title' => Yii::t('yii', 'Добавить аллерген'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-warning'
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Удалить'),
                            'data-toggle' => 'tooltip',
                            'class' => 'btn btn-sm btn-danger',
                            'data' => ['confirm' => 'Вы уверены что хотите удалить пользователя?'],
                        ]);
                    },
                ],
            ]

        ],
    ]); ?>


</div>
