<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Products */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="products-view">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <? if(Yii::$app->user->can('admin')){?>
    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => "Вы уверены, что хотите удалить продукт? '$model->name?'",
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?}?>

    <?= DetailView::widget([
        'model' => $model,
        'options' => [
        'class' => 'table table-hover table-responsive'],
        'attributes' => [
            [
                'attribute' => 'name',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'products_category_id',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'products_subcategory_id',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'water',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'protein',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'fat',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'carbohydrates_total',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'carbohydrates_saccharide',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'carbohydrates_starch',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'carbohydrates_lactose',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'carbohydrates_sacchorose',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'carbohydrates_cellulose',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'dust_total',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'dust_nacl',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'apple_acid',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'na',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'k',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'ca',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'mg',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'p',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'fe',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'i',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'se',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'f',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_a',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_b_carotene',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_b1',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_b2',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_pp',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_c',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'vitamin_d',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'energy_kkal',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
            [
                'attribute' => 'energy_kdj',
                'contentOptions' => ['class' => 'table-light text-center'],
                'captionOptions' => ['class' => 'table-success-2'],
            ],
        ],
    ]) ?>

</div>
