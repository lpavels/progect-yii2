<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingPlan */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Training Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-plan-view">

   <!-- <h1><?/*= Html::encode($this->title) */?></h1>-->

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?//= Html::a('Удалить', ['delete', 'id' => $model->id], [
          //  'class' => 'btn btn-danger',
          //  'data' => [
          //      'confirm' => 'Are you sure you want to delete this item?',
          //      'method' => 'post',
          //  ],
          //  ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'field1',
            'field2',
            'field3',
            'field4',
            'field5',
            'field6',
            'field7',
            'field8',
            'field9',
            'field10',
            'field11',
            'field12',
            'field13',
        ],
    ]) ?>

</div>
