<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\MenusDays;
use common\models\Days;
use common\models\RecipesCollection;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Технологические карты';
$this->params['breadcrumbs'][] = $this->title;

$recipes = array(0 => 'Все ...');
$recipes_bd = ArrayHelper::map(RecipesCollection::find()->where(['organization_id' => [7, Yii::$app->user->identity->organization_id]])->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name');
$recipes = ArrayHelper::merge($recipes,$recipes_bd);

$dishes_items = ArrayHelper::map(\common\models\Dishes::find()->orderBy(['recipes_collection_id' => SORT_ASC, 'name'=> SORT_ASC])->all(), 'id', 'name');


?>
<div class="menus-dishes-index">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <div class="container mb-30">
        <div class="row">
            <div class="col-11 col-md-3">
                <?= $form->field($model, 'recipes_id')->dropDownList($recipes, [
                    'class' => 'form-control', 'options' => [$post['recipes_id'] => ['Selected' => true]],
                    'onchange' => '
                  $.get("../menus-dishes/disheslist?id="+$(this).val(), function(data){
                    $("select#techmupform-dishes_id").html(data);
                    document.getElementById("techmupform-dishes_id").disabled = false;
                  });'
                ]); ?>
            </div>

            <div class="col-11 col-md-3">
                <?= $form->field($model, 'dishes_id')->dropDownList($dishes_items, [
                    'class' => 'form-control', 'options' => [$post['dishes_id'] => ['Selected' => true]],
                ]); ?>
            </div>

            <div class="col-11 col-md-3">
                <?= $form->field($model, 'netto')->TextInput(['value' => $post['netto']]); ?>
            </div>

            <div class="col-11 col-md-3">
                <?= $form->field($model, 'count')->TextInput(['value' => $post['count']])->label('Количество питающихся'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['class' => 'btn main-button-3 beforeload mb-3']) ?>
                <button class="btn main-button-3 load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Посмотреть...
                </button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>


<? if($post){?>
<?
    echo '<div class="table p-3 container">';
    echo '<p class="mb-1 mt-3"><b>Технологическая карта кулинарного изделия (блюда):</b> '.$dishes->techmup_number.'</p>';
    echo '<p class="mb-1"><b>Наименование изделия:</b> '.$dishes->name.'</p>';
    echo '<p class="mb-1"><b>Номер рецептуры:</b> '.$dishes->techmup_number.'</p>';
    echo '<p class="mb-3" style="max-width: 1200px;"><b>Наименование сборника рецептур, год выпуска, автор:</b> '.$dishes->get_recipes($dishes->recipes_collection_id)->name.', '. $dishes->get_recipes($dishes->recipes_collection_id)->year.' </p>';
    ?>
    <b>Пищевые вещества:</b><br>
    <table class="table_th0 table-responsive">
        <tr class="">
            <th class="text-center">№</th>
            <th class="text-center">Наименование сырья</th>
            <th class="text-center">Брутто, г.</th>
            <th class="text-center">Нетто, г.</th>
            <th class="text-center">Белки, г.</th>
            <th class="text-center">Жиры, г.</th>
            <th class="text-center">Углеводы, г.</th>
            <th class="text-center">Энергетическая ценность, ккал.</th>
        </tr>
        <?php $super_total_yield = 0; $super_total_protein = 0; $super_total_fat = 0; $super_total_carbohydrates_total = 0; $super_total_energy_kkal = 0; $super_total_vitamin_a = 0; $super_total_vitamin_c = 0; $super_total_vitamin_b1 = 0; $super_total_vitamin_b2 = 0; $super_total_vitamin_d = 0; $super_total_vitamin_pp = 0; $super_total_na = 0; $super_total_k = 0; $super_total_ca = 0; $super_total_f = 0; $super_total_se = 0; $super_total_i = 0; $super_total_fe = 0; $super_total_p = 0; $super_total_mg = 0; $number_row = 1;?>
        <? foreach ($dishes_products as $d_product){?>
            <tr>
                <td class="text-center"><?=$number_row?></td>
                <td class="text-center"><?= $d_product->get_products($d_product->products_id)->name?></td>
                <td class="text-center"><?= round($d_product->gross_weight * $indicator * $post['count'], 1) ?></td>
                <td class="text-center"><?= round($d_product->net_weight * $indicator * $post['count'], 1)?></td>
                <td class="text-center"><? $protein = round($d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'protein') * (($d_product->net_weight/100) *($post['netto'] / $dishes->yield)) * $post['count'], 2); echo $protein; $super_total_protein = $super_total_protein + $protein; ?></td>
                <td class="text-center"><? $fat = round($d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'fat') * (($d_product->net_weight/100) *($post['netto'] / $dishes->yield)) * $post['count'], 2); echo $fat; $super_total_fat = $super_total_fat + $fat;?></td>
                <td class="text-center"><? $carbohydrates_total = round($d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'carbohydrates_total') * (($d_product->net_weight/100) *($post['netto'] / $dishes->yield)) * $post['count'], 2); echo $carbohydrates_total; $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;?></td>
                <td class="text-center"><? $energy_kkal = round($d_product->get_kkal($d_product->products_id, $d_product->dishes_id) * (($d_product->net_weight/100) *($post['netto'] / $dishes->yield)) * $post['count'], 2); echo $energy_kkal; $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;?></td>
            </tr>
            <?$number_row++;?>
        <?}?>
        <tr>
            <td colspan="3"><b>Выход:</b></td>
            <td class="text-center"><b><?= $post['netto'] * $post['count']?></b></td>
            <td class="text-center"><b><?= $super_total_protein; ?></b></td>
            <td class="text-center"><b><?= $super_total_fat; ?></b></td>
            <td class="text-center"><b><?= $super_total_carbohydrates_total; ?></b></td>
            <td class="text-center"><b><?= $super_total_energy_kkal; ?></b></td>
        </tr>
    </table>


<!--    <b>Витамины и минеральные вещества</b>-->
<!--    <table class="table_th0 table-responsive">-->
<!--        <tr class="">-->
<!--            <th class="text-center">№</th>-->
<!--            <th class="text-center">Продукт</th>-->
<!--            <th class="text-center">B1, мг</th>-->
<!--            <th class="text-center">B2, мг</th>-->
<!--            <th class="text-center">А, мкг. рет. экв.</th>-->
<!--            <th class="text-center">РР, мг.</th>-->
<!--            <th class="text-center">C, мг.</th>-->
<!--            <th class="text-center">Na, мг.</th>-->
<!--            <th class="text-center">K, мг.</th>-->
<!--            <th class="text-center">Ca, мг.</th>-->
<!--            <th class="text-center">Mg, мг.</th>-->
<!--            <th class="text-center">P, мг.</th>-->
<!--            <th class="text-center">FE, мг.</th>-->
<!--            <th class="text-center">I, мкг.</th>-->
<!--            <th class="text-center">Se, мкг.</th>-->
<!--        </tr>-->
<!--        --><?//$number_row=1;?>
<!--        --><?// foreach ($dishes_products as $d_product){?>
<!--            <tr>-->
<!--                <td class="text-center">--><?//=$number_row?><!--</td>-->
<!--                <td class="text-center">--><?//= $d_product->get_products($d_product->products_id)->name?><!--</td>-->
<!--                <td class="text-center">--><?// $vitamin_b1 = $d_product->get_products($d_product->products_id)->vitamin_b1 * $indicator * $post['count']; echo $vitamin_b1; $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1; ?><!--</td>-->
<!--                <td class="text-center">--><?// $vitamin_b2 = $d_product->get_products($d_product->products_id)->vitamin_b2 * $indicator* $post['count']; echo $vitamin_b2; $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2; ?><!--</td>-->
<!--                <td class="text-center">--><?// $vitamin_a = $d_product->get_products($d_product->products_id)->vitamin_a * $indicator* $post['count']; echo $vitamin_a; $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a; ?><!--</td>-->
<!--                <td class="text-center">--><?// $vitamin_pp = $d_product->get_products($d_product->products_id)->vitamin_pp * $indicator* $post['count']; echo $vitamin_pp; $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp; ?><!--</td>-->
<!--                <td class="text-center">--><?// $vitamin_c = $d_product->get_products($d_product->products_id)->vitamin_c * $indicator* $post['count']; echo $vitamin_c; $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c; ?><!--</td>-->
<!--                <td class="text-center">--><?// $na = $d_product->get_products($d_product->products_id)->na * $indicator* $post['count']; echo $na; $super_total_na = $super_total_na + $na; ?><!--</td>-->
<!--                <td class="text-center">--><?// $k = $d_product->get_products($d_product->products_id)->k * $indicator* $post['count']; echo $k; $super_total_k = $super_total_k + $k; ?><!--</td>-->
<!--                <td class="text-center">--><?// $ca = $d_product->get_products($d_product->products_id)->ca * $indicator* $post['count']; echo $ca; $super_total_ca = $super_total_ca + $ca; ?><!--</td>-->
<!--                <td class="text-center">--><?// $mg = $d_product->get_products($d_product->products_id)->mg * $indicator* $post['count']; echo $mg; $super_total_mg = $super_total_mg + $mg; ?><!--</td>-->
<!--                <td class="text-center">--><?// $p = $d_product->get_products($d_product->products_id)->p * $indicator* $post['count']; echo $p; $super_total_p = $super_total_p + $p; ?><!--</td>-->
<!--                <td class="text-center">--><?// $fe = $d_product->get_products($d_product->products_id)->fe * $indicator* $post['count']; echo $fe; $super_total_fe = $super_total_fe + $fe; ?><!--</td>-->
<!--                <td class="text-center">--><?// $i = $d_product->get_products($d_product->products_id)->i * $indicator* $post['count']; echo $i; $super_total_i = $super_total_i + $i; ?><!--</td>-->
<!--                <td class="text-center">--><?// $se = $d_product->get_products($d_product->products_id)->se * $indicator* $post['count']; echo $se; $super_total_se = $super_total_se + $se; ?><!--</td>-->
<!--            </tr>-->
<!--            --><?//$number_row++;?>
<!--        --><?//}?>
<!--        <tr>-->
<!--            <td colspan="2"><b>Итого</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_vitamin_b1;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_vitamin_b2;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_vitamin_a;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_vitamin_pp;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_vitamin_c;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_na;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_k;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_ca;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_mg;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_p;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_fe;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_i;?><!--</b></td>-->
<!--            <td class="text-center"><b>--><?//= $super_total_se;?><!--</b></td>-->
<!--        </tr>-->
<!--    </table>-->
    <? echo '<p class="mb-1 mt-3"><b>Способ обработки:</b> '.$dishes->get_culinary_processing($dishes->culinary_processing_id).'</p>';?>
    <? echo '<p class="mb-2" style="max-width: 1200px;"><b>Технология приготовления:</b> '.$dishes->description.'</p>';?>

    <b>Характеристика блюда на выходе:</b>
    <? echo '<p class="mb-3" style="max-width: 1200px;">'.$dishes->dishes_characters.'</p>';?>
<div class="text-center mt-5">
    <?/*= Html::button('<span class="glyphicon glyphicon-download"></span> Скачать в PDF технологическую карту', [
        'title' => Yii::t('yii', 'Скачать в PDF технологическую карту'),
        'data-toggle'=>'tooltip',
        'class'=>'btn btn-secondary',
    ]);*/?>
</div>
<?php echo '</div>'; ?>
<? } ?>
<?
$script = <<< JS
//$('#techmupform-dishes_id').attr('disabled', 'true');
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>