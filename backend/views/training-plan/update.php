<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingPlan */

$this->title = 'Update Training Plan: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Training Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="training-plan-update">

    <!--<h1><?/*= Html::encode($this->title) */?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
