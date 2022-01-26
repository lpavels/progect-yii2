<?php

/* @var $this yii\web\View */

use common\models\MenusDishes;
use common\models\Report;

/* @var $form yii\widgets\ActiveForm */
?>

<?php
$model_report = new Report();
$model_menus_dishes = new MenusDishes();

$sex_arr = [
        'женский',
        'мужской'
]

?>

    <table class="table table-hover table-bordered table-striped mt-3">
        <thead>
        <tr class="text-center">
            <th rowspan="2">№</th>
            <th rowspan="2">id</th>
            <th rowspan="2">Организация</th>
            <th rowspan="2">Идентификационный номер</th>
            <th rowspan="2">ФИО</th>
            <th rowspan="2">Класс</th>
            <th rowspan="2">Год рождения</th>
            <th rowspan="2">Возраст (лет)</th>
            <th rowspan="2">Рост (см)</th>
            <th rowspan="2">Масса (кг)</th>
            <th rowspan="2">Пол</th>
            <th rowspan="2">ИМТ</th>
            <th rowspan="2">Физическое развитие</th>
            <th rowspan="2">ОО (ккал)</th>
            <th rowspan="2">СДДП(ккал)</th>
            <th rowspan="2">ДА(ккал)</th>
            <th rowspan="2">Суточные энерготраты(ккал)</th>


            <th rowspan="2">Время подъёма</th>
            <th rowspan="2">Время отбоя</th>
            <th rowspan="2">Продолжительность сна (мин)</th>

            <th rowspan="2">Время начала занятий в школе</th>
            <th rowspan="2">Время окончания занятий в школе</th>
            <th rowspan="2">Продолжительность занятий в школе (мин)</th>

            <th rowspan="2">время в пути до школы, секции и обратно (пешком) (мин)</th>
            <th rowspan="2">время в пути до школы, секции и обратно (на транспорте) (мин)</th>

            <th rowspan="2">делал зарядку? (0-нет, 1-да)</th>
            <th rowspan="2">продолжительность (мин)</th>

            <th rowspan="2">занимался в кружках доп обр (0-нет,1-да)</th>
            <th rowspan="2">продолжительность (мин)</th>

            <th rowspan="2">занимался в спорт.секции (0-нет,1-да)</th>
            <th rowspan="2">спорт секция (1)</th>
            <th rowspan="2">продолжительность (мин)</th>

            <th rowspan="2">спорт секция (2)</th>
            <th rowspan="2">продолжительность (мин)</th>

            <th rowspan="2">гулял сегодня (0-нет, 1-да)</th>
            <th rowspan="2">продолжительность (мин)</th>

            <th rowspan="2">спал днем (0-нет,1-да)</th>
            <th rowspan="2">продолжительность (мин)</th>

            <th rowspan="2">Пользуется сотовым телефоном во время перемен (0-нет, 1-да)</th>
            <th rowspan="2">кол-во приемов пищи</th>

            <th rowspan="2">Первый прием пищи (ккал)</th>
            <th rowspan="2">Второй прием пищи (ккал)</th>
            <th rowspan="2">Третий прием пищи (ккал)</th>
            <th rowspan="2">Четвертый прием пищи (ккал)</th>
            <th rowspan="2">Пятый прием пищи (ккал)</th>
            <th rowspan="2">Шестой прием пищи (ккал)</th>
            <th rowspan="2">Итого за все приемы пищи (ккал)</th>

            <th rowspan="2">Белки</th>
            <th rowspan="2">Жиры</th>
            <th rowspan="2">Углеводы</th>

            <th rowspan="2">A, мкг рет.экв</th>
            <th rowspan="2">C, мг</th>
            <th rowspan="2">B1, мг</th>
            <th rowspan="2">B2, мг</th>
            <th rowspan="2">D, мкг</th>
            <th rowspan="2">PP, мг</th>
            <th rowspan="2">Na, мг</th>
            <th rowspan="2">K, мг</th>
            <th rowspan="2">Ca, мг</th>
            <th rowspan="2">F, мг</th>
            <th rowspan="2">Mg, мг</th>
            <th rowspan="2">P, мг</th>
            <th rowspan="2">Fe, мг</th>
            <th rowspan="2">I, мкг</th>
            <th rowspan="2">Se, мкг</th>

        </tr>
        </thead>
        <tbody>
        <?
        $count=1;
        foreach ($users as $user){
            $imt_arr = $model_report->imt($user['mass'], $user['height'], $user['sex'], $user['age'],$user['field18'],$user['field19'],$user['sleep_day'],$user['field17'],
                $user['charging'],$user['field2'],$user['walk'],$user['field15'],$user['$additional_education'],$user['field4'],
                $user['sports_section'],$user['sports_section1'],$user['field6'],$user['sports_section2'],$user['field7'],$user['field8'],$user['field9'],
                $user['field20'],$user['field21'],$user['use_telephone']
            )
            ?>
            <tr class="text-center">
                <?// print_r($sportsSection_arr[0]['name']);die(); ?>
                <td><?=$count++?></td>
                <td><?=$user['id']?></td>
                <td><?=$organizations[$user['organization_id']-1]['short_title']?></td> <!--отнимаем 1, т.к. массив организаций начинается с 0, а в базе id начинается с 1-->
                <td><?=$user['key_login']?></td>
                <td><?=$user['name']?></td>
                <td><?=$user['class'].' ' .$user['bukva_klassa']?></td>
                <td><?=$user['year_birth']?></td>
                <td><?=$user['age']?></td>
                <td><?=$user['height']?></td>
                <td><?=$user['mass']?></td>
                <td><?=$sex_arr[$user['sex']]?></td>
                <td><?=$imt_arr[0]?></td>
                <td><?=$imt_arr[1]?></td>
                <td><?=$imt_arr[2]?></td>
                <td><?=$imt_arr[3]?></td>
                <td><?=$imt_arr[4]?></td>
                <td><?=$imt_arr[5]?></td>

                <td><?=$user['field18']?></td>
                <td><?=$user['field19']?></td>
                <td><?=$model_report->time_sleep($user['field19'],$user['field18'])?></td>

                <td><?=$user['field20']?></td>
                <td><?=$user['field21']?></td>
                <td><?=$model_report->time_school($user['field20'],$user['field21'])?></td>

                <td><?=$user['field8']?></td>
                <td><?=$user['field9']?></td>


                <td><?=$user['charging']?></td>
                <td><?=$user['field2']?></td>

                <td><?=$user['additional_education']?></td>
                <td><?=$user['field4']?></td>

                <td><?=$user['sports_section']?></td>
                <td><?=$sportsSection_arr[$user['sports_section1']-1]['name']?></td>
                <td><?=$user['field6']?></td>

                <td><?=$sportsSection_arr[$user['sports_section2']-1]['name']?></td>
                <td><?=$user['field7']?></td>

                <td><?=$user['walk']?></td>
                <td><?=$user['field15']?></td>

                <td><?=$user['sleep_day']?></td>
                <td><?=$user['field17']?></td>

                <td><?=$user['use_telephone']?></td>

                <td><?=$user['food_intake']?></td>

                <?
                $first_meal=0;
                $second_meal=0;
                $third_meal=0;
                $fourth_meal=0;
                $fifth_meal=0;
                $sixth_meal=0;
                $meal_total=0;
                $first_meal = $model_menus_dishes->get_kkal_nutrition($user['menu_id'], 1, 1, 1); //ккал за прием пищи
                $second_meal = $model_menus_dishes->get_kkal_nutrition($user['menu_id'], 1, 1, 2); //ккал за прием пищи
                $third_meal = $model_menus_dishes->get_kkal_nutrition($user['menu_id'], 1, 1, 3); //ккал за прием пищи
                $fourth_meal = $model_menus_dishes->get_kkal_nutrition($user['menu_id'], 1, 1, 4); //ккал за прием пищи
                $fifth_meal = $model_menus_dishes->get_kkal_nutrition($user['menu_id'], 1, 1, 5); //ккал за прием пищи
                $sixth_meal = $model_menus_dishes->get_kkal_nutrition($user['menu_id'], 1, 1, 6); //ккал за прием пищи
                $meal_total = $first_meal+$second_meal+$third_meal+$fourth_meal+$fifth_meal+$sixth_meal; //ккал за день

                $protein=0;
                $fat=0;
                $carbohydrates=0;
                for ($g=1;$g<7;$g++)
                {
                    $protein =$protein+ $model_menus_dishes->get_bju_nutrition($user['menu_id'], 1, 1, $g,'protein'); //белки всего
                    $fat =$fat+ $model_menus_dishes->get_bju_nutrition($user['menu_id'], 1, 1, $g,'fat'); //жиры всего
                    $carbohydrates =$carbohydrates+ $model_menus_dishes->get_bju_nutrition($user['menu_id'], 1, 1, $g,'carbohydrates_total'); //углеводы всего
                }
                ?>

                <td><?=round($first_meal,1)?></td>
                <td><?=round($second_meal,1)?></td>
                <td><?=round($third_meal,1)?></td>
                <td><?=round($fourth_meal,1)?></td>
                <td><?=round($fifth_meal,1)?></td>
                <td><?=round($sixth_meal,1)?></td>
                <td><?=round($meal_total,1)?></td>

                <td><?=round($protein,1)?></td>
                <td><?=round($fat,1)?></td>
                <td><?=round($carbohydrates,1)?></td>

                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'vitamin_a'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'vitamin_c'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'vitamin_b1'),1);?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'vitamin_b2'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'vitamin_d'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'vitamin_pp'),1);?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'na'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'k'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'ca'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'f'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'mg'),1);?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'p'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'fe'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'i'),1); ?></td>
                <td><?= round($model_menus_dishes->get_super_total_vitamin($user['menu_id'], 1,1, 'se'),1); ?></td>
            </tr>
        <?}?>
        <!--<tr class="text-center font-weight-bold bg-warning">
            <td colspan="4">Итого</td>
        </tr>-->
        </tbody>
    </table>


<?
$script = <<< JS
    $(".beforeload").click(function() {
      $(".beforeload").css('display','none');
      $(".load").css('display','block');
    });

    $("#pechat222").click(function () {
    var table = $('#tableId');
        if (table && table.length) {
            var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
            $(table).table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "Отчет.xls",
                fileext: ".xls",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: preserveColors
            });
        }
    });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>