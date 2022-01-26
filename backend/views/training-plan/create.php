<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingPlan */

$this->title = 'Добавить информацию по обучению';
$this->params['breadcrumbs'][] = ['label' => 'Training Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-plan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
