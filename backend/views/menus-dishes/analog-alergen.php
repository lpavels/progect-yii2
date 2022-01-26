<?php

use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

?>
<div class="block ml-2 mt-3">
<?php foreach($alergens as $alergen)
{
    echo '<p class="mb-0"><b>Алерген:</b> ' . $alergen->name . '</p>';
}
    echo '<p class="mb-0"><b>Подобрать аналоги к блюду:</b> ' . $this_dishes->name .'</p>';
echo '<p class="mb-0"><b>Категория блюда:</b> ' . \common\models\DishesCategory::findOne($this_dishes->dishes_category_id)->name . '</p>';
    echo '<p class="mb-0"><b>Аналоги блюд из сборника рецептур:</b> ' . $this_dishes->get_recipes_collection($this_dishes->recipes_collection_id)->name . '</p>';

?>
    <br>
<?php if(!empty($correct_dishes)){?>
    <table class="table_th0 table-responsive">
        <tr class="">
            <th class="text-center">№</th>
            <th class="text-center">Блюдо</th>
        </tr>
        <tbody>
        <? foreach ($correct_dishes as $key => $c_dish){?>
            <tr>
                <td><?=$key +1;?></td>
                <td><?= $c_dish->name?></td>
            </tr>
        <?}?>

        </tbody>
    </table>
    <?php }else{echo '<p style="color:red" class="mb-0"><b>Аналогов блюд не найдено</b></p>';}?>
    </div>
<?

$script = <<< JS
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>