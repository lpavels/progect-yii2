<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RecipesCollection */

$this->title = 'Обновление рецептуры коллекции: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Recipes Collections', 'url' => ['recipes-index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['recipes-view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="recipes-collection-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('recipes-form', [
        'model' => $model,
    ]) ?>

</div>
