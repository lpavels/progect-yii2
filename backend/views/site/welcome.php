<?php

/* @var $this yii\web\View */

use common\models\User;
use yii\bootstrap4\Html;

$this->title = 'Добро пожаловать';

?>
    <div class="container">
        <div class="alert alert-success" role="alert">
            Вы успешно зарегистрировались в программе!
        </div>

        <?
        //print_r($id);
        $queru = User::find()->where(['key_login' => $id])->one();
        ?>
        <div class="row mt-5">
            <div class="col-5">
                <b><font class="text-uppercase font-weight-bold" color="#dc3545">Ваш уникальный номер для входа в
                        программу:</font> <br> Данный номер, понадобится для входа в личный кабинет, прохождения
                    входного и выходного тестирования, получения документа, подтверждающего прохождение обучения</b>

            </div>
            <div class="col-2">
                <div class="resulte"><?= $queru->key_login ?></div>
            </div>
            <div class="col-4">
                <button type="button" id="click_save" class="btn btn-outline-dark btn-sm">Скопировать номер в буфер
                    обмен
                </button>
            </div>
        </div>
        <div class="text-center mt-5">
            <h5>Теперь Вы можете авторизироваться в системе</h5>
        </div>
        <div class="text-center mt-2">
            <?= Html::a('Авторизация',
                ['/site/login'],
                [
                    'class' => 'btn btn-outline-primary',
                    'data-toggle' => 'tooltip',
                ])
            ?>
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> Сохранить регистрационные данные в PDF формате',
                ['export?id=' . $id],
                [
                    'class' => 'btn btn-outline-danger',
                    'title' => Yii::t('yii', 'Вы можете сохранить данные в PDF формате'),
                    'data-toggle' => 'tooltip',
                ])
            ?>
        </div>
        <div class="text-center mt-5">

        </div>
    </div>
<?
$script = <<< JS

var click_save = $('#click_save');
click_save.on('click', function () {
    var tmp = $("<textarea>");
	$("body").append(tmp);
	tmp.val($('.resulte').text()).select();
	document.execCommand("copy");
	tmp.remove();
});	
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>