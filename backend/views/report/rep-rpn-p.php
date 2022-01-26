<?php

use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\User;
use common\models\UserEdu20;
use common\models\UserEdu21;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$municipality_null = array('0' => 'Все муниципальные образования');
$municipality = Municipality::find()->where(['region_id' => $depEdu->region_id])->orderBy(['name' => SORT_ASC])->all();
$municipality_item = ArrayHelper::map($municipality, 'id', 'name');
$municipality_item = ArrayHelper::merge($municipality_null, $municipality_item);

if (!empty($post['municipality_id']))
{
    $name_organization_null = array('0' => 'Все организации');
    $name_organizations = Organization::find()->where(['municipality_id' => $post['municipality_id'], 'status'=>1])->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])->all();
    $name_organization_items = ArrayHelper::map($name_organizations, 'id', 'short_title');
    $name_organization_items = ArrayHelper::merge($name_organization_null, $name_organization_items);
}
else
{
    $name_organization_items = array('0' => 'Все организации');
}

$year_ar = [2022=>2022,2021=>2021,2020=>2020];

$two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-4 col-form-label font-weight-bold text-right']];

$user_model = new User();
$user_model21 = new UserEdu21();
$user_model20 = new UserEdu20();
?>
    <style>
        th, td {
            border: 1px solid black !important;
            color: black;
        }

        th {
            background-color: #ede8b9;
            font-size: 15px;
        }
    </style>
    <div class="report-rpn container">
        <div class="text-center"><h4>Отчёт по дошкольной программе</h4></div>
        <?php
        $form = ActiveForm::begin(); ?>
        <p class="text-success text-center">
            <b><?= Region::findOne(Organization::findOne(YII::$app->user->identity->organization_id)->region_id)->name ?></b>
        </p>
        <?= $form->field($model, 'title', $two_column)->dropDownList($year_ar,
            [
                'class' => 'form-control col-4',
                'options' => [$post['title'] => ['Selected' => true]],
            ])->label('Год обучения');?>

        <?= $form->field($model, 'municipality_id', $two_column)->dropDownList($municipality_item,
            [
                'class' => 'form-control col-4',
                'options' => [$post['municipality_id'] => ['Selected' => true]],

                'onchange' => '
                        var id_m = $(this).val();
                        $.get("../report/organization-name-school?id_m="+id_m, function(data){
                          console.log(data);$("select#organization-short_title").html(data);
                          document.getElementById("organization-short_title").disabled = false;
                        });'
            ]); ?>

        <?= $form->field($model, 'short_title', $two_column)->dropDownList($name_organization_items,
            [
                'class' => 'form-control col-4',
                'options' => [$post['short_title'] => ['Selected' => true]],
            ])->label('Наименование организации'); ?>

        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['name' => 'identificator', 'value' => 'view', 'class' => 'mt-2 btn main-button-3 beforeload']) ?>
                <button class="btn main-button-3 mt-2 load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Пожалуйста, подождите...
                </button>
            </div>
        </div>
        <br>
        <?php ActiveForm::end(); ?>
    </div>
<?
if (!empty($org))//по организации
{?>
    <table class="table table-hover table_th0 table-responsive">
        <thead>
        <tr class="text-center">
            <th rowspan="2">№</th>
            <th rowspan="2" style="max-width: 120px;">Уникальный номер</th>
            <th rowspan="2" style="max-width: 120px;">Тип слушателя</th>
            <th rowspan="2" style="max-width: 120px;">Класс</th>
            <th rowspan="2" style="max-width: 120px;">Входной балл</th>
            <th colspan="7" style="max-width: 120px;">Дошкольная программа</th>

            <th rowspan="2" style="max-width: 200px;">Итоговый балл (%)</th>
            <th rowspan="2" style="max-width: 200px;">Итоговый тест пройден с 1-ого раза</th>
            <th rowspan="2" style="max-width: 200px;">Итоговый тест пройден со 2-ого раза или более</th>
            <th rowspan="2" style="max-width: 200px;">Обучение завершено</th>
            <?
            if ($post['title'] >= 2021)
            {?>
                <th rowspan="2" style="max-width: 200px;">Обучено детей</th>
            <?}
            ?>
        </tr>
        <tr class="text-center">
            <?
            foreach ($themes as $them)
            {?>
                <th style="max-width: 120px;"><?= $them->short_name ?></th>
                <?
            }
            ?>
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
        $theme6_total = 0;
        $independent_work_total = 0;
        $final_test_total = 0;
        $final_test_count_total = 0;
        $final_test_1st_total = 0;
        $final_test_2st_total = 0;
        $training_completed_total = 0;
        $child_total = 0;
        foreach ($data as $d)
        {
            ?>
            <tr class="text-center <? if ($count == 1) { echo 'prepend-org';}?>">
                <td><?=$count++?></td>
                <td><?=$d->key_login ?></td>
                <td><?=$d->type_listener?></td>
                <td><?=$d->class_number . ' ' . $d->letter_number?></td>
                <td><?=$d->input_test.'0%'; $input_test_total+= $d->input_test?></td>

                <td><?=(!empty($d->theme1)) ? $d->theme1 : 0; $theme1_total+=$d->theme1;?></td>
                <td><?=(!empty($d->theme2)) ? $d->theme2 : 0; $theme2_total+=$d->theme2;?></td>
                <td><?=(!empty($d->theme3)) ? $d->theme3 : 0; $theme3_total+=$d->theme3;?></td>
                <td><?=(!empty($d->theme4)) ? $d->theme4 : 0; $theme4_total+=$d->theme4;?></td>
                <td><?=(!empty($d->theme5)) ? $d->theme5 : 0; $theme5_total+=$d->theme5;?></td>
                <td><?=(!empty($d->theme6)) ? $d->theme6 : 0; $theme6_total+=$d->theme6;?></td>
                <td><?=(!empty($d->independent_work)) ? $d->independent_work : 0; $independent_work_total+=$d->independent_work;?></td>

                <td><? if(isset($d->final_test)) {echo $d->final_test.'0%'; $final_test_count_total++; $final_test_total+=$d->final_test;} else{echo '-';} ?></td>
                <td><?=(!empty($d->final_test_1st)) ? $d->final_test_1st: 0; $final_test_1st_total+=$d->final_test_1st; ?></td>
                <td><?=(!empty($d->final_test_2st)) ? $d->final_test_2st : 0; $final_test_2st_total+=$d->final_test_2st; ?></td>
                <td><?=(!empty($d->training_completed)) ? $d->training_completed : 0; $training_completed_total+=$d->training_completed; ?></td>
                <?
                if ($post['title'] >= 2021)
                {?>
                    <td><?=(!empty($d->number_children)) ? $d->number_children : 0; $child_total+=$d->number_children; ?></td>
                <?}
                ?>
            </tr>
            <?
        }
        ?>
        <tr class="text-center font-weight-bold bg-warning">
            <td colspan="4">Итого</td>
            <td><?= ($count < 2) ? '0' : round($input_test_total*10/($count-1),1).'%' ?></td>
            <td><?= $theme1_total ?></td>
            <td><?= $theme2_total ?></td>
            <td><?= $theme3_total ?></td>
            <td><?= $theme4_total ?></td>
            <td><?= $theme5_total ?></td>
            <td><?= $theme6_total ?></td>
            <td><?= $independent_work_total ?></td>
            <td><?= ($final_test_count_total==0)?'0':  round($final_test_total*10/$final_test_count_total,1).'%'?></td>
            <td><?= $final_test_1st_total ?></td>
            <td><?= $final_test_2st_total ?></td>
            <td><?= $training_completed_total ?></td>
            <? if ($post['title'] >= 2021)
            {?>
                <td><?= $child_total ?></td>
            <?} ?>
        </tr>
        </tbody>
    </table>
<?php } //по организации

elseif (!empty($mun_org)) //по муниципальному
{
    if (empty($organizations))
    {
        echo '<br><p class="text-center text-danger font-weight-bold">В муниципальном районе не добавлена ни одна организация!</p>';
    }
    else
    {
        if ($post['title'] == 2022)
        {
            $mun_array = $user_model->reportNew($post['municipality_id'], 2, 2);
        }
        elseif ($post['title'] == 2021)
        {
            $mun_array = $user_model21->reportNew($post['municipality_id'], 2, 2);
        }
        elseif ($post['title'] == 2020)
        {
            $mun_array = $user_model20->reportNew($post['municipality_id'], 2, 2);
        }
        if (!isset($mun_array[0]) || !isset($mun_array[1]))
        {
            echo '<br><p class="text-center text-danger font-weight-bold">К обучению не приступила ни одна организация!</p>';
        }
        else
        {?>
            <table class="table table-hover table_th0 table-responsive">
                <thead>
                <tr class="text-center">
                    <th rowspan="2">№</th>
                    <th rowspan="2">Название организации</th>
                    <th rowspan="2" style="max-width: 120px;">Приступили к обучению (1-да, 0-нет)</th>

                    <th colspan="3" style="max-width: 180px;">Количество зарегистрировавшихся человек</th>
                    <th colspan="11">Количество человек прошедших обучение</th>
                    <th colspan="3">Завершили обучение</th>
                    <?
                    if ($post['title'] >= 2021)
                    {?>
                        <th rowspan="2">Обучено детей</th>
                    <?}
                    ?>
                </tr>
                <tr class="text-center">
                    <th style="max-width: 122px;">Всего</th>
                    <th style="max-width: 122px;">Взрослых</th>
                    <th style="max-width: 122px;">Детей</th>

                    <th style="max-width: 150px;">Входной тест (среднее значение %)</th>
                    <? foreach ($themes as $them)
                    {
                        ?>
                        <th style="max-width: 120px;"><?= $them->short_name ?></th>
                        <?
                    } ?>
                    <th style="max-width: 140px;">Самостоятельная работа</th>
                    <th style="max-width: 122px;">Итоговый тест (среднее значение %)</th>
                    <th style="max-width: 150px;">Итоговый тест пройден с 1-ого раза</th>
                    <th style="max-width: 150px;">Итоговый тест пройден со 2-ого раза или более</th>

                    <th style="max-width: 122px;">Всего</th>
                    <th style="max-width: 122px;">Взрослых</th>
                    <th style="max-width: 122px;">Детей</th>
                </tr>
                </thead>
                <tbody>
                <?
                $count = 1;
                $start_work_total = 0;
                $people_total = 0;
                $parent_total = 0;
                $child_total = 0;
                $inputTest_total = 0;
                $inputTest_count = 0;
                $theme1_total = 0;
                $theme2_total = 0;
                $theme3_total = 0;
                $theme4_total = 0;
                $theme5_total = 0;
                $theme6_total = 0;
                $independentWork_total = 0;
                $finalTest_total = 0;
                $finalTest_count = 0;
                $finalTest_1st_total = 0;
                $finalTest_2st_total = 0;
                $trainingCompleted_total = 0;

                $trainingCompletedAll_total = 0;
                $trainingCompletedParent_total = 0;
                $trainingCompletedChild_total = 0;

                $childrenTrained_total = 0;

                foreach ($organizations as $organization)
                {
                    /**/
                    if (array_key_exists($organization->id.'_training_id_1',$mun_array[0]) || array_key_exists($organization->id.'_training_id_2',$mun_array[0]))
                    {
                        $people = $mun_array[0][$organization->id.'_training_id_1']+$mun_array[0][$organization->id.'_training_id_2'];
                        $people_total+=$people;
                    }
                    else
                    {
                        $people = 0;
                    }
                    if (array_key_exists($organization->id.'_training_id_2',$mun_array[0]))
                    {
                        $parent = $mun_array[0][$organization->id.'_training_id_2'];
                        $parent_total+=$parent;
                    }
                    else{$parent = 0;}
                    if (array_key_exists($organization->id.'_training_id_1',$mun_array[0]))
                    {
                        $child = $mun_array[0][$organization->id.'_training_id_1'];
                        $child_total+=$child;
                    }
                    else{$child=0;}
                    if (array_key_exists($organization->id.'_inputTestCount_calc',$mun_array[1]))
                    {
                        $inputTest = $mun_array[1][$organization->id.'_inputTest_calc']*10/$mun_array[1][$organization->id.'_inputTestCount_calc'];
                        $inputTest_total+=$inputTest;
                        $inputTest_count++;

                        $start_work = 1;
                        $start_work_total++;
                    } //входной тест
                    else { $inputTest=0; $start_work=0; }

                    if (array_key_exists($organization->id.'_theme1',$mun_array[1]))
                    {
                        $theme1 = $mun_array[1][$organization->id.'_theme1'];
                        $theme1_total+=$theme1;
                    }
                    else { $theme1=0; }
                    if (array_key_exists($organization->id.'_theme2',$mun_array[1]))
                    {
                        $theme2 = $mun_array[1][$organization->id.'_theme2'];
                        $theme2_total+=$theme2;
                    }
                    else { $theme2=0; }
                    if (array_key_exists($organization->id.'_theme3',$mun_array[1]))
                    {
                        $theme3 = $mun_array[1][$organization->id.'_theme3'];
                        $theme3_total+=$theme3;
                    }
                    else { $theme3=0; }
                    if (array_key_exists($organization->id.'_theme4',$mun_array[1]))
                    {
                        $theme4 = $mun_array[1][$organization->id.'_theme4'];
                        $theme4_total+=$theme4;
                    }
                    else { $theme4=0; }
                    if (array_key_exists($organization->id.'_theme5',$mun_array[1]))
                    {
                        $theme5 = $mun_array[1][$organization->id.'_theme5'];
                        $theme5_total+=$theme5;
                    }
                    else { $theme5=0; }
                    if (array_key_exists($organization->id.'_theme6',$mun_array[1]))
                    {
                        $theme6 = $mun_array[1][$organization->id.'_theme6'];
                        $theme6_total+=$theme6;
                    }
                    else { $theme6=0; }
                    if (array_key_exists($organization->id.'_independentWork',$mun_array[1]))
                    {
                        $independentWork = $mun_array[1][$organization->id.'_independentWork'];
                        $independentWork_total+=$independentWork;
                    }
                    else { $independentWork=0; }
                    if (array_key_exists($organization->id.'_finalTestCount_calc',$mun_array[1]))
                    {
                        $finalTest = $mun_array[1][$organization->id.'_finalTest_calc']*10/$mun_array[1][$organization->id.'_finalTestCount_calc'];
                        $finalTest_total+=$finalTest;
                        $finalTest_count++;
                    } //итоговый тест
                    else { $finalTest=0; }
                    if (array_key_exists($organization->id.'_finalTest_1st',$mun_array[1]))
                    {
                        $finalTest_1st = $mun_array[1][$organization->id.'_finalTest_1st'];
                        $finalTest_1st_total+=$finalTest_1st;
                    }
                    else { $finalTest_1st=0; }
                    if (array_key_exists($organization->id.'_finalTest_2st',$mun_array[1]))
                    {
                        $finalTest_2st = $mun_array[1][$organization->id.'_finalTest_2st'];
                        $finalTest_2st_total+=$finalTest_2st;
                    }
                    else { $finalTest_2st=0; }
                    if (array_key_exists($organization->id.'_trainingCompletedAll',$mun_array[1]))
                    {
                        $trainingCompletedAll = $mun_array[1][$organization->id.'_trainingCompletedAll'];
                        $trainingCompletedAll_total+=$trainingCompletedAll;
                    }
                    else { $trainingCompletedAll=0; }
                    if (array_key_exists($organization->id.'_trainingCompletedParent',$mun_array[1]))
                    {
                        $trainingCompletedParent = $mun_array[1][$organization->id.'_trainingCompletedParent'];
                        $trainingCompletedParent_total+=$trainingCompletedParent;
                    }
                    else { $trainingCompletedParent=0; }
                    if (array_key_exists($organization->id.'_trainingCompletedChild',$mun_array[1]))
                    {
                        $trainingCompletedChild = $mun_array[1][$organization->id.'_trainingCompletedChild'];
                        $trainingCompletedChild_total+=$trainingCompletedChild;
                    }
                    else { $trainingCompletedChild=0; }

                    if (array_key_exists($organization->id.'_numberChildren',$mun_array[1]))
                    {
                        $childrenTrained = $mun_array[1][$organization->id.'_numberChildren'];
                        $childrenTrained_total+=$childrenTrained;
                    }
                    else { $childrenTrained=0; }
                    /*(END)*/
                    ?>

                    <tr class="text-center <? if ($count == 1){echo 'prepend-mun';}?>">
                        <td><?= $count++ ?></td>
                        <td><?= $organization->short_title ?></td>
                        <td><?= $start_work ?></td>
                        <td><?= $people ?></td>
                        <td><?= $parent ?></td>
                        <td><?= $child ?></td>
                        <td><?= round($inputTest,1) ?></td>
                        <td><?= $theme1 ?></td>
                        <td><?= $theme2 ?></td>
                        <td><?= $theme3 ?></td>
                        <td><?= $theme4 ?></td>
                        <td><?= $theme5 ?></td>
                        <td><?= $theme6 ?></td>
                        <td><?= $independentWork ?></td>
                        <td><?= round($finalTest,1) ?></td>
                        <td><?= $finalTest_1st ?></td>
                        <td><?= $finalTest_2st ?></td>
                        <td><?= $trainingCompletedAll ?></td>
                        <td><?= $trainingCompletedParent ?></td>
                        <td><?= $trainingCompletedChild ?></td>
                        <?
                        if ($post['title'] >= 2021)
                        {?>
                            <td><?= $childrenTrained ?></td>
                        <?}
                        ?>
                    </tr>
                    <?
                }?>
                <tr class="text-center font-weight-bold bg-warning">
                    <td colspan="2">Итого</td>
                    <td><?= $start_work_total ?></td>
                    <td><?= $people_total ?></td>
                    <td><?= $parent_total ?></td>
                    <td><?= $child_total ?></td>
                    <td><?= ($inputTest_count == 0) ? 0 : round($inputTest_total/$inputTest_count,1) ?></td>
                    <td><?= $theme1_total ?></td>
                    <td><?= $theme2_total ?></td>
                    <td><?= $theme3_total ?></td>
                    <td><?= $theme4_total ?></td>
                    <td><?= $theme5_total ?></td>
                    <td><?= $theme6_total ?></td>
                    <td><?= $independentWork_total ?></td>
                    <td><?= ($finalTest_count == 0) ? 0 : round($finalTest_total/$finalTest_count,1) ?></td>
                    <td><?= $finalTest_1st_total ?></td>
                    <td><?= $finalTest_2st_total ?></td>
                    <td><?= $trainingCompletedAll_total ?></td>
                    <td><?= $trainingCompletedParent_total ?></td>
                    <td><?= $trainingCompletedChild_total ?></td>
                    <?
                    if ($post['title'] >= 2021)
                    {?>
                        <td><?= $childrenTrained_total ?></td>
                    <?}
                    ?>
                </tr>
                </tbody>
            </table>
            <?
        }
    }
} //по муниципальному

elseif (!empty($reg_org)) //по региону
{
    if ($post['title'] == 2022)
    {
        $reg_array = $user_model->reportNew($depEdu->region_id, 2, 3);
    }
    elseif ($post['title'] == 2021)
    {
        $reg_array = $user_model21->reportNew($depEdu->region_id, 2, 3);
    }
    elseif ($post['title'] == 2020)
    {
        $reg_array = $user_model20->reportNew($depEdu->region_id, 2, 3);
    }

    if (!isset($reg_array[0]) || !isset($reg_array[1]))
    {
        echo '<br><p class="text-center text-danger font-weight-bold">Обучение не начато ни в одном муниципальном районе!</p>';
    }
    else
    {?>
        <table class="table table-hover table_th0 table-responsive">
            <thead>
            <tr class="text-center">
                <th rowspan="2">№</th>
                <th rowspan="2">Муниципальный район</th>
                <th rowspan="2" style="max-width: 120px;">Приступили к обучению (1-да, 0-нет)</th>
                <th rowspan="2" style="max-width: 160px;">Количество образовательных организаций приступивших к работе</th>

                <th colspan="3" style="max-width: 180px;">Количество зарегистрировавшихся человек</th>
                <th colspan="11">Количество человек прошедших обучение</th>
                <th colspan="3">Завершили обучение</th>
                <?
                if ($post['title'] >= 2021)
                {?>
                    <th rowspan="2">Обучено детей</th>
                <?}
                ?>
            </tr class="text-center">
            <tr class="text-center">
                <th style="max-width: 122px;">Всего</th>
                <th style="max-width: 122px;">Взрослых</th>
                <th style="max-width: 122px;">Детей</th>

                <th style="max-width: 150px;">Входной тест (среднее значение %)</th>
                <? foreach ($themes as $them)
                {
                    ?>
                    <th style="max-width: 120px;"><?= $them->short_name ?></th>
                    <?
                } ?>
                <th style="max-width: 135px;">Самостоятельная работа</th>
                <th style="max-width: 122px;">Итоговый тест (среднее значение %)</th>
                <th style="max-width: 150px;">Итоговый тест пройден с 1-ого раза</th>
                <th style="max-width: 150px;">Итоговый тест пройден со 2-ого раза или более</th>

                <th style="max-width: 122px;">Всего</th>
                <th style="max-width: 122px;">Взрослых</th>
                <th style="max-width: 122px;">Детей</th>
            </tr>
            </thead>
            <tbody>
            <?
            $count = 1;
            $startWork_total = 0;
            $countOrgStart_total = 0;
            $people_total = 0;
            $parent_total = 0;
            $child_total = 0;
            $inputTest_total = 0;
            $inputTest_count = 0;
            $theme1_total = 0;
            $theme2_total = 0;
            $theme3_total = 0;
            $theme4_total = 0;
            $theme5_total = 0;
            $theme6_total = 0;
            $independentWork_total = 0;
            $finalTest_total = 0;
            $finalTest_count = 0;
            $finalTest_1st_total = 0;
            $finalTest_2st_total = 0;
            $trainingCompleted_total = 0;

            $trainingCompletedAll_total = 0;
            $trainingCompletedParent_total = 0;
            $trainingCompletedChild_total = 0;

            $childrenTrained_total = 0;
            foreach ($municipalitys as $municipality)
            {
                /**/
                if (array_key_exists($municipality->id.'_countOrgStart',$reg_array[1])) //"Приступили к обучению" && "Количество образовательных организаций приступивших к работе"
                {
                    $countOrgStart = $reg_array[1][$municipality->id.'_countOrgStart'];
                    $countOrgStart_total+=$countOrgStart;
                    $startWork = 1;
                    $startWork_total++;
                }
                else { $countOrgStart=0;$startWork=0; }

                if (array_key_exists($municipality->id.'_training_id_1',$reg_array[0]) || array_key_exists($municipality->id.'_training_id_2',$reg_array[0])) //Количество зарегистрировавшихся человек (всего)
                {
                    $people = $reg_array[0][$municipality->id.'_training_id_1']+$reg_array[0][$municipality->id.'_training_id_2'];
                    $people_total+=$people;
                }
                else{ $people = 0; }

                if (array_key_exists($municipality->id.'_training_id_2',$reg_array[0])) //Количество зарегистрировавшихся человек (взрослых)
                {
                    $parent = $reg_array[0][$municipality->id.'_training_id_2'];
                    $parent_total+=$parent;
                }
                else{$parent = 0;}
                if (array_key_exists($municipality->id.'_training_id_1',$reg_array[0])) //Количество зарегистрировавшихся человек (детей)
                {
                    $child = $reg_array[0][$municipality->id.'_training_id_1'];
                    $child_total+=$child;
                }
                else{$child=0;}

                if (array_key_exists($municipality->id.'_inputTest',$reg_array[1])) //входной тест
                {
                    $inputTest = $reg_array[1][$municipality->id.'_inputTest']/$countOrgStart;
                    $inputTest_total+=$inputTest;
                    $inputTest_count++;
                } //входной тест
                else { $inputTest=0;}
                if (array_key_exists($municipality->id.'_theme1',$reg_array[1]))
                {
                    $theme1 = $reg_array[1][$municipality->id.'_theme1'];
                    $theme1_total+=$theme1;
                }
                else { $theme1=0; }
                if (array_key_exists($municipality->id.'_theme2',$reg_array[1]))
                {
                    $theme2 = $reg_array[1][$municipality->id.'_theme2'];
                    $theme2_total+=$theme2;
                }
                else { $theme2=0; }
                if (array_key_exists($municipality->id.'_theme3',$reg_array[1]))
                {
                    $theme3 = $reg_array[1][$municipality->id.'_theme3'];
                    $theme3_total+=$theme3;
                }
                else { $theme3=0; }
                if (array_key_exists($municipality->id.'_theme4',$reg_array[1]))
                {
                    $theme4 = $reg_array[1][$municipality->id.'_theme4'];
                    $theme4_total+=$theme4;
                }
                else { $theme4=0; }
                if (array_key_exists($municipality->id.'_theme5',$reg_array[1]))
                {
                    $theme5 = $reg_array[1][$municipality->id.'_theme5'];
                    $theme5_total+=$theme5;
                }
                else { $theme5=0; }
                if (array_key_exists($municipality->id.'_theme6',$reg_array[1]))
                {
                    $theme6 = $reg_array[1][$municipality->id.'_theme6'];
                    $theme6_total+=$theme6;
                }
                else { $theme6=0; }
                if (array_key_exists($municipality->id.'_independentWork',$reg_array[1]))
                {
                    $independentWork = $reg_array[1][$municipality->id.'_independentWork'];
                    $independentWork_total+=$independentWork;
                }
                else { $independentWork=0; }

                if (array_key_exists($municipality->id.'_finalTest',$reg_array[1])) //итоговый тест
                {
                    $finalTest = $reg_array[1][$municipality->id.'_finalTest']/$reg_array[1][$municipality->id.'_countOrgFinal'];
                    $finalTest_total+=$finalTest;
                    $finalTest_count++;
                } //итоговый тест
                else { $finalTest=0; }
                if (array_key_exists($municipality->id.'_finalTest_1st',$reg_array[1]))
                {
                    $finalTest_1st = $reg_array[1][$municipality->id.'_finalTest_1st'];
                    $finalTest_1st_total+=$finalTest_1st;
                }
                else { $finalTest_1st=0; }
                if (array_key_exists($municipality->id.'_finalTest_2st',$reg_array[1]))
                {
                    $finalTest_2st = $reg_array[1][$municipality->id.'_finalTest_2st'];
                    $finalTest_2st_total+=$finalTest_2st;
                }
                else { $finalTest_2st=0; }
                if (array_key_exists($municipality->id.'_trainingCompletedAll',$reg_array[1]))
                {
                    $trainingCompletedAll = $reg_array[1][$municipality->id.'_trainingCompletedAll'];
                    $trainingCompletedAll_total+=$trainingCompletedAll;
                }
                else { $trainingCompletedAll=0; }
                if (array_key_exists($municipality->id.'_trainingCompletedParent',$reg_array[1]))
                {
                    $trainingCompletedParent = $reg_array[1][$municipality->id.'_trainingCompletedParent'];
                    $trainingCompletedParent_total+=$trainingCompletedParent;
                }
                else { $trainingCompletedParent=0; }
                if (array_key_exists($municipality->id.'_trainingCompletedChild',$reg_array[1]))
                {
                    $trainingCompletedChild = $reg_array[1][$municipality->id.'_trainingCompletedChild'];
                    $trainingCompletedChild_total+=$trainingCompletedChild;
                }
                else { $trainingCompletedChild=0; }

                if (array_key_exists($municipality->id.'_numberChildren',$reg_array[1]))
                {
                    $childrenTrained = $reg_array[1][$municipality->id.'_numberChildren'];
                    $childrenTrained_total+=$childrenTrained;
                }
                else { $childrenTrained=0; }
                /*(END)*/
                ?>

                <tr class="text-center <? if ($count == 1) {echo 'prepend-reg';}?>">
                    <td><?= $count++ ?></td>
                    <td><?= $municipality->name ?></td>
                    <td><?= $startWork ?></td>
                    <td><?= $countOrgStart ?></td>
                    <td><?= $people ?></td>
                    <td><?= $parent ?></td>
                    <td><?= $child ?></td>
                    <td><?= round($inputTest,1) ?></td>
                    <td><?= $theme1 ?></td>
                    <td><?= $theme2 ?></td>
                    <td><?= $theme3 ?></td>
                    <td><?= $theme4 ?></td>
                    <td><?= $theme5 ?></td>
                    <td><?= $theme6 ?></td>
                    <td><?= $independentWork ?></td>
                    <td><?= round($finalTest,1) ?></td>
                    <td><?= $finalTest_1st ?></td>
                    <td><?= $finalTest_2st ?></td>
                    <td><?= $trainingCompletedAll ?></td>
                    <td><?= $trainingCompletedParent ?></td>
                    <td><?= $trainingCompletedChild ?></td>
                    <?if ($post['title'] >= 2021){?>
                        <td><?= $childrenTrained ?></td>
                    <?}?>
                </tr>
                <?
            }?>
            <tr class="text-center font-weight-bold bg-warning">
                <td colspan="2">Итого</td>
                <td><?= $startWork_total ?></td>
                <td><?= $countOrgStart_total ?></td>
                <td><?= $people_total ?></td>
                <td><?= $parent_total ?></td>
                <td><?= $child_total ?></td>
                <td><?= ($inputTest_count == 0) ? 0 : round($inputTest_total/$inputTest_count,1) ?></td>
                <td><?= $theme1_total ?></td>
                <td><?= $theme2_total ?></td>
                <td><?= $theme3_total ?></td>
                <td><?= $theme4_total ?></td>
                <td><?= $theme5_total ?></td>
                <td><?= $theme6_total ?></td>
                <td><?= $independentWork_total ?></td>
                <td><?= ($finalTest_count == 0) ? 0 : round($finalTest_total/$finalTest_count,1) ?></td>
                <td><?= $finalTest_1st_total ?></td>
                <td><?= $finalTest_2st_total ?></td>
                <td><?= $trainingCompletedAll_total ?></td>
                <td><?= $trainingCompletedParent_total ?></td>
                <td><?= $trainingCompletedChild_total ?></td>
                <?
                if ($post['title'] >= 2021){?>
                    <td><?= $childrenTrained_total ?></td>
                <?}
                ?>
            </tr>
            </tbody>
        </table>
        <?
    }
} //по региону ?>

    <script type="text/javascript">
        var year = '<?php echo $post['title'];?>';
        var org = '<?php echo $org;?>';
        var mun_org = '<?php echo $mun_org;?>';
        var reg_org = '<?php echo $reg_org;?>';

        if (org === '1'){
            col1 = '<?= ($count < 2) ? '0' : round($input_test_total * 10 / ($count - 1), 1) . '%';?>';
            col2 = '<?= $theme1_total;?>';
            col3 = '<?= $theme2_total;?>';
            col4 = '<?= $theme3_total;?>';
            col5 = '<?= $theme4_total;?>';
            col6 = '<?= $theme5_total;?>';
            col7 = '<?= $theme6_total;?>';
            col8 = '<?= $independent_work_total;?>';
            col9 = '<?= ($final_test_count_total == 0) ? '0' : round($final_test_total * 10 / $final_test_count_total, 1) . '%';?>';
            col10 = '<?= $final_test_1st_total;?>';
            col11 = '<?= $final_test_2st_total;?>';
            col12 = '<?= $training_completed_total;?>';
            if (year ==='2021'||year ==='2022'){
                col13 = '<?= $child_total;?>';
            }
        }
        else if (mun_org === '1'){
            col1 = '<?= $start_work_total ?>';
            col2 = '<?= $people_total ?>';
            col3 = '<?= $parent_total ?>';
            col4 = '<?= $child_total ?>';
            col5 = '<?= ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?>';
            col6 = '<?= $theme1_total ?>';
            col7 = '<?= $theme2_total ?>';
            col8 = '<?= $theme3_total ?>';
            col9 = '<?= $theme4_total ?>';
            col10 = '<?= $theme5_total ?>';
            col11 = '<?= $theme6_total ?>';
            col12 = '<?= $independentWork_total ?>';
            col13 = '<?= ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?>';
            col14 = '<?= $finalTest_1st_total ?>';
            col15 = '<?= $finalTest_2st_total ?>';
            col16 = '<?= $trainingCompletedAll_total ?>';
            col17 = '<?= $trainingCompletedParent_total ?>';
            col18 = '<?= $trainingCompletedChild_total ?>';
            if (year ==='2021'||year ==='2022'){
                col19 = '<?= $childrenTrained_total ?>';
            }
        }
        else if (reg_org === '1'){
            col1 = '<?= $startWork_total ?>';
            col2 = '<?= $countOrgStart_total ?>';
            col3 = '<?= $people_total ?>';
            col4 = '<?= $parent_total ?>';
            col5 = '<?= $child_total ?>';
            col6 = '<?= ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?>';
            col7 = '<?= $theme1_total ?>';
            col8 = '<?= $theme2_total ?>';
            col9 = '<?= $theme3_total ?>';
            col10 = '<?= $theme4_total ?>';
            col11 = '<?= $theme5_total ?>';
            col12 = '<?= $theme6_total ?>';
            col13 = '<?= $independentWork_total ?>';
            col14 = '<?= ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?>';
            col15 = '<?= $finalTest_1st_total ?>';
            col16 = '<?= $finalTest_2st_total ?>';
            col17 = '<?= $trainingCompletedAll_total ?>';
            col18 = '<?= $trainingCompletedParent_total ?>';
            col19 = '<?= $trainingCompletedChild_total ?>';
            if (year ==='2021'||year ==='2022'){
                col20 = '<?= $childrenTrained_total ?>';
            }
        }
    </script>

<?
$script = <<< JS
    $(".beforeload").click(function() {
      $(".beforeload").css('display','none');
      $(".load").css('display','block');
    });

    if (org==='1'){
        $('.prepend-org').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="4">Итого</td>' +
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
            '<td>'+col12+'</td>' +
            '<td>'+col13+'</td>' +
            '</tr>'
        );
    }
    else if (mun_org === '1'){
        $('.prepend-mun').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="2">Итого</td>' +
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
            '<td>'+col12+'</td>' +
            '<td>'+col13+'</td>' +
            '<td>'+col14+'</td>' +
            '<td>'+col15+'</td>' +
            '<td>'+col16+'</td>' +
            '<td>'+col17+'</td>' +
            '<td>'+col18+'</td>' +
            '<td>'+col19+'</td>' +
            '</tr>'
        );
    }
    else if (reg_org === '1'){
        $('.prepend-reg').before(
            '<tr class="text-center font-weight-bold bg-warning">' +
            '<td colspan="2">Итого</td>' +
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
            '<td>'+col12+'</td>' +
            '<td>'+col13+'</td>' +
            '<td>'+col14+'</td>' +
            '<td>'+col15+'</td>' +
            '<td>'+col16+'</td>' +
            '<td>'+col17+'</td>' +
            '<td>'+col18+'</td>' +
            '<td>'+col19+'</td>' +
            '<td>'+col20+'</td>' +
            '</tr>'
        );
    }
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>