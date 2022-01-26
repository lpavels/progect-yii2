<?php

use common\models\Kids;
use common\models\DailyRoutine;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\DailyRoutine */
/* @var $model common\models\Kids */

$this->title = 'Отчёт';
?>

    <div class="day-report container w-75">

        <h1 class=""><?= Html::encode($this->title) ?></h1>
        <p class="font-weight-bold">Заключение:</p>

        <p>Индекс массы тела: <?= $queteletIndexVal ?>; физическое развитие: <?= $queteletIndexText ?>.</p>

        <p class="font-weight-bold">Рекомендации:</p>
        <p> <?= $text1 ?></p>
        <p> <?= $text2 ?></p>
        <p> <?= $text3 ?></p>
        <p> <?= $text4 ?></p>
        <p> <?= $text5 ?></p>
        <p> <?= $text6 ?></p>
        <p> <?= $text7 ?></p>

        <table class="table table-bordered table-sm text-center">
            <thead>
            <tr>
                <td rowspan="2"></td>
                <td colspan="2">Фактическое значение энерготрат</td>
            </tr>
            <tr>
                <td>ккал</td>
                <td>%</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Основной обмен</td>
                <td><?= $OO ?></td>
                <td><?= round($OO/$totalEnergy*100,1) ?></td>
            </tr>
            <tr>
                <td>Специфическое динамическое действие пищи</td>
                <td><?= $SDDP ?></td>
                <td><?= round($SDDP/$totalEnergy*100,1) ?></td>
            </tr>
            <tr>
                <td>Двигательная активность</td>
                <td><?=$DA?></td>
                <td><?= round($DA/$totalEnergy*100,1) ?></td>
            </tr>
            <tr>
                <td>Суточные энерготраты</td>
                <td><?=$totalEnergy?></td>
                <td>100</td>
            </tr>
            </tbody>
        </table>
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