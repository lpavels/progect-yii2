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

$this->title = 'Меню раскладка за день по приемам пищи';
$this->params['breadcrumbs'][] = $this->title;
$brutto_netto_items = [0 => 'Нетто', 1 => 'Брутто'];
if($post)
{
    //Создание уникального массива блюд по ключам
    $count_porciya = [];
    $dishes_in_nutrition = [];
    $dishes_count = [];
    $dishes_yield = [];
    $count_dishes_in_nutrition = [];
    $count_menu = count($menus_dishes2); //количество меню, которое вернулось
    for($i=0;$i<$count_menu;$i++) {
        $count_dishes_in_menu[$i] = count($menus_dishes2[$i]); //количество блюд в каждом меню
    }
    for($i=0;$i<$count_menu;$i++) { //обходим меню
        for($j=0;$j<$count_dishes_in_menu[$i];$j++) { //обходим блюда
            /*echo $menus_dishes2[$i][$j]['nutrition_id'];
            echo ".";
            echo $menus_dishes2[$i][$j]['dishes_id'];
            echo "_";
            echo $menus_dishes2[$i][$j]['menu_id'];
            echo "<br>";*/
            $count_dishes_in_nutrition[$menus_dishes2[$i][$j]['nutrition_id']][$menus_dishes2[$i][$j]['dishes_id']] = $menus_dishes2[$i][$j]['dishes_id'];
            $dishes_in_nutrition[$menus_dishes2[$i][$j]['nutrition_id'].'_'.$menus_dishes2[$i][$j]['dishes_id']]=$menus_dishes2[$i][$j]['dishes_id'];
            $dishes_count[$menus_dishes2[$i][$j]['nutrition_id'].'_'.$menus_dishes2[$i][$j]['dishes_id'].'_'.$menus_dishes2[$i][$j]['menu_id']]=$menus_dishes2[$i][$j]['dishes_id'];
            $dishes_yield[$menus_dishes2[$i][$j]['nutrition_id'].'_'.$menus_dishes2[$i][$j]['dishes_id'].'_'.$menus_dishes2[$i][$j]['menu_id']]=$menus_dishes2[$i][$j]['yield'];
        }
    }


foreach ($post_menus as $p_menu)
{
    foreach ($nutritions as $nutrition)
    {
        foreach ($menus_dishes as $m_dish)
        {
            if(array_key_exists($nutrition->id . '_' . $m_dish['dishes_id'].'_'.$p_menu->id, $dishes_count) && $m_dish['menu_id'] == $p_menu->id && $m_dish['nutrition_id'] == $nutrition->id)
            {
                $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']] = $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']] + $menus_count_ids[$p_menu->id.'_'.$nutrition->id];
                $count_porciya_menu[$nutrition->id . '_' . $m_dish['dishes_id'] . '_' . $p_menu->id] = $menus_count_ids[$p_menu->id.'_'.$nutrition->id];
            }
        }
    }
}
   // print_r($count_porciya_menu);exit;

    //print_r($post_menus);
$menus_dishes_id = [];
if($post['RaskladkaForm']['brutto_netto'] == 0){
    $brutto_netto = 'net_weight';
}
    if($post['RaskladkaForm']['brutto_netto'] == 1){
        $brutto_netto = 'gross_weight';
    }
foreach($menus_dishes as $m_dish){
    $menus_dishes_id[] = $m_dish['id'];
}

    $mas_yield = [];
    $mass = $model_menus_dishes->get_total_raskladka_yield($post_menus, $menus_dishes_id, strtotime($post['RaskladkaForm']['data']));
    foreach($mass as $mas){
        if($mas['products_id'] == 213 || $mas['products_id'] == 214 || \common\models\Products::findOne($mas['products_id'])->products_category_id == 29 || $mas['products_id'] == 218)
        {
            $mas_yield[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['products_id']] = $mas_yield[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['products_id']] + (($mas[$brutto_netto] * (($mas['menus_yield'] / $mas['dishes_yield']))) * $count_porciya_menu[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['menu_id']]);
        }
        else{
            $mas_yield[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['products_id']] = $mas_yield[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['products_id']] + (($mas[$brutto_netto] * (($mas['menus_yield'] / $mas['dishes_yield'])) / 1000) * $count_porciya_menu[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['menu_id']]);
        }
    }

}
?>
<style>
    .fixtable-fixed {
        position: fixed;
        top: 0;
        z-index: 101;
        background-color: #FCF8E4;
        border-bottom: 1px solid #ddd;
    }
    .block-menu{
        background-color: #ffeeb3;
        margin-bottom: 20px;
        border-radius: 5px;
    }
</style>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

<?if(empty($menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')))){?>
    <p class="text-center" style="color: red"><b>У Вас не созданы меню и не добавлены блюда.(Перейдите в раздел "Архив меню" или "Настройка меню")</b></p>
<?}?>


    <div class="container mb-30 mt-5">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <form action = "/menus-dishes/raskladka" name="raskladka" id="raskladka" method="POST">
            <div class="col">
                <b>Дата</b>
                <?= $form->field($model_form, 'data')->textInput(['type'=>'date','class' => 'form-control', 'autocomplete' => 'off', 'value' => $post['RaskladkaForm']['data']])->label(false) ?>
            </div>

                <div class="col">
                    <b>Брутто/Нетто</b>
                    <?= $form->field($model_form, 'brutto_netto')->dropDownList($brutto_netto_items, [
                'class' => 'form-control', 'options' => [$post['RaskladkaForm']['brutto_netto'] => ['Selected' => true]]
                ])->label(false);?>
                </div>

        </div>
        <br>
        <div class="row">
            <div class="col-sm">
                <b>Выберите меню</b>
            </div>
            <div class="col-sm">
                <b>Характеристика питающихся</b>
            </div>
            <div class="col-sm">
                <b>Возрастная категория</b>
            </div>
            <div class="col-sm">
                <b>Продолжительность</b>
            </div>
        </div>
        <br><br>
        <?  $count = 0; $count_pit = 0; foreach($menus as $menu){

            $count ++;
            $p_c = 'count'.$menu->id; $p_m = 'menu'.$menu->id;
            $menu_name = 'menu'.$count;
            $menus_nutrition = \common\models\MenusNutrition::find()->where(['menu_id' => $menu->id])->all();?>

            <div class="block-menu">
                <div class="row pt-3 pl-3" >
                    <? if($post[$p_m] == 1){?>
                    <?= $form->field($model_form, $menu_name)->checkbox(['name' => 'menu'.$menu->id,  'value' => '1', 'checked ' => true])->label('')?>
                    <?}else{
                    echo $form->field($model_form, $menu_name)->checkbox(['name' => 'menu'.$menu->id,])->label('');
                    }?>

                    <div class="col-sm">
                        <?=$menu->name?>
                    </div>
                    <div class="col-sm">
                        <input type="text" class= "form-control" value="<?=$menu->get_characters($menu->feeders_characters_id)?>" disabled>
                    </div>
                    <div class="col-sm">
                        <input type="text" class= "form-control" value="<?=$menu->get_age($menu->age_info_id)?>" disabled>
                    </div>

                    <div class="col-sm">
                        <input type="text" class= "form-control" value="<?=date('d.m.Y', $menu->date_start).' - '. date('d.m.Y', $menu->date_end)?>" disabled>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-3 mt-4">
                        <b>Количество питающихся:</b>
                    </div>
                    <?foreach($menus_nutrition as $m_nutrition){ $count_pit++; $count_name = 'count'.$count_pit;?>
                        <div class="col-sm-2">
                            <?= $form->field($model_form, $count_name)->textInput(['name' => 'count'.$menu->id.'_'.$m_nutrition->nutrition_id, 'value'=>$post[$p_c.'_'.$m_nutrition->nutrition_id]])->label(\common\models\NutritionInfo::findOne($m_nutrition->nutrition_id)->name);?>
                        </div>
                    <?}?>

                </div>
            </div>

        <?}?>

    <?= Html::submitButton('Посмотреть', ['class' => 'btn main-button-3 form-control mt-4 col-12']) ?>


<?php ActiveForm::end(); ?>
    </div>

        <? if($post){ $first_row = [];?>
            <div style="margin-top: 20px">
            </div>

            <br>

            <div>
                <table class="table_th0 table-hover table-responsive fixtable" >
                    <thead>
                    <tr>
                        <th style="min-width: 200px" colspan="2">Прием пищи</th>
                        <th class="text-center align-middle"  style="min-width: 50px">Ед. Изм.</th>
                        <? foreach($nutritions as $nutrition){?>
                        <th class="text-center" colspan="<? if(!empty($count_dishes_in_nutrition[$nutrition->id])){echo count($count_dishes_in_nutrition[$nutrition->id]);}else{ echo 0; }?>"><?= $nutrition->name?></th>
                        <?}?>
                        <th class="text-center">Итого</th>
                    </tr>


                    <tr>
                    <td class="align-middle" colspan="2">Название блюда</td>
                    <td class="text-center align-middle"></td>
                    <? $count_dish = 0; foreach($nutritions as $nutrition){?>
                        <? foreach($menus_dishes as $m_dish){?>
                            <?if(array_key_exists($nutrition->id . '_' . $m_dish['dishes_id'], $dishes_in_nutrition)){?>
                                <!--<td style="height: 150px; padding: 0; font-size:14px;" class="text-center align-middle rotated"><?/*= $model_menus_dishes->get_dishes($dishes_in_nutrition[$nutrition->id . '_' . $m_dish['dishes_id']]); unset($dishes_in_nutrition[$nutrition->id . '_' . $m_dish['dishes_id']]); $first_row[$nutrition->id . '_' .$m_dish['dishes_id']] = $m_dish['dishes_id'];*/?></td>-->
                                <td class="text-center align-middle pl-1 pr-1" style="font-size: 11px;"><b><?= $model_menus_dishes->get_dishes($dishes_in_nutrition[$nutrition->id . '_' . $m_dish['dishes_id']]); unset($dishes_in_nutrition[$nutrition->id . '_' . $m_dish['dishes_id']]); $first_row[$nutrition->id . '_' .$m_dish['dishes_id']] = $m_dish['dishes_id'];?></b></td>
                                <? $count_dish ++; ?>
                            <?}?>
                        <?}?>
                    <?}?>
                        <td><b><?=$count_dish; ?></b></td>
                    </tr>
                    <tr >
                        <td class="align-middle" colspan="2">Количество порций</td>
                        <td class="text-center align-middle">шт</td>
                        <? $itog = 0; foreach($nutritions as $nutrition){?>
                            <? foreach($menus_dishes as $m_dish){?>
                                <?if(array_key_exists($nutrition->id . '_' . $m_dish['dishes_id'], $count_porciya)){?>
                                    <td class="text-center align-middle"><?=
                                        $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']];
                                        $itog = $itog + $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']];
                                        unset($count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']]); ?>
                                    </td>
                                <?}?>
                            <?}?>
                        <?}?>
                        <td><b><?= $itog?></b></td>
                    </tr>
                        <? foreach($post_menus as $key =>$p_menu){?>
                            <tr>
                                <? if($key == 0) { ?>
                                    <td class="text-center align-middle" rowspan="<?= count($post_menus);?>" colspan="1">Выход - масса порций</td>
                                <?}?>
                                <? if($key != 0) { ?>
                                    <td class="text-center align-middle"></td>
                                <?}?>
                                <td class="text-center align-middle" colspan="1" style="width: 170px; font-size: 11px;" ><b><?=$p_menu->name?></b></td>
                                <td class="text-center align-middle">г</td>
                                <? $itog = 0; foreach($first_row as $key => $f_row){?>
                                    <?if(array_key_exists($key.'_'.$p_menu->id, $dishes_yield)){?>
                                        <td class="text-center align-middle"><?= $dishes_yield[$key.'_'.$p_menu->id]; $itog = $dishes_yield[$key.'_'.$p_menu->id] +$itog; unset($dishes_yield[$key.'_'.$p_menu->id]); ?></td>
                                    <?}else{?>
                                        <td class="text-center align-middle">-</td>
                                    <?}?>
                                <?}?>
                                <td><b><?=$itog?></b></td>
                            </tr>
                    </thead>
                    <tbody>
                        <?}?>
                    <?foreach($products as $product){ $itog = 0;?>
                        <tr>
                            <td class="align-middle" colspan="2"><?=$product['name']; ?></td>
                            <?if($product['id'] == 213 || $product['id'] == 214 || $product['products_category_id'] == 29 || $product['id'] == 218){?>
                                <td class="align-middle text-center">г</td>
                            <?}else{?>
                            <td class="align-middle text-center">кг</td>
                            <?}?>
                            <? foreach($first_row as $key => $f_row){?>
                                <?if(array_key_exists($key . '_' . $product['id'], $mas_yield)){?>
                                    <td class="text-center align-middle"><?= number_format($mas_yield[$key . '_' . $product['id']], 3, '.', ''); $itog = $itog + number_format($mas_yield[$key . '_' . $product['id']], 3, '.', ''); ?></td>
                                <?}else{?>
                                    <td class="text-center align-middle"></td>
                                    <?}?>
                            <?}?>
                            <td class="align-middle"><b><?= $itog;?></b></td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>

                <div class="text-center">
                    <?/*= Html::a('<span class="glyphicon glyphicon-download"></span> Скачать в Excel', ['export-prognos-raskladka?arrau1=' . $string.'&arrau2=' . $string2.'&post_brutto_netto='.$post_brutto_netto.'&post_date='.$post_date.'&arrau3='.$string3],
                        [
                            'class'=>'btn btn-secondary mt-3',
                            'style' =>['width'=>'500px'],
                            'title' => Yii::t('yii', 'Скачать отчет в формате Excel'),
                            'data-toggle'=>'tooltip',
                        ])*/
                    ?>
                </div>
            </div>


        <? }?>

<?php
$js = <<< JS

function FixTable(table) {
	var inst = this;
	this.table  = table;
 
	$('thead > tr > th',$(this.table)).each(function(index) {
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
$this->registerJs($js, \yii\web\View::POS_READY);