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
use common\models\Organization;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет по контролю';
$this->params['breadcrumbs'][] = $this->title;

$yes_no_items = [
    '1' => "Родительский",
    '2' => "Внутренний",
    //'3' => "Общественный",
];

/*$organization_id = Yii::$app->user->identity->organization_id;

$region_id = Organization::findOne($organization_id)->region_id;

if (Yii::$app->user->can('minobr') || Yii::$app->user->can('rospotrebnadzor_nutrition'))
{
    $municipalities = \common\models\Municipality::find()->where(['region_id' => $region_id])->all();
    $municipality_null = array(0 => 'Все муниципальные округа ...');
    $municipality_items = ArrayHelper::map($municipalities, 'id', 'name');
    $municipality_items = ArrayHelper::merge($municipality_null, $municipality_items);
    $organization = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id])->all();
}
if (Yii::$app->user->can('subject_minobr'))
{
    $my_org = Organization::findOne($organization_id);
    $municipalities = \common\models\Municipality::find()->where(['id' =>$my_org->municipality_id])->all();
    $municipality_items = ArrayHelper::map($municipalities, 'id', 'name');
    $organization = Organization::find()->where(['type_org' => 3, 'municipality_id' => $my_org->municipality_id])->all();
}
$organization_null = array(0 => 'Все организации ...');
$organization_items = ArrayHelper::map($organization, 'id', 'title');
$organization_items = ArrayHelper::merge($organization_null, $organization_items);*/
if (!empty($post))
{
    $params_date_start = ['class' => 'form-control', 'options' => [$post['date_start'] => ['Selected' => true]]];
    $params_date_end = ['class' => 'form-control', 'options' => [$post['date_end'] => ['Selected' => true]]];
    $params_field = ['class' => 'form-control', 'options' => [$post['field'] => ['Selected' => true]]];
}
else{
    $params_field = ['class' => 'form-control', 'options' => [1 => ['Selected' => true]]];
}

?>
<style>
    th, td {
        border: 1px solid black!important;
        color: black;

    }
    th {
        background-color: #ede8b9;
        font-size: 13px;
    }
</style>
<h1 class="text-center"><?= Html::encode($this->title) ?></h1>


<?php $form = ActiveForm::begin([]); ?>
<div class="container mb-5 mt-5">
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'date_start')->textInput(['type'=>'date','class'=>'form-control', 'autocomplete' => 'off', 'value' => $post['date_start']])->label('Начало периода'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'date_end')->textInput(['type'=>'date','class'=>'form-control', 'autocomplete' => 'off', 'value' => $post['date_end']])->label('Конец периода'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'field')->dropDownList($yes_no_items, $params_field)->label('Вид контроля'); ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="margin: 0 auto">
            <?= Html::submitButton('Посмотреть', ['name' => 'identificator', 'value' => 'view', 'class' => 'btn main-button-3 beforeload mt-3']) ?>
            <button class="btn main-button-3 load mt-3" type="button" disabled style="display: none">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Посмотреть...
            </button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div>
<? if ($post){ ?>
    <?if(empty($models)){?>
        <p class="text-center text-danger">Нет данных</p>
    <?}else{?>
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
            <? $count = 0; $sred = []; $ball = []; foreach ($models as $model) { $count++;?>
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
                <?}?>
                <tr class="table-danger">
                    <td class="" colspan="4">Среднее значение за период:</td>
                    <td class="text-center align-middle"><?= round($sred['procent']/$count, 1);?></td>
                    <td class="text-center align-middle"><?= round($sred['test']/$count, 1);?></td>
                    <td class="text-center align-middle"><?= round($sred['test_food']/$count, 1);?></td>
                    <td class="text-center align-middle"><? echo round($sred['test_food']/$count, 1) + round($sred['test']/$count, 1);?></td>

                    <td class="text-center align-middle" colspan="2"><?= round($ball['1']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['2']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['3']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['4']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['5']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['6']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['7']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['8']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['9']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['10']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['11']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['12']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['13']/$count, 1);?></td>
                    <td class="text-center align-middle" colspan="2"><?= round($ball['14']/$count, 1);?></td>
                </tr>

        </tbody>
    </table><br><br><br>
    <div style="font-size: 13px;">
        <h1 >Список вопросов</h1>
        <p><b>Вопрос 1:</b> Соответствует ли фактическое меню, объемы порций двухнедельному меню, утвержденному руководителем образовательной организации?</p>
        <p><b>Вопрос 2:</b> Организовано ли питание детей, требующих индивидуального подхода в организации питания с учетом имеющихся нарушений здоровья (сахарный диабет, целиакия, пищевая аллергия)</p>
        <p><b>Вопрос 3:</b> Все ли дети с сахарным диабетом, пищевой аллергией, ОВЗ, фенилкетонурией, целиакией, муковисцидозом питаются в столовой?</p>
        <p><b>Вопрос 4:</b> Все ли дети моют руки перед едой?</p>
        <p><b>Вопрос 5:</b> Созданы ли условия для мытья и дезинфекции рук?</p>
        <p><b>Вопрос 6:</b> Все ли дети едят сидя?</p>
        <p><b>Вопрос 7:</b> Все ли дети успевают поесть за перемену (хватает ли времени для приема пищи)?</p>
        <p><b>Вопрос 8:</b> Есть ли замечания по чистоте посуды?</p>
        <p><b>Вопрос 9:</b> Есть ли замечания по чистоте столов?</p>
        <p><b>Вопрос 10:</b> Есть ли замечания к сервировке столов?</p>
        <p><b>Вопрос 11:</b> Теплые ли блюда выдаются детям?</p>
        <p><b>Вопрос 12:</b> Участвуют ли дети в накрывании на столы?</p>
        <p><b>Вопрос 13:</b> Лица, накрывающие на столы, работают в специальной одежде (халат, головной убор)?</p>
        <p><b>Вопрос 14:</b> Организовано ли наряду с основным питанием дополнительное питание (возможность самостоятельного приобретения блюд через линию раздачи или буфет)?</p>
    </div>
        <?}?>
<? } ?>

<?

$script = <<< JS
//$('#menus-parent_id').attr('disabled', 'true');
$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
  
});


/*$( ".beforeload" ).click(function() {
  $('.beforeload').append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});*/






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

$this->registerJs($script, yii\web\View::POS_READY);
?>
