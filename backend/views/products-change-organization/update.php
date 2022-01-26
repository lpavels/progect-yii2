<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductsChangeOrganization */

$this->title = 'Update Products Change Organization: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Products Change Organizations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="products-change-organization-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
