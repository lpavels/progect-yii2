<?php

use common\models\AuthAssignment;
use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поиск пользователей';
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="products-index">
        <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

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

                'key_login',
                [
                    'attribute' => 'organization_id',
                    'value' => function ($model) {
                        return $model->get_name_organization($model->organization_id);
                    },
                ],
                'name',
                'class',
                'bukva_klassa',
                'year_birth',
                'created_at',

                //[
                //    'attribute' => 'Номер организации',
                //    'value' => 'id',
                //    'headerOptions' => ['class' => 'grid_table_th thththth'],
                //    'contentOptions' => ['class' => ''],
                //],
                //[
                //    'attribute' => 'federal_district_id',
                //    'value' => function ($model) {
                //        return $model->get_district($model->federal_district_id);
                //    },
                //    'filter' => $p_cat,
                //    'headerOptions' => ['class' => 'grid_table_th thththth'],
                //    'contentOptions' => ['class' => ''],
                //    'visible' => Yii::$app->user->can('admin')
                //],

                //[
                //    'attribute' => 'region_id',
                //    'value' => function ($model) {
                //        return $model->get_region($model->region_id);
                //    },
                //    'filter' => $p_cat2,
                //    'headerOptions' => ['class' => 'grid_table_th'],
                //    'contentOptions' => ['class' => ''],
                //    'visible' => Yii::$app->user->can('admin')
                //],
                //[
                //    'attribute' => 'municipality_id',
                //    'value' => function ($model) {
                //        return $model->get_municipality($model->municipality_id);
                //    },
                //    'filter' => $p_cat3,
                //    'headerOptions' => ['class' => 'grid_table_th'],
                //    'contentOptions' => ['class' => ''],
                //    'visible' => Yii::$app->user->can('admin')
                //],

                //[
                //    'attribute' => 'title',
                //    'value' => 'title',
                //    'headerOptions' => ['class' => 'grid_table_th'],
                //    'contentOptions' => ['class' => ''],
                //],
                //[
                //    'attribute' => 'short_title',
                //    'value' => 'short_title',
                //    'headerOptions' => ['class' => 'grid_table_th'],
                //    'contentOptions' => ['class' => ''],
                //],

                //[
                //    'attribute' => 'type_org',
                //    'value' => function ($model) {
                //        return $model->get_type_org($model->type_org);
                //    },
                //    'filter' => $items,
                //    'headerOptions' => ['class' => 'grid_table_th'],
                //    'contentOptions' => ['class' => ''],
                //],

                //[
                //    'header' => 'Руководитель организации',
                //    'class' => 'yii\grid\ActionColumn',
                //    'template' => '{add-account}',
                //    'contentOptions' => ['class' => 'action-column'],
                //    'buttons' => [
                //        'add-account' => function ($url, $model, $key) {
                //            if (!empty($model->dir_keylogin)) return $model->dir_keylogin;
                //            else
                //            {
                //                return Html::a('<span class="glyphicon glyphicon-plus"></span>', ['/organizations/add-director?id='.$model->id], [
                //                    'title' => Yii::t('yii', 'Добавить руководителя организации'),
                //                    'data-toggle'=>'tooltip',
                //                    'class'=>'btn btn-sm btn-success clickDisabled',
                //                    //'data' => ['confirm' => 'Вы уверены что хотите добавить пользователя?'],
                //                ]);
                //            }
                //        },
                //    ],
                //]
            ],
        ]); ?>
    </div>
<?
$script = <<< JS
$(".clickDisabled").click(function() {
  $(".clickDisabled").css('display','none');
});

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>