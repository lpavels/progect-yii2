<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KidsQ */

$this->title = 'Добавить ребенка в группу';
$this->params['breadcrumbs'][] = ['label' => 'Kids Qs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kids-q-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
