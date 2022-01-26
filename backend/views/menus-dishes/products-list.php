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

$this->title = 'Отчет о перечне продуктов';
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
//    !!! В $post['days_id'] ХРАНИТСЯ ИНФОРМАЦИЯ БРУТТО/НЕТТО    !!!!
$chemistry_items = [0 => 'Брутто', 1 => 'Нетто'];
$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
$params_chemistry = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$params_norma= ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];
$norma_items = [0 => 'Показать по дням', 1 => 'Показать по приемам пищи'];
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
    $days = Days::find()->where(['id' => $ids])->all();



    $chemistry_items = [0 => 'Брутто', 1 => 'Нетто'];
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_chemistry = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];
    $params_norma= ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];
    $count_cycle_day = count($menu_cycle) * count($days);
    //print_r(count($products_categories));



    $normativ = [];
    $normativ['vitamin_c'] = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_c', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['vitamin_b1'] = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_b1', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['vitamin_b2'] = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_b2', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['vitamin_a'] = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_a', 'age_info_id' => $my_menus->age_info_id])->one()->value;

    $normativ['vitamin_d'] = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_d', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['vitamin_pp'] = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_pp', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['ca'] = \common\models\NormativVitaminDay::find()->where(['name' => 'ca', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['k'] = \common\models\NormativVitaminDay::find()->where(['name' => 'k', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['f'] = \common\models\NormativVitaminDay::find()->where(['name' => 'f', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['p'] = \common\models\NormativVitaminDay::find()->where(['name' => 'p', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['mg'] = \common\models\NormativVitaminDay::find()->where(['name' => 'mg', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['fe'] = \common\models\NormativVitaminDay::find()->where(['name' => 'fe', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['i'] = \common\models\NormativVitaminDay::find()->where(['name' => 'i', 'age_info_id' => $my_menus->age_info_id])->one()->value;
    $normativ['se'] = \common\models\NormativVitaminDay::find()->where(['name' => 'se', 'age_info_id' => $my_menus->age_info_id])->one()->value;
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
        font-size: 13px;
        border: 1px solid #c2c2c2!important;
    }
    td{
        border: 1px solid #c2c2c2!important;
        font-size: 14px!important;
    }

</style>



    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?if(empty($my_menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr')))){?>
        <p class="text-center" style="color: red"><b>У Вас не созданы меню и не добавлены блюда.(Перейдите в раздел "Архив меню" или "Настройка меню")</b></p>
    <?}?>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="container mb-30 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <?= $form->field($model, 'menu_id')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
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
                ]); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'cycle')->dropDownList($norma_items, $params_norma)->label('Вид отображения'); ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'days_id')->dropDownList($chemistry_items, $params_chemistry)->label('Брутто/Нетто'); ?>
            </div>
        </div>

        <!--        Блок с заполняемыми инпутами для информации. id не менять иначе не сработает-->
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
        <?php ActiveForm::end(); ?>
    </div>

<?php //print_r($products)?>
<?php if($post){?>
<!--       !!! В $post['days_id'] ХРАНИТСЯ ИНФОРМАЦИЯ БРУТТО/НЕТТО !!!!      -->
    <?if($post['days_id'] == 0){echo '<h4 class="text-center">Перечень продуктов, Брутто, г</h4>';}else{echo '<h4 class="text-center">Перечень продуктов, Нетто, г</h4>';}?>
<div class="row justify-content-center">
    <?if($post['cycle'] == 0){?>
    <div class="col-auto">
    <table class="table_th0 fixtable table-hover ">
    <tr class="">
        <th class="text-center align-middle " rowspan="2">№</th>
        <th class="text-center align-middle " rowspan="2">Группа продукта</th>
        <th class="text-center align-middle " rowspan="2">Продукт</th>
        <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->
        <?foreach ($menu_cycle as $cycle){?>
                <th class="text-center align-middle" colspan=<?=count($days)?>."'><?=$cycle.' неделя '?></th>
        <? } ?>
        <th class="text-center align-middle" rowspan="2">Итого</th>
        <th class="text-center align-middle" rowspan="2">Среднесуточное значение</th>

        <th class="text-center" colspan="6">Витамины</th>
        <th class="text-center" colspan="10">Минеральные вещества</th>
    </tr>
    <tr class="">
        <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->
        <?foreach ($menu_cycle as $cycle){
            foreach($days as $day){?>
                <th class="text-center"><?=$day->name;?></th>
            <? } ?>
        <? } ?>

        <th class="text-center">A, мкг рет.экв</th>
        <th class="text-center">C, мг</th>
        <th class="text-center">B1, мг</th>
        <th class="text-center">B2, мг</th>
        <th class="text-center">D, мкг</th>
        <th class="text-center">PP, мг</th>
        <th class="text-center">Na, мг</th>
        <th class="text-center">K, мг</th>
        <th class="text-center">Ca, мг</th>
        <th class="text-center">F, мкг</th>
        <th class="text-center">Mg, мг</th>
        <th class="text-center">P, мг</th>
        <th class="text-center">Fe, мг</th>
        <th class="text-center">I, мкг</th>
        <th class="text-center">Se, мкг</th>
    </tr>
        <?$number_row=1; ?>
        <? $vitamins_string =[]; foreach($products_categories as $product_cat){?>
            <? foreach($products as $product){
                $totality = 0;
                if($product_cat->id == $product->products_category_id){
                ?>
                <tr>
                    <!-- вывод Название и категории -->
                    <td class="text-center"><?=$number_row?></td>
            <td><?=$product_cat->name?></td>
                    <?/*ПОИСК ЗАМЕНЫ. зАМЕНЯЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПРОДУКТ НА ИНОЙ.*/
                    $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $product->id])->one();
                    if(!empty($products_change)){?>
                        <td style="min-width: 200px;"><b>Заменено на </b><?=Products::findOne($products_change->change_products_id)->name;?>(Было: <small><i><?=$product->name?></i></small>)</td>
                    <?}else{?>
                        <td  style="min-width: 200px;" class="align-middle"><?=$product->name?></td>
                    <?}?>
                    <!-- Заполнение таблицы данными-->


                    <?$vitamins = []; foreach ($menu_cycle as $cycle){
                        foreach($days as $day){;?>
                            <td class="text-center"><? $total = $product->get_total_yield_day($product->id, $post['menu_id'], $cycle, $day->id, $post['days_id']); echo $total['yield']; if($total['yield'] == '-'){$total['yield'] = 0;} $totality = $total['yield'] + $totality; ?></td>
                            <?$vitamins['vitamin_a'] = $vitamins['vitamin_a'] + $total['vitamin_a'];?>
                            <?$vitamins['vitamin_c'] = $vitamins['vitamin_c'] + $total['vitamin_c'];?>
                            <?$vitamins['vitamin_b1'] = $vitamins['vitamin_b1'] + $total['vitamin_b1'];?>
                            <?$vitamins['vitamin_b2'] = $vitamins['vitamin_b2'] + $total['vitamin_b2'];?>
                            <?$vitamins['vitamin_d'] = $vitamins['vitamin_d'] + $total['vitamin_d'];?>

                            <?$vitamins['vitamin_pp'] = $vitamins['vitamin_pp'] + $total['vitamin_pp'];?>
                            <?$vitamins['na'] = $vitamins['na'] + $total['na'];?>
                            <?$vitamins['k'] = $vitamins['k'] + $total['k'];?>
                            <?$vitamins['ca'] = $vitamins['ca'] + $total['ca'];?>
                            <?$vitamins['f'] = $vitamins['f'] + $total['f'];?>
                            <?$vitamins['mg'] = $vitamins['mg'] + $total['mg'];?>
                            <?$vitamins['p'] = $vitamins['p'] + $total['p'];?>
                            <?$vitamins['fe'] = $vitamins['fe'] + $total['fe'];?>
                            <?$vitamins['i'] = $vitamins['i'] + $total['i'];?>
                            <?$vitamins['se'] = $vitamins['se'] + $total['se'];?>
                        <? } ?>
                    <? } ?>
            <!--   Вывод столбцов итоги и сред знач-->
                    <td class="text-center"><b><?/*$totality = $product->get_super_total_yield_day($product->id, $post['menu_id'], $post['days_id']); */ echo $totality; if($totality == '-'){$totality = 0;}?></b></td>
                    <td class="text-center"><b><?=round($totality/(count($menu_cycle) * count($days)), 2);?></b></td>

                    <?if(!empty($products_change)){?>
                    <?$product = Products::findOne($products_change->change_products_id);?>
                    <?}?>

                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_a']/$count_cycle_day,2);$vitamins_string['vitamin_a'] = $vitamins_string['vitamin_a'] + round($vitamins['vitamin_a']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_c']/$count_cycle_day,3);$vitamins_string['vitamin_c'] = $vitamins_string['vitamin_c'] + round($vitamins['vitamin_c']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_b1']/$count_cycle_day,3);$vitamins_string['vitamin_b1'] = $vitamins_string['vitamin_b1'] + round($vitamins['vitamin_b1']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_b2']/$count_cycle_day,3);$vitamins_string['vitamin_b2'] = $vitamins_string['vitamin_b2'] + round($vitamins['vitamin_b2']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_d']/$count_cycle_day,3);$vitamins_string['vitamin_d'] = $vitamins_string['vitamin_d'] + round($vitamins['vitamin_d']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_pp']/$count_cycle_day,3);$vitamins_string['vitamin_pp'] = $vitamins_string['vitamin_pp'] + round($vitamins['vitamin_pp']/$count_cycle_day,2);?></td>

                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['na']/$count_cycle_day,2);$vitamins_string['na'] = $vitamins_string['na'] + round($vitamins['na']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['k']/$count_cycle_day,2);$vitamins_string['k'] = $vitamins_string['k'] + round($vitamins['k']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['ca']/$count_cycle_day,2);$vitamins_string['ca'] = $vitamins_string['ca'] + round($vitamins['ca']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['f']/$count_cycle_day,2);$vitamins_string['f'] = $vitamins_string['f'] + round($vitamins['f']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['mg']/$count_cycle_day,2);$vitamins_string['mg'] = $vitamins_string['mg'] + round($vitamins['mg']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['p']/$count_cycle_day,2);$vitamins_string['p'] = $vitamins_string['p'] + round($vitamins['p']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['fe']/$count_cycle_day,2);$vitamins_string['fe'] = $vitamins_string['fe'] + round($vitamins['fe']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['i']/$count_cycle_day,2);$vitamins_string['i'] = $vitamins_string['i'] + round($vitamins['i']/$count_cycle_day,2);?></td>
                    <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['se']/$count_cycle_day,2);$vitamins_string['se'] = $vitamins_string['se'] + round($vitamins['se']/$count_cycle_day,2);?></td>

                </tr>
                <?$number_row++;?>
                <? } ?>
            <? } ?>
        <?}?>
        <tr style="background-color: #ede8b9!important;">
            <td class="text-center" colspan="3" ><b>Итого за день</b></td>
            <?foreach ($menu_cycle as $cycle){
                foreach($days as $day){?>
                    <td class="text-center">-</td>
                <? } ?>
            <? } ?>
            <td class="text-center" >-</td>
            <td class="text-center" >-</td>

            <td class="text-center" ><b><?=$vitamins_string['vitamin_a']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['vitamin_c']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['vitamin_b1']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['vitamin_b2']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['vitamin_d']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['vitamin_pp']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['na']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['k']?></b></td>
            <b></b>
            <td class="text-center" ><b><?=$vitamins_string['ca']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['f']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['mg']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['p']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['fe']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['i']?></b></td>
            <td class="text-center" ><b><?=$vitamins_string['se']?></b></td>
        </tr><b></b>



</table>
    </div>
    <?}else{?>
        <?$nutritions = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->orderBy(['nutrition_id'=> SORT_ASC])->all();
        ?>
                <div class="col-auto">
                    <table class="table_th0 fixtable table-hover">
                        <tr class="">
                            <th class="text-center align-middle" rowspan="2">Прием пищи</th>
                            <th class="text-center align-middle" rowspan="2">№</th>
                            <th class="text-center align-middle" rowspan="2">Группа продукта</th>
                            <th class="text-center align-middle" rowspan="2">Продукт</th>
                            <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->
                            <?foreach ($menu_cycle as $cycle){?>
                                <th class="text-center align-middle" colspan=<?=count($days)?>."'><?=$cycle.' неделя '?></th>
                            <? } ?>
                            <th class="text-center align-middle" rowspan="2">Итого</th>
                            <th class="text-center align-middle" rowspan="2">Среднесуточное значение</th>
                            <th class="text-center" colspan="6">Витамины</th>
                            <th class="text-center" colspan="10">Минеральные вещества</th>
                        </tr>
                        <tr class="">
                            <!-- Динамический вывод названий столбцов '<ЦИКЛ><ДЕНЬ>'-->
                            <?foreach ($menu_cycle as $cycle){
                                foreach($days as $day){?>
                                    <th class="text-center"><?=$day->name;?></th>
                                <? } ?>
                            <? } ?>


                            <th class="text-center">A, мкг рет.экв</th>
                            <th class="text-center">C, мг</th>
                            <th class="text-center">B1, мг</th>
                            <th class="text-center">B2, мг</th>
                            <th class="text-center">D, мкг</th>
                            <th class="text-center">PP, мг</th>
                            <th class="text-center">Na, мг</th>
                            <th class="text-center">K, мг</th>
                            <th class="text-center">Ca, мг</th>
                            <th class="text-center">F, мкг</th>
                            <th class="text-center">Mg, мг</th>
                            <th class="text-center">P, мг</th>
                            <th class="text-center">Fe, мг</th>
                            <th class="text-center">I, мкг</th>
                            <th class="text-center">Se, мкг</th>
                        </tr>
                        <?$number_row=1; $priem = [];?>
                        <?foreach ($nutritions as $nutrition){ $vitamins_string =[];?>

                            <?$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'nutrition_id' => $nutrition->nutrition_id])->orderby(['dishes_id' => SORT_ASC])->all();
                            $dishes_ids = [];
                            $categories_ids = [];

                            foreach ($menus_dishes as $m_dish)
                            {
                            $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->all();
                            foreach ($dishes_products as $d_product)
                            {
                            if (!in_array($d_product->products_id, $dishes_ids))
                            {
                            $dishes_ids[] = $d_product->products_id;
                            }
                            }
                            }
                            $products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
                            $products_count = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->count();
                            foreach ($products as $product)
                            {
                            if (!in_array($product->products_category_id, $categories_ids))
                            {
                            $categories_ids[] = $product->products_category_id;
                            }
                            }
                            $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();?>
                        <? foreach($products_categories as $product_cat){?>
                            <? foreach($products as $product){
                                $totality = 0;
                                if($product_cat->id == $product->products_category_id){
//                                    ?>
                                    <tr>
                                        <?if($priem[$nutrition->nutrition_id] != 1){ $priem[$nutrition->nutrition_id] = 1;?>
                                        <td class="text-center align-middle" rowspan="<?=$products_count?>"><?=NutritionInfo::findOne($nutrition->nutrition_id)->name;?></td>
                                        <?}?>
<!--                                         вывод Название и категории -->
                                        <td class="text-center"><?=$number_row?></td>
                                        <td><?=$product_cat->name?></td>
                                        <?/*ПОИСК ЗАМЕНЫ. зАМЕНЯЛ ЛИ ПОЛЬЗОВАТЕЛЬ ПРОДУКТ НА ИНОЙ.*/
                                        $products_change = ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'products_id' => $product->id])->one();
                                        if(!empty($products_change)){?>
                                            <td ><b>Заменено на </b><?=Products::findOne($products_change->change_products_id)->name;?>(Было: <small><i><?=$product->name?></i></small>)</td>
                                        <?}else{?>
                                            <td class="align-middle"><?=$product->name?></td>
                                        <?}?>
<!--                                        Заполнение таблицы данными-->
<!---->
                                        <?$vitamins = []; foreach ($menu_cycle as $cycle){
                                            foreach($days as $day){?>
                                                <td class="text-center"><?$total = $product->get_total_yield_nutrition($product->id, $post['menu_id'], $cycle, $day->id, $nutrition->nutrition_id, $post['days_id']); echo $total['yield']; if($total['yield'] == '-'){$total['yield'] = 0;} $totality = $total['yield'] + $totality; ?></td>
                                                <?$vitamins['vitamin_a'] = $vitamins['vitamin_a'] + $total['vitamin_a'];?>
                                                <?$vitamins['vitamin_c'] = $vitamins['vitamin_c'] + $total['vitamin_c'];?>
                                                <?$vitamins['vitamin_b1'] = $vitamins['vitamin_b1'] + $total['vitamin_b1'];?>
                                                <?$vitamins['vitamin_b2'] = $vitamins['vitamin_b2'] + $total['vitamin_b2'];?>
                                                <?$vitamins['vitamin_d'] = $vitamins['vitamin_d'] + $total['vitamin_d'];?>

                                                <?$vitamins['vitamin_pp'] = $vitamins['vitamin_pp'] + $total['vitamin_pp'];?>
                                                <?$vitamins['na'] = $vitamins['na'] + $total['na'];?>
                                                <?$vitamins['k'] = $vitamins['k'] + $total['k'];?>
                                                <?$vitamins['ca'] = $vitamins['ca'] + $total['ca'];?>
                                                <?$vitamins['f'] = $vitamins['f'] + $total['f'];?>
                                                <?$vitamins['mg'] = $vitamins['mg'] + $total['mg'];?>
                                                <?$vitamins['p'] = $vitamins['p'] + $total['p'];?>
                                                <?$vitamins['fe'] = $vitamins['fe'] + $total['fe'];?>
                                                <?$vitamins['i'] = $vitamins['i'] + $total['i'];?>
                                                <?$vitamins['se'] = $vitamins['se'] + $total['se'];?>
                                            <? } ?>
                                        <? } ?>
<!--                                           Вывод столбцов итоги и сред знач-->
                                        <td class="text-center"><? echo $totality; if($totality == '-'){$totality = 0;}?></td>
                                        <td class="text-center"><?=round($totality/(count($menu_cycle) * count($days)), 2);?></td>
                                        <?if(!empty($products_change)){?>
                                            <?$product = Products::findOne($products_change->change_products_id);?>
                                        <?}?>

                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_a']/$count_cycle_day,2);$vitamins_string['vitamin_a'] = $vitamins_string['vitamin_a'] + round($vitamins['vitamin_a']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_c']/$count_cycle_day,3);$vitamins_string['vitamin_c'] = $vitamins_string['vitamin_c'] + round($vitamins['vitamin_c']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_b1']/$count_cycle_day,3);$vitamins_string['vitamin_b1'] = $vitamins_string['vitamin_b1'] + round($vitamins['vitamin_b1']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_b2']/$count_cycle_day,3);$vitamins_string['vitamin_b2'] = $vitamins_string['vitamin_b2'] + round($vitamins['vitamin_b2']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_d']/$count_cycle_day,3);$vitamins_string['vitamin_d'] = $vitamins_string['vitamin_d'] + round($vitamins['vitamin_d']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['vitamin_pp']/$count_cycle_day,3);$vitamins_string['vitamin_pp'] = $vitamins_string['vitamin_pp'] + round($vitamins['vitamin_pp']/$count_cycle_day,2);?></td>

                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['na']/$count_cycle_day,2);$vitamins_string['na'] = $vitamins_string['na'] + round($vitamins['na']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['k']/$count_cycle_day,2);$vitamins_string['k'] = $vitamins_string['k'] + round($vitamins['k']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['ca']/$count_cycle_day,2);$vitamins_string['ca'] = $vitamins_string['ca'] + round($vitamins['ca']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['f']/$count_cycle_day,2);$vitamins_string['f'] = $vitamins_string['f'] + round($vitamins['f']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['mg']/$count_cycle_day,2);$vitamins_string['mg'] = $vitamins_string['mg'] + round($vitamins['mg']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['p']/$count_cycle_day,2);$vitamins_string['p'] = $vitamins_string['p'] + round($vitamins['p']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['fe']/$count_cycle_day,2);$vitamins_string['fe'] = $vitamins_string['fe'] + round($vitamins['fe']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['i']/$count_cycle_day,2);$vitamins_string['i'] = $vitamins_string['i'] + round($vitamins['i']/$count_cycle_day,2);?></td>
                                        <td class="text-center" style="background-color: #f0f0f0;"><?=round($vitamins['se']/$count_cycle_day,2);$vitamins_string['se'] = $vitamins_string['se'] + round($vitamins['se']/$count_cycle_day,2);?></td>
                                    </tr>
                                    <?$number_row++;?>
                                <? } ?>
                            <? } ?>
                        <?}?>
                            <tr style="background-color: #ede8b9!important;">
                                <td class="text-center" colspan="4" ><b>Итого за <?=NutritionInfo::findOne($nutrition->nutrition_id)->name;?></b></td>
                                <?foreach ($menu_cycle as $cycle){
                                    foreach($days as $day){?>
                                        <td class="text-center">-</td>
                                    <? } ?>
                                <? } ?>
                                <td class="text-center" >-</td>
                                <td class="text-center" >-</td>

                                <td class="text-center" ><b><?=$vitamins_string['vitamin_a']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['vitamin_c']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['vitamin_b1']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['vitamin_b2']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['vitamin_d']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['vitamin_pp']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['na']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['k']?></b></td>
<b></b>
                                <td class="text-center" ><b><?=$vitamins_string['ca']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['f']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['mg']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['p']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['fe']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['i']?></b></td>
                                <td class="text-center" ><b><?=$vitamins_string['se']?></b></td>
                            </tr><b></b>
                            <tr class="table-success" ">
                                <td class="text-center" colspan="4" ><b>Нормативы за <?=NutritionInfo::findOne($nutrition->nutrition_id)->name;?></b></td>
                                <?$nutrition_koeff = \common\models\NutritionProcent::find()->where(['nutrition_id' => $nutrition->nutrition_id])->one()->procent/100;?>
                                <?foreach ($menu_cycle as $cycle){
                                    foreach($days as $day){?>
                                        <td class="text-center">-</td>
                                    <? } ?>
                                <? } ?>
                                <td class="text-center" >-</td>
                                <td class="text-center" >-</td>

                                <td class="text-center" ><b><?=$normativ['vitamin_a']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['vitamin_c']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['vitamin_b1']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['vitamin_b2']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['vitamin_d']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b>-<?//=$normativ['vitamin_pp']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b>-<?//=$normativ['na']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['k']*$nutrition_koeff?></b></td>
                                <b></b>
                                <td class="text-center" ><b><?=$normativ['ca']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['f']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['mg']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['p']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['fe']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['i']*$nutrition_koeff?></b></td>
                                <td class="text-center" ><b><?=$normativ['se']*$nutrition_koeff?></b></td>
                            </tr><b></b>
                        <?}?>

                    </table>
                </div>
    <?}?>
</div>
    <div class="text-center mt-5">
        <?if($post['cycle'] == 0){?>
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> Экспорт в Excel',
                ['excel-products-list?menu_id=' . $post['menu_id'].'&days_id='.$post['days_id']],
                [
                    'class'=>'btn btn-secondary',
                    'style' =>['width'=>'500px'],
                    'title' => Yii::t('yii', 'Вы можете скачать перечень продуктов в формате exceel'),
                    'data-toggle'=>'tooltip',
                ])
            ?>
        <?}else{?>
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> Экспорт в Excel',
                ['excel-products-list-nutrition?menu_id=' . $post['menu_id'].'&days_id='.$post['days_id']],
                [
                    'class'=>'btn btn-secondary',
                    'style' =>['width'=>'500px'],
                    'title' => Yii::t('yii', 'Вы можете скачать перечень продуктов в формате exceel'),
                    'data-toggle'=>'tooltip',
                ])
            ?>
        <?}?>
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
