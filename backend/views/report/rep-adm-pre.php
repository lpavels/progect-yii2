<?php

use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\User;
use common\models\UserEdu20;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<table id="tableId" class="table table-hover table-bordered table-striped mt-3 table2excel_with_colors">
    <thead>
    <tr class="text-center">
        <th rowspan="2">№</th>
        <th rowspan="2">Субъект федерации</th>

        <th style="max-width: 122px;">1 тема</th>
        <th style="max-width: 122px;">2 тема</th>
        <th style="max-width: 122px;">3 тема</th>
        <th style="max-width: 122px;">4 тема</th>
        <th style="max-width: 122px;">5 тема</th>
        <th style="max-width: 122px;">6 тема</th>
        <th style="max-width: 122px;">7 тема</th>
        <th style="max-width: 122px;">8 тема</th>
        <th style="max-width: 122px;">9 тема</th>
        <th style="max-width: 122px;">10 тема</th>
        <th style="max-width: 122px;">11 тема</th>
        <th style="max-width: 122px;">12 тема</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $count = 1;

    foreach ($regions as $region) {
        /**/
        //if (array_key_exists($region->id . '_training_id_1',$fed_array[0]))
        //{
        //    $people = $fed_array[0][$region->id . '_training_id_1'] + $fed_array[0][$region->id . '_training_id_2'];
        //    $people_total += $people;
        //} else {
        //    $people = 0;
        //}

        /*(END)*/
        ?>

        <tr class="text-center">
            <td><?= $count++ ?></td>
            <td><?= $region->name ?></td>
            <td><?= array_key_exists($region->id . '_program2',$data_fin) ? $data_fin[$region->id . '_program2'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program3',$data_fin) ? $data_fin[$region->id . '_program3'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program4',$data_fin) ? $data_fin[$region->id . '_program4'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program5',$data_fin) ? $data_fin[$region->id . '_program5'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program6',$data_fin) ? $data_fin[$region->id . '_program6'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program7',$data_fin) ? $data_fin[$region->id . '_program7'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program8',$data_fin) ? $data_fin[$region->id . '_program8'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program9',$data_fin) ? $data_fin[$region->id . '_program9'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program10',$data_fin) ? $data_fin[$region->id . '_program10'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program11',$data_fin) ? $data_fin[$region->id . '_program11'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program12',$data_fin) ? $data_fin[$region->id . '_program12'] : '-' ?></td>
            <td><?= array_key_exists($region->id . '_program13',$data_fin) ? $data_fin[$region->id . '_program13'] : '-' ?></td>

        </tr>
        <?php
    }
    ?>
    <tr class="text-center font-weight-bold bg-warning">
        <td colspan="2">Итого</td>
        <td><?
            //= $startWork_total ?></td>
        <td><?
            //= $countMunStart_total ?></td>
        <td><?
            //= $countOrgStart_total ?></td>
        <td><?
            //= $people_total ?></td>
        <td><?
            //= $people_total ?></td>
        <td><?
            //= $parent_total ?></td>
        <td><?
            //= $child_total ?></td>
        <td><?
            //= ($inputTest_count == 0) ? 0 : round($inputTest_total / $inputTest_count, 1) ?></td>
        <td><?
            //= $theme1_total ?></td>
        <td><?
            //= $theme2_total ?></td>
        <td><?
            //= $theme3_total ?></td>
        <td><?
            //= $theme4_total ?></td>
        <td><?
            //= $theme5_total ?></td>
        <td><?
            //= $theme6_total ?></td>
        <td><?
            //= $independentWork_total ?></td>
        <td><?
            //= ($finalTest_count == 0) ? 0 : round($finalTest_total / $finalTest_count, 1) ?></td>
        <td><?
            //= $finalTest_1st_total ?></td>
        <td><?
            //= $finalTest_2st_total ?></td>
        <td><?
            //= $trainingNotCompl_total ?></td>
        <td><?
            //= $trainingCompletedAll_total ?></td>
        <td><?
            //= $trainingCompletedParent_total ?></td>
        <td><?
            //= $trainingCompletedChild_total ?></td>
        <td><?
            //= $childrenTrained_total ?></td>
    </tr>
    </tbody>
</table>
<input type="button" class="btn btn-warning btn-block table2excel mb-3 mt-3"
       title="Вы можете скачать в формате Excel" value="Скачать в Excel" id="pechat222">

