<?php

use common\models\CharactersStolovaya;
use common\models\NutritionApplications;
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

$this->title = 'Характеристика детей и имеющихся меню';
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

    if (Yii::$app->user->can('food_director'))
    {
        /*print_r(123);
        exit;*/
        $ids = [];
        $nutrition_aplications = NutritionApplications::find()->where(['sender_org_id' => Yii::$app->user->identity->organization_id, 'status' => 1])->orWhere(['reciever_org_id' => Yii::$app->user->identity->organization_id, 'status' => 1])->all();
        foreach ($nutrition_aplications as $n_aplication)
        {
            if ($n_aplication->sender_org_id != Yii::$app->user->identity->organization_id)
            {
                $ids[] = $n_aplication->sender_org_id;
            }
            if ($n_aplication->reciever_org_id != Yii::$app->user->identity->organization_id)
            {
                $ids[] = $n_aplication->reciever_org_id;
            }
        }


        $organization_id = Yii::$app->user->identity->organization_id;
        $org = Organization::findOne($organization_id);
        $organizations = Organization::find()->where(['id' => $ids])->all();

    }
    $mun = \common\models\Municipality::findOne($post['menu_id'])->name;
    //$region_id = \common\models\Municipality::findOne($post['menu_id'])->region_id;
    //$region = \common\models\Region::findOne($region_id)->name;
}

?>
<style>
    th, td {
        border: 1px solid black!important;
        color: black;
        font-size: 15px;

    }
    th {
        background-color: #ede8b9;
        font-size: 15px;
    }
    thead, th {
        background-color: #ede8b9;
        font-size: 15px;
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
	<div class="ml-3 mt-5 mb-3">
        <p><b>СД</b> - Сахарный диабет, <b>Цел</b> - Целиакия, <b>Мук</b> - Муковисцидоз, <b>ФКУ</b> - Фенилкетонурия, <b>ПА</b> - Пищевая аллергия, <b>ОВЗ</b> - Ограниченные возможности здоровья</p>
    </div>
    <table class="table table-bordered table-sm fixtable" style="width: 1200px">
        <thead>
        <tr>
            <td rowspan="2">№</td>
            <td rowspan="2">Муниципальное образование</td>
            <td rowspan="2">Наименование общеобразовательной организации</td>
            <td rowspan="1" colspan="6" style="width: 300px">Количество ШКОЛЬНИКОВ 1-4 кл. имеющих, заболевания требующие индивидуального подхода в организации питания</td>
            <td rowspan="1" colspan="8">Количество внесенных меню (ЗАВТРАКОВ)</td>
            <td rowspan="1" colspan="8">Количество внесенных меню (ОБЕДОВ)</td>

        </tr>
        <tr>
            <td>СД</td>
            <td>Цел</td>
            <td>Мук</td>
            <td>ФКУ</td>
            <td>овз</td>
            <td>ПА</td>

            <td>Всего</td>
            <td>Осн</td>
            <td>СД</td>
            <td>Цел</td>
            <td>Мук</td>
            <td>ФКУ</td>
            <td>овз</td>
            <td>ПА</td>


            <td>Всего</td>
            <td>Осн</td>
            <td>СД</td>
            <td>Цел</td>
            <td>Мук</td>
            <td>ФКУ</td>
            <td>овз</td>
            <td>ПА</td>

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
                <td class="align-middle" style="width: 200px"><?=$mun?></td>
                <td class="align-middle" style="width: 200px"><?echo (empty($organization->short_title)) ? $organization->title : $organization->short_title;?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['sahar'])){echo '-';}else{$itog_string['student_sahar'] = $itog_string['student_sahar'] + $cs_mas['sahar']; echo $cs_mas['sahar'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['cialic'])){echo '-';}else{$itog_string['student_cialic'] = $itog_string['student_cialic'] + $cs_mas['cialic']; echo $cs_mas['cialic'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['mukovis'])){echo '-';}else{$itog_string['student_mukovis'] = $itog_string['student_mukovis'] + $cs_mas['mukovis']; echo $cs_mas['mukovis'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['fenilketon'])){echo '-';}else{$itog_string['student_fenilketon'] = $itog_string['student_fenilketon'] + $cs_mas['fenilketon']; echo $cs_mas['fenilketon'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['ovz'])){echo '-';}else{$itog_string['student_ovz'] = $itog_string['student_ovz'] + $cs_mas['ovz']; echo $cs_mas['ovz'];}?></td>
                <td class="text-center align-middle"><? if (empty($cs_mas['allergy'])){echo '-';}else{$itog_string['student_allergy'] = $itog_string['student_allergy'] + $cs_mas['allergy']; echo $cs_mas['allergy'];}?></td>

                <td class="text-center align-middle"><?if (empty($menu_mas['zavtrak']['itog'])){ echo 0; }else{ echo $menu_mas['zavtrak']['itog'];$itog_string['vnes_zavtrak_itog_count'] = $itog_string['vnes_zavtrak_itog_count'] + $menu_mas['zavtrak']['itog'];}?></td>
                <td class="text-center align-middle"><?if (empty($menu_mas['zavtrak'][3])){ echo 0; }else{ echo $menu_mas['zavtrak'][3];$itog_string['vnes_zavtrak_3_count'] = $itog_string['vnes_zavtrak_3_count'] + $menu_mas['zavtrak'][3];}?></td>
                <td class="text-center align-middle <?if($cs_mas['sahar'] > 0 && empty($menu_mas['zavtrak'][5]) && empty($menu_mas['obed'][5])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][5])){ echo 0; }else{ echo $menu_mas['zavtrak'][5];$itog_string['vnes_zavtrak_5_count'] = $itog_string['vnes_zavtrak_5_count'] + $menu_mas['zavtrak'][5];}?></td>
                <td class="text-center align-middle <?if($cs_mas['cialic'] > 0 && empty($menu_mas['zavtrak'][6]) && empty($menu_mas['obed'][6])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][6])){ echo 0; }else{ echo $menu_mas['zavtrak'][6];$itog_string['vnes_zavtrak_6_count'] = $itog_string['vnes_zavtrak_6_count'] + $menu_mas['zavtrak'][6];}?></td>
                <td class="text-center align-middle <?if($cs_mas['mukovis'] > 0 && empty($menu_mas['zavtrak'][8]) && empty($menu_mas['obed'][8])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][8])){ echo 0; }else{ echo $menu_mas['zavtrak'][8];$itog_string['vnes_zavtrak_8_count'] = $itog_string['vnes_zavtrak_8_count'] + $menu_mas['zavtrak'][8];}?></td>
                <td class="text-center align-middle <?if($cs_mas['fenilketon'] > 0 && empty($menu_mas['zavtrak'][7]) && empty($menu_mas['obed'][7])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][7])){ echo 0; }else{ echo $menu_mas['zavtrak'][7];$itog_string['vnes_zavtrak_7_count'] = $itog_string['vnes_zavtrak_7_count'] + $menu_mas['zavtrak'][7];}?></td>
                <td class="text-center align-middle <?if($cs_mas['ovz'] > 0 && empty($menu_mas['zavtrak'][4]) && empty($menu_mas['obed'][4])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][4])){ echo 0; }else{ echo $menu_mas['zavtrak'][4];$itog_string['vnes_zavtrak_4_count'] = $itog_string['vnes_zavtrak_4_count'] + $menu_mas['zavtrak'][4];}?></td>
                <td class="text-center align-middle <?if($cs_mas['allergy'] > 0 && empty($menu_mas['zavtrak'][10]) && empty($menu_mas['obed'][10])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][10])){ echo 0; }else{ echo $menu_mas['zavtrak'][10];$itog_string['vnes_zavtrak_10_count'] = $itog_string['vnes_zavtrak_10_count'] + $menu_mas['zavtrak'][10];}?></td>

                <td class="text-center align-middle"><?if (empty($menu_mas['obed']['itog'])){ echo 0; }else{ echo $menu_mas['obed']['itog'];$itog_string['vnes_obed_itog_count'] = $itog_string['vnes_obed_itog_count'] + $menu_mas['obed']['itog'];}?></td>
                <td class="text-center align-middle "><?if (empty($menu_mas['obed'][3])){ echo 0; }else{ echo $menu_mas['obed'][3];$itog_string['vnes_obed_3_count'] = $itog_string['vnes_obed_3_count'] + $menu_mas['obed'][3];}?></td>
                <td class="text-center align-middle <?if($cs_mas['sahar'] > 0 && empty($menu_mas['zavtrak'][5]) && empty($menu_mas['obed'][5])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][5])){ echo 0; }else{ echo $menu_mas['zavtrak'][5];$itog_string['vnes_zavtrak_5_count'] = $itog_string['vnes_zavtrak_5_count'] + $menu_mas['zavtrak'][5];}?></td>
                <td class="text-center align-middle <?if($cs_mas['cialic'] > 0 && empty($menu_mas['zavtrak'][6]) && empty($menu_mas['obed'][6])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][6])){ echo 0; }else{ echo $menu_mas['zavtrak'][6];$itog_string['vnes_zavtrak_6_count'] = $itog_string['vnes_zavtrak_6_count'] + $menu_mas['zavtrak'][6];}?></td>
                <td class="text-center align-middle <?if($cs_mas['mukovis'] > 0 && empty($menu_mas['zavtrak'][8]) && empty($menu_mas['obed'][8])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][8])){ echo 0; }else{ echo $menu_mas['zavtrak'][8];$itog_string['vnes_zavtrak_8_count'] = $itog_string['vnes_zavtrak_8_count'] + $menu_mas['zavtrak'][8];}?></td>
                <td class="text-center align-middle <?if($cs_mas['fenilketon'] > 0 && empty($menu_mas['zavtrak'][7]) && empty($menu_mas['obed'][7])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][7])){ echo 0; }else{ echo $menu_mas['zavtrak'][7];$itog_string['vnes_zavtrak_7_count'] = $itog_string['vnes_zavtrak_7_count'] + $menu_mas['zavtrak'][7];}?></td>
                <td class="text-center align-middle <?if($cs_mas['ovz'] > 0 && empty($menu_mas['zavtrak'][4]) && empty($menu_mas['obed'][4])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][4])){ echo 0; }else{ echo $menu_mas['zavtrak'][4];$itog_string['vnes_zavtrak_4_count'] = $itog_string['vnes_zavtrak_4_count'] + $menu_mas['zavtrak'][4];}?></td>
                <td class="text-center align-middle <?if($cs_mas['allergy'] > 0 && empty($menu_mas['zavtrak'][10]) && empty($menu_mas['obed'][10])){echo "bg-danger";}?>"><?if (empty($menu_mas['zavtrak'][10])){ echo 0; }else{ echo $menu_mas['zavtrak'][10];$itog_string['vnes_zavtrak_10_count'] = $itog_string['vnes_zavtrak_10_count'] + $menu_mas['zavtrak'][10];}?></td>

            </tr>
        <?}?>
        <tr class="table-danger">
            <td class="" colspan="3">Итого <?=$mun;?>:</td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_sahar']))? 0:$itog_string['student_sahar'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_cialic']))? 0:$itog_string['student_cialic'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_mukovis']))? 0:$itog_string['student_mukovis'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_fenilketon']))? 0:$itog_string['student_fenilketon'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_ovz']))? 0:$itog_string['student_ovz'] ;?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['student_allergy']))? 0:$itog_string['student_allergy'] ;?></td>

            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_itog_count']))? 0:$itog_string['vnes_zavtrak_itog_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_3_count']))? 0:$itog_string['vnes_zavtrak_3_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_5_count']))? 0:$itog_string['vnes_zavtrak_5_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_6_count']))? 0:$itog_string['vnes_zavtrak_6_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_8_count']))? 0:$itog_string['vnes_zavtrak_8_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_7_count']))? 0:$itog_string['vnes_zavtrak_7_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_4_count']))? 0:$itog_string['vnes_zavtrak_4_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_zavtrak_10_count']))? 0:$itog_string['vnes_zavtrak_10_count']?></td>

            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_itog_count']))? 0:$itog_string['vnes_obed_itog_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_3_count']))? 0:$itog_string['vnes_obed_3_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_5_count']))? 0:$itog_string['vnes_obed_5_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_6_count']))? 0:$itog_string['vnes_obed_6_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_8_count']))? 0:$itog_string['vnes_obed_8_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_7_count']))? 0:$itog_string['vnes_obed_7_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_4_count']))? 0:$itog_string['vnes_obed_4_count']?></td>
            <td class="text-center align-middle"><?echo(empty($itog_string['vnes_obed_10_count']))? 0:$itog_string['vnes_obed_10_count']?></td>


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



function FixTable(table) {
	var inst = this;
	this.table  = table;
 
	$('thead > tr > td',$(this.table)).each(function(index) {
		var div_fixed = $('<div/>').addClass('fixtable-fixed');
		var div_relat = $('<div/>').addClass('fixtable-relative');
		div_fixed.html($(this).html());
		div_relat.html($(this).html());
		$(this).html('').append(div_fixed).append(div_relat);
		$(div_fixed).hide();
	});
	
 
	this.StyleColumns();
	this.FixColumns();
 
	$(window).scroll(function(){
		inst.FixColumns()
	}).resize(function(){
		inst.StyleColumns()
	});
}
 
FixTable.prototype.StyleColumns = function() {
	var inst = this;
	$('tr > th', $(this.table)).each(function(){
		var div_relat = $('div.fixtable-relative', $(this));
		var th = $(div_relat).parent('th');
		$('div.fixtable-fixed', $(this)).css({
			'width': $(th).outerWidth(true) - parseInt($(th).css('border-left-width')) + 'px',
			'height': $(th).outerHeight(true) + 'px',
			'left': $(div_relat).offset().left - parseInt($(th).css('padding-left')) + 'px',
			'padding-top': $(div_relat).offset().top - $(inst.table).offset().top + 'px',
			'padding-left': $(th).css('padding-left'),
			'padding-right': $(th).css('padding-right')
		});
	});
}
 
FixTable.prototype.FixColumns = function() {
	var inst = this;
	var show = false;
	var s_top = $(window).scrollTop();
	var h_top = $(inst.table).offset().top;
 
	if (s_top < (h_top + $(inst.table).height() - $(inst.table).find('.fixtable-fixed').outerHeight()) && s_top > h_top) {
		show = true;
	}
 
	$('tr > th > div.fixtable-fixed', $(this.table)).each(function(){
		show ? $(this).show() : $(this).hide()
	});
}
 
$(document).ready(function(){
	$('.fixtable').each(function() {
		new FixTable(this);
	});
});



JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
