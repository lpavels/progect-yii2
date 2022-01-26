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

$this->title = 'Формирование документов';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();
$menu_cycle_count = $first_menu->cycle;
$menu_cycle = [];
$menu_cycle[0] = 'За все недели';
for($i=1;$i<=$menu_cycle_count;$i++){
    $menu_cycle[$i] = $i;//массив из подходящи циклов
}


$chemistry_items = [0 => 'Цикличное', 1 => 'За день'];
$analiz_items = [0 => 'Не включать в отчет', 1 => 'Включить в отчет'];
$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_cycle = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$params_chemistry = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$two_column = ['options' => ['class' => 'row justify-content-center mt-3'], 'labelOptions' => ['class' => 'col-11 col-md-2 col-form-label font-weight-bold']];
if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);
    $menu_cycle_count = $my_menus->cycle;
    $menu_cycle = [];
    $menu_cycle[0] = 'За все недели';
    for($i=1;$i<=$menu_cycle_count;$i++){
        $menu_cycle[$i] = $i;//массив из подходящи циклов
    }
    $my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();
    foreach($my_days as $m_day){
        $ids[] = $m_day->days_id;
    }

    $chemistry_items = [0 => 'Скрыть химический состав', 1 => 'Показать химический состав'];
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];
    $params_chemistry = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];

    $count_my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->count();
    if($post['cycle'] == 0){
        $count_my_days = $count_my_days * $menu_cycle_count;
    }
    $normativ_vitamin_day_vitamin_a = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_a', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ_vitamin_day_k = \common\models\NormativVitaminDay::find()->where(['name' => 'k', 'age_info_id' => $my_menus->age_info_id])->one()->value;

    //$nutritions_count = count($nutritions);
   //print_r($count_my_days);
    //print_r($menus_dishes);
}

?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="mb-5 mt-5">

                <?= $form->field($model, 'yield', $two_column)->dropDownList($chemistry_items, ['class' => 'form-control col-11 col-md-4'])->label('Отображение'); ?>

                <?= $form->field($model, 'cycle', $two_column)->dropDownList($menu_cycle, ['class' => 'form-control col-11 col-md-4']) ?>

                <?= $form->field($model, 'created_at', $two_column)->textInput(['class' => 'datepicker-here form-control col-11 col-md-4', 'autocomplete' => 'off'] )->label('Дата') ?>

                <?= $form->field($model, 'menu_id', $two_column)->dropDownList($my_menus_items, [
                    'class' => 'form-control col-11 col-md-4',
                    'onchange' => '
                  /*$.get("../menus-dishes/cyclelist?id="+$(this).val(), function(data){
                    $("select#menusdishes-cycle").html(data);
                  });*/
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

        <?= $form->field($model, 'nutrition_id', $two_column)->dropDownList($analiz_items, ['class' => 'form-control col-11 col-md-4'] )->label('Оценка пищевой ценности') ?>
        <?= $form->field($model, 'dishes_id', $two_column)->dropDownList($analiz_items, ['class' => 'form-control col-11 col-md-4'] )->label('Анализ химического состава') ?>






        <div class="row mt-5">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Cкачать меню', ['name'=>'identificator', 'value' => 'menu', 'class' => 'btn main-button-3 beforeload']) ?>
            </div>
        </div>

        <div class="row mt-2">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Cкачать технологические карты блюд', ['name'=>'identificator', 'value' => 'techmup', 'class' => 'btn main-button-3 beforeload']) ?>
            </div>
        </div>
        <p class="text-center">*Скачивание файлов может занять некоторое время</p>
        <?php ActiveForm::end(); ?>
    </div>
<?php $cycle_ids = [];
if($post['cycle'] != 0){
$cycle_ids[$post['cycle']] = $post['cycle'];
}
else{
    for($i=1;$i<=$menu_cycle_count;$i++){
        $cycle_ids[$i] = $i;//массив из подходящи циклов
    }
}

?>

<?
//print_r($data);
$script = <<< JS


    $('.field-menusdishes-created_at').hide();
    
    var field = $('#menusdishes-yield');
    field.on('change', function () {
           if (field.val() === "0") {
               console.log('222');
               $('.field-menusdishes-cycle').show();
               $('.field-menusdishes-created_at').hide();
           }
           else{
               console.log('sss');
              $('.field-menusdishes-cycle').hide();
               $('.field-menusdishes-created_at').show();
           }
    });
    field.trigger('change');
    
    
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
