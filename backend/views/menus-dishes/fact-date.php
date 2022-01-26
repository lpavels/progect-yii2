<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\Days;
use common\models\MenusDays;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Фактическое меню за день';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->one();

if(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr') || Yii::$app->user->can('minobr')){
    $my_menus = Menus::find()->where(['organization_id' => Yii::$app->session['organization_id'], 'status_archive' => 0])->all();
    $my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
    $first_menu = Menus::find()->where(['organization_id' => Yii::$app->session['organization_id'], 'status_archive' => 0])->one();
}

$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
//$post['date'] = '';
if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
}

?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <?if(empty($my_menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr') || Yii::$app->user->can('minobr')))){?>
        <p class="text-center" style="color: red"><b>У Вас не созданы меню и не добавлены блюда.(Перейдите в раздел "Архив меню" или "Настройка меню")</b></p>
    <?}?>

    <?php $form = ActiveForm::begin(); ?>
    <div class="container mb-30 mt-5">
        <div class="row">
            <div class="col">
                <?= $form->field($model2, 'menu_id')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
                    'onchange' => '
                  $.get("../menus-dishes/insertcharacters?id="+$(this).val(), function(data){
                  console.log(data);
                    $("input#characters").val(data);
                  });
                  $.get("../menus-dishes/insertage?id="+$(this).val(), function(data){
                  console.log(data);
                    $("input#age").val(data);
                  });
                  $.get("../menus-dishes/insertdays?id="+$(this).val(), function(data){
                  console.log(data);
                    $("#insert-days").text(data);
                  });
                  $.get("../menus-dishes/insertsrok?id="+$(this).val(), function(data){
                  console.log(data);
                    $("#insert-srok").val(data);
                  });'


                ]); ?>
            </div>

            <div class="col">
                <?= $form->field($model2, 'date')->textInput(['class'=>'datepicker-here form-control', 'autocomplete' => 'off', 'value' => $post['date']]) ?>
            </div>

        </div>
        <?if(empty($post)){$menu_id = $first_menu->id;} else{$menu_id = $post['menu_id'];}?>
        <div class="row">
            <div class="col">
                <label><b>Характеристика питающихся</b>
                <input type="text" class="form-control" id="characters" disabled value="<?= $model->insert_info($menu_id, 'feeders_characters');?>"></label>
            </div>
            <div class="col">
                <label><b>Возрастная категория</b>
                    <input type="text" class="form-control" id="age" disabled value="<?=$model->insert_info($menu_id, 'age_info');?>"></label>
            </div>
            <div class="col">
                <label><b>Срок действия меню</b>
                    <input type="text" class="form-control" id="insert-srok" disabled value="<?=$model->insert_info($menu_id, 'sroki');?>"></label>
            </div>


        </div>
        <div class="row">
            <div class="col">
                <b>Дни меню: </b> <p id="insert-days"><?=$model->insert_info($menu_id, 'days');?></p>
            </div>
        </div>
        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['class' => 'btn main-button-3 beforeload']) ?>
<!--                <button class="btn main-button-3 load" type="button" disabled style="display: none">-->
<!--                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>-->
<!--                    Посмотреть...-->
<!--                </button>-->
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<? if(!empty($post)){ echo '<br><br><b><p class="text-center" style="font-size: 20px;">'.$cycle.' неделя '.$model->get_days($day_of_week).' '.$post['date'].'</p></b>';} ?>
    <div class="row justify-content-center">
        <div class="col-auto">
            <?php $super_total_yield = 0; $super_total_protein = 0; $super_total_fat = 0; $super_total_carbohydrates_total = 0; $super_total_energy_kkal = 0; $super_total_vitamin_a = 0; $super_total_vitamin_c = 0; $super_total_vitamin_b1 = 0; $super_total_vitamin_b2 = 0; $super_total_vitamin_d = 0; $super_total_vitamin_pp = 0; $super_total_na = 0; $super_total_k = 0; $super_total_ca = 0; $super_total_f = 0; $super_total_se = 0;?>
            <?if(!empty($nutritions)){?>
            <? foreach($nutritions as $nutrition){?>


            <div class="block" style="margin-top: 10px;">
                <table class="table_th0 table-hover table-responsive last" >
                    <thead>
                    <tr class="text-center"><? echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $nutrition->name .'</p>'?></tr>
                    <tr>
                        <th class="text-center align-middle" rowspan="2" style="min-width: 200px">№ рецептуры</th>
                        <th class="text-center align-middle" rowspan="2" style="min-width: 400px">Название блюда</th>
                        <th class="text-center align-middle" rowspan="2">Выход</th>
                        <th class="text-center align-middle" rowspan="2">Белки</th>
                        <th class="text-center align-middle" rowspan="2">Жиры</th>
                        <th class="text-center align-middle" rowspan="2">Углеводы</th>
                        <th class="text-center align-middle" rowspan="2">Эн. ценность</th>
                        <? if($post['days_id'] == 1){?>
                            <th class="text-center" colspan="6">Витамины</th>
                            <th class="text-center" colspan="10">Минеральные вещества</th>
                        <?}?>
                    </tr>
                    <tr>

                            <th class="text-center">A, мкг рет.экв</th>
                            <th class="text-center">C, мг</th>
                            <th class="text-center">B1, мг</th>
                            <th class="text-center">B2, мг</th>
                            <th class="text-center">D, мкг</th>
                            <th class="text-center">PP, мг</th>
                            <th class="text-center">Na, мг</th>
                            <th class="text-center">K, мг</th>
                            <th class="text-center">Ca, мг</th>
                            <th class="text-center">F, мкг</th>
                            <th class="text-center">Mg, мг</th>
                            <th class="text-center">P, мг</th>
                            <th class="text-center">Fe, мг</th>
                            <th class="text-center">I, мкг</th>
                            <th class="text-center">Se, мкг</th>

                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 0;
                    $yield = 0; $indicator = 0; $energy_kkal = 0; $protein = 0; $fat = 0; $carbohydrates_total = 0; $vitamins = [];?>
                <?foreach($menus_dishes as $key => $m_dish){?>
                    <?//echo $nutrition->id. ' ==='.$m_dish->nutrition_id;?>
                        <? if($nutrition->id == $m_dish->nutrition_id){?>
                        <? $count++;?>
                        <!--ВЫВОД ПОСТРОЧНО КАЖДОГО БЛЮДА В РАЗАРЕЗЕ ПРИЕМА ПИЩИ-->
                        <tr data-id="<?= $m_dish->id;?>">
                            <td class="text-center"><?= $m_dish->get_techmup($m_dish->dishes_id)?></td>
                            <td><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
                            <td class="text-center"><?= $m_dish->yield; $yield = $yield + $m_dish->yield;?></td>
                            <td class="text-center"><? $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'),1); echo $protein_dish; $protein = $protein_dish + $protein;?></td>
                            <td class="text-center"><? $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'),1); echo $fat_dish; $fat = $fat_dish + $fat;?></td>
                            <td class="text-center"><? $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'),1); echo $carbohydrates_total_dish; $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total; ?></td>
                            <td class="text-center"><? $kkal = round($m_dish->get_kkal_dish($m_dish->id),1); echo $kkal; $energy_kkal = $energy_kkal + $kkal; ?></td>

                            <td class="text-center"><? $vitamins['vitamin_a'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'),2); echo $vitamins['vitamin_a']; $vitamin_a = $vitamin_a + $vitamins['vitamin_a']?></td>
                            <td class="text-center"><? $vitamins['vitamin_c'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'),2); echo $vitamins['vitamin_c']; $vitamin_c = $vitamin_c + $vitamins['vitamin_c']?></td>
                            <td class="text-center"><? $vitamins['vitamin_b1'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'),2); echo $vitamins['vitamin_b1']; $vitamin_b1 = $vitamin_b1 + $vitamins['vitamin_b1']?></td>
                            <td class="text-center"><? $vitamins['vitamin_b2'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'),2); echo $vitamins['vitamin_b2']; $vitamin_b2 = $vitamin_b2 + $vitamins['vitamin_b2']?></td>
                            <td class="text-center"><? $vitamins['vitamin_d'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'),2); echo $vitamins['vitamin_d']; $vitamin_d = $vitamin_d + $vitamins['vitamin_d']?></td>
                            <td class="text-center"><? $vitamins['vitamin_pp'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'),2); echo $vitamins['vitamin_pp']; $vitamin_pp = $vitamin_pp + $vitamins['vitamin_pp']?></td>
                            <td class="text-center"><? $vitamins['na'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'),2); echo $vitamins['na']; $na = $na + $vitamins['na']?></td>
                            <td class="text-center"><? $vitamins['k'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'),2); echo $vitamins['k']; $k = $k + $vitamins['k']?></td>
                            <td class="text-center"><? $vitamins['ca'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'),2); echo $vitamins['ca']; $ca = $ca + $vitamins['ca']?></td>
                            <td class="text-center"><? $vitamins['f'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'),2); echo $vitamins['f']; $f = $f + $vitamins['f']?></td>
                            <td class="text-center"><? $vitamins['mg'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'mg'),2); echo $vitamins['mg']; $mg = $mg + $vitamins['mg']?></td>
                            <td class="text-center"><? $vitamins['p'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'p'),2); echo $vitamins['p']; $p = $p + $vitamins['p']?></td>
                            <td class="text-center"><? $vitamins['fe'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'fe'),2); echo $vitamins['fe']; $fe = $fe + $vitamins['fe']?></td>
                            <td class="text-center"><? $vitamins['i'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'i'),2); echo $vitamins['i']; $i = $i + $vitamins['i']?></td>
                            <td class="text-center"><? $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'),2); echo $vitamins['se']; $se = $se + $vitamins['se']?></td>
                        <? unset($menus_dishes[$key]); ?>
                        </tr>
                <?}else{break;}?>
                <?}?>
                <? if($count > 0){?>
                    <!--ВЫВОД СТРОЧКИ "ИТОГО" В РАЗРЕЗЕ КАЖДОГО ПРИЕМА ПИЩИ-->

                    <tr class="table-primary">
                    <td colspan="2">Итого за <? echo $nutrition->name?></td>
                    <!--МАССИВ data[<id приема пищи>][<название поля>] ХРАНИТ В СЕБЕ СРЕДНИЕ ПОКАЗАТЕЛИ ЗА <прием пищи>(т.е сумма за все завтраки, обеды и тд..) (самый низ таблицы)
                        $super_total_<название_поля> - ХРАНИТ ЗНАЧЕНИЕ 'ИТОГО ЗА ДЕНЬ'. РАСЧИТЫВАЕТСЯ ВСЕ В td и ниже вставляется в другие td-->
                    <td class="text-center"><? echo $yield; $data[$nutrition->id]['yield'] = $data[$nutrition->id]['yield'] + $yield; $super_total_yield = $super_total_yield + $yield;?></td>
                    <td class="text-center"><? echo $protein; $data[$nutrition->id]['protein'] = $data[$nutrition->id]['protein'] + $protein; $super_total_protein = $super_total_protein + $protein;?></td>
                    <td class="text-center"><? echo $fat; $data[$nutrition->id]['fat'] = $data[$nutrition->id]['fat'] + $fat; $super_total_fat = $super_total_fat + $fat;?></td>
                    <td class="text-center"><? echo $carbohydrates_total; $data[$nutrition->id]['carbohydrates_total'] = $data[$nutrition->id]['carbohydrates_total'] + $carbohydrates_total; $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;?></td>
                    <td class="text-center"><? echo $energy_kkal; $data[$nutrition->id]['energy_kkal'] = $data[$nutrition->id]['energy_kkal'] + $energy_kkal; $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;?></td>
                        <td class="text-center"><?= $vitamin_a; $data[$nutrition->id]['vitamin_a'] = $data[$nutrition->id]['vitamin_a'] + $vitamin_a; $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;?></td>
                        <td class="text-center"><?= $vitamin_c; $data[$nutrition->id]['vitamin_c'] = $data[$nutrition->id]['vitamin_c'] + $vitamin_c; $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;?></td>
                        <td class="text-center"><?= $vitamin_b1; $data[$nutrition->id]['vitamin_b1'] = $data[$nutrition->id]['vitamin_b1'] + $vitamin_b1; $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;?></td>
                        <td class="text-center"><?= $vitamin_b2; $data[$nutrition->id]['vitamin_b2'] = $data[$nutrition->id]['vitamin_b2'] + $vitamin_b2; $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;?></td>
                        <td class="text-center"><?= $vitamin_d; $data[$nutrition->id]['vitamin_d'] = $data[$nutrition->id]['vitamin_d'] + $vitamin_d; $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d;?></td>
                        <td class="text-center"><?= $vitamin_pp; $data[$nutrition->id]['vitamin_pp'] = $data[$nutrition->id]['vitamin_pp'] + $vitamin_pp; $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;?></td>
                        <td class="text-center"><?= $na; $data[$nutrition->id]['vitamin_na'] = $data[$nutrition->id]['vitamin_na'] + $na; $super_total_na = $super_total_na + $na;?></td>
                        <td class="text-center"><?= $k; $data[$nutrition->id]['vitamin_k'] = $data[$nutrition->id]['vitamin_k'] + $k; $super_total_k = $super_total_k + $k;?></td>
                        <td class="text-center"><?= $ca; $data[$nutrition->id]['vitamin_ca'] = $data[$nutrition->id]['vitamin_ca'] + $ca; $super_total_ca = $super_total_ca + $ca;?></td>
                        <td class="text-center"><?= $f; $data[$nutrition->id]['vitamin_f'] = $data[$nutrition->id]['vitamin_f'] + $f; $super_total_f = $super_total_f + $f;?></td>
                        <td class="text-center"><?= $mg; $data[$nutrition->id]['vitamin_mg'] = $data[$nutrition->id]['vitamin_mg'] + $mg; $super_total_mg = $super_total_mg + $mg;?></td>
                        <td class="text-center"><?= $p; $data[$nutrition->id]['vitamin_p'] = $data[$nutrition->id]['vitamin_p'] + $p; $super_total_p = $super_total_p + $p;?></td>
                        <td class="text-center"><?= $fe; $data[$nutrition->id]['vitamin_fe'] = $data[$nutrition->id]['vitamin_fe'] + $fe; $super_total_fe = $super_total_fe + $fe;?></td>
                        <td class="text-center"><?= $i; $data[$nutrition->id]['vitamin_i'] = $data[$nutrition->id]['vitamin_i'] + $i; $super_total_i = $super_total_i + $i;?></td>
                        <td class="text-center"><?= $se; $data[$nutrition->id]['vitamin_se'] = $data[$nutrition->id]['vitamin_se'] + $se; $super_total_se = $super_total_se + $se;?></td>
                    </tr>
                    <tr class="table-success">
                        <td>Рекомендуемая величина</td>
                        <td></td>
                        <td></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'protein_middle_weight');?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'fat_middle_weight');?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'carbohydrates_middle_weight');?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'middle_kkal');?></td>
                        <td class="text-center"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr class="table-warning">
                        <!--Для бжу функции есть в модели, но чтобы не нагружать страницу расчитывается через переменные-->
                        <td colspan="2">Соотношение БЖУ</td>
                        <td></td>
                        <td class="text-center">1</td>
                        <td class="text-center"><? echo round(($fat/$protein), 2) ?></td>
                        <td class="text-center"><? echo round(($carbohydrates_total/$protein), 2) ?></td>

                    </tr>
                    <?}?>
                    <?}?>
                    <tr class="table-danger itog_day">
                        <td>Итого за день</td>
                        <td></td>
                        <td class="text-center"><?= $super_total_yield; ?></td>
                        <td class="text-center"><?= $super_total_protein; ?></td>
                        <td class="text-center"><?= $super_total_fat;?></td>
                        <td class="text-center"><?= $super_total_carbohydrates_total; ?></td>
                        <td class="text-center"><?= $super_total_energy_kkal; ?></td>
                            <td class="text-center"><?= $super_total_vitamin_a; ?></td>
                            <td class="text-center"><?= $super_total_vitamin_c; ?></td>
                            <td class="text-center"><?= $super_total_vitamin_b1; ?></td>
                            <td class="text-center"><?= $super_total_vitamin_b2; ?></td>
                            <td class="text-center"><?= $super_total_vitamin_d; ?></td>
                            <td class="text-center"><?= $super_total_vitamin_pp; ?></td>
                            <td class="text-center"><?= $super_total_na; ?></td>
                            <td class="text-center"><?= $super_total_k; ?></td>
                            <td class="text-center"><?= $super_total_ca; ?></td>
                            <td class="text-center"><?= $super_total_f; ?></td>
                            <td class="text-center"><?= $super_total_mg; ?></td>
                            <td class="text-center"><?= $super_total_p; ?></td>
                            <td class="text-center"><?= $super_total_fe; ?></td>
                            <td class="text-center"><?= $super_total_i; ?></td>
                            <td class="text-center"><?= $super_total_se; ?></td>

                    </tr>

                    <tr class="table-success">
                        <td colspan="2">Рекомендуемая величина за день</td>

                        <td></td>
                        <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'protein_middle_weight');?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'fat_middle_weight');?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'carbohydrates_middle_weight');?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'middle_kkal');?></td>
                            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>

                    </tr>
                    <tr class="procent_day table-danger">
                        <td colspan="2">Процентное соотношение БЖУ за день</td>
                        <td></td>
                        <td class="text-center"><?= '100%'; ?></td>
                        <td class="text-center"><? if($super_total_protein != 0){echo round(($super_total_fat/$super_total_protein) * 100, 2) .'%';} else{ echo 0;}?></td>
                        <td class="text-center"><? if($super_total_protein != 0){echo  round(($super_total_carbohydrates_total/$super_total_protein) * 100, 2) .'%'; ;} else{ echo 0;}?></td>
                    </tr>
                </table>
                <div class="text-center mt-2">
                <?= Html::a('<span class="glyphicon glyphicon-download"></span> Скачать информацию в PDF формате',
                    [
                        'fact-date-pdf?menu_id=' . $post['menu_id'] .'&date='.$post['date']
                    ],
                    [
                        'class'=>'btn btn-outline-secondary',
                        'title' => Yii::t('yii', 'Вы можете скачать меню за день в PDF формате'),
                        'data-toggle'=>'tooltip',
                    ])
                ?>
                </div>


            <?php } ?>
        </div>
    </div>

<?
$script = <<< JS
//Прикрепление строчек итого за день и бжу за день к последней таблице
$(".last:last").append($(".itog_day"));
$(".last:last").append($(".procent_day"));


/*$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
  
});*/


/*$( ".beforeload" ).click(function() {
  $('.beforeload').append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});*/
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>









<!--        <tr class="itog_day">-->
<!--            <td>Итого за день</td>-->
<!--            <td>--><?//= $model->get_super_total_yield($post['menu_id'], $post['cycle'], $post['days_id'], 'super_total'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_field($post['menu_id'], $post['cycle'], $post['days_id'], 'super_total', 'protein'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_field($post['menu_id'], $post['cycle'], $post['days_id'], 'super_total', 'fat'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_field($post['menu_id'], $post['cycle'], $post['days_id'], 'super_total', 'carbohydrates_total'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_field($post['menu_id'], $post['cycle'], $post['days_id'], 'super_total', 'energy_kkal'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], 'vitamin_a'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], 'vitamin_c'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], 'vitamin_b1'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], 'vitamin_b2'); ?><!--</td>-->
<!--            <td>--><?//= $model->get_super_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], 'vitamin_d'); ?><!--</td>-->
<!--        </tr>-->