<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\RecipesCollection;
use common\models\DishesCategory;
use kartik\export\ExportMenu;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'База данных блюд';
$this->params['breadcrumbs'][] = $this->title;

$recipes = array('' => 'Все ...');
$recipes_bd = ArrayHelper::map(RecipesCollection::find()->where(['organization_id' => [7, Yii::$app->user->identity->organization_id]])->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$recipes = ArrayHelper::merge($recipes,$recipes_bd);


if(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')){
    $recipes = array('' => 'Все ...');
    $recipes_bd = ArrayHelper::map(RecipesCollection::find()->where(['organization_id' => [7, Yii::$app->session['organization_id']]])->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
    $recipes = ArrayHelper::merge($recipes,$recipes_bd);
}

$categories = array('' => 'Все ...');
$categories_bd = ArrayHelper::map(DishesCategory::find()->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$categories = ArrayHelper::merge($categories,$categories_bd);
?>
<div class="dishes-index mt-2">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
<div class="mt-5">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'class' => 'menus-table table-responsive'],
        'tableOptions' => [
                'class' => 'table table-bordered table-responsive'
        ],
        'filterModel' => $searchModel,
        'rowOptions' => ['class' => 'grid_table_tr'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_table_th'],
            ],
            //'id',
            [
                'attribute' => 'recipes_collection_id',
                'value' => function($model){
                    return $model->get_recipes($model->recipes_collection_id)->name;
                },
                'filter' => $recipes,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
                //'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'attribute' => 'dishes_category_id',
                'value' => function($model){
                    return $model->get_category_dish($model->dishes_category_id);
                },
                'filter' => $categories,
                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
                //'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'attribute' => 'name',
                'value' => 'name',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
            ],
            //'culinary_processing_id',
            [
                'attribute' => 'yield',
                'value' => 'yield',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            'description:ntext',
            //'appearance',
            //'consistency',
            //'color',
            //'taste',
            //'smell',
            [
                'attribute' => 'techmup_number',
                'value' => 'techmup_number',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            //'number_of_dish',
            //'created_at',
            [
                'header' => 'Просмотр техкарты',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => 'action-column text-center'],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::button('<span class="glyphicon glyphicon-list-alt text-nowrap"> из сборника</span>', [
                            'title' => Yii::t('yii', 'Просмотр'),
                            'data-toggle'=>'tooltip',
                            //'class'=>'btn btn-sm btn-success',
                            'data-dishes_id' => $model->id,
                            'class'=>'btn btn-sm main-button-see',
                            'onclick' => '
                          $.get("../menus-dishes/showtechmup?id=" + $(this).attr("data-dishes_id"), function(data){
                          $("#showTechmup .modal-body").empty();
                            $("#showTechmup .modal-body").append(data);
                            //console.log(data);
                            $("#showTechmup").modal("show");
                          });'
                        ]);
                    },

                ],
            ]
        ],
    ]); ?>
    <?
    Yii::$app->params['bsVersion'];
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_table_th'],
            ],
            //'id',
            [
                'attribute' => 'recipes_collection_id',
                'value' => function($model){
                    return $model->get_recipes($model->recipes_collection_id)->name;
                },
                'filter' => $recipes,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
                //'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'attribute' => 'dishes_category_id',
                'value' => function($model){
                    return $model->get_category_dish($model->dishes_category_id);
                },
                'filter' => $categories,
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => ''],
                //'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'attribute' => 'name',
                'value' => 'name',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle'],
            ],
            //'culinary_processing_id',
            [
                'attribute' => 'yield',
                'value' => 'yield',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle text-center'],
            ],
            'description:ntext',
            //'appearance',
            //'consistency',
            //'color',
            //'taste',
            //'smell',
            [
                'attribute' => 'techmup_number',
                'value' => 'techmup_number',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle text-center'],
            ],
            //'number_of_dish',
            //'created_at',
            [
                'header' => 'Просмотр техкарты',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => 'action-column align-middle'],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::button('<span class="glyphicon glyphicon-list-alt text-nowrap"> на 100 грамм</span>', [
                            'title' => Yii::t('yii', 'Просмотр'),
                            'data-toggle'=>'tooltip',
                            //'class'=>'btn btn-sm btn-success',
                            'data-dishes_id' => $model->id,
                            'class'=>'btn btn-sm main-button-see',
                            'onclick' => '
                          $.get("../menus-dishes/showtechmup?id=" + $(this).attr("data-dishes_id"), function(data){
                          $("#showTechmup .modal-body").empty();
                            $("#showTechmup .modal-body").append(data);
                            //console.log(data);
                            $("#showTechmup").modal("show");
                          });'
                        ]);
                    },

                ],
            ]

        ],
    ]);
    ?>
</div>
</div>
<!--МОДАЛЬНОЕ ОКНО ДЛЯ ТЕХКАРТ-->
<div id="showTechmup" class="modal fade">
    <div class="modal-dialog modal-lg" style="">
        <div class="modal-content">
            <div class="modal-header-p3">
                <h4 class="modal-title">Технологическая карта
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="row">

                </div>
            </div>
        </div>
    </div>
</div>
