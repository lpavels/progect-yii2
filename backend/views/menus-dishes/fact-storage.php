<?php

use common\models\MenusDishes;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\Days;
use common\models\MenusDays;
use yii\helpers\ArrayHelper;



/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Фактическая накопительная ведомость(раздел в разработке)';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();

$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];

$norma_items = [0 => 'Скрыть нормативы', 1 => 'Показать нормативы с разницей', 2 => 'Показать нормативы без разницы'];
$chemistry_items = [0 => 'Скрыть пищевой и химический состав', 1 => 'Показать пищевой и химический состав'];
$brutto_netto_items = [0 => 'Нетто', 1 => 'Брутто'];
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
    foreach($my_days as $m_day){
        if ($m_day->days_id != 7){
            $ids_for_php[] = $m_day->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
        }
        else{
            $ids_for_php[] = 0;
        }
    }
    /*if(in_array(0, $ids_for_php)){
        $max_index_day = 0;
    }
    else{
        $max_index_day = max($ids_for_php);
    }*/
    $max_index_day = min($ids);
   // print_r($max_index_day);

    $days = Days::find()->where(['id' => $ids])->all();
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_chemistry = ['class' => 'form-control', 'options' => [$post['display_him_feed'] => ['Selected' => true]]];
    $params_norma = ['class' => 'form-control', 'options' => [$post['display_normativ'] => ['Selected' => true]]];
    $params_brutto_netto = ['class' => 'form-control', 'options' => [$post['brutto_netto'] => ['Selected' => true]]];

    $date_end = strtotime($post['date_end']);
    $current_date = strtotime($post['date']);
    //print_r($ids_for_php);
    //print_r($categories);







    $start_date = date('d.m.Y', $my_menus->date_start);//Дата старта меню
    $day_of_week = date("w", strtotime($post['date']));//День недели выбранной даты
    $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
    /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
    /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
    if ($day_of_week_start_date == 0)
    {
        $day_of_week_start_date = 7;
    }
    if ($day_of_week == 0)
    {
        $day_of_week = 7;
    }
    /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
    $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

    $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
    $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
    $count_week = ceil((((strtotime($post['date']) - $my_menus->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

    $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
    /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
    if ($day_of_week == 1)
    {
        $cycle = $count_week - 1;
    }
    /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
    if ($count_week == 0)
    {
        $cycle = 1;
    }

    /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
    while ($cycle > $my_menus->cycle)
    {
        $cycle = $cycle - $my_menus->cycle;
    }
    if ($cycle == 0)
    {
        $cycle = $my_menus->cycle;
    }
    $cycle2 = $cycle;
    /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/
    print_r($date_end);
}

//$ccc = new \common\models\ProductsCategory();
//$mas = $ccc->get_fact_feed_him_sostav_for_category(39, 21);
//print_r($mas)


?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <div class="container mb-30">
        <div class="row">
            <div class="col">
                <?= $form->field($model3, 'menu_id')->dropDownList($my_menus_items, [
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
                <?= $form->field($model3, 'date')->textInput(['class'=>'datepicker-here form-control', 'autocomplete'=>'off', 'value' => $post['date']])->label('Дата начала') ?>
            </div>

            <div class="col">
                <?= $form->field($model3, 'date_end')->textInput(['class'=>'datepicker-here form-control',  'autocomplete'=>'off', 'value' => $post['date_end']])->label('Дата окончания') ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?= $form->field($model3, 'display_him_feed')->dropDownList($chemistry_items, $params_chemistry)->label('Пищевой и химический состав') ?>
            </div>

            <div class="col">
                <?= $form->field($model3, 'display_normativ')->dropDownList($norma_items, $params_norma)->label('Нормативы') ?>
            </div>

            <div class="col">
                <?= $form->field($model3, 'brutto_netto')->dropDownList($brutto_netto_items, $params_brutto_netto)->label() ?>
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


<?php //print_r($products)?>
<?php if($post){?>
    <div class="row justify-content-center">
        <div class="col-auto">
            <table class="testt table_th0 table-responsive">
                <tr class="">
                    <th class="text-center align-middle " rowspan="2">№</th>
                    <th class="text-center align-middle text-nowrap " rowspan="2">Категория продукта</th>
                    <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ><ДАТА>'-->
                    <?//foreach ($menu_cycle as $cycle){?>
                    <?while($current_date <= $date_end){?>
                        <?if (in_array(date("w", $current_date), $ids_for_php)){?>
                        <th class="text-center"><?=date("d.m.Y", $current_date).'<br>'.$model->get_days(date("w", $current_date));?></th>
                        <?php $current_date = $current_date + 86400; ?>
                        <? } else{$current_date = $current_date + 86400;}?>
                    <? } ?>
                    <!-- Динамический вывод названий столбцов 'НОРМАТИВ ЗА<ПРИЕМ ПИЩИ>'-->
                    <?foreach ($nutritions as $nutrition){?>
                        <? if($post['display_normativ'] == 1 || $post['display_normativ'] == 2){ ?>
                            <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Норматив за <?=$nutrition->name?></th>
                        <? } ?>
                        <? if($post['display_normativ'] == 1){ ?>
                            <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Разница с нормативом, г</th>
                            <th rowspan="2" class="text-center align-middle text-nowrap main-info-see">Разница с нормативом, %</th>
                        <? } ?>
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
                        <th class="text-center align-middle text-nowrap main-info-see">Na, мг</th>-->
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
<!--                    --><?//foreach ($menu_cycle as $cycle){
//                        foreach($days as $day){?>
<!--                            <th class="text-center align-middle main-info-see">--><?//=$day->name;?><!--</th>-->
<!--                        --><?// } ?>
<!--                    --><?// } ?>

                </tr>


<!--            ДЛЯ ОПРЕДЕЛЕНИЯ МАССИВА ЧИСЛО_ЦИКЛ_ДЕНЬ НЕДЕЛИ-->
                <? $current_date = strtotime($post['date']); ?>
                <?$mas_for_him_feed = []; while($current_date <= $date_end){?>
                    <?if (in_array(date("w", $current_date), $ids_for_php)){?>
                        <?if($current_date == strtotime($post['date'])){ $mas_for_him_feed[] = $current_date.'_'.$cycle;?>
<!--                                                            <td class="text-center">--><?//= $cycle.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                        <?}else{?>
                            <? if($max_index_day == date("w", $current_date)){ $cycle2 = $cycle2 + 1;
                                if($cycle2 <= $my_menus->cycle){ $mas_for_him_feed[] = $current_date.'_'.$cycle2;?>
<!--                                                                          <td class="text-center">--><?//= $cycle2.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                                <?}else{
                                    $cycle2 = 1; $mas_for_him_feed[] = $current_date.'_'.$cycle2;?>
<!--                                                                            <td class="text-center">--><?//= $cycle2.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                                <?}
                            }else{ $mas_for_him_feed[] = $current_date.'_'.$cycle2;?>
<!--                                                                     <td class="text-center">--><?//= $cycle2.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                            <?}?>
                        <?}?>
                        <?php $current_date = $current_date + 86400; ?>
                    <? } else{$current_date = $current_date + 86400;}?>
                <? } ?>
<!--                ЭТО КОНЕЦ-->
                <? $c = new \common\models\ProductsCategory(); $mas = $c->get_fact_feed_him_sostav_for_category(32, $post['menu_id'], strtotime($post['date']), strtotime($post['date_end']), $mas_for_him_feed); print_r($mas);exit;?>


<?
/*$masiv = '1585515600_1';
$elements = explode("_", $masiv);
$data = $elements[0];
$cycle = $elements[1];
$day = date("w", $elements[0]);
print_r($cycle.'<br>');print_r($data.'<br>');print_r($day.'<br>');/*exit;*/
/*$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => 21, 'cycle' => $cycle, 'days_id' => $day])->all(); print_r($menus_dishes);
exit;*/
?>
<? print_r($mas_for_him_feed);?>
                <? $count = 0; foreach($categories as $category){ $count++; $cycle2 = $cycle; ?>
                    <!-- В ЭТОТ МАССИВ СКЛАДЫВАЕТСЯ ВЕСЬ ХИМ И ПИЩ СОСТАВ ЗА ВСЮ КАТЕГОРИЮ, ПРИ КАЖДОМ НОВОМ ПРОБЕГЕ МАССИВ БУДЕТ ФОРМИРОВАТЬСЯ ЗАНОВА-->
                    <? $mas = $category->get_fact_feed_him_sostav_for_category($category->id, $post['menu_id'], strtotime($post['date']), strtotime($post['date_end']), $mas_for_him_feed);?>
                    <!-- В ЭТОТ МАССИВ СКЛАДЫВАЕТСЯ ВЕСЬ ХИМ И ПИЩ СОСТАВ ЗА ВСЮ КАТЕГОРИЮ, ПРИ КАЖДОМ НОВОМ ПРОБЕГЕ МАССИВ БУДЕТ ФОРМИРОВАТЬСЯ ЗАНОВА-->
                    <? //$mas_yield = $category->get_fact_storage_yield_for_category($category->id, $post['menu_id'], $cycle, strtotime($post['date']), $date_end);?>
                <tr>
                    <!-- вывод Категории -->
                    <td class="text-center align-middle"><?=$count?></td>
                    <td class="text-left align-middle"><?=$category->name?></td>
                    <? $current_date = strtotime($post['date']); ?>
                    <!-- ВЫВОД ВЫХОДОВ ПО КАЖДОЙ КАТЕГОРИИ, ЕСЛИ В ТЕКУЩИЙ ДЕНЬ БЫЛО ФАКТИЧЕСКОЕ МЕНЮ, ТО БУДУТ ПОКАЗАНЫ РАСЧЕТЫ НА ФАКТИЧЕСКОЕ МЕНЮ -->

                    <?while($current_date <= $date_end){?>
                        <?if (in_array(date("w", $current_date), $ids_for_php)){?>
                            <?if($current_date == strtotime($post['date'])){?>
<!--                                <td class="text-center">--><?//= $cycle.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                                <td class="text-center"><?=$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle, $current_date, $post['brutto_netto']);?></td>
                            <?}else{?>
                                <? if($max_index_day == date("w", $current_date)){ $cycle2 = $cycle2 + 1;
                                    if($cycle2 <= $my_menus->cycle){?>
<!--                                      <td class="text-center">--><?//= $cycle2.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                                        <td class="text-center"><?=$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle2, $current_date, $post['brutto_netto']);?></td>
                                    <?}else{
                                        $cycle2 = 1;?>
<!--                                        <td class="text-center">--><?//= $cycle2.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                                        <td class="text-center"><?=$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle2, $current_date, $post['brutto_netto']);?></td>
                                    <?}
                                }else{?>
<!--                                     <td class="text-center">--><?//= $cycle2.'цикл '. date("w", $current_date).'д/нед';?><!--</td>-->
                                    <td class="text-center"><?=$category->get_fact_storage_yield($category->id, $post['menu_id'], $cycle2, $current_date, $post['brutto_netto']);?></td>
                                <?}?>
                            <?}?>
                            <?php $current_date = $current_date + 86400; ?>
                        <? } else{$current_date = $current_date + 86400;}?>
                    <? } ?>
                    <? if($post['display_him_feed'] == 1){ ?>
                    <td><?=round($mas['protein'],1);?></td>
                    <td><?=round($mas['fat'],1);?></td>
                    <td><?=round($mas['carbohydrates_total'],1);?></td>
                    <td><?=round($mas['energy_kkal'],1);?></td>
                    <td><?=round($mas['carbohydrates_saccharide'],1);?></td>
                    <td><?=round($mas['carbohydrates_starch'],1);?></td>
                    <td><?=round($mas['carbohydrates_lactose'],1);?></td>
                    <td><?=round($mas['carbohydrates_sacchorose'],1);?></td>
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
                    <?}?>

                </tr>
                <? } ?>
<!--                <tr>-->
<!--                    <td>Итого</td>-->
<!--                    <td>--><?//=$count;?><!-- групп продуктов</td>-->
<!--                </tr>-->


            </table>
        </div>
    </div>
    <?php
    echo Html::button('<span class="glyphicon glyphicon-download"></span> Экспорт в Excel', [
        'title' => Yii::t('yii', 'Скачать в PDF'),
        'data-toggle'=>'tooltip',
        'class'=>'btn btn-success mt-3',
    ]);?>
<?php } ?>



<?
$script = <<< JS
//Прикрепление строчек итого за день и бжу за день к последней таблице
$(".last:last").append($(".itog_day"));
$(".last:last").append($(".procent_day"));


$( window ).unload(function() {
  console.log("Bye now!");
});
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