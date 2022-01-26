<?php

use common\models\CharactersStolovaya;
use common\models\Organization;
use common\models\SchoolBreak;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Button;
use common\models\Menus;
use common\models\Days;
use common\models\MenusDays;
use common\models\MenusDishes;
use common\models\MenusNutrition;
use common\models\NutritionInfo;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет по школам';
$this->params['breadcrumbs'][] = $this->title;


$organization_id = Yii::$app->user->identity->organization_id;
$region_id = Organization::findOne($organization_id)->region_id;
$my_mun = Organization::findOne($organization_id)->municipality_id;
//$municipalities = \common\models\Municipality::find()->where(['region_id' => $region_id])->all();
$municipalities = \common\models\Municipality::find()->where(['id' => $my_mun])->all();
//$municipality_null = array(0 => 'Все муниципальные округа ...');
$municipality_items = ArrayHelper::map($municipalities, 'id', 'name');
//$municipality_items = ArrayHelper::merge($municipality_null, $municipality_items);


if(!empty($post)){
    $params_mun = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];
    $params_chemistry = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];

    $count_my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->count();
    if($post['cycle'] == 0){
        $count_my_days = $count_my_days * $menu_cycle_count;
    }

    $organizations = Organization::find()->where(['municipality_id' => $post['menu_id'], 'type_org' => 3])->orderBy(['short_title'=> SORT_ASC,'title'=> SORT_ASC])->all();
    $mun = \common\models\Municipality::findOne($post['menu_id'])->name;
    //$region_id = \common\models\Municipality::findOne($post['menu_id'])->region_id;
    //$region = \common\models\Region::findOne($region_id)->name;
}

?>
<style>
    th, td {
        border: 1px solid black!important;
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
                'class' => 'form-control', 'options' => [$my_mun => ['Selected' => true]],


            ])->label('Муниципальный округ'); ?>
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




<?if(!empty($post)){?>
    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <td rowspan="3">№</td>
            <td rowspan="3">Муниципальное образование</td>
            <td rowspan="3">Наименование общеобразовательной организации</td>
            <td rowspan="3">Количество школ внесших информацию</td>
            <td rowspan="1" colspan="2">в т.ч.</td>
            <td rowspan="2" colspan="3">Количество школьников 1-4 кл. обучающихся очно</td>
            <td rowspan="2" colspan="2">Продолжительность перемен для питания детей (в мин.)</td>
            <td rowspan="3" >Количество школ в которых занижена продолжительность перемен (менее 20 минут)</td>
            <td rowspan="2" colspan="6">Количество ШКОЛ имеющих школьников 1-4 кл. с заболеваниями требующими индивидуального подхода в организации питания</td>
            <td rowspan="2" colspan="6">Количество ШКОЛЬНИКОВ 1-4 кл. имеющих, заболевания требующие индивидуального подхода в организации питания</td>
            <td rowspan="1" colspan="4">Количество</td>
            <td rowspan="3">Количество оборудованных посадочных мест</td>
            <td rowspan="3">Площадь на 1 посадочное место</td>
            <td rowspan="3">Максимальное количество одномоментно питающихся детей</td>
            <td rowspan="3">фактическая площадь на одно посадочное место</td>
            <td rowspan="3">Имеется дефицит площади столовой</td>
            <td rowspan="3">Количество школ в которых количество питающихся превышает количество посадочных мест</td>
            <td rowspan="3">Количество функционирующих умывальников уст. перед входом в столовую</td>
            <td rowspan="3">Количество детей на один умывальник (д.б. не менее 1 на 20 дет)</td>
            <td rowspan="3">Имеется дефицит умывальников</td>
        </tr>
        <tr>
            <td rowspan="2">шк.1-4 кл. обуч только в первую смену</td>
            <td rowspan="2">шк.1-4 кл. обуч в две смены</td>
        </tr>
        <tr>
            <td>всего</td>
            <td>в I-ю смену</td>
            <td>во II-ю</td>
            <td>мин</td>
            <td>макс</td>

            <td>СД</td>
            <td>Цел</td>
            <td>Мук</td>
            <td>ФКУ</td>
            <td>овз</td>
            <td>ПА</td>

            <td>СД</td>
            <td>Цел</td>
            <td>Мук</td>
            <td>ФКУ</td>
            <td>овз</td>
            <td>ПА</td>



            <td>Всего</td>
            <td>%</td>
            <td>Всего</td>
            <td>%</td>
        </tr>

        </thead>
        <tbody>
        <? $itog_string = []; $count = 0; $sred = []; foreach ($organizations as $organization) { $count++;?>
            <? $character_stolovaya = \common\models\CharactersStolovaya::find()->where(['organization_id' => $organization->id])->one();?>
            <? $school_break_min = SchoolBreak::find()->where(['organization_id' => $organization->id])->min('duration');?>
            <? $school_break_max = SchoolBreak::find()->where(['organization_id' => $organization->id])->max('duration');?>
            <? $school_break_count = SchoolBreak::find()->where(['organization_id' => $organization->id])->count();if($school_break_count != 0){$school_break_sred = round(SchoolBreak::find()->where(['organization_id' => $organization->id])->sum('duration')/$school_break_count,1);}else{$school_break_sred = 0;}?>
            <? $information_education = \common\models\InformationEducation::find()->where(['organization_id' => $organization->id])->one();?>
            <? $cs_mas = []; $characters_studies = \common\models\CharactersStudy::find()->where(['organization_id' => $organization->id, 'class_number' => [1,2,3,4]])->all();
            $number = [];
            $vt_smena = $information_education->quantity14 - $information_education->quantity14_first;
            foreach ($characters_studies as $characters_study){
                $number[$characters_study->number_peremena] = $number[$characters_study->number_peremena] + $characters_study->count_ochno;

                $cs_mas['count_kid'] = $cs_mas['count_kid'] + $characters_study->count;

                $cs_mas['sahar'] = $cs_mas['sahar'] + $characters_study->sahar;
                $cs_mas['ovz'] = $cs_mas['ovz'] + $characters_study->ovz;
                $cs_mas['cialic'] = $cs_mas['cialic'] + $characters_study->cialic;
                $cs_mas['allergy'] = $cs_mas['allergy'] + $characters_study->allergy;
                $cs_mas['mukovis'] = $cs_mas['mukovis'] + $characters_study->mukovis;
                $cs_mas['fenilketon'] = $cs_mas['fenilketon'] + $characters_study->fenilketon;

                if($characters_study->types_pit == 1){
                    $cs_mas['zavtrak'] = $cs_mas['zavtrak'] + $characters_study->count_ochno;
                    //$cs_mas['ohvat_zavtrak'] = $cs_mas['ohvat_zavtrak'] + $characters_study->sahar + $characters_study->cialic + $characters_study->allergy + $characters_study->fenilketon + $characters_study->mukovis + $characters_study->ovz;
                    $cs_mas['otkaz_zavtrak'] = $cs_mas['otkaz_zavtrak'] + $characters_study->otkaz_sahar + $characters_study->otkaz_cialic + $characters_study->otkaz_allergy + $characters_study->otkaz_inoe;

                }
                if($characters_study->types_pit == 2){
                    $cs_mas['obed'] = $cs_mas['obed'] + $characters_study->count_ochno;
                    //$cs_mas['ohvat_obed'] = $cs_mas['ohvat_obed'] + $characters_study->sahar + $characters_study->cialic + $characters_study->allergy + $characters_study->fenilketon + $characters_study->mukovis + $characters_study->ovz;
                    $cs_mas['otkaz_obed'] = $cs_mas['otkaz_obed'] + $characters_study->otkaz_sahar + $characters_study->otkaz_cialic + $characters_study->otkaz_allergy + $characters_study->otkaz_inoe;

                }

                $cs_mas['otkaz'] = $cs_mas['otkaz'] + $characters_study->otkaz_sahar + $characters_study->otkaz_cialic + $characters_study->otkaz_allergy + $characters_study->otkaz_inoe;
                $cs_mas['otkaz_allergy'] = $cs_mas['otkaz_allergy'] + $characters_study->otkaz_allergy;
                $cs_mas['otkaz_cialic'] = $cs_mas['otkaz_cialic'] + $characters_study->otkaz_cialic;
                $cs_mas['otkaz_sahar'] = $cs_mas['otkaz_sahar'] + $characters_study->otkaz_sahar;
            }
            if(empty($cs_mas['otkaz'])){
                $cs_mas['otkaz'] = 0;
            }
            if(!empty($number)){
                $pita = max($number);
            }else{
                $pita = 0;
            }


            $menu_mas = [];
            $menus = \common\models\Menus::find()->where(['organization_id' => $organization->id, 'age_info_id' => 6, 'status_archive' => 0])->all();
            foreach($menus as $menu){
                if(MenusDishes::find()->where(['menu_id' => $menu->id, 'nutrition_id' => 1])->count() > 0){
                    $menu_mas['zavtrak'][$menu->feeders_characters_id] = $menu_mas['zavtrak'][$menu->feeders_characters_id] + 1;
                    $menu_mas['zavtrak']['itog'] = $menu_mas['zavtrak']['itog'] + 1;
                }
                if(MenusDishes::find()->where(['menu_id' => $menu->id, 'nutrition_id' => 3])->count() > 0){
                    $menu_mas['obed'][$menu->feeders_characters_id] = $menu_mas['obded'][$menu->feeders_characters_id] + 1;
                    $menu_mas['obed']['itog'] = $menu_mas['obed']['itog'] + 1;
                }
            }
            $menu = \common\models\Menus::find()->where(['organization_id' => $organization->id, 'feeders_characters_id' => 3, 'age_info_id' => 6])->count();
            ?>

            <tr>
                <td class="text-center align-middle"><?=$count?></td>
                <td class="text-center align-middle"><?=$mun?></td>
                <td class="align-middle"><?echo (empty($organization->short_title)) ? $organization->title : $organization->short_title;?></td>
                <td class="align-middle"><?if (!empty($organization->short_title) && !empty($character_stolovaya) && !empty($characters_studies) && !empty($information_education)){  $itog_string['kolichestvo'] = $itog_string['kolichestvo'] + 1; echo 1;}else{  $itog_string['kolichestvo'] = $itog_string['kolichestvo'] + 0; echo 0;}?></td>
                <?if(empty($information_education->quantity14) || empty($information_education->quantity14_first)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><? if ($vt_smena == 0 && $information_education->quantity14_first > 0){  $itog_string['tch1'] = $itog_string['tch1'] + 1; echo 1;}else{  $itog_string['tch1'] = $itog_string['tch1'] + 0; echo 0;}?></td>
                    <td class="text-center align-middle"><? if ($vt_smena > 0 && $information_education->quantity14_first > 0){  $itog_string['tch2'] = $itog_string['tch2'] + 1; echo 1;}else{  $itog_string['tch2'] = $itog_string['tch2'] + 0; echo 0;}?></td>
                <?}?>
                <?if(empty($information_education->quantity14) || empty($information_education->quantity14_first)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?$itog_string['ochno_vsego'] = $itog_string['ochno_vsego'] + $information_education->quantity14; echo $information_education->quantity14;?></td>
                    <td class="text-center align-middle"><?$itog_string['ochno_pervaya'] = $itog_string['ochno_pervaya'] + $information_education->quantity14_first; echo $information_education->quantity14_first;?></td>
                    <td class="text-center align-middle"><?$itog_string['ochno_vtoraya'] = $itog_string['ochno_vtoraya'] + $vt_smena; echo $vt_smena;?></td>
                <?}?>

                <?if($school_break_count == 0){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?= $school_break_min; ?></td>
                    <td class="text-center align-middle"><?= $school_break_max; ?></td>
                <?}?>
                <?if(empty($school_break_min)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}elseif($school_break_min >= 20){?>
                    <td class="text-center align-middle">0</td>
                <?}else{?>
                    <td class="text-center align-middle"><?=1;$itog_string['peremena_niz'] = $itog_string['peremena_niz'] + 1;?></td>
                <?}?>
                <td class="text-center align-middle"><? if (empty($cs_mas['sahar'])){echo '-';}else{$itog_string['school_sahar'] = $itog_string['school_sahar'] + 1; echo 1;}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['cialic'])){echo '-';}else{$itog_string['school_cialic'] = $itog_string['school_cialic'] + 1; echo 1;}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['mukovis'])){echo '-';}else{$itog_string['school_mukovis'] = $itog_string['school_mukovis'] + 1; echo 1;}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['fenilketon'])){echo '-';}else{$itog_string['school_fenilketon'] = $itog_string['school_fenilketon'] + 1; echo 1;}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['ovz'])){echo '-';}else{$itog_string['school_ovz'] = $itog_string['school_ovz'] + 1; echo 1;}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['allergy'])){echo '-';}else{$itog_string['school_allergy'] = $itog_string['school_allergy'] + 1; echo 1;}?></td>

                <td class="text-center align-middle"><? if (empty($cs_mas['sahar'])){echo '-';}else{$itog_string['student_sahar'] = $itog_string['student_sahar'] + $cs_mas['sahar']; echo $cs_mas['sahar'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['cialic'])){echo '-';}else{$itog_string['student_cialic'] = $itog_string['student_cialic'] + $cs_mas['cialic']; echo $cs_mas['cialic'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['mukovis'])){echo '-';}else{$itog_string['student_mukovis'] = $itog_string['student_mukovis'] + $cs_mas['mukovis']; echo $cs_mas['mukovis'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['fenilketon'])){echo '-';}else{$itog_string['student_fenilketon'] = $itog_string['student_fenilketon'] + $cs_mas['fenilketon']; echo $cs_mas['fenilketon'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['ovz'])){echo '-';}else{$itog_string['student_ovz'] = $itog_string['student_ovz'] + $cs_mas['ovz']; echo $cs_mas['ovz'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['allergy'])){echo '-';}else{$itog_string['student_allergy'] = $itog_string['student_allergy'] + $cs_mas['allergy']; echo $cs_mas['allergy'];}?></td>
                <?if(empty($characters_studies)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?if ($cs_mas['otkaz'] > 0){ $itog_string['school_otkaz'] = $itog_string['school_otkaz'] + 1; echo 1;}else{ echo 0;$itog_string['school_otkaz_count'] = $itog_string['school_otkaz_count'] + 1;}?></td>
                    <td class="text-center align-middle"><?if ($cs_mas['otkaz'] > 0){ $itog_string['school_otkaz_procent'] = $itog_string['school_otkaz_procent'] + 100; echo 100;}else{ $itog_string['school_otkaz_procent'] = $itog_string['school_otkaz_procent'] + 0; echo 0;}?></td>
                    <td class="text-center align-middle"><?= $cs_mas['otkaz'];$itog_string['student_otkaz'] = $itog_string['student_otkaz'] + $cs_mas['otkaz'];?></td>
                    <td class="text-center align-middle"><?if (empty($information_education->quantity14)){ echo 'нд';}else{ echo round($cs_mas['otkaz']/$information_education->quantity14*100, 1);$itog_string['student_otkaz_procent'] = $itog_string['student_otkaz_procent'] + round($cs_mas['otkaz']/$information_education->quantity14*100, 1);}?></td>
                <?}?>



                <?if(empty($character_stolovaya->spot)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?=$character_stolovaya->spot; $itog_string['kolichestvo_posad'] = $itog_string['kolichestvo_posad'] + $character_stolovaya->spot;?></td>
                    <td class="text-center align-middle"><?=round($character_stolovaya->square/$character_stolovaya->spot, 1);$itog_string['ploshad_posad'] = $itog_string['ploshad_posad'] + round($character_stolovaya->square/$character_stolovaya->spot, 1);$itog_string['ploshad_posad_count'] = $itog_string['ploshad_posad_count'] + 1;?></td>
                <?}?>
                <?if($pita == 0){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?=$pita;?></td>
                <?}?>
                <?if($pita == 0 || empty($character_stolovaya->spot)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?$fact = round(round($character_stolovaya->square/$character_stolovaya->spot, 1) * $character_stolovaya->spot / $pita,2); echo $fact; $itog_string['fact_ploshad'] = $itog_string['fact_ploshad'] + $fact;$itog_string['fact_ploshad_count'] = $itog_string['fact_ploshad_count'] + 1;?></td>
                <?}?>

                <?if($pita == 0 || empty($character_stolovaya->spot)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}elseif(round(round($character_stolovaya->square/$character_stolovaya->spot, 1) * $character_stolovaya->spot / $pita,2) < 0.7){?>
                    <td class="text-center align-middle"><?=1; $itog_string['deficit_ploshad'] = $itog_string['deficit_ploshad'] + 1;?></td>
                <?}else{?>
                    <td class="text-center align-middle">0</td>
                <?}?>

                <?if($pita == 0 || empty($character_stolovaya->spot)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}elseif($pita > $character_stolovaya->spot){?>
                    <td class="text-center align-middle"><?=1; $itog_string['deficit_previsaet'] = $itog_string['deficit_previsaet'] + 1;?></td>
                <?}else{?>
                    <td class="text-center align-middle">0</td>
                <?}?>

                <?if(empty($character_stolovaya->count_washing)){?>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                    <td class="text-center align-middle bg-secondary">нд</td>
                <?}else{?>
                    <td class="text-center align-middle"><?=$character_stolovaya->count_washing;?></td>
                    <td class="text-center align-middle"><?=round($character_stolovaya->spot/$character_stolovaya->count_washing, 0);?></td>
                    <td class="text-center align-middle"><?if(round($character_stolovaya->spot/$character_stolovaya->count_washing, 0) > 20){echo 1;$itog_string['deficit_umivalnik'] = $itog_string['deficit_umivalnik'] + 1;}else{ echo 0;}?></td>
                <?}?>
            </tr>
        <?}?>
        <tr class="table-danger">
            <td class="" colspan="3">Итого <?=$mun;?>:</td>
            <td class=""><?=$itog_string['kolichestvo'];?></td>
            <td class="text-center align-middle"><?=$itog_string['tch1'];?></td>
            <td class="text-center align-middle"><?=$itog_string['tch2'];?></td>
            <td class="text-center align-middle"><?=$itog_string['ochno_vsego'];?></td>
            <td class="text-center align-middle"><?=$itog_string['ochno_pervaya'];?></td>
            <td class="text-center align-middle"><?=$itog_string['ochno_vtoraya'];?></td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle"><?=$itog_string['peremena_niz']?></td></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_sahar']))? 0:$itog_string['school_sahar'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_cialic']))? 0:$itog_string['school_cialic'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_mukovis']))? 0:$itog_string['school_mukovis'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_fenilketon']))? 0:$itog_string['school_fenilketon'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_ovz']))? 0:$itog_string['school_ovz'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_allergy']))? 0:$itog_string['school_allergy'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_sahar']))? 0:$itog_string['student_sahar'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_cialic']))? 0:$itog_string['student_cialic'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_mukovis']))? 0:$itog_string['student_mukovis'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_fenilketon']))? 0:$itog_string['student_fenilketon'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_ovz']))? 0:$itog_string['student_ovz'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_allergy']))? 0:$itog_string['student_allergy'] ;?></td>
            <td class="text-center align-middle"><?=$itog_string['school_otkaz']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['school_otkaz_count']))? 'нд':round($itog_string['school_otkaz_procent']/$itog_string['school_otkaz_count'],1);?></td>
            <td class="text-center align-middle"><?=$itog_string['student_otkaz']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['ochno_vsego']))? 'нд': round($itog_string['student_otkaz']/$itog_string['ochno_vsego']*100, 1)?></td>
            <td class="text-center align-middle"><?=$itog_string['kolichestvo_posad']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['ploshad_posad_count']))? 'нд': round($itog_string['ploshad_posad']/$itog_string['ploshad_posad_count'], 1)?></td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle"><?echo(empty($itog_string['fact_ploshad_count']))? 'нд': round($itog_string['fact_ploshad']/$itog_string['fact_ploshad_count'], 1)?></td></td>
            <td class="text-center align-middle"><?=$itog_string['deficit_ploshad']?></td>
            <td class="text-center align-middle"><?=$itog_string['deficit_previsaet']?></td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle"><?=$itog_string['deficit_umivalnik']?></td>
        </tr>

        </tbody>
    </table><br><br><br>
<?}?>


<?
//print_r($data);
$script = <<< JS



$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
  
});


JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
