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

$this->title = 'Проверка меню на аллергены';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->one();

$menu_cycle_count = $first_menu->cycle;
$menu_cycle = [];
$menu_cycle[0] = 'Показать за все недели';
for($i=1;$i<=$menu_cycle_count;$i++){
    $menu_cycle[$i] = $i;//массив из подходящи циклов
}


$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_cycle = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);
    $menu_cycle_count = $my_menus->cycle;
    $menu_cycle = [];
    $menu_cycle[0] = 'Показать за все недели';
    for($i=1;$i<=$menu_cycle_count;$i++){
        $menu_cycle[$i] = $i;//массив из подходящи циклов
    }
    $my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();
    foreach($my_days as $m_day){
        $ids[] = $m_day->days_id;
    }

    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];



    $cycle_ids = [];
    if($post['cycle'] != 0){
        $cycle_ids[$post['cycle']] = $post['cycle'];
    }
    else{
        for($i=1;$i<=$menu_cycle_count;$i++){
            $cycle_ids[$i] = $i;//массив из подходящи циклов
        }
    }
}

?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

<?if(empty($my_menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')))){?>
    <p class="text-center" style="color: red"><b>У Вас не созданы меню и не добавлены блюда.(Перейдите в раздел "Архив меню" или "Настройка меню")</b></p>
<?}?>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="container mb-30">
        <div class="row">
            <div class="col-11 col-md-6">
                <?= $form->field($model, 'menu_id')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
                    'onchange' => '
                  $.get("../menus-dishes/cyclelist?id="+$(this).val(), function(data){
                    $("select#menusdishes-cycle").html(data);
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
            </div>

            <div class="col-11 col-md-6">
                <?= $form->field($model, 'cycle')->dropDownList($menu_cycle, $params_cycle) ?>
            </div>
        </div>


        <!--        Блок с заполняемыми инпутами для информации. id не менять иначе не сработает-->
        <?if(empty($post)){$menu_id = $first_menu->id;} else{$menu_id = $post['menu_id'];}?>
        <div class="row">
            <div class="col">
                <label><b>Характеристика питающихся</b>
                    <input type="text" class="form-control" id="characters" disabled value="<?= $model2->insert_info($menu_id, 'feeders_characters');?>"></label>
            </div>
            <div class="col">
                <label><b>Возрастная категория</b>
                    <input type="text" class="form-control" id="age" disabled value="<?=$model2->insert_info($menu_id, 'age_info');?>"></label>
            </div>
            <div class="col">
                <label><b>Срок действия меню</b>
                    <input type="text" class="form-control" id="insert-srok" disabled value="<?=$model2->insert_info($menu_id, 'sroki');?>"></label>
            </div>
        </div>
        <br>
        <!--        Конец блока с заполнением-->


        <p class="text-center"><b>Выберите аллерген</b></p>
        <div class="row">

            <? $count = 0; $mas_allergen = []; foreach($allergens as $allergen){


            $count ++; //$allergen_name = 'menu'.$menu->id;
            $p_m = 'allergen'.$allergen->id;
            $allergen_name = 'allergen'.$count;
            $count_name = 'count'.$count;?>
            <div class="col-11 col-md-2">
                <? if($post_allergen[$p_m] == 1){ $mas_allergen[] = $allergen->id;?>
                    <?= $form->field($model, $allergen_name)->checkbox(['name' => 'allergen'.$allergen->id,  'value' => '1', 'checked ' => true])->label($allergen->name)?>
                <?}else{
                    echo $form->field($model, $allergen_name)->checkbox(['name' => 'allergen'.$allergen->id])->label($allergen->name);
                }?>
            </div>

            <?}?>
        </div>

        <? foreach ($mas_allergen as $m_a){
            $alergen_list .= $m_a.'_';
            ?>
        <?} //print_r($alergen_list);exit;?>



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
    </div>

<div class="row justify-content-center">
    <div class="col-auto">

    <?if(!empty($days)){?>
        <?php $count_cycle = 0;?>
        <?php foreach($cycle_ids as $cycle_id){ $count++;
            echo '<b><p class="mb-0 text-center" style="font-size: 20px; font-weight: 500;">Неделя '. $cycle_id .'</p></b>'
        ?>
<? foreach($days as $day){?>
<? echo '<b><p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $day->name .'</p></b>'?>
    <? foreach($nutritions as $nutrition){?>
    <div class="block mt-0" style="margin-top: 10px;">
        <table class="table_th0 table-hover" >
            <thead>
            <tr class="text-center"><? echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $nutrition->name .'</p>'?></tr>
            <tr>
                <th class="text-center align-middle" style="width: 200px" rowspan="2">№ рецептуры</th>
                <th class="text-center align-middle" style="width: 400px" rowspan="2">Название блюда</th>
                <th class="text-center align-middle" style="width: 60px" rowspan="2">Выход</th>
                <th class="text-center align-middle" style="width: 60px" rowspan="2">Белки</th>
                <th class="text-center align-middle" style="width: 60px" rowspan="2">Жиры</th>
                <th class="text-center align-middle" style="width: 60px" rowspan="2">Углеводы</th>
                <th class="text-center align-middle" >Эн. ценность</th>
                <th class="text-center align-middle" style="width: 200px" rowspan="2">Содержание аллергена</th>
                <th class="text-center align-middle" style="width: 200px" rowspan="2">Поиск аналогов</th>

            </tr>

            </thead>
            <tbody>
        <? $count = 0;
        ?>

        <?foreach($menus_dishes as $key => $m_dish){ ?>
                <? if($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id){?>
                <?$result = $m_dish->get_allergen_dish($m_dish->dishes_id, $mas_allergen); ?>
                <? $count++;?>
                <!--ВЫВОД ПОСТРОЧНО КАЖДОГО БЛЮДА В РАЗАРЕЗЕ ПРИЕМА ПИЩИ-->
                <tr data-id="<?= $m_dish->id;?>" <?if($result != '-'){echo 'style = "color:red"';}?>>
                <td class="text-center"><?= $m_dish->get_techmup($m_dish->dishes_id)?></td>
                <td><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
                <td class="text-center"><?= $m_dish->yield ?></td>
                    <td class="text-center"><? $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'),1); echo $protein_dish;?></td>
                    <td class="text-center"><? $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'),1); echo $fat_dish; $fat = $fat_dish + $fat;?></td>
                    <td class="text-center"><? $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'),1); echo $carbohydrates_total_dish; ?></td>
                    <td class="text-center"><? $kkal = round($m_dish->get_kkal_dish($m_dish->id),1); echo $kkal; ?></td>
                    <td class="text-center"><?= $result ?></td>
                    <?if($result != '-'){?>
                    <td class="text-center">
                        <?= Html::button('Посмотреть аналоги', [
                            'title' => Yii::t('yii', 'Посмотреть аналоги'),
                            'data-toggle'=>'tooltip',
                            'data-dishes_id' => $m_dish->dishes_id,
                            'data-alergen' => $alergen_list,
                            'class'=>'btn btn-sm main-button-3',
                            'onclick' => '
                          $.get("../menus-dishes/analog_alergen?id=" + $(this).attr("data-dishes_id") + "&alergen=" + $(this).attr("data-alergen"), function(data){
                          $("#showTechmup .modal-body").empty();
                            $("#showTechmup .modal-body").append(data);
                            //console.log(data);
                            $("#showTechmup").modal("show");
                          });'
                        ]);?>
                    </td>
                    <?}else{?>
                        <td class="text-center"></td>
                    <?}?>

                <? unset($menus_dishes[$key]) ?>
                </tr>
        <?}else{break;}?>
        <?}?>


        </tbody>
        </table>
        <?php } ?>
        <?php } ?>
        <?php } ?>

    </div>
        <br>
        <!--<div class="text-center">
            <?/*= Html::a('<span class="glyphicon glyphicon-download"></span> Скачать в Excel', ['export-menus-period?menu_id=' . $post['menu_id'].'&cycle='.$post['cycle']],
                [
                    'class'=>'btn btn-secondary',
                    'style' =>['width'=>'500px'],
                    'title' => Yii::t('yii', 'Скачать отчет в формате Excel'),
                    'data-toggle'=>'tooltip',
                ])
            */?>
        </div>-->
<?php } ?>
    <br>


    </div>
</div>




<!--МОДАЛЬНОЕ ОКНО ДЛЯ ТЕХКАРТ-->
<div id="showTechmup" class="modal fade">
    <div class="modal-dialog modal-lg" style="">
        <div class="modal-content">
            <div class="modal-header-p3">
                <h4 class="modal-title">Поиск блюд без указанных аллергенов
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="row">

                </div>
            </div>
        </div>
    </div>
</div>

<?
//print_r($data);
$script = <<< JS



$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
  
});


/*$( ".beforeload" ).click(function() {
  $('.beforeload').append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});*/
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
