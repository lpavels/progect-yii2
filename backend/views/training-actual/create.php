<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TrainingActual */

$this->title = 'Фактическая информация по ребенку';
$this->params['breadcrumbs'][] = ['label' => 'Training Actuals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-actual-create">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
