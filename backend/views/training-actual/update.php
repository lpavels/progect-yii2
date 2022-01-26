<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingActual */

$this->title = 'Обновление фактической информации ';
$this->params['breadcrumbs'][] = ['label' => 'Training Actuals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="training-actual-update">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
