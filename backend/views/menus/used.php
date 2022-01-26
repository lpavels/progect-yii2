<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет о использовании архивных меню';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menus-index">
    <div class="row justify-content-center">
        <div class="col-auto">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
            <br>
    <div class="">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
                'class' => 'menus-table table-responsive'],
            'tableOptions' => [
                'class' => 'table table-bordered table-responsive'
            ],
            'rowOptions' => ['class' => 'grid_table_tr'],
            'columns' => [
                [   'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'grid_table_th'],
                ],
                [
                    'attribute' => 'organization_id',
                    'value' => function($model){
                        return $model->get_organization($model->organization_id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
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
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'age_info_id',
                    'value' => function($model){
                        return $model->get_age($model->age_info_id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'Количество загрузок из архива',
                    'value' => function($model){
                        return $model->get_count_download($model->id);
                    },
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'header' => 'Подробнее меню',
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view-used}',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'action-column text-center'],
                    'buttons' => [

                        'view-used' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                'title' => Yii::t('yii', 'Список организаций загрузивших меню'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm main-button-see'
                            ]);
                        },
                    ],
                ]

            ],
        ]); ?>
    </div>

        </div>
</div>
</div>
