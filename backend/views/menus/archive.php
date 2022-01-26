<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Архив меню';
$this->params['breadcrumbs'][] = $this->title;

$sub = array('' => 'Все ...');
$sub_bd = ArrayHelper::map(\common\models\FeedersCharacters::find()->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$sub = ArrayHelper::merge($sub,$sub_bd);

$age = array('' => 'Все ...');
$age_bd = ArrayHelper::map(\common\models\AgeInfo::find()->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$age = ArrayHelper::merge($age,$age_bd);
?>
<div class="menus-index">
    <div class="row justify-content-center">
        <div class="col-auto">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
            <?if(Yii::$app->user->can('admin')){?>
    <p>
        <?= Html::a('Добавить новое меню в архив', ['create-archive'], ['class' => 'btn main-button-3 col-md-3']) ?><br><br>
        <?= Html::a('Перейти к добавлению блюд в архивные меню', ['menus-dishes/archive-index'], ['class' => 'btn main-button-3 col-md-4']) ?>
    </p>
            <?}?>
            <div class="container" style="border: 1px solid #b7b6ba">
                <div class="row pb-2">
                    <p class="text-center" style="margin: 0 auto; color:#78787a"><b>Инструкция по кнопкам</b></p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['menus/archive'], [
                            'class'=>'btn btn-sm main-button-see'
                        ]). '<b> - Детальный просмотр архивного меню по дням с пищевым составом </b>';?>
                    </div>
                    <div class="col-6">
                        <?= Html::a('<span class="glyphicon glyphicon-download"></span>', ['menus/archive'], [
                            'class'=>'btn btn-sm btn-success'
                        ]). '<b> - Скачать это архивное меню к себе для использования </b>';?>
                    </div>
                </div>
            </div>
    <div class="">
<!--        <p style="font-size: 20px;"><b>Архив моих меню</b></p>-->

        <?if(Yii::$app->user->can('admin')){?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'menus-table table-responsive'],
                'tableOptions' => [
                    'class' => 'table table-bordered table-responsive'
                ],
                'rowOptions' => function($model) {
                    if($model->show_indicator !== 1 && Yii::$app->user->can('admin')) {
                        return ['style' => 'background-color:#c3e6cb;'];
                    }
                    if($model->show_indicator !== 1 && Yii::$app->user->can('school_director')) {
                        if(substr_count($model->name, 'Омск')>=1) {
                            //echo substr_count($model->name, 'Омск');
                            return ['style' => 'background-color:#ffff00;'];
                        }
                        else {
                            return ['style' => 'background-color:#c3e6cb;'];
                        }

                    }

                },

                'columns' => [
                    [   'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'grid_table_th'],
                    ],
                    [
                        'attribute' => 'name',
                        'value' => 'name',
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => ''],
                    ],
                    [
                        'attribute' => 'feeders_characters_id',
                        'value' => function($model){
                            return $model->get_characters($model->feeders_characters_id);
                        },
                        'filter' => $sub,
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => ''],
                    ],
                    [
                        'attribute' => 'age_info_id',
                        'value' => function($model){
                            return $model->get_age($model->age_info_id);
                        },
                        'filter' => $age,
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'Дни меню',
                        'value' => function($model){
                            return $model->get_days($model->id, 'short_name');
                        },
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => ''],
                    ],
                    [
                        'attribute' => 'cycle',
                        'value' => 'cycle',
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    /*[
                        'attribute' => 'status_archive',
                        'value' => 'status_archive',
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],*/
                    [
                        'attribute' => 'Дата добавления',
                        'value' => function($model){
                            return $model->get_date($model->id);
                        },
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'header' => 'Действия',
                        'class' => 'yii\grid\ActionColumn',
                        'template' => ' {view-menus} {put-archive} {setting-archive} {delete-archive}',
                        'headerOptions' => ['class' => 'grid_table_th'],
                        'contentOptions' => ['class' => 'action-column text-center'],
                        'buttons' => [
                            'view-menus' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                    'title' => Yii::t('yii', 'Подробный просмотр меню и его состава'),
                                    'data-toggle'=>'tooltip',
                                    'class'=>'btn btn-sm main-button-see'
                                ]);
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                    'title' => Yii::t('yii', 'Редактировать'),
                                    'data-toggle'=>'tooltip',
                                    'class'=>'btn btn-sm main-button-edit'
                                ]);
                            },
                            'put-archive' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-download"></span>', $url, [
                                    'title' => Yii::t('yii', 'Применить это меню'),
                                    'data-toggle'=>'tooltip',
                                    'class'=>'btn btn-sm btn-success'
                                ]);
                            },

                            'setting-archive' => function ($url, $model, $key) {
                                if(Yii::$app->user->can('admin')){
                                    return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
                                        'title' => Yii::t('yii', 'Настройки'),
                                        'data-toggle'=>'tooltip',
                                        'class'=>'btn btn-sm btn-secondary'
                                    ]);
                                }
                            },
                            'delete-archive' => function ($url, $model, $key) {
                                if(Yii::$app->user->can('admin') || Yii::$app->user->identity->organization_id == $model->organization_id){
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                        'title' => Yii::t('yii', 'Удалить'),
                                        'data-toggle' => 'tooltip',
                                        'class' => 'btn btn-sm main-button-delete mt-1',
                                        'data' => ['confirm' => 'Вы уверены что хотите удалить меню?'],
                                    ]);
                                }
                            },
                        ],
                    ]
                ],
            ]); ?>
        <?}else{?>








        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'class' => 'menus-table table-responsive'],
            'tableOptions' => [
                'class' => 'table table-bordered table-responsive'
            ],
            'rowOptions' => function($model) {
                if($model->show_indicator !== 1 && Yii::$app->user->can('admin')) {
                   return ['style' => 'background-color:#c3e6cb;'];
                }
				if($model->show_indicator !== 1 && Yii::$app->user->can('school_director')) {
					if(substr_count($model->name, 'Омск')>=1) {
						//echo substr_count($model->name, 'Омск');
						return ['style' => 'background-color:#ffff00;'];
					}
					else {
						return ['style' => 'background-color:#c3e6cb;'];
					}
			        
                }
				
            },
			
            'columns' => [
                [   'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'grid_table_th'],
                ],
                /*[
                    'attribute' => 'organization_id',
                    'value' => function($model){
                        return $model->get_organization($model->organization_id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],*/
                [
                    'attribute' => 'name',
                    'value' => 'name',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'feeders_characters_id',
                    'value' => function($model){
                        return $model->get_characters($model->feeders_characters_id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'age_info_id',
                    'value' => function($model){
                        return $model->get_age($model->age_info_id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'Дни меню',
                    'value' => function($model){
                        return $model->get_days($model->id, 'short_name');
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'cycle',
                    'value' => 'cycle',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                /*[
                    'attribute' => 'status_archive',
                    'value' => 'status_archive',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'text-center'],
                ],*/
                [
                    'attribute' => 'Дата добавления',
                    'value' => function($model){
                        return $model->get_date($model->id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'header' => 'Действия',
                    'class' => 'yii\grid\ActionColumn',
                    'template' => ' {view-menus} {put-archive} {setting-archive} {delete-archive}',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'action-column text-center'],
                    'buttons' => [
                        'view-menus' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                'title' => Yii::t('yii', 'Подробный просмотр меню и его состава'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm main-button-see'
                            ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => Yii::t('yii', 'Редактировать'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm main-button-edit'
                            ]);
                        },
                        'put-archive' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-download"></span>', $url, [
                                'title' => Yii::t('yii', 'Применить это меню'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm btn-success'
                            ]);
                        },

                        'setting-archive' => function ($url, $model, $key) {
                            if(Yii::$app->user->can('admin')){
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
                                'title' => Yii::t('yii', 'Настройки'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm btn-secondary'
                            ]);
                            }
                        },
                        'delete-archive' => function ($url, $model, $key) {
                            if(Yii::$app->user->can('admin') || Yii::$app->user->identity->organization_id == $model->organization_id){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Удалить'),
                                'data-toggle' => 'tooltip',
                                'class' => 'btn btn-sm main-button-delete mt-1',
                                'data' => ['confirm' => 'Вы уверены что хотите удалить меню?'],
                            ]);
                            }
                        },
                    ],
                ]
            ],
        ]); ?>
        <?}?>
    </div>


<!--            <br><br>-->
<!--            <div class="">-->
<!--                <p style="font-size: 20px;"><b>Архив меню других организаций</b></p>-->
<!--                --><?//= GridView::widget([
//                    'dataProvider' => $dataProvider2,
//                    'options' => [
//                        'class' => 'menus-table table-responsive'],
//                    'tableOptions' => [
//                        'class' => 'table table-bordered table-responsive'
//                    ],
//                    'rowOptions' => ['class' => 'grid_table_tr'],
//                    'columns' => [
//                        [   'class' => 'yii\grid\SerialColumn',
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                        ],
//                        [
//                            'attribute' => 'organization_id',
//                            'value' => function($model){
//                                return $model->get_organization($model->organization_id);
//                            },
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'attribute' => 'name',
//                            'value' => 'name',
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'attribute' => 'feeders_characters_id',
//                            'value' => function($model){
//                                return $model->get_characters($model->feeders_characters_id);
//                            },
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'attribute' => 'age_info_id',
//                            'value' => function($model){
//                                return $model->get_age($model->age_info_id);
//                            },
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'attribute' => 'Дни меню',
//                            'value' => function($model){
//                                return $model->get_days($model->id, 'short_name');
//                            },
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'attribute' => 'cycle',
//                            'value' => 'cycle',
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'attribute' => 'Дата добавления',
//                            'value' => function($model){
//                                return $model->get_date($model->id);
//                            },
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => ''],
//                        ],
//                        [
//                            'header' => 'Настройки меню',
//                            'class' => 'yii\grid\ActionColumn',
//                            'template' => '{view} {put-archive} {setting-archive} {delete-archive}',
//                            'headerOptions' => ['class' => 'grid_table_th'],
//                            'contentOptions' => ['class' => 'action-column text-center'],
//                            'buttons' => [
//
//                                'view' => function ($url, $model, $key) {
//                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
//                                        'title' => Yii::t('yii', 'Просмотр'),
//                                        'data-toggle'=>'tooltip',
//                                        'class'=>'btn btn-sm main-button-see'
//                                    ]);
//                                },
//                                'update' => function ($url, $model, $key) {
//                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
//                                        'title' => Yii::t('yii', 'Редактировать'),
//                                        'data-toggle'=>'tooltip',
//                                        'class'=>'btn btn-sm main-button-edit'
//                                    ]);
//                                },
//                                'put-archive' => function ($url, $model, $key) {
//                                    return Html::a('<span class="glyphicon glyphicon-download"></span>', $url, [
//                                        'title' => Yii::t('yii', 'Применить это меню'),
//                                        'data-toggle'=>'tooltip',
//                                        'class'=>'btn btn-sm btn-success'
//                                    ]);
//                                },
//
//                                'setting-archive' => function ($url, $model, $key) {
//                                    if(Yii::$app->user->can('admin')){
//                                        return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, [
//                                            'title' => Yii::t('yii', 'Настройки'),
//                                            'data-toggle'=>'tooltip',
//                                            'class'=>'btn btn-sm btn-secondary'
//                                        ]);
//                                    }
//                                },
//                                'delete-archive' => function ($url, $model, $key) {
//                                    if(Yii::$app->user->can('admin')){
//                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
//                                            'title' => Yii::t('yii', 'Удалить'),
//                                            'data-toggle' => 'tooltip',
//                                            'class' => 'btn btn-sm main-button-delete',
//                                            'data' => ['confirm' => 'Вы уверены что хотите удалить меню?'],
//                                        ]);
//                                    }
//                                },
//                            ],
//                        ]
//                    ],
//                ]); ?>
<!--            </div>-->




        </div>
</div>
</div>
