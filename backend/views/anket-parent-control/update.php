<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnketParentControl */

$this->title = 'Update Anket Parent Control: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Anket Parent Controls', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="anket-parent-control-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
