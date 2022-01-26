<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сборники рецептур';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recipes-collection-index">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?if(!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr'))){?>
        <p><?= Html::a('Добавить сборник рецептуры', ['recipes-create'], ['class' => 'btn btn-success']) ?></p>
    <?}?>

    <?= GridView::widget([
        'options' => [
            'class' => 'menus-table table-responsive'],
        'tableOptions' => [
            'class' => 'table table-bordered table-responsive'
        ],
        'dataProvider' => $dataProvider,
        'rowOptions' => ['class' => 'grid_table_tr'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_table_th'],
            ],
            [
                'attribute' => 'organization_id',
                'value' => function($model){
                    return $model->get_organization($model->organization_id);
                },
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle'],
            ],
            [
                'attribute' => 'name',
                'value' => 'name',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle'],
            ],
            [
                'attribute' => 'short_title',
                'value' => 'short_title',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle'],
            ],
            [
                'attribute' => 'year',
                'value' => 'year',
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle text-center'],
            ],
            [
                'attribute' => 'Количество блюд в сборнике',
                'value' => function($model){
                    return $model->get_count_dishes($model->id);
                },
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle text-center'],
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return $model->get_date($model->created_at);
                },
                'headerOptions' => ['class' => 'grid_table_th'],
                'contentOptions' => ['class' => 'align-middle text-center'],
            ],
            //'created_at',
            [
                'header' => 'Настройки сборника',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{recipes-view} {recipes-update} {recipes-delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [

                    'recipes-view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('yii', 'Просмотр'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-success'
                        ]);
                    },
                    'recipes-update' => function ($url, $model, $key) {
                        if(!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')))
                        {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => Yii::t('yii', 'Редактировать'),
                                'data-toggle' => 'tooltip',
                                'class' => 'btn btn-sm btn-primary'
                            ]);
                        }
                    },
                    'recipes-delete' => function ($url, $model, $key) {
                        if(!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')))
                        {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Удалить'),
                                'data-toggle' => 'tooltip',
                                'class' => 'btn btn-sm btn-danger',
                                'data' => ['confirm' => 'Вы уверены что хотите удалить пользователя?'],
                            ]);
                        }
                    },
                ],
            ]

        ],
    ]); ?>


</div>
