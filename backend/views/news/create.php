<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\News */

$this->title = 'Добавление новости';
?>


<div class="news-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category')->dropdownList(['Новости','Видео','Важно']) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'news_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fix')->checkbox() ?>

    <div class="font-weight-bold">Доступно для групп:</div>
    <?foreach ($roles as $role){?>
        <?= $form->field($model_access, $role)->checkbox() ?>
    <?}?>

    <div class="form-group">
        <?= Html::submitButton('Добавить новость на сайт', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
