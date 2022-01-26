<?php

use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\User;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

$user_model = new User();
$year_ar = [2022=>2022,2021=>2021,2020=>2020];

$two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-4 col-form-label font-weight-bold text-right']];
?>
    <div class="report-dir-school container">
        <div class="text-center"><h4>Отчёт по школьной программе</h4></div>
        <?php
        echo '<p class="text-success text-center"><b>' . Region::findOne(Organization::findOne(YII::$app->user->identity->organization_id)->region_id)->name . ', ' . Municipality::findOne(Organization::findOne(Yii::$app->user->identity->organization_id)->municipality_id)->name . ', ' . Organization::findOne(Yii::$app->user->identity->organization_id)->short_title . '</b></p>';
        $form = ActiveForm::begin();
        echo $form->field($model, 'title', $two_column)->dropDownList($year_ar,
            [
                'class' => 'form-control col-4',
                'options' => [$post => ['Selected' => true]],
            ])->label('Год обучения');
        ?>
        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['name' => 'identificator', 'value' => 'view', 'class' => 'btn main-button-3 beforeload mt-2']) ?>
                <button class="btn main-button-3 mt-2 load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Пожалуйста, подождите...
                </button>
            </div>
        </div>
        <br>
        <?php ActiveForm::end(); ?>
    </div>

<? if (!empty($show)){?>
    <table class="table table-hover table_th0 table-responsive">
        <thead>
        <tr class="text-center">
            <th rowspan="2">№</th>
            <th rowspan="2" style="max-width: 120px;">Уникальный номер</th>
            <th rowspan="2" style="max-width: 120px;">ФИО</th>
            <th rowspan="2" style="max-width: 120px;">Тип слушателя</th>
            <th rowspan="2" style="max-width: 120px;">Класс</th>
            <th rowspan="2" style="max-width: 120px;">Входной балл</th>
            <th colspan="6" style="max-width: 120px;">Школьная программа</th>

            <th rowspan="2" style="max-width: 200px;">Итоговый балл (%)</th>
            <th rowspan="2" style="max-width: 200px;">Итоговый тест пройден с 1-ого раза</th>
            <th rowspan="2" style="max-width: 200px;">Итоговый тест пройден со 2-ого раза или более</th>
            <th rowspan="2" style="max-width: 200px;">Обучение завершено</th>
        </tr>
        <tr class="text-center">
            <? foreach ($themes as $them){?>
                <th style="max-width: 120px;"><?= $them->short_name ?></th>
            <?}?>
            <th style="max-width: 150px;">Самостоятельная работа<br></th>
        </tr>
        </thead>
        <tbody>
        <?
        $count = 1;
        $input_test_total = 0;
        $theme1_total = 0;
        $theme2_total = 0;
        $theme3_total = 0;
        $theme4_total = 0;
        $theme5_total = 0;
        $independent_work_total = 0;
        $final_test_total = 0;
        $final_test_count_total = 0;
        $final_test_1st_total = 0;
        $final_test_2st_total = 0;
        $training_completed_total = 0;
        foreach ($data as $d){?>
            <tr class="text-center <? if ($count == 1) {echo 'prepend-org';}?>">
                <td><?=$count++?></td>
                <td><?=$d->key_login ?></td>
                <td><?=$d->name ?></td>
                <td><?=$d->type_listener?></td>
                <td><?=$d->class_number . ' ' . $d->letter_number?></td>
                <td><?=$d->input_test.'0%'; $input_test_total+= $d->input_test?></td>

                <td><?=(!empty($d->theme1)) ? $d->theme1 : 0; $theme1_total+=$d->theme1;?></td>
                <td><?=(!empty($d->theme2)) ? $d->theme2 : 0; $theme2_total+=$d->theme2;?></td>
                <td><?=(!empty($d->theme3)) ? $d->theme3 : 0; $theme3_total+=$d->theme3;?></td>
                <td><?=(!empty($d->theme4)) ? $d->theme4 : 0; $theme4_total+=$d->theme4;?></td>
                <td><?=(!empty($d->theme5)) ? $d->theme5 : 0; $theme5_total+=$d->theme5;?></td>
                <td><?=(!empty($d->independent_work)) ? $d->independent_work : 0; $independent_work_total+=$d->independent_work;?></td>

                <td><? if(isset($d->final_test)) {echo $d->final_test.'0%'; $final_test_count_total++; $final_test_total+=$d->final_test;} else{echo '-';} ?></td>
                <td><?=(!empty($d->final_test_1st)) ? $d->final_test_1st: 0; $final_test_1st_total+=$d->final_test_1st; ?></td>
                <td><?=(!empty($d->final_test_2st)) ? $d->final_test_2st : 0; $final_test_2st_total+=$d->final_test_2st; ?></td>
                <td><?=(!empty($d->training_completed)) ? $d->training_completed : 0; $training_completed_total+=$d->training_completed; ?></td>
            </tr>
        <?}?>
        <tr class="text-center font-weight-bold bg-warning">
            <td colspan="5">Итого</td>
            <td><?= ($count == 1) ? '0' : round($input_test_total*10/($count-1),1).'%' ?></td>
            <td><?= $theme1_total ?></td>
            <td><?= $theme2_total ?></td>
            <td><?= $theme3_total ?></td>
            <td><?= $theme4_total ?></td>
            <td><?= $theme5_total ?></td>
            <td><?= $independent_work_total ?></td>
            <td><?= ($final_test_count_total==0)?'0':  round($final_test_total*10/$final_test_count_total,1).'%'?></td>
            <td><?= $final_test_1st_total ?></td>
            <td><?= $final_test_2st_total ?></td>
            <td><?= $training_completed_total ?></td>
        </tr>
        </tbody>
    </table>
<?php } ?>

    <script type="text/javascript">
        var org = '<?php echo $post;?>';

        if (org === '2021'){
            col1 = '<?php echo ($count < 2) ? '0' : round($input_test_total * 10 / ($count - 1), 1) . '%';?>';
            col2 = '<?php echo $theme1_total;?>';
            col3 = '<?php echo $theme2_total;?>';
            col4 = '<?php echo $theme3_total;?>';
            col5 = '<?php echo $theme4_total;?>';
            col6 = '<?php echo $theme5_total;?>';
            col7 = '<?php echo $independent_work_total;?>';
            col8 = '<?php echo ($final_test_count_total == 0) ? '0' : round($final_test_total * 10 / $final_test_count_total, 1) . '%';?>';
            col9 = '<?php echo $final_test_1st_total;?>';
            col10 = '<?php echo $final_test_2st_total;?>';
            col11 = '<?php echo $training_completed_total;?>';
        }
    </script>

<?
$script = <<< JS
    $(".beforeload").click(function() {
      $(".beforeload").css('display','none');
      $(".load").css('display','block');
    });

    if (org==='2021'){
        $('.prepend-org').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="5">Итого</td>' +
            '<td>'+col1+'</td>' +
            '<td>'+col2+'</td>' +
            '<td>'+col3+'</td>' +
            '<td>'+col4+'</td>' +
            '<td>'+col5+'</td>' +
            '<td>'+col6+'</td>' +
            '<td>'+col7+'</td>' +
            '<td>'+col8+'</td>' +
            '<td>'+col9+'</td>' +
            '<td>'+col10+'</td>' +
            '<td>'+col11+'</td>' +
            '</tr>'
        );
    }
JS;
$this->registerJs($script, View::POS_READY);
?>