<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Дети по группам';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kids-q-index" style="width: 1000px;">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить детей в группу', ['create'], ['class' => 'btn  main-button-3']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'group_id',
                'value' => function ($model) {
                    return $model->group($model->group_id);
                },
            ],
            //'topic',
            [
                'attribute' => 'sex',
                'value' => function ($model) {
                    return $model->sex($model->sex);
                },
            ],
            'lastname',
            'name',
            //'creat_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{training-actual/create} {update} {delete}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('yii', 'Просмотр'),
                            'data-toggle' => 'tooltip',
                            'class' => 'btn btn-sm btn-success'
                        ]);
                    },
                    'training-actual/create' => function ($url, $model, $key) {
                        return Html::a('<span style="color: white" class="glyphicon glyphicon-plus"></span>', '/training-actual/create?id='.$model->id, [
                            'title' => Yii::t('yii', 'Добавить фактическую информацию'),
                            'data-toggle'=>'tooltip',
                            'class'=>'btn btn-sm btn-warning'
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Редактировать'),
                            'data-toggle' => 'tooltip',
                            'class' => 'btn btn-sm btn-primary'
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('yii', 'Удалить'),
                            'data-toggle' => 'tooltip',
                            'class' => 'btn btn-sm btn-danger',
                            'data' => ['confirm' => 'Вы уверены что хотите удалить ребёнка?'],
                        ]);
                    },
                ],
            ]
        ],
    ]); ?>


</div>
