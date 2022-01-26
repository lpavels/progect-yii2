<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список мероприятий контроля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="anket-parent-control-index" style="width: 900px;">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?//= Html::a('Create Anket Parent Control', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'organization_id',
            [
                'attribute' => 'date',
                'value' => function($model){
                    return date('d.m.Y', $model->date);
                },
                'label' => 'Дата проведения контроля',
                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
                //'visible' => Yii::$app->user->can('admin'),
            ],
            //'date',
            'name',


            //'question1',
            //'question2',
            //'question3',
            //'question4',
            //'question5',
            //'question6',
            //'question7',
            //'question8',
            //'question9',
            //'question10',
            //'question11',
            //'question12',
            //'question13',
            //'question14',
            //'count',
            //'masa_porcii',
            //'masa_othodov',
            //'created_at',
            [
                'attribute' => 'Результат',
                'value' => function($model){
                    return $model->get_result($model->id);
                },

                'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                'contentOptions' => ['class' => ''],
                //'visible' => Yii::$app->user->can('admin'),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [

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
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Удалить'),
                            'data-toggle' => 'tooltip',
                            'class' => 'btn btn-sm btn-danger',
                            'data' => ['confirm' => 'Вы уверены что хотите удалить категорию блюда?'],
                        ]);
                    },
                ],
            ]
        ],
    ]); ?>


</div>
