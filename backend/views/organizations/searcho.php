<?php

use common\models\AuthAssignment;
use common\models\DepartmentEducation;
use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\User;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$item_name = AuthAssignment::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->item_name;

$this->title = 'Поиск организаций';
$this->params['breadcrumbs'][] = $this->title;

$p_cat = array('' => 'Все ...');
$p_cat_bd = ArrayHelper::map(FederalDistrict::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
$p_cat = ArrayHelper::merge($p_cat, $p_cat_bd);

$p_cat2 = array('' => 'Все ...');
$p_cat_bd2 = ArrayHelper::map(Region::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
$p_cat2 = ArrayHelper::merge($p_cat2, $p_cat_bd2);

if ($item_name == 'admin')
{
    $p_cat3 = array('' => 'Все ...');
    $p_cat_bd3 = ArrayHelper::map(Municipality::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    $p_cat3 = ArrayHelper::merge($p_cat3, $p_cat_bd3);
}
elseif ($item_name == 'RPN')
{
    $municipality_arr = Municipality::find()->where(['region_id' => DepartmentEducation::findOne(['key_login_rpn'=>Yii::$app->user->identity->key_login])->region_id])->orderBy(['name' => SORT_ASC])->all();
    if (empty($municipality_arr)) $municipality_arr =Municipality::find()->where(['region_id' => DepartmentEducation::findOne(['key_login_ministry_education'=>Yii::$app->user->identity->key_login])->region_id])->orderBy(['name' => SORT_ASC])->all();

    $p_cat3 = array('' => 'Все ...');
    $p_cat_bd3 = ArrayHelper::map($municipality_arr, 'id', 'name');
    $p_cat3 = ArrayHelper::merge($p_cat3, $p_cat_bd3);
}
elseif ($item_name == 'RPN_mun')
{
    $p_cat3 = ArrayHelper::map(Municipality::find()->where(['id' => DepartmentEducation::findOne(['key_login_departament'=>Yii::$app->user->identity->key_login])->municipality_id])->all(), 'id', 'name');
    if (empty($p_cat3)) ArrayHelper::map(Municipality::find()->where(['id' => DepartmentEducation::findOne(['key_login_ministry_education'=>Yii::$app->user->identity->key_login])->municipality_id])->all(), 'id', 'name');
}

$items = [
    '' => 'Все ...',
    '1' => 'Общеобразовательная организация',
    '2' => 'Организация дошкольного образования',
    '3' => 'Организация профессионального образования',
    '4' => 'Организация дополнительного образования',
    '5' => 'Медицинская организация',
    '6' => 'Организация социального обслуживания',
    '8' => 'Физкультурно-спортивная организация',
    '9' => 'Иная'
];


?>
    <div class="products-index">
        <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Добавление организации', ['create-org'], ['class' => 'btn main-button-3']) ?>
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
                //['class' => 'yii\grid\SerialColumn',
                //    'headerOptions' => ['class' => 'grid_table_th'],
                //],

                [
                    'attribute' => 'Номер организации',
                    'value' => 'id',
                    'headerOptions' => ['class' => 'grid_table_th thththth'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'federal_district_id',
                    'value' => function ($model) {
                        return $model->get_district($model->federal_district_id);
                    },
                    'filter' => $p_cat,
                    'headerOptions' => ['class' => 'grid_table_th thththth'],
                    'contentOptions' => ['class' => ''],
                    'visible' => Yii::$app->user->can('admin')
                ],

                [
                    'attribute' => 'region_id',
                    'value' => function ($model) {
                        return $model->get_region($model->region_id);
                    },
                    'filter' => $p_cat2,
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                    'visible' => Yii::$app->user->can('admin')
                ],
                [
                    'attribute' => 'municipality_id',
                    'value' => function ($model) {
                        return $model->get_municipality($model->municipality_id);
                    },
                    'filter' => $p_cat3,
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                    //'visible' => Yii::$app->user->can('admin')
                ],

                [
                    'attribute' => 'title',
                    'value' => 'title',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],
                [
                    'attribute' => 'short_title',
                    'value' => 'short_title',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],

                [
                    'attribute' => 'type_org',
                    'value' => function ($model) {
                        return $model->get_type_org($model->type_org);
                    },
                    'filter' => $items,
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                ],

                [
                    'header' => 'Руководитель организации',
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{add-account}',
                    'contentOptions' => ['class' => 'action-column'],
                    'buttons' => [
                        'add-account' => function ($url, $model, $key) {
                            if (!empty($model->dir_keylogin)) return $model->dir_keylogin;
                            else
                            {
                                return Html::a('<span class="glyphicon glyphicon-plus"></span>', ['/organizations/add-director?id='.$model->id], [
                                    'title' => Yii::t('yii', 'Добавить руководителя организации'),
                                    'data-toggle'=>'tooltip',
                                    'class'=>'btn btn-sm btn-success clickDisabled',
                                    //'data' => ['confirm' => 'Вы уверены что хотите добавить пользователя?'],
                                ]);
                            }
                        },
                    ],
                ]
            ],
        ]); ?>
    </div>

<?php
$script = <<< JS
$(".clickDisabled").click(function() {
  $(".clickDisabled").css('display','none');
});

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>