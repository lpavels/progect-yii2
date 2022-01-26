<?php

use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Region;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управления образования';
$this->params['breadcrumbs'][] = $this->title;


$district_items = ArrayHelper::map(FederalDistrict::find()->all(), 'id', 'name');
if (!empty($searchModel->district_id))
{
    $region_items = ArrayHelper::map(Region::find()->where(['district_id'=>$searchModel->district_id])->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name');
    $municipality_items = ArrayHelper::map(Municipality::find()->where(['region_id'=>$searchModel->region_id])->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name');
}
else {$region_items = [];$municipality_items = [];}

if (!empty($searchModel->region_id) && !empty($searchModel->district_id))
{
    $municipality_items = ArrayHelper::map(Municipality::find()->where(['region_id'=>$searchModel->region_id])->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name');
}
else {$municipality_items = [];}

?>
<div class="departament-education-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'rowOptions' => function ($model) {
        //    $status = AdminTasks::findOne($model->tasks_id)->status;
        //    if ($status == 2)
        //    {
        //        return ['style' => 'background-color:#c3e6cb'];
        //    }
        //    elseif (($status != 2) && (strtotime(date('d.m.Y H:i')) > strtotime($model->date_deadline)))
        //    {
        //        return ['style' => 'background-color:#f76425;'];
        //    }
        //    else
        //    {
        //        return ['style' => 'background-color:#f7f025;'];
        //    }
        //},
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'district_id',
                'value' => function ($model) {
                    return FederalDistrict::findOne($model->district_id)->name;
                },
                'filter' => $district_items
            ],
            [
                'attribute' => 'region_id',
                'value' => function ($model) {
                    return Region::findOne($model->region_id)->name;
                },
                'filter' => $region_items
            ],
            [
                'attribute' => 'municipality_id',
                'value' => function ($model) {
                    return Municipality::findOne($model->municipality_id)->name;
                },
                'filter' => $municipality_items
            ],
            'key_login_departament',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{create}',
                'contentOptions' => ['class' => 'action-column'],
                'buttons' => [
                    'create' => function ($url, $model, $key) {
                        if (!$model->key_login_departament)
                        {
                            return Html::a('<span class="fa fa-plus" ></span>', $url, [
                                'title' => Yii::t('yii', 'Создать ключ входа'),
                                'data-toggle' => 'tooltip',
                                'class' => 'btn btn-sm btn-success'
                            ]);
                        }
                    },
                ],
            ]
        ],
    ]); ?>

</div>