<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RecipesCollection */

$this->title = 'Добавление сборника рецептуры';
$this->params['breadcrumbs'][] = ['label' => 'Recipes Collections', 'url' => ['recipes-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="recipes-collection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('recipes-form', [
        'model' => $model,
    ]) ?>

</div>
