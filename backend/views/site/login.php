<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \common\models\LoginForm */

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\web\View;

$this->title = 'Авторизация';
?>
    <div class="site-login m-4">
	<h1><?= Html::encode($this->title) ?></h1>
            <p class="text-danger" style="font-size:20px;">Вход в архивную версию 2020 года для скачивания сертификата: <a href="https://edu2020.demography.site">нажми на ссылку<a/></p>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'login', ['options' => ['class' => 'col-sm-7 col-lg-3 mb-2 pl-0']])->textInput(['class' => 'form-control', 'autofocus' => true, 'placeholder' => 'Идентификацонный номер'])->label(false) ?>
            <?= Html::submitButton('Войти в программу', ['class' => 'btn main-button-3', 'name' => 'login-button']) ?>
            <?php ActiveForm::end(); ?>
        </div>

<?php
$js = <<< JS
	$('form').on('beforeSubmit', function(){
        var form = $(this);    
        var submit = form.find(':submit');
        submit.html('<span class="fa fa-spin fa-spinner"></span> Пожалуйста, подождите...');
        submit.prop('disabled', true);
    });
JS;
$this->registerJs($js, View::POS_READY);
