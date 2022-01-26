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

$this->title = 'Проверка меню на хлебные единицы';
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

<?if(empty($my_menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition')))){?>
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
                <?php $super_total_yield = 0; $super_total_protein = 0; $super_total_fat = 0; $super_total_carbohydrates_total = 0; $super_total_energy_kkal = 0; $super_total_vitamin_a = 0; $super_total_vitamin_c = 0; $super_total_vitamin_b1 = 0; $super_total_vitamin_b2 = 0; $super_total_vitamin_d = 0; $super_total_vitamin_pp = 0; $super_total_na = 0; $super_total_k = 0; $super_total_ca = 0; $super_total_f = 0; $super_total_se = 0;?>
<? echo '<b><p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $day->name .'</p></b>'?>
    <? foreach($nutritions as $nutrition){?>
                    <?$total_nutrition = round($model2->get_bju_nutrition($post['menu_id'], $cycle_id, $day->id, $nutrition->id, 'carbohydrates_total'), 2)/12;
                    $max_he = $model2->get_max_normativ_he($post['menu_id'], $nutrition->id);
					//print_r($total_nutrition);
                    ?>
    <div class="block mt-0" style="margin-top: 10px;">
        <table class="table_th0 table-hover"  >
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
                <th <?if($total_nutrition > $max_he){echo 'style ="background-color:#ff000080"';}?> class="text-center align-middle" style="width: 200px" rowspan="2">Значение в ХЕ</th>
                <th class="text-center align-middle" style="width: 200px" rowspan="2">Поиск аналогов</th>

            </tr>

            </thead>
            <tbody>
        <? $count = 0; $energy_kkal = 0; $protein = 0; $fat = 0; $carbohydrates_total = 0;
        ?>

        <?foreach($menus_dishes as $key => $m_dish){ ?>
                <? if($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id){?>
                <?//$result = $m_dish->get_allergen_dish($m_dish->dishes_id, $mas_allergen); ?>
                <? $count++;?>
                <!--ВЫВОД ПОСТРОЧНО КАЖДОГО БЛЮДА В РАЗАРЕЗЕ ПРИЕМА ПИЩИ-->
                <tr data-id="<?= $m_dish->id;?>">
                <td class="text-center"><?= $m_dish->get_techmup($m_dish->dishes_id)?></td>
                <td><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
                <td class="text-center"><?= $m_dish->yield ?></td>
                    <td class="text-center"><? $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'),1); echo $protein_dish; $protein = $protein_dish + $protein;?></td>
                    <td class="text-center"><? $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'),1); echo $fat_dish; $fat = $fat_dish + $fat;?></td>
                    <td class="text-center"><? $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'),1); echo $carbohydrates_total_dish; $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total;?></td>
                    <td class="text-center"><? $kkal = round($m_dish->get_kkal_dish($m_dish->id),1); echo $kkal; $energy_kkal = $kkal + $energy_kkal;?></td>
                    <td <?if($total_nutrition > $max_he){echo 'style ="background-color:#ff000080"';}?> class="text-center"><?= round($carbohydrates_total_dish/12, 1);?></td>
                    <?if($total_nutrition > $max_he){?>
                        <td class="text-center">
                            <?= Html::button('Поиск замены', [
                                'title' => Yii::t('yii', 'Посмотреть аналоги'),
                                'data-toggle'=>'tooltip',
                                'data-dishes_id' => $m_dish->id,
                                'class'=>'btn btn-sm main-button-3',
                                'onclick' => '
                          $.get("../menus-dishes/analog_sahar?menu_dishes_id=" + $(this).attr("data-dishes_id"), function(data){
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
                    <? if($count > 0){ ?>
                        <!--ВЫВОД СТРОЧКИ "ИТОГО" В РАЗРЕЗЕ КАЖДОГО ПРИЕМА ПИЩИ-->

                        <tr class="table-primary">
                            <td colspan="2">Итого за <? echo $nutrition->name?></td>
                            <!--МАССИВ data[<id приема пищи>][<название поля>] ХРАНИТ В СЕБЕ СРЕДНИЕ ПОКАЗАТЕЛИ ЗА <прием пищи>(т.е сумма за все завтраки, обеды и тд..) (самый низ таблицы)
                                $super_total_<название_поля> - ХРАНИТ ЗНАЧЕНИЕ 'ИТОГО ЗА ДЕНЬ'. РАСЧИТЫВАЕТСЯ ВСЕ В td и ниже вставляется в другие td-->
                            <td class="text-center"><? $yield = $model2->get_total_yield($post['menu_id'], $cycle_id, $day->id, $nutrition->id); echo $yield; $data[$nutrition->id]['yield'] = $data[$nutrition->id]['yield'] + $yield; $super_total_yield = $super_total_yield + $yield;?></td>
                            <td class="text-center"><? echo $protein; $data[$nutrition->id]['protein'] = $data[$nutrition->id]['protein'] + $protein; $super_total_protein = $super_total_protein + $protein;?></td>
                            <td class="text-center"><? echo $fat; $data[$nutrition->id]['fat'] = $data[$nutrition->id]['fat'] + $fat; $super_total_fat = $super_total_fat + $fat;?></td>
                            <td class="text-center"><? echo $carbohydrates_total; $data[$nutrition->id]['carbohydrates_total'] = $data[$nutrition->id]['carbohydrates_total'] + $carbohydrates_total; $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;?></td>
                            <td class="text-center"><? echo $energy_kkal; $data[$nutrition->id]['energy_kkal'] = $data[$nutrition->id]['energy_kkal'] + $energy_kkal; $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;?></td>
                            <td class="text-center"><?=round($carbohydrates_total/12, 2);?></td>
                            <td></td>

                        </tr>
                        <tr class="table-success">
                            <td colspan="2">Рекомендуемая величина за <? echo $nutrition->name?></td>
                            <td></td>
                            <td class="text-center"><?= $model2->get_recommended_normativ($post['menu_id'], $nutrition->id, 'protein_middle_weight');?></td>
                            <td class="text-center"><?= $model2->get_recommended_normativ($post['menu_id'], $nutrition->id, 'fat_middle_weight');?></td>
                            <td class="text-center"><?= $model2->get_recommended_normativ($post['menu_id'], $nutrition->id, 'carbohydrates_middle_weight');?></td>
                            <td class="text-center"><?= $model2->get_recommended_normativ($post['menu_id'], $nutrition->id, 'middle_kkal');?></td>
                            <td class="text-center"><?=$model2->get_normativ_he_for_itog_nutrition($post['menu_id'], $nutrition->id)?></td>
                            <td></td>
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
            <td class="text-center"><?=round($super_total_carbohydrates_total/12, 1);?></td>

        </tr>

                <tr class="table-success">
                    <td colspan="2">Рекомендуемая величина за день</td>

                    <td></td>
                    <td class="text-center"><?= $model2->get_recommended_normativ_of_day($post['menu_id'], 'protein_middle_weight');?></td>
                    <td class="text-center"><?= $model2->get_recommended_normativ_of_day($post['menu_id'], 'fat_middle_weight');?></td>
                    <td class="text-center"><?= $model2->get_recommended_normativ_of_day($post['menu_id'], 'carbohydrates_middle_weight');?></td>
                    <td class="text-center"><?= $model2->get_recommended_normativ_of_day($post['menu_id'], 'middle_kkal');?></td>
                    <td class="text-center"><?=$model2->get_normativ_he_for_itog_day($post['menu_id'])?></td>
                </tr>


        </tbody>
        </table>
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
                <h4 class="modal-title">Поиск блюд с пониженным значением углеводов
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
