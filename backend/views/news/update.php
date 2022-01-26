<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\News */

$this->title = 'Редактирование новости';
?>

<?php
$arr=[];
for ($i=0;$i<count($access_role);$i++)
{
    $arr[] = $access_role[$i]['role'];
}
?>

<div class="news-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category')->dropdownList(['news','video','alert']) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'news_text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fix')->checkbox() ?>

    <div class="font-weight-bold">Доступно для групп:</div>
    <?foreach ($roles as $role){
        if ((in_array($role, $arr)))
        {?>
            <?= $form->field($model_access, $role)->checkbox(['checked'=>true]) ?>
        <?}else{?>
            <?= $form->field($model_access, $role)->checkbox() ?>
        <?}?>
    <?}?>

    <div class="form-group">
        <?= Html::submitButton('Обновить новость на сайте', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
