<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductsChange */

$this->title = 'Create Products Change';
$this->params['breadcrumbs'][] = ['label' => 'Products Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="products-change-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
