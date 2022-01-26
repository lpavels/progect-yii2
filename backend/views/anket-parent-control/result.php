<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AnketParentControl */

$this->title = 'Результат контроля';
$this->params['breadcrumbs'][] = ['label' => 'Anket Parent Controls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<style>
    th, td {
        border: 1px solid black!important;
        color: black;

    }
    th {
        /*background-color: #ede8b9;*/
        font-size: 13px;
    }
</style>

<div class="anket-parent-control-view">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>


    <table class="table_th0 table-hover" style="width: 100%; font-size: 14px;">
        <thead>
        <tr class="text-center"><? echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $nutrition->name .'</p>'?></tr>
        <tr>

            <th class="text-center align-middle" rowspan="2" style="width: 40px">Дата</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">День по циклу</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">Перемена</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">Количество питающихся детей</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">Процент несъеденной пищи</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">Количество баллов по тесту</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">Количество баллов по пищеблоку</th>
            <th class="text-center align-middle" rowspan="2" style="width: 70px">Количество баллов за весь контроль</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 1</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 2</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 3</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 4</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 5</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 6</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 7</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 8</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 9</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 10</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 11</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 12</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 13</th>
            <th class="text-center align-middle" colspan="2" style="width: 70px">Вопрос 14</th>
        </tr>
        <tr>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>

            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
            <th class="text-center align-middle" style="width: 70px">Ответ</th>
            <th class="text-center align-middle" style="width: 70px">Баллы</th>
        </tr>

        </thead>
        <tbody>
        <? $count = 0; $sred = []; ?>
        <tr>
            <td class="text-center align-middle"><?= date('d.m.Y',$model->date) ?></td>
            <td class="text-center align-middle"><? $date = date('w',$model->date); if($date == 0) echo 7; else echo $date; ?></td>
            <td class="text-center align-middle"><?=$model->peremena ?></td>
            <td class="text-center align-middle"><?= $model->count; $sred['count'] = $sred['count'] + $model->count;?></td>
            <td class="text-center align-middle"><? $procent = $model->get_result_food($model->id, 'procent'); echo $procent; $sred['procent'] = $sred['procent'] + $procent;?></td>
            <td class="text-center align-middle"><? $test = $model->get_result_test($model->id); echo $test; $sred['test'] = $sred['test'] + $test;?></td>
            <td class="text-center align-middle"><? $test_food = $model->get_result_food($model->id, 'ball'); echo $test_food; $sred['test_food'] = $sred['test_food'] + $test_food;?></td>
            <td class="text-center align-middle"><? echo $test + $test_food;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(1, $model->question1, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(1, $model->question1, 'ball'); $ball['1'] = $ball['1'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(2, $model->question2, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(2, $model->question2, 'ball'); $ball['2'] = $ball['2'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(3, $model->question3, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(3, $model->question3, 'ball'); $ball['3'] = $ball['3'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(4, $model->question4, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(4, $model->question4, 'ball'); $ball['4'] = $ball['4'] + $b; echo $b;?></td>

            <td class="text-center align-middle"><?= $model->yes_no(5, $model->question5, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(5, $model->question5, 'ball'); $ball['5'] = $ball['5'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(6, $model->question6, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(6, $model->question6, 'ball');$ball['6'] = $ball['6'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(7, $model->question7, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(7, $model->question7, 'ball');$ball['7'] = $ball['7'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(8, $model->question8, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(8, $model->question8, 'ball');$ball['8'] = $ball['8'] + $b;echo $b;?></td>

            <td class="text-center align-middle"><?= $model->yes_no(9, $model->question9, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(9, $model->question9, 'ball'); $ball['9'] = $ball['9'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(10, $model->question10, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(10, $model->question10, 'ball');$ball['10'] = $ball['10'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(11, $model->question11, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(11, $model->question11, 'ball');$ball['11'] = $ball['11'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(12, $model->question12, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(12, $model->question12, 'ball');$ball['12'] = $ball['12'] + $b; echo $b;?></td>

            <td class="text-center align-middle"><?= $model->yes_no(13, $model->question13, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(13, $model->question13, 'ball');$ball['13'] = $ball['13'] + $b; echo $b;?></td>
            <td class="text-center align-middle"><?= $model->yes_no(14, $model->question14, 'answer');?></td>
            <td class="text-center align-middle"><? $b = $model->yes_no(14, $model->question14, 'ball');$ball['14'] = $ball['14'] + $b; echo $b;?></td>
        </tr>



        </tbody>
    </table><br><br><br>


    <p class="text-center">
        <?= Html::a('Выход', ['site/index'], ['class' => 'btn main-button-3']) ?>
    </p>
</div>
