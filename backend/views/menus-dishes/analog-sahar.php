<?php

use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

?>
<div class="block ml-2 mt-3">
<?php
    echo '<p class="mb-0"><b>Подобрать аналоги к блюду:</b> ' . $this_dishes->name .'</p>';
echo '<p class="mb-0"><b>Категория блюда:</b> ' . \common\models\DishesCategory::findOne($this_dishes->dishes_category_id)->name . '</p>';
    echo '<p class="mb-0"><b>Аналоги блюд из сборника рецептур:</b> ' . $this_dishes->get_recipes_collection($this_dishes->recipes_collection_id)->name . '</p>';

?>
    <br>

    <table class="table_th0 table-responsive">
        <tr class="">
            <th class="text-center">№</th>
            <th class="text-center" style="width: 300px">Выбранное блюдо</th>
            <th class="text-center">Выход</th>
            <th class="text-center">Углеводы</th>
            <th class="text-center">Значение в ХЕ</th>
        </tr>
        <tbody>
            <tr>
                <td class="text-center"><?=$key +1;?></td>
                <td><?= $this_dishes->name?></td>
                <td class="text-center"><?= $m_dish->yield?></td>
                <td class="text-center"><? $check_carbo = round($m_dish->get_bju_dish_with_your_yield($m_dish->dishes_id, 'carbohydrates_total', $m_dish->yield),1); echo $check_carbo; ?></td>
                <td class="text-center"><?= round($check_carbo/12, 1); ?></td>
            </tr>
        </tbody>
    </table>

    <br>
<?php $count = 0; if(!empty($correct_dishes)){?>
    <table class="table_th0 table-responsive">
        <tr class="">
            <th class="text-center">№</th>
            <th class="text-center" style="width: 300px">Блюдо</th>
            <th class="text-center">Выход</th>
            <th class="text-center">Углеводы</th>
            <th class="text-center">Значение в ХЕ</th>
        </tr>
        <tbody>
        <? foreach ($correct_dishes as $key => $c_dish){?>
            <? $carbohydrates_total_dish = round($m_dish->get_bju_dish_with_your_yield($c_dish->id, 'carbohydrates_total', $m_dish->yield),1);
            if($check_carbo > $carbohydrates_total_dish){ $count++?>
            <tr>
                <td class="text-center"><?=$count;?></td>
                <td><?= $c_dish->name?></td>
                <td class="text-center"><?= $m_dish->yield?></td>
                <td class="text-center"><?=$carbohydrates_total_dish; ?></td>
                <td class="text-center"><?= round($carbohydrates_total_dish/12, 1); ?></td>
            </tr>
            <?}?>
        <?}?>

        </tbody>
    </table>
    <br>
    <?php }
    if($count == 0){echo '<p style="color:red" class="mb-0"><b>Указанное блюдо содержит самое низкое значение углеводов из всех блюд категории</b></p>';}?>
    </div>
<?

$script = <<< JS
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>