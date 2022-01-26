<?php

use common\models\DishesProducts;
use common\models\Products;
use common\models\ProductsCategory;
use common\models\ProductsChangeOrganization;
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

$this->title = 'Отчет о перечне продуктов за указанный период';
$this->params['breadcrumbs'][] = $this->title;

$products_model = new Products();

$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();
$model_menus_dishes = new MenusDishes();
if(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr') || Yii::$app->user->can('minobr')){
    if(!empty(Yii::$app->session['organization_id']))
    {
        $my_menus = Menus::find()->where(['organization_id' => Yii::$app->session['organization_id'], 'status_archive' => 0])->all();
        $my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
        $first_menu = Menus::find()->where(['organization_id' => Yii::$app->session['organization_id'], 'status_archive' => 0])->one();
        //echo Yii::$app->session['organization_id'];
    }else{
        $my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
        $my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
        $first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();
    }
}


$chemistry_items = [0 => 'Брутто', 1 => 'Нетто'];
$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_chemistry = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$params_norma= ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$norma_items = [0 => 'Показать по дням', 1 => 'Показать по приемам пищи'];
if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);



    $chemistry_items = [0 => 'Брутто', 1 => 'Нетто'];
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    //print_r(count($products_categories));
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


    thead, th {
        background-color: #ede8b9!important;
        font-size: 15px;
        border: 1px solid #c2c2c2!important;
    }
    td{
        border: 1px solid #c2c2c2!important;
    }

</style>




    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin([]); ?>
    <div class="container mb-30 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <?= $form->field($model, 'field')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['field'] => ['Selected' => true]],
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
                ])->label('Меню');; ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'date_start')->textInput(['class'=>'datepicker-here form-control', 'autocomplete' => 'off', 'value' => $post['date_start']])->label('Начало периода'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'date_end')->textInput(['class'=>'datepicker-here form-control', 'autocomplete' => 'off', 'value' => $post['date_end']])->label('Конец периода'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'field2')->dropDownList($chemistry_items, $params_chemistry)->label('Брутто/Нетто'); ?>
            </div>
        </div>

        <!--        Блок с заполняемыми инпутами для информации. id не менять иначе не сработает-->
        <?if(empty($post)){$menu_id = $first_menu->id;} else{$menu_id = $post['field'];}?>
        <div class="row">
            <div class="col">
                <label><b>Характеристика питающихся</b>
                    <input type="text" class="form-control" id="characters" disabled value="<?= $model_menus_dishes->insert_info($menu_id, 'feeders_characters');?>"></label>
            </div>
            <div class="col">
                <label><b>Возрастная категория</b>
                    <input type="text" class="form-control" id="age" disabled value="<?=$model_menus_dishes->insert_info($menu_id, 'age_info');?>"></label>
            </div>
            <div class="col">
                <label><b>Срок действия меню</b>
                    <input type="text" class="form-control" id="insert-srok" disabled value="<?=$model_menus_dishes->insert_info($menu_id, 'sroki');?>"></label>
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
        <div class="row">
            <div style="margin: 0 auto; font-size: 14px;">
                <p class="text-center"><b>*Загрузка отчета может занимать до 3х минут при определенных условиях</b></p>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php //print_r($products)?>
<?php if($post){?>
<!--       !!! В $post['days_id'] ХРАНИТСЯ ИНФОРМАЦИЯ БРУТТО/НЕТТО !!!!      -->

<div class="row justify-content-center">
    <?if($post['field2'] == 0){echo '<h4>Перечень продуктов, Брутто, г</h4>';}else{echo '<h4>Перечень продуктов, Нетто, г</h4>';}?>
    <?if($post['cycle'] == 0){?>
    <div class="col-auto">
    <table class="table_th0 fixtable">
        <thead>
    <tr class="">
        <th class="text-center align-middle" rowspan="1">№</th>
        <th class="text-center align-middle" rowspan="1">Группа продукта</th>
        <th class="text-center align-middle" rowspan="1">Продукт</th>

        <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->

        <? $count_days = 0; foreach ($datas as $key => $data){
            if(date("w", strtotime($data[0])) == 0){
                $day_name = 7;
            }else{
                $day_name = date("w", strtotime($data[0]));
            }?>
            <th class="text-center"><?= $data[0]."<br>".$data[1]." неделя<br>".Days::findOne($day_name)->name; $count_days ++;?></th>
        <?}?>
        <th class="text-center align-middle" rowspan="1">Итого</th>
        <th class="text-center align-middle" rowspan="1">Среднесуточное значение</th>
    </tr>
        </thead>
        <tbody>
        <?$number_row=1;?>
        <? foreach($products_categories as $product_cat){?>
            <? foreach($products as $product){
                $totality = 0;
                if($product_cat->id == $product['products_category_id']){
                ?>
                <tr>
<!--                    вывод Название и категории -->
                    <td class="text-center"><?=$number_row?></td>
                    <td><?=$product_cat->name?></td>
                    <?/*ПОИСК ЗАМЕНЫ. зАМЕНЯЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПРОДУКТ НА ИНОЙ.*/
                    $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $product['id']])->one();
                    if(!empty($products_change)){?>
                        <td ><b>Заменено на </b><?=Products::findOne($products_change->change_products_id)->name;?>(Было: <small><i><?=$product['name']?></i></small>)</td>
                    <?}else{?>
                        <td class="align-middle"><?=$product['name']?></td>
                    <?}?>



                    <?foreach ($datas as $data){
                        if(date("w", strtotime($data[0])) == 0){
                            $day_name = 7;
                        }else{
                            $day_name = date("w", strtotime($data[0]));
                        }?>
                        <td class="text-center"><? $total = $products_model->get_total_yield_day_period($post['field'], $product['id'], $data, $post['field2']); echo $total; if($total == '-'){$total = 0;} $totality = $total + $totality; ?></td>
                    <?}?>
                    <td class="text-center"><b><? echo $totality; if($totality == '-'){$totality = 0;}?></b></td>
                    <td class="text-center"><b><?=round($totality/$count_days, 2);?></b></td>
                </tr>
                <?$number_row++;?>
                <? } ?>
            <? } ?>
        <?}?>
        </tbody>



</table>
    </div>
    <?}?>

</div>
    <div class="text-center mt-5">
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> Экспорт в Excel',
                ['excel-fact-date-products-list?menu_id=' . $post['field'].'&date_start_diapozon='.$post['date_start'].'&date_end_diapozon='.$post['date_end'].'&brutto_netto='.$post['field2']],
                [
                    'class'=>'btn btn-secondary',
                    'style' =>['width'=>'500px'],
                    'title' => Yii::t('yii', 'Вы можете скачать перечень продуктов в формате exceel'),
                    'data-toggle'=>'tooltip',
                ])
            ?>
    </div>
    <div class="row">
        <div style="margin: 0 auto; font-size: 14px;">
            <p class="text-center"><b>*Скачивание отчета может занимать некоторое время</b></p>
        </div>
    </div>
<?php } ?>




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
 
	$('tr > th',$(this.table)).each(function(index) {
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
