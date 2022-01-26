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

$this->title = 'Прогнозная накопительная ведомость';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->one();

$menu_cycle_count = $first_menu->cycle;
$menu_cycle = [];
for($i=1;$i<=$menu_cycle_count;$i++){
    $menu_cycle[$i] = $i;//массив из подходящи циклов
}

$norma_items = [0 => 'Показать по дням', 1 => 'Показать по приемам пищи'];
$chemistry_items = [0 => 'Скрыть пищевой и химический состав'/*, 1 => 'Показать пищевой и химический состав'*/];
$brutto_netto_items = [0 => 'Нетто', 1 => 'Брутто'];
$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_chemistry = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$params_norma = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$params_brutto_netto = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);
    $menu_cycle_count = $my_menus->cycle;
    $menu_cycle = [];
    for($i=1;$i<=$menu_cycle_count;$i++){
        $menu_cycle[$i] = $i;//массив из подходящи циклов
    }
    $my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();
    foreach($my_days as $m_day){
        $ids[] = $m_day->days_id;
    }
    $days = Days::find()->where(['id' => $ids])->all();


    $chemistry_items = [0 => 'Скрыть пищевой и химический состав', 1 => 'Показать пищевой и химический состав'];
    $norma_items = [0 => 'Показать по дням', 1 => 'Показать по приемам пищи'];
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_chemistry = ['class' => 'form-control', 'options' => [$post['display_him_feed'] => ['Selected' => true]]];
    $params_norma = ['class' => 'form-control', 'options' => [$post['display_normativ'] => ['Selected' => true]]];
    $params_brutto_netto = ['class' => 'form-control', 'options' => [$post['brutto_netto'] => ['Selected' => true]]];

    $count_my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->count()*$my_menus->cycle;
}
?>

<h1 class="text-center"><?= Html::encode($this->title) ?></h1>

<?if(empty($my_menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')))){?>
    <p class="text-center" style="color: red"><b>У Вас не созданы меню и не добавлены блюда.(Перейдите в раздел "Архив меню" или "Настройка меню")</b></p>
<?}?>

<?php $form = ActiveForm::begin([]); ?>


<style>
    .fixtable-fixed {
        position: fixed;
        top: 0;
        z-index: 101;
        background-color: #FCF8E4;
        border-bottom: 1px solid #ddd;
    }
    thead, th {
        background-color: #ede8b9!important;
        font-size: 15px;
        border: 1px solid #c2c2c2!important;
    }
    td{
        border: 1px solid #c2c2c2!important;
    }

</style>


<div class="container mb-30 mt-5">
    <div class="row">
        <div class="col-11 col-md-4">
            <?= $form->field($model, 'menu_id')->dropDownList($my_menus_items, [
                'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
                'onchange' => '
                  
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
        </div>

        <div class="col-11 col-md-3">
            <?= $form->field($model, 'display_normativ')->dropDownList($norma_items, $params_norma)->label('Нормативы'); ?>
        </div>

        <div class="col-11 col-md-3">
            <?= $form->field($model, 'display_him_feed')->dropDownList($chemistry_items, $params_chemistry)->label('Пищевой и химический состав'); ?>
        </div>



        <div class="col-11 col-md-2">
            <?= $form->field($model, 'brutto_netto')->dropDownList($brutto_netto_items, $params_brutto_netto)->label(); ?>
        </div>

    </div>

    <!--        Блок с заполняемыми инпутами для информации. id не менять иначе не сработает-->
    <?if(empty($post)){$menu_id = $first_menu->id;} else{$menu_id = $post['menu_id'];} /*print_r($model3->insert_info($menu_id, 'feeders_characters')); exit;*/?>
    <div class="row">
        <div class="col">
            <label><b>Характеристика питающихся</b>
                <input type="text" class="form-control" id="characters" disabled value="<?= $model3->insert_info($menu_id, 'feeders_characters');?>"></label>
        </div>
        <div class="col">
            <label><b>Возрастная категория</b>
                <input type="text" class="form-control" id="age" disabled value="<?=$model3->insert_info($menu_id, 'age_info');?>"></label>
        </div>
        <div class="col">
            <label><b>Срок действия меню</b>
                <input type="text" class="form-control" id="insert-srok" disabled value="<?=$model3->insert_info($menu_id, 'sroki');?>"></label>
        </div>
    </div>
    <!--        Конец блока с заполнением-->

    <div class="row">
        <div class="form-group" style="margin: 0 auto">
            <?= Html::submitButton('Посмотреть', ['name'=>'identificator', 'value' => 'view', 'class' => 'btn main-button-3 mb-3 beforeload']) ?>
            <button class="btn main-button-3 load" type="button" disabled style="display: none">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Посмотреть...
            </button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<br>


<?php if($post){?>
    <div class="row justify-content-center">
        <div class="col-auto">
            <table class="testt table_th0 table-responsive">
                <tr class="">
                    <th class="text-center align-middle" rowspan="2">№</th>
                    <th class="text-center align-middle text-nowrap" rowspan="2">Категория продукта</th>
                    <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->
                    <? if($post['display_normativ'] == 0){ ?>
                    <?foreach ($menu_cycle as $cycle){?>
                        <th class="text-center align-middle" colspan=<?=count($days)?>."'><?=$cycle.' неделя '?></th>
                    <? } ?>
                        <th class="text-center align-middle" rowspan="2">Сумма</th>
                        <th class="text-center align-middle" rowspan="2">Ср.знач.</th>
                        <th class="text-center align-middle" rowspan="2">Среднесуточный норматив</th>
                    <? } ?>
                    <!-- Динамический вывод названий столбцов 'НОРМАТИВ ЗА<ПРИЕМ ПИЩИ>'-->
                    <? if($post['display_normativ'] == 1){ ?>
                    <?foreach ($nutritions as $nutrition){?>

                            <th rowspan="1" class="text-center align-middle text-nowrap"><?=$nutrition->name?></th>
                        <? } ?>
                        <th rowspan="1" class="text-center align-middle text-nowrap">Сутки</th>
                    <? } ?>
                    <!-- Вывод названий столбцов 'П И Х СОСТАВ' $post['days_id']- идентификатор представления/скрытия из формы-->
                    <? if($post['display_him_feed'] == 1){ ?>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Белки</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Жиры</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Углеводы</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Эн. ценность</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Моно- и дисахариды</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Крахмал</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Лактоза</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Сахароза</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Клетчатка</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Зола, всего</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Зола, в т.ч.добавл.NaCl</th>
                        <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Орг.кислоты в пересчете на яблочную</th>
<!--                        <th colspan="9" class="text-center align-middle main-info-see">Минеральные вещества</th>-->
<!--                        <th colspan="7" class="text-center align-middle main-info-see">Витамины</th>-->
                        <th class="text-center align-middle text-nowrap main-info-see">Na, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">К, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">Ca, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">F, мкг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">Se, мкг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">I, мкг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">Mg, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">P, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">Fe, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">А, мкг рет.экв</th>
                        <th class="text-center align-middle text-nowrap main-info-see">B-каротин, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">В1, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">В2, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">С, мг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">D, мкг</th>
                        <th class="text-center align-middle text-nowrap main-info-see">РР, мг</th>
                    <? } ?>
                </tr>
                <tr class="">
                    <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->
                    <? if($post['display_normativ'] == 0){ ?>
                    <?foreach ($menu_cycle as $cycle){
                        foreach($days as $day){?>
                            <th class="text-center align-middle"><?=$day->name;?></th>
                        <? } ?>
                    <? } ?>
                    <? } ?>


                </tr>
                <!--  Если таблица по циклам и дням то формируем массив $m в котором уже сгенерированы все значения   -->
                <? if($post['display_normativ'] == 0){
                $m = [];
                $values = $model4->get_total_yield_category($post['menu_id']);
                    if($post['brutto_netto'] == 0){
                        $brutto_netto = 'net_weight';
                    }
                    if($post['brutto_netto'] == 1){
                        $brutto_netto = 'gross_weight';
                    }
                foreach ($values as $key => $value){
                    $m[$value['products_category_id'].'_'.$value['cycle'].'_'.$value['days_id']] = $m[$value['products_category_id'].'_'.$value['cycle'].'_'.$value['days_id']] + ($value[$brutto_netto] * ($value['menus_yield']/ $value['dishes_yield']));
                      }
                } ?>

                <!--  Если таблица по приемам пищи то формируем массив $m в котором уже сгенерированы все значения   -->
                <? if($post['display_normativ'] == 1){
                    $values = $model4->get_total_yield_nutrition_category($post['menu_id']);
                    if($post['brutto_netto'] == 0){
                        $brutto_netto = 'net_weight';
                    }
                    if($post['brutto_netto'] == 1){
                        $brutto_netto = 'gross_weight';
                    }
                    $m = [];
                    foreach ($values as $value){
                        $m[$value['products_category_id'].'_'.$value['nutrition_id']] = $m[$value['products_category_id'].'_'.$value['nutrition_id']] + ($value['net_weight'] * ($value['menus_yield']/ $value['dishes_yield']));?>
                    <?}
                }?>

                <? $count = 0; $mas_itogo_cycle = []; $mas_itogo_feed_him = [];
                foreach($categories as $category){ $count++; ?>
                <!--массив с пищевым химическим составом                -->
                <? if($post['display_him_feed'] == 1){ ?>
                <? $mas = $category->get_prognos_storage_feed_him_sostav_for_category($category->id, $post['menu_id']);?>
                <?}?>



                    <tr>
                        <!-- вывод Категории -->
                        <td class="text-center align-middle"><?=$count?></td>
                        <td class="text-left align-middle"><?=$category->name?></td>

                        <!-- Заполнение таблицы данными блок с днями-->
                        <? if($post['display_normativ'] == 0){
                            $itog = 0;?>
                        <?foreach ($menu_cycle as $cycle){
                            foreach($days as $day){?>
                                <?if (array_key_exists($category->id.'_'.$cycle.'_'.$day->id, $m)) { ?>
                                    <td class="text-center"><?=round($m[$category->id.'_'.$cycle.'_'.$day->id],1); $itog = $itog + round($m[$category->id.'_'.$cycle.'_'.$day->id],1);?></td>
                                <?}else{?>
                                    <td class="text-center">-</td>
                                <?}
                            }
                         }?>
                         <td class="text-center"><?='<b>'.$itog.'</b>';?></td>
                            <td><?='<b>'.round($itog/$count_my_days,1).'</b>';?></td>
                            <td class="text-center align-middle"><?=$model4->get_prognos_storage_normativ($category->id, $post['menu_id']);?></td>
                        <?}?>


                        <!-- Заполнение таблицы данными блок с нормативами за прием пищи-->
                        <? if($post['display_normativ'] == 1){ ?>
                            <?$itog =0;?>
                        <?foreach ($nutritions as $nutrition){?>
                                <?if (array_key_exists($category->id.'_'.$nutrition->id, $m)) { ?>
                                    <td class="text-center"><?=round($m[$category->id.'_'.$nutrition->id]/$count_my_days,1); $itog = $itog + round($m[$category->id.'_'.$nutrition->id]/$count_my_days,1);?></td>
                                <?}else{?>
                                    <td class="text-center">-</td>
                                <?}?>
                        <?}?>
                            <td><?=$itog;?></td>

                        <?}?>


                        <? if($post['display_him_feed'] == 1){ ?>
                            <td><? $protein = round($mas['protein'],1); echo $protein; $mas_itogo_feed_him['protein'] = $protein + $mas_itogo_feed_him['protein']; ?></td>
                            <td><? $fat = round($mas['fat'],1); echo $fat; $mas_itogo_feed_him['fat'] = $fat + $mas_itogo_feed_him['fat']; ?></td>
                            <td><? $carbohydrates_total = round($mas['carbohydrates_total'],1); echo $carbohydrates_total; $mas_itogo_feed_him['carbohydrates_total'] = $carbohydrates_total + $mas_itogo_feed_him['carbohydrates_total']; ?></td>
                            <td><? $energy_kkal = ($protein * 4) + ($fat * 9) + ($carbohydrates_total * 4); echo $energy_kkal; $mas_itogo_feed_him['energy_kkal'] = $energy_kkal + $mas_itogo_feed_him['energy_kkal']; ?></td>
                            <td><? $carbohydrates_saccharide = round($mas['carbohydrates_saccharide'],1); echo $carbohydrates_saccharide; $mas_itogo_feed_him['carbohydrates_saccharide'] = $carbohydrates_saccharide + $mas_itogo_feed_him['carbohydrates_saccharide']; ?></td>
                            <td><? $carbohydrates_starch = round($mas['carbohydrates_starch'],1); echo $carbohydrates_starch; $mas_itogo_feed_him['carbohydrates_starch'] = $carbohydrates_starch + $mas_itogo_feed_him['carbohydrates_starch']; ?></td>
                            <td><? $carbohydrates_lactose = round($mas['carbohydrates_lactose'],1); echo $carbohydrates_lactose; $mas_itogo_feed_him['carbohydrates_lactose'] = $carbohydrates_lactose + $mas_itogo_feed_him['carbohydrates_lactose']; ?></td>
                            <td><? $carbohydrates_sacchorose = round($mas['carbohydrates_sacchorose'],1); echo $carbohydrates_sacchorose; $mas_itogo_feed_him['carbohydrates_sacchorose'] = $carbohydrates_sacchorose + $mas_itogo_feed_him['carbohydrates_sacchorose']; ?></td>

                            <td><?=round($mas['carbohydrates_cellulose'],1);?></td>
                            <td><?=round($mas['dust_total'],1);?></td>
                            <td><?=round($mas['dust_nacl'],1);?></td>

                            <td><?=round($mas['apple_acid'],1);?></td>
                            <td><?=round($mas['na'],1);?></td>
                            <td><?=round($mas['k'],1);?></td>

                            <td><?=round($mas['ca'],1);?></td>
                            <td><?=round($mas['f'],1);?></td>
                            <td><?=round($mas['se'],1);?></td>

                            <td><?=round($mas['i'],1);?></td>
                            <td><?=round($mas['mg'],1);?></td>
                            <td><?=round($mas['p'],1);?></td>

                            <td><?=round($mas['fe'],1);?></td>
                            <td><?=round($mas['vitamin_a'],1);?></td>
                            <td><?=round($mas['vitamin_b_carotene'],1);?></td>

                            <td><?=round($mas['vitamin_b1'],1);?></td>
                            <td><?=round($mas['vitamin_b2'],1);?></td>
                            <td><?=round($mas['vitamin_c'],1);?></td>
                            <td><?=round($mas['vitamin_d'],1);?></td>
                            <td><?=round($mas['vitamin_pp'],1);?></td>
                        <? } ?>

                        <?}?>
            </table>
        </div>
    </div>
    <br>
    <div class="text-center">
        <?= Html::a('<span class="glyphicon glyphicon-download"></span> Скачать в Excel', ['export-prognos-storage?menu_id=' . $post['menu_id'].'&normativ='.$post['display_normativ'].'&brutto_netto='.$post['brutto_netto']],
            [
                'class'=>'btn btn-secondary',
                'style' =>['width'=>'500px'],
                'title' => Yii::t('yii', 'Скачать отчет в формате Excel'),
                'data-toggle'=>'tooltip',
            ])
        ?>
    </div>
<?}?>



<?

$script = <<< JS

$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
  
});

JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
