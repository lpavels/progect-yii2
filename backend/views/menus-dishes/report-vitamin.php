<?php

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

$this->title = 'Отчет по витаминам и микроэлементам';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->one();


$menu_cycle_count = $first_menu->cycle;
$cycle_ids = [];
$cycle_ids[0] = 'За все недели';
for($i=1;$i<=$menu_cycle_count;$i++){
    $cycle_ids[$i] = $i;//массив из подходящи циклов
}

$days_items = MenusDays::find()->where(['menu_id' => $first_menu->id])->all();
foreach ($days_items as $m_day){
    $days_ids[] = $m_day->days_id;
}

$days_items = ArrayHelper::map(Days::find()->where(['id' => $days_ids])->all(), 'id', 'name');
$days_null = array('0' => 'По всем дням');
$days_items = ArrayHelper::merge($days_null, $days_items);

$nutritions_items = MenusNutrition::find()->where(['menu_id' => $first_menu->id])->all();
foreach ($nutritions_items as $m_nutrition){
    $nutrition_ids[] = $m_nutrition->nutrition_id;
}

$nutritions_items = ArrayHelper::map(NutritionInfo::find()->where(['id' => $nutrition_ids])->all(), 'id', 'name');
$nutritions_null = array('0' => 'По всем приемам пищи');
$nutritions_items = ArrayHelper::merge($nutritions_null, $nutritions_items);

$chemistry_items = [0 => 'По циклам', 1 => 'По дням', 2 => 'По приемам пищи'];
$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_cycle = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$two_column = ['options' => ['class' => 'row justify-content-center mt-3'], 'labelOptions' => ['class' => 'col-11 col-md-2 col-form-label font-weight-bold']];
if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);
    $menu_cycle_count = $my_menus->cycle;
    $cycle_ids = [];
    $cycle_ids[0] = 'За все недели';
    for($i=1;$i<=$menu_cycle_count;$i++){
        $cycle_ids[$i] = $i;//массив из подходящи циклов
    }
    $my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();
    foreach($my_days as $m_day){
        $ids[] = $m_day->days_id;
    }

    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];

    $count_my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->count();
    if($post['cycle'] == 0){
        $count_my_days = $count_my_days * $menu_cycle_count;
    }
    $normativ_vitamin_day_vitamin_a = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_a', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ_vitamin_day_k = \common\models\NormativVitaminDay::find()->where(['name' => 'k', 'age_info_id' => $my_menus->age_info_id])->one()->value;


    $days_items = MenusDays::find()->where(['menu_id' => $first_menu->id])->all();
    foreach ($days_items as $m_day){
        $days_ids[] = $m_day->days_id;
    }

    $days_items = ArrayHelper::map(Days::find()->where(['id' => $days_ids])->all(), 'id', 'name');
    $days_null = array('0' => 'По всем дням');
    $days_items = ArrayHelper::merge($days_null, $days_items);

    //print_r($cycle_ids);exit;



}

?>

<style>
    .day_container{
        display: flex;
        flex-wrap: wrap;
    }
    .day_item{
        flex: 300px 0 0;
        margin-top: 25px;
    }

    .nutrition_item{
        flex: 300px 0 0;
        margin-top: 25px;
    }

    .cycle_item{
        flex: 300px 0 0;
        margin-top: 25px;
    }
</style>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="mb-5 mt-5">



                <?= $form->field($model, 'menu_id', $two_column)->dropDownList($my_menus_items, [
                    'class' => 'form-control col-11 col-md-4'/*, 'options' => [$post['menu_id'] => ['Selected' => true]]*/,
                    'onchange' => '
                  $.get("../menus-dishes/cyclelist?id="+$(this).val(), function(data){
                    $("select#menusdishes-cycle").html(data);
                  });
                  
                  $.get("../menus-dishes/dayfulllist?id="+$(this).val(), function(data){
                    $("select#menusdishes-created_at").html(data);
                  });
                  
                  $.get("../menus-dishes/nutritionlist?id="+$(this).val(), function(data){
                    $("select#menusdishes-nutrition_id").html(data);
                    console.log(data);
                  });
                                    //ДЛЯ ЗАПОЛНЕНИЯ ИНПУТОВ: ВОЗРВСТ КАТЕГОРИЯ СРОКИ

                  $.get("../menus-dishes/insertcharacters?id="+$(this).val(), function(data){
                  console.log(data);
                    $("input#characters").val(data);
                  });
                  $.get("../menus-dishes/insertage?id="+$(this).val(), function(data){
                  console.log(data);
                    $("input#age").val(data);
                  });
                  $.get("../menus-dishes/insertsrok?id="+$(this).val(), function(data){
                  console.log(data);
                    $("#insert-srok").val(data);
                  });'
                ]); ?>
        <?if(empty($post)){$menu_id = $first_menu->id;} else{$menu_id = $post['menu_id'];}?>
             <?php
             $age_info = 'age_info';
            echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                  <label class="col-11 col-md-2 col-form-label font-weight-bold">Возрастная категория:</label>
                  <input type="text" class="form-control col-11 col-md-4" id="age" value="'.$model->insert_info($menu_id, $age_info).'" readonly="true">
                  <div class="invalid-feedback"></div>
                  </div>';?>

            <?php
            $feeders_characters = 'feeders_characters';
            echo '<div class="row justify-content-center mt-3 field-organization-short_title">
                 <label class="col-11 col-md-2 col-form-label font-weight-bold">Характеристика питающихся:</label>
                 <input type="text" class="form-control col-11 col-md-4" id="characters" value="'.$model->insert_info($menu_id, $feeders_characters).'" readonly="true">
                 <div class="invalid-feedback"></div>
                 </div>';?>

            <?php
            $sroki = 'sroki';
            echo '<div class="row justify-content-center mt-2 field-organization-short_title">
                     <label class="col-11 col-md-2 col-form-label font-weight-bold">Срок действия меню:</label>
                     <input type="text" class="form-control col-11 col-md-4" id="insert-srok" value="'.$model->insert_info($menu_id, $sroki).'" readonly="true">
                     <div class="invalid-feedback"></div>
                     </div>';?>


        <?= $form->field($model, 'yield', $two_column)->dropDownList($chemistry_items, ['class' => 'form-control col-11 col-md-4'])->label('Отображение'); ?>

        <?= $form->field($model, 'cycle', $two_column)->dropDownList($cycle_ids, ['class' => 'form-control col-11 col-md-4']) ?>

        <?= $form->field($model, 'created_at', $two_column)->dropDownList($days_items, ['class' => 'form-control col-11 col-md-4'])->label('День') ?>

        <?= $form->field($model, 'nutrition_id', $two_column)->dropDownList($nutritions_items, ['class' => 'form-control col-11 col-md-4'] )->label('Прием пищи') ?>









        <div class="row mt-3">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['name'=>'identificator', 'value' => 'menu', 'class' => 'btn main-button-3 beforeload']) ?>
            </div>
        </div>

        <p class="text-center">*Загрузка таблиц может занять некоторое время</p>
        <?php ActiveForm::end(); ?>
    </div>












<?php $data = [];?>

<?if(!empty($post)){?>

    <?php $count_cycle = 0; unset($cycle_ids[0]);?>
    <div class="day_container">
    <?php foreach($cycle_ids as $cycle_id){ $count++; $data_cycle = []; ?>

        <? foreach($days as $day){?>

            <?php $super_total_yield = 0; $super_total_protein = 0; $super_total_fat = 0; $super_total_carbohydrates_total = 0; $super_total_energy_kkal = 0; $super_total_vitamin_a = 0; $super_total_vitamin_c = 0; $super_total_vitamin_b1 = 0; $super_total_vitamin_b2 = 0; $super_total_vitamin_d = 0; $super_total_vitamin_pp = 0; $super_total_na = 0; $super_total_k = 0; $super_total_ca = 0; $super_total_f = 0; $super_total_se = 0;?>
            <?//print_r($nutritions);exit;?>
            <? foreach($nutritions as $nutrition){?>

<!--


                <? $count = 0;
                $indicator = 0; $energy_kkal = 0; $protein = 0; $fat = 0; $carbohydrates_total = 0; $vitamins = []; unset($vitamins); $vitamin_a = 0; $vitamin_c = 0; $vitamin_b1 = 0; $vitamin_b2 = 0; $vitamin_d = 0; $vitamin_pp = 0; $na = 0; $k = 0; $ca = 0; $f = 0; $p = 0; $se = 0; $i = 0; $mg = 0; $fe = 0;?>
                <?foreach($menus_dishes as $key => $m_dish){ ?>
                    <? if($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id){ ?>

                    <!--ПО ЦИКЛАМ-->
                    <?if($post['yield'] == 0){?>

                        <?if($post['cycle'] == 0 || $post['cycle'] == $cycle_id){?>
                        <? $count++;?>
                                <? $vitamins['vitamin_a'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'),2); $vitamin_a = $vitamin_a + $vitamins['vitamin_a']?>
                                <? $vitamins['vitamin_c'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'),2); $vitamin_c = $vitamin_c + $vitamins['vitamin_c']?>
                                <? $vitamins['vitamin_b1'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'),2);$vitamin_b1 = $vitamin_b1 + $vitamins['vitamin_b1']?>
                                <? $vitamins['vitamin_b2'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'),2); $vitamin_b2 = $vitamin_b2 + $vitamins['vitamin_b2']?>
                                <? $vitamins['vitamin_d'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'),2); $vitamin_d = $vitamin_d + $vitamins['vitamin_d']?>
                                <? $vitamins['vitamin_pp'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'),2);$vitamin_pp = $vitamin_pp + $vitamins['vitamin_pp']?>
                                <? $vitamins['na'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'),2); $na = $na + $vitamins['na']?>
                                <? $vitamins['k'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'),2); $k = $k + $vitamins['k']?>
                                <? $vitamins['ca'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'),2); $ca = $ca + $vitamins['ca']?>
                                <? $vitamins['f'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'),2); $f = $f + $vitamins['f']?>
                                <? $vitamins['mg'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'mg'),2);$mg = $mg + $vitamins['mg']?>
                                <? $vitamins['p'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'p'),2); $p = $p + $vitamins['p']?>
                                <? $vitamins['fe'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'fe'),2); $fe = $fe + $vitamins['fe']?>
                                <? $vitamins['i'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'i'),2); $i = $i + $vitamins['i']?>
                                <? $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'),2); $se = $se + $vitamins['se']?>

                            <? unset($menus_dishes[$key]); ?>
<!--                        </tr>-->
                    <?}?>
                    <?}?>

                    <!--ПО ДНЯМ-->
                    <?if($post['yield'] == 1){
                        //echo '<b><p class="mb-0 text-center" style="font-size: 20px; font-weight: 500;">Неделя '. $cycle_id .'</p></b>'?>

                        <?if(($post['cycle'] == 0 && $post['created_at'] == 0) || ($post['cycle'] == $cycle_id && $post['created_at'] == 0) || ($post['cycle'] == 0 && $post['created_at'] == $day->id) || ($post['cycle'] == $cycle_id && $post['created_at'] == $day->id)){?>
                            <? $count++;?>
                            <? $vitamins['vitamin_a'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'),2); $vitamin_a = $vitamin_a + $vitamins['vitamin_a']?>
                            <? $vitamins['vitamin_c'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'),2); $vitamin_c = $vitamin_c + $vitamins['vitamin_c']?>
                            <? $vitamins['vitamin_b1'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'),2);$vitamin_b1 = $vitamin_b1 + $vitamins['vitamin_b1']?>
                            <? $vitamins['vitamin_b2'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'),2); $vitamin_b2 = $vitamin_b2 + $vitamins['vitamin_b2']?>
                            <? $vitamins['vitamin_d'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'),2); $vitamin_d = $vitamin_d + $vitamins['vitamin_d']?>
                            <? $vitamins['vitamin_pp'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'),2);$vitamin_pp = $vitamin_pp + $vitamins['vitamin_pp']?>
                            <? $vitamins['na'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'),2); $na = $na + $vitamins['na']?>
                            <? $vitamins['k'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'),2); $k = $k + $vitamins['k']?>
                            <? $vitamins['ca'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'),2); $ca = $ca + $vitamins['ca']?>
                            <? $vitamins['f'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'),2); $f = $f + $vitamins['f']?>
                            <? $vitamins['mg'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'mg'),2);$mg = $mg + $vitamins['mg']?>
                            <? $vitamins['p'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'p'),2); $p = $p + $vitamins['p']?>
                            <? $vitamins['fe'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'fe'),2); $fe = $fe + $vitamins['fe']?>
                            <? $vitamins['i'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'i'),2); $i = $i + $vitamins['i']?>
                            <? $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'),2); $se = $se + $vitamins['se']?>

                            <? unset($menus_dishes[$key]); ?>
                            <!--                        </tr>-->
                        <?}?>
                    <?}?>



                    <!--ПО ПРИЕМАМ ПИЩИ-->
                    <?if($post['yield'] == 2){
                        //echo '<b><p class="mb-0 text-center" style="font-size: 20px; font-weight: 500;">Неделя '. $cycle_id .'</p></b>'?>

                        <?if(($post['cycle'] == 0 && $post['created_at'] == 0 && $post['nutrition_id'] == 0) || ($post['cycle'] == $cycle_id && $post['created_at'] == 0 && $post['nutrition_id'] == 0) || ($post['cycle'] == 0 && $post['created_at'] == $day->id && $post['nutrition_id'] == 0) || ($post['cycle'] == 0 && $post['created_at'] == 0 && $post['nutrition_id'] == $nutrition->id) || ($post['cycle'] == 0 && $post['created_at'] == $day->id && $post['nutrition_id'] == $nutrition->id) || ($post['cycle'] == $cycle_id && $post['created_at'] == 0 && $post['nutrition_id'] == $nutrition->id) || ($post['cycle'] == $cycle_id && $post['created_at'] == $day->id && $post['nutrition_id'] == 0) || ($post['cycle'] == $cycle_id && $post['created_at'] == $day->id && $post['nutrition_id'] == $nutrition->id)){?>
                            <? $count++;?>
                            <? $vitamins['vitamin_a'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'),2); $vitamin_a = $vitamin_a + $vitamins['vitamin_a']?>
                            <? $vitamins['vitamin_c'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'),2); $vitamin_c = $vitamin_c + $vitamins['vitamin_c']?>
                            <? $vitamins['vitamin_b1'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'),2);$vitamin_b1 = $vitamin_b1 + $vitamins['vitamin_b1']?>
                            <? $vitamins['vitamin_b2'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'),2); $vitamin_b2 = $vitamin_b2 + $vitamins['vitamin_b2']?>
                            <? $vitamins['vitamin_d'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'),2); $vitamin_d = $vitamin_d + $vitamins['vitamin_d']?>
                            <? $vitamins['vitamin_pp'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'),2);$vitamin_pp = $vitamin_pp + $vitamins['vitamin_pp']?>
                            <? $vitamins['na'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'),2); $na = $na + $vitamins['na']?>
                            <? $vitamins['k'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'),2); $k = $k + $vitamins['k']?>
                            <? $vitamins['ca'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'),2); $ca = $ca + $vitamins['ca']?>
                            <? $vitamins['f'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'),2); $f = $f + $vitamins['f']?>
                            <? $vitamins['mg'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'mg'),2);$mg = $mg + $vitamins['mg']?>
                            <? $vitamins['p'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'p'),2); $p = $p + $vitamins['p']?>
                            <? $vitamins['fe'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'fe'),2); $fe = $fe + $vitamins['fe']?>
                            <? $vitamins['i'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'i'),2); $i = $i + $vitamins['i']?>
                            <? $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'),2); $se = $se + $vitamins['se']?>

                            <? unset($menus_dishes[$key]); ?>
                            <!--                        </tr>-->
                        <?}?>
                    <?}?>

                    <?}?>
                    <?}?>

                <? if($count > 0){ ?>
                    <?if($post['yield'] == 2){?>
                        <?if(($post['cycle'] == 0 && $post['created_at'] == 0 && $post['nutrition_id'] == 0) || ($post['cycle'] == $cycle_id && $post['created_at'] == 0 && $post['nutrition_id'] == 0) || ($post['cycle'] == 0 && $post['created_at'] == $day->id && $post['nutrition_id'] == 0) || ($post['cycle'] == 0 && $post['created_at'] == 0 && $post['nutrition_id'] == $nutrition->id) || ($post['cycle'] == 0 && $post['created_at'] == $day->id && $post['nutrition_id'] == $nutrition->id) || ($post['cycle'] == $cycle_id && $post['created_at'] == 0 && $post['nutrition_id'] == $nutrition->id) || ($post['cycle'] == $cycle_id && $post['created_at'] == $day->id && $post['nutrition_id'] == 0) || ($post['cycle'] == $cycle_id && $post['created_at'] == $day->id && $post['nutrition_id'] == $nutrition->id)){?>
            <div class="nutrition_item">


                <table class="table_th0 table-hover table-responsive last" >
                    <thead>

                    <tr style=" background-color: #ede8b9!important;"><td colspan="2" class="text-center"><? echo '<p class="mb-0" style="font-size: 18px; font-weight: 500;">'. $cycle_id .' Неделя '. $day->name .'</p>'?></td></tr>
                    <tr style=" background-color: #ede8b9!important;"><td colspan="2" class="text-center"><? echo '<p class="mb-0" style="font-size: 18px; font-weight: 500;">'. $nutrition->name .'</p>'?></td></tr>
                    <tr style=" background-color: #F08080!important;">
                        <th>Показатели</th>
                        <th>Значения</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>A, мкг рет.экв</td><td class="text-center"><?= $vitamin_a;?></td></tr>
                    <tr><td>C, мг</td><td class="text-center"><?= $vitamin_c;?></td></tr>
                    <tr><td>B1, мг</td><td class="text-center"><?= $vitamin_b1;?></td></tr>
                    <tr><td>B2, мг</td><td class="text-center"><?= $vitamin_b2; ?></td></tr>
                    <tr><td>D, мкг</td><td class="text-center"><?= $vitamin_d;?></td></tr>
                    <tr><td>PP, мг</td><td class="text-center"><?= $vitamin_pp;?></td></tr>
                    <tr><td>Na, мг</td><td class="text-center"><?= $na;?></td></tr>
                    <tr><td>K, мг</td><td class="text-center"><?= $k; ?></td></tr>
                    <tr><td>Ca, мг</td><td class="text-center"><?= $ca;?></td></tr>
                    <tr><td>F, мкг</td><td class="text-center"><?= $f;?></td></tr>
                    <tr><td>Mg, мг</td><td class="text-center"><?= $mg;?></td></tr>
                    <tr><td>P, мг</td><td class="text-center"><?= $p; ?></td></tr>
                    <tr><td>Fe, мг</td><td class="text-center"><?= $fe;?></td></tr>
                    <tr><td>I, мкг</td><td class="text-center"><?= $i;?></td></tr>
                    <tr><td>Se, мкг</td><td class="text-center"><?= $se;?></td></tr>
                    </tbody>
                </table>
            </div>
                        <?}?>
                        <?}?>


                    <?//if($post['created_at'] == $day->id){?>
                            <? $data[$nutrition->id]['vitamin_a'] = $data[$nutrition->id]['vitamin_a'] + $vitamin_a; $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;?>
                            <? $data[$nutrition->id]['vitamin_c'] = $data[$nutrition->id]['vitamin_c'] + $vitamin_c; $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;?>
                            <? $data[$nutrition->id]['vitamin_b1'] = $data[$nutrition->id]['vitamin_b1'] + $vitamin_b1; $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;?>
                            <?$data[$nutrition->id]['vitamin_b2'] = $data[$nutrition->id]['vitamin_b2'] + $vitamin_b2; $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;?>
                            <? $data[$nutrition->id]['vitamin_d'] = $data[$nutrition->id]['vitamin_d'] + $vitamin_d; $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d;?>
                            <? $data[$nutrition->id]['vitamin_pp'] = $data[$nutrition->id]['vitamin_pp'] + $vitamin_pp; $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;?>
                            <? $data[$nutrition->id]['vitamin_na'] = $data[$nutrition->id]['vitamin_na'] + $na; $super_total_na = $super_total_na + $na;?>
                            <?$data[$nutrition->id]['vitamin_k'] = $data[$nutrition->id]['vitamin_k'] + $k; $super_total_k = $super_total_k + $k;?>
                            <? $data[$nutrition->id]['vitamin_ca'] = $data[$nutrition->id]['vitamin_ca'] + $ca; $super_total_ca = $super_total_ca + $ca;?>
                            <? $data[$nutrition->id]['vitamin_f'] = $data[$nutrition->id]['vitamin_f'] + $f; $super_total_f = $super_total_f + $f;?>
                            <? $data[$nutrition->id]['vitamin_mg'] = $data[$nutrition->id]['vitamin_mg'] + $mg; $super_total_mg = $super_total_mg + $mg;?>
                            <?$data[$nutrition->id]['vitamin_p'] = $data[$nutrition->id]['vitamin_p'] + $p; $super_total_p = $super_total_p + $p;?>
                            <?$data[$nutrition->id]['vitamin_fe'] = $data[$nutrition->id]['vitamin_fe'] + $fe; $super_total_fe = $super_total_fe + $fe;?>
                            <? $data[$nutrition->id]['vitamin_i'] = $data[$nutrition->id]['vitamin_i'] + $i; $super_total_i = $super_total_i + $i;?>
                            <? $data[$nutrition->id]['vitamin_se'] = $data[$nutrition->id]['vitamin_se'] + $se; $super_total_se = $super_total_se + $se;?>
                        <?//}?>

                <?}?>

            <?}?>



                    <? $data_cycle['vitamin_a'] = $data_cycle['vitamin_a'] + $super_total_vitamin_a;?>
                    <? $data_cycle['vitamin_c'] = $data_cycle['vitamin_c'] + $super_total_vitamin_c;?>
                    <? $data_cycle['vitamin_b1'] = $data_cycle['vitamin_b1'] + $super_total_vitamin_b1;?>
                    <? $data_cycle['vitamin_b2'] = $data_cycle['vitamin_b2'] + $super_total_vitamin_b2;?>
                    <? $data_cycle['vitamin_d'] = $data_cycle['vitamin_d'] + $super_total_vitamin_d;?>
                    <? $data_cycle['vitamin_pp'] = $data_cycle['vitamin_pp'] + $super_total_vitamin_pp;?>
                    <? $data_cycle['vitamin_na'] = $data_cycle['vitamin_na'] + $super_total_na;?>
                    <? $data_cycle['vitamin_k'] = $data_cycle['vitamin_k'] + $super_total_k;?>
                    <? $data_cycle['vitamin_ca'] = $data_cycle['vitamin_ca'] + $super_total_ca;?>
                    <? $data_cycle['vitamin_f'] = $data_cycle['vitamin_f'] + $super_total_f;?>
                    <? $data_cycle['vitamin_mg'] = $data_cycle['vitamin_mg'] + $super_total_mg;?>
                    <? $data_cycle['vitamin_p'] = $data_cycle['vitamin_p'] + $super_total_p;?>
                    <? $data_cycle['vitamin_fe'] = $data_cycle['vitamin_fe'] + $super_total_fe;?>
                    <? $data_cycle['vitamin_i'] = $data_cycle['vitamin_i'] + $super_total_i;?>
                    <? $data_cycle['vitamin_se'] = $data_cycle['vitamin_se'] + $super_total_se?>


                    <?if($post['yield'] == 1){?>
                        <?if(($post['cycle'] == 0 && $post['created_at'] == 0) || ($post['cycle'] == $cycle_id && $post['created_at'] == 0) || ($post['cycle'] == 0 && $post['created_at'] == $day->id) || ($post['cycle'] == $cycle_id && $post['created_at'] == $day->id)){
                        ?>

                    <div class="day_item">
                    <table class="table_th0 table-hover table-responsive last" >
                        <thead>
                        <tr style=" background-color: #ede8b9!important;"><td colspan="2" class="text-center"><? echo '<p class="mb-0" style="font-size: 18px; font-weight: 500;">'. $cycle_id .' Неделя '. $day->name .'</p>'?></td></tr>
                        <tr style=" background-color: #F08080!important;">
                            <th>Показатели</th>
                            <th>Значения</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td>A, мкг рет.экв</td><td class="text-center"><?= $super_total_vitamin_a;?></td></tr>
                        <tr><td>C, мг</td><td class="text-center"><?= $super_total_vitamin_c;?></td></tr>
                        <tr><td>B1, мг</td><td class="text-center"><?= $super_total_vitamin_b1;?></td></tr>
                        <tr><td>B2, мг</td><td class="text-center"><?= $super_total_vitamin_b2; ?></td></tr>
                        <tr><td>D, мкг</td><td class="text-center"><?= $super_total_vitamin_d;?></td></tr>
                        <tr><td>PP, мг</td><td class="text-center"><?= $super_total_vitamin_pp;?></td></tr>
                        <tr><td>Na, мг</td><td class="text-center"><?= $super_total_na;?></td></tr>
                        <tr><td>K, мг</td><td class="text-center"><?= $super_total_k; ?></td></tr>
                        <tr><td>Ca, мг</td><td class="text-center"><?= $super_total_ca;?></td></tr>
                        <tr><td>F, мкг</td><td class="text-center"><?= $super_total_f;?></td></tr>
                        <tr><td>Mg, мг</td><td class="text-center"><?= $super_total_mg;?></td></tr>
                        <tr><td>P, мг</td><td class="text-center"><?= $super_total_p; ?></td></tr>
                        <tr><td>Fe, мг</td><td class="text-center"><?= $super_total_fe;?></td></tr>
                        <tr><td>I, мкг</td><td class="text-center"><?= $super_total_i;?></td></tr>
                        <tr><td>Se, мкг</td><td class="text-center"><?= $super_total_se;?></td></tr>

                        </tbody>
                    </table>
                    </div>

                        <?}?>
                        <?}?>

        <?php } ?>

                    <?if($post['yield'] == 0){?>
                    <div class="cycle_item">
                    <table class="table_th0 table-hover table-responsive last" >
                        <thead>
                        <tr style=" background-color: #ede8b9!important;"><td colspan="2" class="text-center"><? echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">Итого за цикл</p>'?></td></tr>
                        <tr style=" background-color: #F08080!important;">
                            <th>Показатели</th>
                            <th>Значения</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td>A, мкг рет.экв</td><td class="  "><?= $data_cycle['vitamin_a'] ?></td></tr>
                        <tr><td>C, мг</td><td class="text-center"><?= $data_cycle['vitamin_c'] ?></td></tr>
                        <tr><td>B1, мг</td><td class="text-center"><?=$data_cycle['vitamin_b1']?></td></tr>
                        <tr><td>B2, мг</td><td class="text-center"><?=$data_cycle['vitamin_b2'] ?></td></tr>
                        <tr><td>D, мкг</td><td class="text-center"><?=$data_cycle['vitamin_d'] ?></td></tr>
                        <tr><td>PP, мг</td><td class="text-center"><?=$data_cycle['vitamin_pp']?></td></tr>
                        <tr><td>Na, мг</td><td class="text-center"><?=$data_cycle['vitamin_na']?></td></tr>
                        <tr><td>K, мг</td><td class="text-center"><?= $data_cycle['vitamin_k'] ?></td></tr>
                        <tr><td>Ca, мг</td><td class="text-center"><?=$data_cycle['vitamin_ca']?></td></tr>
                        <tr><td>F, мкг</td><td class="text-center"><?=$data_cycle['vitamin_f'] ?></td></tr>
                        <tr><td>Mg, мг</td><td class="text-center"><?=$data_cycle['vitamin_mg']?></td></tr>
                        <tr><td>P, мг</td><td class="text-center"><?= $data_cycle['vitamin_p'] ?></td></tr>
                        <tr><td>Fe, мг</td><td class="text-center"><?=$data_cycle['vitamin_fe']?></td></tr>
                        <tr><td>I, мкг</td><td class="text-center"><?=$data_cycle['vitamin_i']?></td></tr>
                        <tr><td>Se, мкг</td><td class="text-center"><?=  $data_cycle['vitamin_se']?></td></tr>
                        </tbody>
                    </table>
                    </div>
                        <?}?>

    <?php } ?>
    </div>
        <? $data_itog =[];?>
<!--        --><?// foreach($nutritions as $nutrition){?>
<!--                --><?//$data_itog['vitamin_a'] = $data_itog['vitamin_a'] + round($data[$nutrition->id]['vitamin_a']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_c'] = $data_itog['vitamin_c'] + round($data[$nutrition->id]['vitamin_c']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_b1'] = $data_itog['vitamin_b1'] + round($data[$nutrition->id]['vitamin_b1']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_b2'] = $data_itog['vitamin_b2'] + round($data[$nutrition->id]['vitamin_b2']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_d'] = $data_itog['vitamin_d'] + round($data[$nutrition->id]['vitamin_d']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_pp'] = $data_itog['vitamin_pp'] + round($data[$nutrition->id]['vitamin_pp']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_na'] = $data_itog['vitamin_na'] + round($data[$nutrition->id]['vitamin_na']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_k'] = $data_itog['vitamin_k'] + round($data[$nutrition->id]['vitamin_k']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_ca'] = $data_itog['vitamin_ca'] + round($data[$nutrition->id]['vitamin_ca']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_f'] = $data_itog['vitamin_f'] + round($data[$nutrition->id]['vitamin_f']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_mg'] = $data_itog['vitamin_mg'] + round($data[$nutrition->id]['vitamin_mg']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_p'] = $data_itog['vitamin_p'] + round($data[$nutrition->id]['vitamin_p']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_fe'] = $data_itog['vitamin_fe'] + round($data[$nutrition->id]['vitamin_fe']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_i'] = $data_itog['vitamin_i'] + round($data[$nutrition->id]['vitamin_i']/$count_my_days, 2);?>
<!--                    --><?// $data_itog['vitamin_se'] = $data_itog['vitamin_se'] + round($data[$nutrition->id]['vitamin_se']/$count_my_days, 2);?>
<!---->
<!---->
<!---->
<!---->
<!--                    --><?//if($post['yield'] == 2){?>
<!--                        <table class="table_th0 table-hover table-responsive last" >-->
<!--                <thead>-->
<!--                <tr><td colspan="2">--><?// echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">Средние показатели за '. $nutrition->name.'</p>'?><!--</td></tr>-->
<!--                <tr>-->
<!--                    <th>Показатели</th>-->
<!--                    <th>Значения</th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!--                <tr><td>A, мкг рет.экв</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_a']/$count_my_days, 2);?><!--</td></tr>-->
<!--                <tr><td>C, мг</td><td class="text-center"> --><?//= round($data[$nutrition->id]['vitamin_c']/$count_my_days, 2); ?><!--</tr>-->
<!--                <tr><td>B1, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_b1']/$count_my_days, 2);?><!--</td></tr>-->
<!--                <tr><td>B2, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_b2']/$count_my_days, 2);?><!--</td></tr>-->
<!--                <tr><td>D, мкг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_d']/$count_my_days, 2); ?><!--</td></tr>-->
<!--                <tr><td>PP, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_pp']/$count_my_days, 2);?><!--</td></tr>-->
<!--                <tr><td>Na, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_na']/$count_my_days, 3);?><!--</td></tr>-->
<!--                <tr><td>K, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_k']/$count_my_days, 2); ?><!--</td></tr>-->
<!--                <tr><td>Ca, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_ca']/$count_my_days, 3);?><!--</td></tr>-->
<!--                <tr><td>F, мкг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_f']/$count_my_days, 3); ?><!--</td></tr>-->
<!--                <tr><td>Mg, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_mg']/$count_my_days, 3);?><!--</td></tr>-->
<!--                <tr><td>P, мг</td><td class="text-center">--><?//=  round($data[$nutrition->id]['vitamin_p']/$count_my_days, 3); ?><!--</td></tr>-->
<!--                <tr><td>Fe, мг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_fe']/$count_my_days, 3);?><!--</td></tr>-->
<!--                <tr><td>I, мкг</td><td class="text-center">--><?//= round($data[$nutrition->id]['vitamin_i']/$count_my_days, 3); ?><!--</td></tr>-->
<!--                <tr><td>Se, мкг</td><td class="text-center">--><?//=round($data[$nutrition->id]['vitamin_se']/$count_my_days, 3); ?><!--</td></tr>-->
<!--                </tbody>-->
<!--            </table>-->
<!--                        --><?//}?>
<!---->
<!---->
<!--        --><?//}?>
<!--        --><?//if($post['yield'] == 1){?>
<!--<table class="table_th0 table-hover table-responsive last" >-->
<!--    <thead>-->
<!--    <tr><td colspan="2">--><?// echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">Средние показатели за день</p>'?><!--</td></tr>-->
<!--    <tr>-->
<!--        <th>Показатели</th>-->
<!--        <th>Значения</th>-->
<!--    </tr>-->
<!--    </thead>-->
<!--    <tbody>-->
<!--    <tr><td>A, мкг рет.экв</td><td class="text-center">--><?//= round($data_itog['vitamin_a'],1);?><!--</td></tr>-->
<!--    <tr><td>C, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_c'],1);?><!--</td></tr>-->
<!--    <tr><td>B1, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_b1'],1);?><!--</td></tr>-->
<!--    <tr><td>B2, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_b2'],1);?><!--</td></tr>-->
<!--    <tr><td>D, мкг</td><td class="text-center">--><?//= round($data_itog['vitamin_d'],1);?><!--</td></tr>-->
<!--    <tr><td>PP, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_pp'],1);?><!--</td></tr>-->
<!--    <tr><td>Na, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_na'],1);?><!--</td></tr>-->
<!--    <tr><td>K, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_k'],1);?><!--</td></tr>-->
<!--    <tr><td>Ca, мг</td><td class="text-center">--><?//=round($data_itog['vitamin_ca'],1);?><!--</td></tr>-->
<!--    <tr><td>F, мкг</td><td class="text-center">--><?//=round($data_itog['vitamin_f'],1);?><!--</td></tr>-->
<!--    <tr><td>Mg, мг</td><td class="text-center">--><?//=round($data_itog['vitamin_mg'],1);?><!--</td></tr>-->
<!--    <tr><td>P, мг</td><td class="text-center">--><?//= round($data_itog['vitamin_p'],1);?><!--</td></tr>-->
<!--    <tr><td>Fe, мг</td><td class="text-center">--><?//=round($data_itog['vitamin_fe'],1);?><!--</td></tr>-->
<!--    <tr><td>I, мкг</td><td class="text-center">--><?//=round($data_itog['vitamin_i'],1);?><!--</td></tr>-->
<!--    <tr><td>Se, мкг</td><td class="text-center">--><?//= round($data_itog['vitamin_se'],1);?><!--</td></tr>-->
<!--    </tbody>-->
<!--</table>-->
<!-- --><?//}?>

<br>
<!--<div class="text-center">-->
<!--    --><?//= Html::a('<span class="glyphicon glyphicon-download"></span> Скачать в Excel', ['export-menus-period?menu_id=' . $post['menu_id'].'&cycle='.$post['cycle'].'&him='.$post['days_id']],
//        [
//            'class'=>'btn btn-secondary',
//            'style' =>['width'=>'500px'],
//            'title' => Yii::t('yii', 'Скачать отчет в формате Excel'),
//            'data-toggle'=>'tooltip',
//        ])
//    ?>
<!--</div>-->
<br>


<?}?>


<?
//print_r($data);
$script = <<< JS

window.onload = function() {
    $('.field-menusdishes-created_at').hide();
    $('.field-menusdishes-nutrition_id').hide();
    }

    
    var field = $('#menusdishes-yield');
    field.on('change', function () {
           if (field.val() === "0") {
               console.log('222');
               $('.field-menusdishes-cycle').show();
               $('.field-menusdishes-created_at').hide();
               $('.field-menusdishes-nutrition_id').hide();
           }
            else if(field.val() === "1") {
               console.log('sss');
              //$('.field-menusdishes-cycle').hide();
              $('.field-menusdishes-nutrition_id').hide();
              $('.field-menusdishes-created_at').show();
           }
            else if(field.val() === "2") {
               console.log('sss');
              $('.field-menusdishes-cycle').show();
              $('.field-menusdishes-nutrition_id').show();
              $('.field-menusdishes-created_at').show();
           }
    });
    field.trigger('change');
    
    
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
