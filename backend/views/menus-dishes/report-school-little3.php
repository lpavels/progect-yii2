<?php

use common\models\DepartmentEducation;
use common\models\Municipality;
use common\models\Organization;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use common\models\MenusDays;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Результаты проведения мероприятий контроля';
$this->params['breadcrumbs'][] = $this->title;

if (Yii::$app->user->can('RPN_mun'))
{
    $depEdu = DepartmentEducation::findOne(['key_login_departament' => Yii::$app->user->identity->key_login]);
    $municipalities = Municipality::find()->where(['id' => $depEdu['municipality_id']])->all();
}
elseif (Yii::$app->user->can('RPN'))
{
    $depEdu = DepartmentEducation::findOne(['key_login_rpn' => Yii::$app->user->identity->key_login]);
    $municipalities = Municipality::find()->where(['region_id' => $depEdu['region_id']])->all();
}
$municipality_items = ArrayHelper::map($municipalities, 'id', 'name');

if (!empty($post))
{
    $params_mun = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];
    $params_chemistry = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];

    $count_my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->count();
    if ($post['cycle'] == 0)
    {
        $count_my_days = $count_my_days * $menu_cycle_count;
    }

    $organizations = Organization::find()->where(['municipality_id' => $post['menu_id'], 'type_org' => [1, 2, 3, 4,5,6,7,8,9]])->orderBy(['short_title' => SORT_ASC, 'title' => SORT_ASC])->all();
    $mun = Municipality::findOne($post['menu_id'])->name;
    //$region_id = \common\models\Municipality::findOne($post['menu_id'])->region_id;
    //$region = \common\models\Region::findOne($region_id)->name;
}

?>
<style>
    th, td {
        border: 1px solid black !important;
        color: black;
        font-size: 10px;

    }

    th {
        background-color: #ede8b9;
        font-size: 15px;
    }

    thead, th {
        background-color: #ede8b9;
        font-size: 10px;
    }
</style>

<?php $form = ActiveForm::begin([]); ?>
<div class="container mb-30">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-11 col-md-12">
            <?= $form->field($model, 'menu_id')->dropDownList($municipality_items, [
                'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]])->label('Муниципальный округ'); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group" style="margin: 0 auto">
        <?= Html::submitButton('Посмотреть', ['class' => 'btn main-button-3 beforeload']) ?>
        <button class="btn main-button-3 load" type="button" disabled style="display: none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Посмотреть...
        </button>
    </div>
</div>

<?php ActiveForm::end(); ?>

<? if (!empty($post)) { ?>
    <table class="table table-bordered table-sm" style="width:70%">
        <thead>
        <tr>
            <td rowspan="3">№</td>
            <td rowspan="3">Муниципальное образование</td>
            <td rowspan="3">Наименование общеобразовательной организации</td>
            <td class="text-center align-middle" colspan="7">ЗАВТРАКИ</td>
            <td class="text-center align-middle" colspan="7">ОБЕДЫ</td>
        </tr>
        <tr>
            <td rowspan="2">Количество мероприятий внутреннего контроля</td>
            <td colspan="3">Количество баллов</td>
            <td colspan="3">Процент несъеденной пищи</td>

            <td rowspan="2">Количество мероприятий внутреннего контроля</td>
            <td colspan="3">Количество баллов</td>
            <td colspan="3">Процент несъеденной пищи</td>
        </tr>
        <tr>
            <td>мин</td>
            <td>ср</td>
            <td>макс</td>
            <td>мин</td>
            <td>ср</td>
            <td>макс</td>

            <td>мин</td>
            <td>ср</td>
            <td>макс</td>
            <td>мин</td>
            <td>ср</td>
            <td>макс</td>
        </tr>

        </thead>
        <tbody>
        <? $itog_string = [];
        $count = 0;
        $sred = [];
        foreach ($organizations as $organization) {
            $count++; ?>
            <tr>
                <td class="text-center align-middle"><?= $count ?></td>
                <td class="text-center align-middle"><?= $mun ?></td>
                <td class="align-middle"><? echo (empty($organization->short_title)) ? $organization->title : $organization->short_title; ?></td>
                <?
                if (!empty($control_zavtrak))
                {
                    unset($control_zavtrak);
                }
                if (!empty($control_obed))
                {
                    unset($control_obed);
                }
                $control_zavtrak = $model->get_control_information($organization->id, 1);
                $control_obed = $model->get_control_information($organization->id, 3);
                ?>
                <? if ($control_zavtrak == 'null' || empty($control_zavtrak) || $control_zavtrak['vnutr'] == 0) { ?>
                    <td colspan="7" class="text-center align-middle text-danger">Контроль не проводился</td>
                <? }
                else
                {
                    $itog_string['zavtrak_control_count']++; ?>
                    <td class="text-center align-middle"><?= $control_zavtrak['vnutr'];
                        $itog_string['zavtrak_vnutr'] = $itog_string['zavtrak_vnutr'] + $control_zavtrak['vnutr']; ?></td>
                    <td class="text-center align-middle"><?= round($control_zavtrak['min_ball'], 1);
                        if (empty($itog_string['zavtrak_min_ball'])) {
                            $itog_string['zavtrak_min_ball'] = round($control_zavtrak['min_ball'], 1);
                        } elseif ($itog_string['zavtrak_min_ball'] > round($control_zavtrak['min_ball'], 1)) {
                            $itog_string['zavtrak_min_ball'] = round($control_zavtrak['min_ball'], 1);
                        } ?></td>
                    <td class="text-center align-middle"><?= round($control_zavtrak['sred_ball'], 1);
                        $itog_string['zavtrak_sred_ball'] = $itog_string['zavtrak_sred_ball'] + round($control_zavtrak['sred_ball'], 1); ?></td>
                    <td class="text-center align-middle"><?= round($control_zavtrak['max_ball'], 1);
                        if (empty($itog_string['zavtrak_max_ball'])) {
                            $itog_string['zavtrak_max_ball'] = round($control_zavtrak['max_ball'], 1);
                        } elseif ($itog_string['zavtrak_max_ball'] < round($control_zavtrak['max_ball'], 1)) {
                            $itog_string['zavtrak_max_ball'] = round($control_zavtrak['max_ball'], 1);
                        } ?></td>
                    <td class="text-center align-middle"><?= round($control_zavtrak['min_procent'], 1);
                        if (empty($itog_string['zavtrak_min_procent'])) {
                            $itog_string['zavtrak_min_procent'] = round($control_zavtrak['min_procent'], 1);
                        } elseif ($itog_string['zavtrak_min_procent'] > round($control_zavtrak['min_procent'], 1)) {
                            $itog_string['zavtrak_min_procent'] = round($control_zavtrak['min_procent'], 1);
                        } ?></td>
                    <td class="text-center align-middle <? if (round($control_zavtrak['sred_procent'], 1) >= 30) {
                        echo "bg-danger";
                    } ?>"><?= round($control_zavtrak['sred_procent'], 1);
                        $itog_string['zavtrak_sred_procent'] = $itog_string['zavtrak_sred_procent'] + round($control_zavtrak['sred_procent'], 1); ?></td>
                    <td class="text-center align-middle <? if (round($control_zavtrak['max_procent'], 1) >= 30) {
                        echo "bg-danger";
                    } ?>"><?= round($control_zavtrak['max_procent'], 1);
                        if (empty($itog_string['zavtrak_max_procent'])) {
                            $itog_string['zavtrak_max_procent'] = round($control_zavtrak['max_procent'], 1);
                        } elseif ($itog_string['zavtrak_max_procent'] < round($control_zavtrak['max_procent'], 1)) {
                            $itog_string['zavtrak_max_procent'] = round($control_zavtrak['max_procent'], 1);
                        } ?></td>
                <? } ?>

                <? if ($control_obed == 'null' || empty($control_obed) || $control_obed['vnutr'] == 0) { ?>
                    <td colspan="7" class="text-center align-middle text-danger">Контроль не проводился</td>
                <? }
                else
                {
                    $itog_string['obed_control_count']++; ?>
                    <td class="text-center align-middle"><?= $control_obed['vnutr'];
                        $itog_string['obed_vnutr'] = $itog_string['obed_vnutr'] + $control_obed['vnutr']; ?></td>
                    <td class="text-center align-middle"><?= round($control_obed['min_ball'], 1);
                        if (empty($itog_string['obed_min_ball'])) {
                            $itog_string['obed_min_ball'] = round($control_obed['min_ball'], 1);
                        } elseif ($itog_string['obed_min_ball'] > round($control_obed['min_ball'], 1)) {
                            $itog_string['obed_min_ball'] = round($control_obed['min_ball'], 1);
                        } ?></td>
                    <td class="text-center align-middle"><?= round($control_obed['sred_ball'], 1);
                        $itog_string['obed_sred_ball'] = $itog_string['obed_sred_ball'] + round($control_obed['sred_ball'], 1); ?></td>
                    <td class="text-center align-middle"><?= round($control_obed['max_ball'], 1);
                        if (empty($itog_string['obed_max_ball'])) {
                            $itog_string['obed_max_ball'] = round($control_obed['max_ball'], 1);
                        } elseif ($itog_string['obed_max_ball'] < round($control_obed['min_ball'], 1)) {
                            $itog_string['obed_max_ball'] = round($control_obed['max_ball'], 1);
                        } ?></td>
                    <td class="text-center align-middle"><?= round($control_obed['min_procent'], 1);
                        if (empty($itog_string['obed_min_procent'])) {
                            $itog_string['obed_min_procent'] = round($control_obed['min_procent'], 1);
                        } elseif ($itog_string['obed_min_procent'] > round($control_obed['min_procent'], 1)) {
                            $itog_string['obed_min_procent'] = round($control_obed['min_procent'], 1);
                        } ?></td>
                    <td class="text-center align-middle <? if (round($control_obed['sred_procent'], 1) >= 30) {
                        echo "bg-danger";
                    } ?>"><?= round($control_obed['sred_procent'], 1);
                        $itog_string['obed_sred_procent'] = $itog_string['obed_sred_procent'] + round($control_obed['sred_procent'], 1); ?></td>
                    <td class="text-center align-middle <? if (round($control_obed['max_procent'], 1) >= 30) {
                        echo "bg-danger";
                    } ?>"><?= round($control_obed['max_procent'], 1);
                        if (empty($itog_string['obed_max_procent'])) {
                            $itog_string['obed_max_procent'] = round($control_obed['max_procent'], 1);
                        } elseif ($itog_string['obed_max_procent'] < round($control_obed['max_procent'], 1)) {
                            $itog_string['obed_max_procent'] = round($control_obed['max_procent'], 1);
                        } ?></td>
                <? } ?>
            </tr>
        <? } ?>

        <tr class="table-danger">
            <td class="" colspan="3">Итого <?= $mun; ?>:</td>
            <td class="text-center align-middle"><?= $itog_string['zavtrak_vnutr'] ?></td>
            <td class="text-center align-middle"><?= $itog_string['zavtrak_min_ball'] ?></td>
            <td class="text-center align-middle"><? if (!empty($itog_string['zavtrak_control_count'])) {
                    echo round($itog_string['zavtrak_sred_ball'] / $itog_string['zavtrak_control_count'], 1);
                } ?></td>
            <td class="text-center align-middle"><?= $itog_string['zavtrak_max_ball'] ?></td>
            <td class="text-center align-middle"><?= $itog_string['zavtrak_min_procent'] ?></td>
            <td class="text-center align-middle"><? if (!empty($itog_string['zavtrak_control_count'])) {
                    echo round($itog_string['zavtrak_sred_procent'] / $itog_string['zavtrak_control_count'], 1);
                } ?></td>
            <td class="text-center align-middle"><?= $itog_string['zavtrak_max_procent'] ?></td>
            <td class="text-center align-middle"><?= $itog_string['obed_vnutr'] ?></td>
            <td class="text-center align-middle"><?= $itog_string['obed_min_ball'] ?></td>
            <td class="text-center align-middle"><? if (!empty($itog_string['obed_control_count'])) {
                    echo round($itog_string['obed_sred_ball'] / $itog_string['obed_control_count'], 1);
                } ?></td>
            <td class="text-center align-middle"><?= $itog_string['obed_max_ball'] ?></td>
            <td class="text-center align-middle"><?= $itog_string['obed_min_procent'] ?></td>
            <td class="text-center align-middle"><? if (!empty($itog_string['obed_control_count'])) {
                    echo round($itog_string['obed_sred_procent'] / $itog_string['obed_control_count'], 1);
                } ?></td>
            <td class="text-center align-middle"><?= $itog_string['obed_max_procent'] ?></td>
        </tr>
        </tbody>
    </table><br><br><br>
<? } ?>

<?
$script = <<< JS

$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
  
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
