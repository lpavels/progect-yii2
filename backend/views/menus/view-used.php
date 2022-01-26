<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Menus */

$this->title = 'Детальный просмотр';
$this->params['breadcrumbs'][] = ['label' => 'Menuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="menus-view">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php
        echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Название:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.$menus->name.'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';

    ?>

    <?/*= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'organization_id',
            'feeders_characters_id',
            'age_info_id',
            'name',
            'cycle',
            'date_start',
            'date_end',
            'status_archive',
            'created_at',
        ],
    ]) */?>

</div>
