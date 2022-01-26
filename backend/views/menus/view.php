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
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Характеристика питающихся:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.$menus->get_characters($menus->feeders_characters_id).'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Возрастная категория:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.$menus->get_age($menus->age_info_id).'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Цикл:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.$menus->cycle.'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Дни меню:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.$menus->get_days($menus->id, 'short_name').'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Приемы пищи:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.$menus->get_nutritions($menus->id).'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Дата начала:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.date("d.m.Y", $menus->date_start).'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Дата конца:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.date("d.m.Y", $menus->date_end).'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                <label class="col-11 col-md-2 col-form-label font-weight-bold">Дата создания:</label>
                <input type="text" class="form-control col-11 col-md-4" value="'.date("d.m.Y H:i", strtotime($menus->created_at)).'" readonly="true">
                <div class="invalid-feedback"></div>
              </div>';
    ?>
    <p class="text-center mt-3">
        <?= Html::a('Обновить', ['update', 'id' => $menus->id], ['class' => 'btn main-button-3 col-7 col-md-5']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $menus->id], [
            'class' => 'btn btn-danger col-4 col-md-1',
            'data' => [
                'confirm' => 'Вы уверены что хотите удалить меню?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
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
