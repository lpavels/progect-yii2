<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MenusDishes */

$this->title = 'Create Menus Dishes';
$this->params['breadcrumbs'][] = ['label' => 'Menus Dishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menus-dishes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
