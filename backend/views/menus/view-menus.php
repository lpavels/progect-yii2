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

$this->title = 'Подробный просмотр архивного меню';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::findOne($get);

$menu_cycle_count = $my_menus->cycle;

for($i=1;$i<=$menu_cycle_count;$i++){
    $menu_cycle[$i] = $i;//массив из подходящи циклов
}

?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Вернуться обратно к списку архивных меню', ['/menus/archive'], ['class' => 'profile-link']) ?>

<div>
    <div class="container">
        <p><b>Меню: </b><?= $my_menus->name; ?></p>
        <p><b>Характеристика питающихся: </b><?= $model->insert_info($my_menus->id, 'feeders_characters');?></p>

        <p><b>Цикл: </b><?= $my_menus->cycle; ?> неделя</p>
        <p><b>Возрастная категория: </b><?=$model->insert_info($my_menus->id, 'age_info');?></p>


        <p><b>Срок действия меню: </b><?=$model->insert_info($my_menus->id, 'sroki');?></p>

        <p><b>Дни меню: </b> <?=$model->insert_info($my_menus->id, 'days');?></p>

    </div>
</div>



<?php $cycle_ids = [];

    for($i=1;$i<=$menu_cycle_count;$i++){
        $cycle_ids[$i] = $i;//массив из подходящи циклов
    }


?>
<?php if(empty($menus_dishes)){?>
<div class="text-center">
    <p><b>В это архивное меню не добавлено ни одного блюда</b></p>
</div>
<?php }
else{?>

<div class="row justify-content-center">
    <div class="col-auto">
<!--МАССИВ data[] ХРАНИТ В СЕБЕ СРЕДНИЕ ПОКАЗАТЕЛИ ПО ПИТАНИЮ СМ.НИЖЕ-->
<?php $data = [];?>
    <?if(!empty($days)){?>
        <?php $count_cycle = 0;?>
        <?php foreach($cycle_ids as $cycle_id){ $count++;
            echo '<b><p class="mb-0 text-center" style="font-size: 20px; font-weight: 500;">Неделя '. $cycle_id .'</p></b>'
        ?>
<? foreach($days as $day){?>
<? echo '<b><p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $day->name .'</p></b>'?>
<?php $super_total_yield = 0; $super_total_protein = 0; $super_total_fat = 0; $super_total_carbohydrates_total = 0; $super_total_energy_kkal = 0; $super_total_vitamin_a = 0; $super_total_vitamin_c = 0; $super_total_vitamin_b1 = 0; $super_total_vitamin_b2 = 0; $super_total_vitamin_d = 0; $super_total_vitamin_pp = 0; $super_total_na = 0; $super_total_k = 0; $super_total_ca = 0; $super_total_f = 0; $super_total_se = 0;?>
    <?//$data[1]['yield'] = 0;//if(!empty($nutritions)){?>
    <? foreach($nutritions as $nutrition){?>
    <div class="block mt-0" style="margin-top: 10px;">
        <table class="table_th0 table-hover table-responsive last" >
            <thead>
            <tr class="text-center"><? echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">'. $nutrition->name .'</p>'?></tr>
            <tr>
                <th class="text-center align-middle" rowspan="2" style="min-width: 100px">№ рецептуры</th>
                <th class="text-center align-middle" rowspan="2" style="min-width: 400px">Название блюда</th>
                <th class="text-center align-middle" rowspan="2">Выход</th>
                <th class="text-center align-middle" rowspan="2">Белки</th>
                <th class="text-center align-middle" rowspan="2">Жиры</th>
                <th class="text-center align-middle" rowspan="2">Углеводы</th>
                <th class="text-center align-middle" rowspan="2">Эн. ценность</th>
                    <th class="text-center" colspan="6">Витамины</th>
                    <th class="text-center" colspan="6">Минеральные вещества</th>

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
                    <th class="text-center">F, мг</th>
                    <th class="text-center">Se, мкг</th>

            </tr>
            </thead>
            <tbody>
                    <? $count = 0;
                    $indicator = 0; $energy_kkal = 0; $protein = 0; $fat = 0; $carbohydrates_total = 0; $vitamins = []; unset($vitamins); $vitamin_a = 0; $vitamin_c = 0; $vitamin_b1 = 0; $vitamin_b2 = 0; $vitamin_d = 0; $vitamin_pp = 0; $na = 0; $k = 0; $ca = 0; $f = 0; $se = 0;?>

                    <?foreach($menus_dishes as $key => $m_dish){ ?>
                        <? if($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id){ ?>

                            <? $count++;?>
                            <!--ВЫВОД ПОСТРОЧНО КАЖДОГО БЛЮДА В РАЗАРЕЗЕ ПРИЕМА ПИЩИ-->
                            <tr data-id="<?= $m_dish->id;?>">
                                <td class="text-center"><?= $m_dish->get_techmup($m_dish->dishes_id)?></td>
                                <td><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
                                <td class="text-center"><?= $m_dish->yield ?></td>
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
                                    <td class="text-center"><? $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'),2); echo $vitamins['se']; $se = $se + $vitamins['se']?></td>

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
                        <td class="text-center"><? $yield = $model->get_total_yield($get, $cycle_id, $day->id, $nutrition->id); echo $yield; $data[$nutrition->id]['yield'] = $data[$nutrition->id]['yield'] + $yield; $super_total_yield = $super_total_yield + $yield;?></td>
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
                            <td class="text-center"><?= $se; $data[$nutrition->id]['vitamin_se'] = $data[$nutrition->id]['vitamin_se'] + $se; $super_total_se = $super_total_se + $se;?></td>

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
                        <td class="text-center"><?= $super_total_se; ?></td>

                </tr>


        </tbody>
        </table>
        <?php //} ?>
        <?php } ?>
        <?php } ?>

    </div>

<?php } ?>

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


/*$( ".beforeload" ).click(function() {
  $('.beforeload').append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});*/
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
