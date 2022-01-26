<?php

use common\models\Kids;
use common\models\DailyRoutine;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\DailyRoutine */
/* @var $model common\models\Kids */

$this->title = 'Отчёт по самостоятельной работе';
?>
    <div class="report-independentWork container w-75">
        <h1 class=""><?= Html::encode($this->title) ?></h1>

        <p class="font-weight-bold mb-1">1) Информация об учебном дне.</p>
        <? if ($daily == 1) { ?>
            <p class="font-weight-bold mb-1">Заключение:</p>

            <p class="mb-1">Индекс массы тела: <?= $queteletIndexVal ?>; физическое развитие: <?= $queteletIndexText ?>.</p>

            <p class="font-weight-bold mb-1">Рекомендации:</p>
            <p class="mb-1"> <?= $text1 ?></p>
            <p class="mb-1"> <?= $text2 ?></p>
            <p class="mb-1"> <?= $text3 ?></p>
            <p class="mb-1"> <?= $text4 ?></p>
            <p class="mb-1"> <?= $text5 ?></p>
            <p class="mb-1"> <?= $text6 ?></p>
            <p class="mb-1"> <?= $text7 ?></p>

            <table class="table_th0 table-responsive">
                <thead>
                <tr class="text-center">
                    <td rowspan="2"></td>
                    <td colspan="2">Фактическое значение энерготрат</td>
                </tr>
                <tr class="text-center">
                    <td>ккал</td>
                    <td>%</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Основной обмен</td>
                    <td class="text-center"><?= $OO ?></td>
                    <td class="text-center"><?= round($OO / $totalEnergy * 100, 1) ?></td>
                </tr>
                <tr>
                    <td>Специфическое динамическое действие пищи</td>
                    <td class="text-center"><?= $SDDP ?></td>
                    <td class="text-center"><?= round($SDDP / $totalEnergy * 100, 1) ?></td>
                </tr>
                <tr>
                    <td>Двигательная активность</td>
                    <td class="text-center"><?= $DA ?></td>
                    <td class="text-center"><?= round($DA / $totalEnergy * 100, 1) ?></td>
                </tr>
                <tr>
                    <td>Суточные энерготраты</td>
                    <td class="text-center"><?= $totalEnergy ?></td>
                    <td class="text-center">100</td>
                </tr>
                </tbody>
            </table>
        <?php }else{return '<p>Информация об учебном дне не составлена.</p>';} ?>
    </div>

    <br>
    <div class="report-menu container w-75">
        <p class="font-weight-bold">2) Информация о съеденной за день пище.</p>
        <? if ($menu == 1){?>
            <?
            $super_total_yield = 0;
            $super_total_protein = 0;
            $super_total_fat = 0;
            $super_total_carbohydrates_total = 0;
            $super_total_energy_kkal = 0;

            $menu_id = $menus_dishes[0]->menu_id;
            $cycle = $menus_dishes[0]->cycle;
            $days_id = $menus_dishes[0]->days_id;
            $nutrition_id = $menus_dishes[0]->nutrition_id;
            ?>
            <table class="table_th0 table-responsive">
                <tr class="">
                    <th class="text-center"></th>
                    <th class="text-center">Выход</th>
                    <th class="text-center">Белки</th>
                    <th class="text-center">Жиры</th>
                    <th class="text-center">Углеводы</th>
                    <th class="text-center">Эн. ценность</th>
                </tr>
                <?$itog_protein = 0; $itog_fat=0; $itog_carbohydrates_total=0; $itog_kkal=0; $itog_yield = 0;?>
                <?foreach ($nutritions as $nutrition){?>
                    <tr class="table-primary">
                        <td>Итого за <?= (Yii::$app->user->can('director') ? $nutrition->name : $nutrition->name2 ) ?></td>
                        <td class="text-center"><? $yield = round($model->get_total_yield($menu_id, $cycle, $days_id, $nutrition->id),1); echo $yield; $itog_yield = $itog_yield + $yield;?></td>
                        <td class="text-center"><? $protein = round($model->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition->id,'protein'),1); echo $protein; $itog_protein = $itog_protein + $protein;?></td>
                        <td class="text-center"><? $fat = round($model->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition->id,'fat'),1); echo $fat; $itog_fat = $itog_fat + $fat;?></td>
                        <td class="text-center"><? $carbohydrates_total = round($model->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition->id,'carbohydrates_total'),1); echo $carbohydrates_total; $itog_carbohydrates_total = $itog_carbohydrates_total + $carbohydrates_total;?></td>
                        <td class="text-center"><? $kkal = round($model->get_kkal_nutrition($menu_id, $cycle, $days_id, $nutrition->id),1); echo $kkal; $itog_kkal = $itog_kkal + $kkal;?></td>
                    </tr>
                <?}?>
                <tr class="table-danger itog_day">
                    <td>Итого за день</td>
                    <td class="text-center"><?= $itog_yield; ?></td>
                    <td class="text-center"><?= $itog_protein; ?></td>
                    <td class="text-center"><?= $itog_fat; ?></td>
                    <td class="text-center"><?= $itog_carbohydrates_total;?></td>
                    <td class="text-center"><?= $itog_kkal; ?></td>
                </tr>
                <!--<tr class="procent_day table-danger">
                    <td colspan="2">Процентное соотношение БЖУ за день</td>
                    <td class="text-center"><?/*= $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'protein').'%'; */?></td>
                    <td class="text-center"><?/*= $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'fat').'%'; */?></td>
                    <td class="text-center"><?/*= $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'carbohydrates_total').'%'; */?></td>
                </tr>-->
                <tr class="itog_day table-success">
                    <td colspan="2">Рекомендуемая величина за день</td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($menu_id, 'protein_middle_weight');?></td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($menu_id, 'fat_middle_weight');?></td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($menu_id, 'carbohydrates_middle_weight');?></td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($menu_id, 'middle_kkal');?></td>
                </tr>
            </table>
        <?}?>
    </div>

    <br>
    <div class="report-itog container w-75">
        <h5>Самостоятельная работа <?= $indWork?>.</h5>
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