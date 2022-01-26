<?php

use common\models\ProductsChangeOrganization;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\Days;
use common\models\MenusDays;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Меню за день';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->one();

$menu_cycle_count = $first_menu->cycle;
$menu_cycle = [];
for ($i = 1; $i <= $menu_cycle_count; $i++){
    $menu_cycle[$i] = $i;//массив из подходящи циклов
}

$my_days = MenusDays::find()->where(['menu_id' => $first_menu->id])->all();
foreach ($my_days as $m_day){
    $ids[] = $m_day->days_id;
}
$days = Days::find()->where(['id' => $ids])->all();
$my_days_items = ArrayHelper::map($days, 'id', 'name');

$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_cycle = ['class' => 'form-control', 'options' => [1 => ['Selected' => true]]];
$params_days = ['class' => 'form-control', 'options' => [$ids[0] => ['Selected' => true]]];

if (!empty($post))
{
    $my_menus = Menus::findOne($post['menu_id']);
    $menu_cycle_count = $my_menus->cycle;
    $menu_cycle = [];
    for ($i = 1; $i <= $menu_cycle_count; $i++)
    {
        $menu_cycle[$i] = $i;//массив из подходящи циклов
    }
    $my_days = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();
    foreach ($my_days as $m_day)
    {
        $ids[] = $m_day->days_id;
    }
    $days = Days::find()->where(['id' => $ids])->all();
    $my_days_items = ArrayHelper::map($days, 'id', 'name');
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];
    $params_days = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];
}
?>
<h1 class="text-center"><?= Html::encode($this->title) ?></h1>

<? if (Yii::$app->user->can('director')) { ?>
    <?php $form = ActiveForm::begin(); ?>
    <div class="container mb-30">
        <div class="row">
            <div class="col-11 col-md-4">
                <?= $form->field($model, 'menu_id')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
                    'onchange' => '
                  $.get("../menus-dishes/cyclelistday?id="+$(this).val(), function(data){
                    $("select#menusdishes-cycle").html(data);
                  });
                  $.get("../menus-dishes/daylist?id="+$(this).val(), function(data){
                    $("select#menusdishes-days_id").html(data);
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

            <div class="col-11 col-md-4">
                <?= $form->field($model, 'cycle')->dropDownList($menu_cycle, $params_cycle) ?>
            </div>

            <div class="col-11 col-md-4">
                <?= $form->field($model, 'days_id')->dropDownList($my_days_items, $params_days) ?>
            </div>
        </div>
        <!--        Блок с заполняемыми инпутами для информации. id не менять иначе не сработает-->
        <? if (empty($post)) {
            $menu_id = $first_menu->id;
        } else {
            $menu_id = $post['menu_id'];
        } ?>
        <div class="row">
            <div class="col">
                <label><b>Характеристика питающихся</b>
                    <input type="text" class="form-control" id="characters" disabled
                           value="<?= $model->insert_info($menu_id, 'feeders_characters'); ?>"></label>
            </div>
            <div class="col">
                <label><b>Возрастная категория</b>
                    <input type="text" class="form-control" id="age" disabled
                           value="<?= $model->insert_info($menu_id, 'age_info'); ?>"></label>
            </div>
            <div class="col">
                <label><b>Срок действия меню</b>
                    <input type="text" class="form-control" id="insert-srok" disabled
                           value="<?= $model->insert_info($menu_id, 'sroki'); ?>"></label>
            </div>
        </div>
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
<? } ?>

<div class="row justify-content-center">
    <div class="col-auto">
        <?php $super_total_yield = 0;
        $super_total_protein = 0;
        $super_total_fat = 0;
        $super_total_carbohydrates_total = 0;
        $super_total_energy_kkal = 0;
        $super_total_vitamin_a = 0;
        $super_total_vitamin_c = 0;
        $super_total_vitamin_b1 = 0;
        $super_total_vitamin_b2 = 0;
        $super_total_vitamin_d = 0;
        $super_total_vitamin_pp = 0;
        $super_total_na = 0;
        $super_total_k = 0;
        $super_total_ca = 0;
        $super_total_f = 0;
        $super_total_se = 0; ?>
        <? if (!empty($nutritions)){
            if (Yii::$app->user->can('director'))
            {
                $menu_id = $post['menu_id'];
                $cycle = $post['cycle'];
                $days_id = $post['days_id'];
            }
            else
            {
                $post['menu_id'] = $menu_id;
                $post['cycle'] = 1;
                $post['days_id'] = 1;
            }
        ?>
        <? $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->all();
        if (!empty($products_change)) { ?>
            <p class="text-center mt-3" style="font-size: 18px;"><b>Значения БЖУ, витаминов и минеральных веществ учтены при замене выбранных продуктов:</b></p>
            <? foreach ($products_change as $prod_ch) { ?>
                <p class="text-center">
                    <i><?= \common\models\Products::findOne($prod_ch->products_id)->name . '</i><b> → </b><i>' . \common\models\Products::findOne($prod_ch->change_products_id)->name ?></i>
                </p>
            <? } ?>
        <? } ?>
        <? foreach ($nutritions as $nutrition){ ?>
        <div class="block" style="margin-top: 10px;">
            <table class="table_th0 table-hover table-responsive last">
                <thead>
                <tr class="text-center"><? echo '<p class="mb-0" style="font-size: 20px; font-weight: 500;">' . (Yii::$app->user->can('director') ? $nutrition->name : $nutrition->name2) . '</p>' ?></tr>
                <tr>
                    <? if (Yii::$app->user->can('director')){?><th class="text-center align-middle main-info-see" rowspan="2" style="min-width: 200px">Номер техкарты</th><?}?>

                    <th class="text-center align-middle main-info-see" rowspan="2" style="min-width: 400px">Название блюда</th>
                    <th class="text-center align-middle main-info-see" rowspan="2">Выход (г)</th>
                    <th class="text-center align-middle main-info-see" rowspan="2">Белки (г)</th>
                    <th class="text-center align-middle main-info-see" rowspan="2">Жиры (г)</th>
                    <th class="text-center align-middle main-info-see" rowspan="2">Углеводы (г)</th>
                    <th class="text-center align-middle main-info-see" rowspan="2">Эн. ценность (ккал)</th>
                    <th class="text-center main-info-see" colspan="6">Витамины</th>
                    <th class="text-center main-info-see" colspan="5">Минеральные вещества</th>
                </tr>
                <tr>
                    <th class="text-center main-info-see">A, мкг рет.экв</th>
                    <th class="text-center main-info-see">C, мг</th>
                    <th class="text-center main-info-see">B1, мг</th>
                    <th class="text-center main-info-see">B2, мг</th>
                    <th class="text-center main-info-see">D, мкг</th>
                    <th class="text-center main-info-see">PP, мг</th>
                    <th class="text-center main-info-see">Na, мг</th>
                    <th class="text-center main-info-see">K, мг</th>
                    <th class="text-center main-info-see">Ca, мг</th>
                    <th class="text-center main-info-see">F, мг</th>
                    <th class="text-center main-info-see">Se, мкг</th>
                </tr>
                </thead>
                <tbody>
                <? $count = 0;
                $indicator = 0;
                $energy_kkal = 0;
                $protein = 0;
                $fat = 0;
                $carbohydrates_total = 0; ?>
                <? foreach ($menus_dishes as $key => $m_dish) { ?>
                    <? //echo $nutrition->id. ' ==='.$m_dish->nutrition_id;?>
                    <? if ($nutrition->id == $m_dish->nutrition_id) { ?>
                        <? $count++; ?>
                        <!--ВЫВОД ПОСТРОЧНО КАЖДОГО БЛЮДА В РАЗАРЕЗЕ ПРИЕМА ПИЩИ-->
                        <tr data-id="<?= $m_dish->id; ?>">
                            <? if (Yii::$app->user->can('director')){?><td class="text-center"><?= $m_dish->get_techmup($m_dish->dishes_id) ?></td><?}?>
                            <td><?= $m_dish->get_dishes($m_dish->dishes_id) ?></td>

                            <td class="text-center"><?= $m_dish->yield ?></td>
                            <!--<td class="text-center">--><? //= round($m_dish->get_field($m_dish->dishes_id, $m_dish->yield, 'protein'),1)?><!--</td> без учета уварки-->
                            <td class="text-center"><? $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'), 1); echo $protein_dish; $protein = $protein_dish + $protein; ?></td>
                            <td class="text-center"><? $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'), 1); echo $fat_dish; $fat = $fat_dish + $fat; ?></td>
                            <td class="text-center"><? $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'), 1); echo $carbohydrates_total_dish; $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total; ?></td>

                            <td class="text-center"><? $kkal = round($m_dish->get_kkal_dish($m_dish->id), 1); echo $kkal; $energy_kkal = $energy_kkal + $kkal; ?></td>

                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'), 1) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'), 1) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'), 2) ?></td>
                            <td class="text-center"><?= round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'), 2) ?></td>
                            <? unset($menus_dishes[$key]); ?>
                        </tr>
                    <?}else {break;}} ?>
                <? if ($count > 0) { ?>
                    <!--ВЫВОД СТРОЧКИ "ИТОГО" В РАЗРЕЗЕ КАЖДОГО ПРИЕМА ПИЩИ-->
                    <tr class="table-primary">
                        <td colspan="1">Итого за <?= (Yii::$app->user->can('director') ? $nutrition->name : $nutrition->name2) ?></td>
                        <?if(Yii::$app->user->can('director')){?><td></td><?}?>
                        <td class="text-center"><? $yield = $model->get_total_yield($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id); echo $yield; $super_total_yield = $super_total_yield + $yield; ?></td>
                        <td class="text-center"><? echo $protein; $super_total_protein = $super_total_protein + $protein; ?></td>
                        <td class="text-center"><? echo $fat; $super_total_fat = $super_total_fat + $fat; ?></td>
                        <td class="text-center"><? echo $carbohydrates_total; $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total; ?></td>
                        <td class="text-center"><? echo $energy_kkal; $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal; ?></td>
                        <td class="text-center"><? $vitamin_a = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'vitamin_a'), 1); echo $vitamin_a; $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a; ?></td>
                        <td class="text-center"><? $vitamin_c = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'vitamin_c'), 1); echo $vitamin_c; $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c; ?></td>
                        <td class="text-center"><? $vitamin_b1 = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'vitamin_b1'), 2); echo $vitamin_b1; $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1; ?></td>
                        <td class="text-center"><? $vitamin_b2 = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'vitamin_b2'), 2); echo $vitamin_b2; $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2; ?></td>
                        <td class="text-center"><? $vitamin_d = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'vitamin_d'), 2); echo $vitamin_d; $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d; ?></td>
                        <td class="text-center"><? $vitamin_pp = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'vitamin_pp'), 2); echo $vitamin_pp; $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp; ?></td>
                        <td class="text-center"><? $na = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'na'), 2); echo $na; $super_total_na = $super_total_na + $na; ?></td>
                        <td class="text-center"><? $k = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'k'), 2); echo $k; $super_total_k = $super_total_k + $k; ?></td>
                        <td class="text-center"><? $ca = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'ca'), 2); echo $ca; $super_total_ca = $super_total_ca + $ca; ?></td>
                        <td class="text-center"><? $f = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'f'), 2); echo $f; $super_total_f = $super_total_f + $f; ?></td>
                        <td class="text-center"><? $se = round($model->get_total_vitamin($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'se'), 2); echo $se; $super_total_se = $super_total_se + $se; ?></td>
                    </tr>
                    <tr class="table-success">
                        <td>Рекомендуемая величина</td>
                        <?if(Yii::$app->user->can('director')){?><td></td><?}?>
                        <td></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'protein_middle_weight'); ?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'fat_middle_weight'); ?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'carbohydrates_middle_weight'); ?></td>
                        <td class="text-center"><?= $model->get_recommended_normativ($post['menu_id'], $nutrition->id, 'middle_kkal'); ?></td>
                        <td class="text-center"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="table-warning">
                        <!--Для бжу функции есть в модели, но чтобы не нагружать страницу расчитывается через переменные-->
                        <td colspan="2">Соотношение БЖУ</td>
                        <td></td>
                        <td class="text-center">1</td>
                        <td class="text-center"><?=($protein != 0) ? round(($fat / $protein), 2) : 0 ?></td>
                        <td class="text-center"><?= ($protein != 0) ? round(($carbohydrates_total / $protein), 2) : 0 ?></td>
                    </tr>
                    <tr class="table-warning">
                        <td colspan="2">Удельный вес БЖУ в %</td>
                        <td></td>
                        <td class="text-center"><?= $model->get_procent($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'protein') . '%'; ?></td>
                        <td class="text-center"><?= $model->get_procent($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'fat') . '%'; ?></td>
                        <td class="text-center"><?= $model->get_procent($post['menu_id'], $post['cycle'], $post['days_id'], $nutrition->id, 'carbohydrates_total') . '%'; ?></td>
                    </tr>
                <? } ?>
                <? } ?>
                <tr class="itog_day table-danger">
                    <td>Итого за день</td><?if(Yii::$app->user->can('director')){?><td></td><?}?>
                    <td class="text-center"><?= $super_total_yield; ?></td>
                    <td class="text-center"><?= $super_total_protein; ?></td>
                    <td class="text-center"><?= $super_total_fat; ?></td>
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
                <tr class="itog_day table-success">
                    <td colspan="2">Рекомендуемая величина за день</td>
                    <?if(Yii::$app->user->can('director')){?><td></td><?}?>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'protein_middle_weight');?></td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'fat_middle_weight');?></td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'carbohydrates_middle_weight');?></td>
                    <td class="text-center"><?= $model->get_recommended_normativ_of_day($post['menu_id'], 'middle_kkal');?></td>
                    <? if($post['days_id'] == 1){?><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><?}?>
                </tr>
            </table>
            <?php } ?>
        </div>
    </div>

<?
    $script = <<< JS
//Прикрепление строчек итого за день и бжу за день к последней таблице
$(".last:last").append($(".itog_day"));
$(".last:last").append($(".procent_day"));

$( ".beforeload" ).click(function() {
  $(".beforeload").css('display','none');
  $(".load").css('display','block');
});

JS;
    $this->registerJs($script, yii\web\View::POS_READY);
    ?>
