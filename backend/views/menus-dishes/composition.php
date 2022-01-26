<?php

use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

$super_total_yield = 0;
$super_total_protein = 0;
$super_total_fat = 0;
$super_total_carbohydrates_total = 0;
$super_total_energy_kkal = 0;

$menu_id = $menus_dishes[0]->menu_id;
$cycle = $menus_dishes[0]->cycle;
$days_id = $menus_dishes[0]->days_id;
$nutrition_id = $menus_dishes[0]->nutrition_id;?>

<?php if($indicator_page > 0){?>
    <table class="table_th0 table-responsive">
    <tr class="">
        <th class="text-center">№</th>
        <th class="text-center">Номер блюда</th>
        <th class="text-center">Выход</th>
        <th class="text-center">Белки</th>
        <th class="text-center">Жиры</th>
        <th class="text-center">Углеводы</th>
        <th class="text-center">Эн. ценность</th>
    </tr>
    <?$number_row=1; $total_kkal = 0; $total_protein = 0; $total_carbohydrates_total = 0; $total_fat = 0;?>
    <? foreach ($menus_dishes as $m_dish){?>

        <tr data-id="<?= $m_dish->id;?>">
            <td class="text-center"><?=$number_row?></td>
            <td><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
            <td class="text-center"><?= $m_dish->yield ?></td>
            <td class="text-center"><? $protein = round($m_dish->get_bju_dish($m_dish->id, 'protein'),1); echo $protein; $total_protein = $total_protein + $protein;?></td>
            <td class="text-center"><? $fat = round($m_dish->get_bju_dish($m_dish->id, 'fat'),1); echo $fat; $total_fat = $total_fat + $fat;?></td>
            <td class="text-center"><? $carbohydrates_total = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'),1); echo $carbohydrates_total; $total_carbohydrates_total = $total_carbohydrates_total + $carbohydrates_total;?></td>
            <td class="text-center"><? $kkal = round($m_dish->get_kkal_dish($m_dish->id),1); echo $kkal; $total_kkal = $total_kkal + $kkal;?></td>

        </tr>
    <?$number_row++;?>
    <?}?>
        <tr class="table-primary">
            <td colspan="2">Итого за <?= $m_dish->get_nutrition($nutrition_id);?></td>
            <td class="text-center"><?= $m_dish->get_total_yield($menu_id, $cycle, $days_id, $nutrition_id);?></td>
            <td class="text-center"><?= $total_protein;?></td>
            <td class="text-center"><?= $total_fat;?></td>
            <td class="text-center"><?= $total_carbohydrates_total;?></td>
            <td class="text-center"><?= $total_kkal;?></td>
        </tr>
        <tr class="table-success">
            <td colspan="2">Рекомендуемая величина</td>
            <td class="text-center"></td>
            <td class="text-center"><?= $m_dish->get_recommended_normativ($menu_id, $nutrition_id, 'protein_middle_weight');?></td>
            <td class="text-center"><?= $m_dish->get_recommended_normativ($menu_id, $nutrition_id, 'fat_middle_weight');?></td>
            <td class="text-center"><?= $m_dish->get_recommended_normativ($menu_id, $nutrition_id, 'carbohydrates_middle_weight');?></td>
            <td class="text-center"><?= $m_dish->get_recommended_normativ($menu_id, $nutrition_id, 'middle_kkal');?></td>

        </tr>
        <tr class="table-warning">
            <!--Для бжу функции есть в модели, но чтобы не нагружать страницу расчитывается через переменные-->
            <td colspan="3">Соотношение БЖУ</td>
            <td class="text-center">1</td>
                <td class="text-center"><?= $m_dish->get_bju($menu_id, $cycle, $days_id, $nutrition_id, 'fat'); ?></td>
                <td class="text-center"><?= $m_dish->get_bju($menu_id, $cycle, $days_id, $nutrition_id, 'carbohydrates_total'); ?></td>

        </tr>
        <tr class="table-warning">
            <td colspan="3">Процент от общей массы пищевых веществ</td>
            <td class="text-center"><?= $m_dish->get_procent($menu_id, $cycle, $days_id, $nutrition_id, 'protein').'%'; ?></td>
            <td class="text-center"><?= $m_dish->get_procent($menu_id, $cycle, $days_id, $nutrition_id, 'fat').'%'; ?></td>
            <td class="text-center"><?= $m_dish->get_procent($menu_id, $cycle, $days_id, $nutrition_id, 'carbohydrates_total').'%'; ?></td>
        </tr>
    </table>

    <?php
/*    echo Html::button('<span class="glyphicon glyphicon-download"></span> Скачать состав за прием пищи в Excel', [
        'title' => Yii::t('yii', 'Скачать в PDF'),
        'data-toggle'=>'tooltip',
        'class'=>'btn btn-success mt-3',
    ]);*/?>


    <?php }else{ ?>
    <table class="table_th0 table-responsive">
        <tr class="">
            <th class="text-center"></th>
            <th class="text-center">Выход</th>
            <th class="text-center">Белки</th>
            <th class="text-center">Жиры</th>
            <th class="text-center">Углеводы</th>
            <th class="text-center">Эн. ценность</th>
        </tr>
        <?$itog_protein = 0; $itog_fat=0; $itog_carbohydrates_total=0; $itog_kkal=0; $itog_yield = 0;?>
        <?foreach ($nutritions as $nutrition){?>
            <tr class="table-primary">
                <td>Итого за <?= (Yii::$app->user->can('director') ? $nutrition->name : $nutrition->name2 ) ?></td>
                <td class="text-center"><? $yield = round($model->get_total_yield($menu_id, $cycle, $days_id, $nutrition->id),1); echo $yield; $itog_yield = $itog_yield + $yield;?></td>
                <td class="text-center"><? $protein = round($model->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition->id,'protein'),1); echo $protein; $itog_protein = $itog_protein + $protein;?></td>
                <td class="text-center"><? $fat = round($model->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition->id,'fat'),1); echo $fat; $itog_fat = $itog_fat + $fat;?></td>
                <td class="text-center"><? $carbohydrates_total = round($model->get_bju_nutrition($menu_id, $cycle, $days_id, $nutrition->id,'carbohydrates_total'),1); echo $carbohydrates_total; $itog_carbohydrates_total = $itog_carbohydrates_total + $carbohydrates_total;?></td>
                <td class="text-center"><? $kkal = round($model->get_kkal_nutrition($menu_id, $cycle, $days_id, $nutrition->id),1); echo $kkal; $itog_kkal = $itog_kkal + $kkal;?></td>
            </tr>
        <?}?>
        <tr class="table-danger itog_day">
            <td>Итого за день</td>
            <td class="text-center"><?= $itog_yield; ?></td>
            <td class="text-center"><?= $itog_protein; ?></td>
            <td class="text-center"><?= $itog_fat; ?></td>
            <td class="text-center"><?= $itog_carbohydrates_total;?></td>
            <td class="text-center"><?= $itog_kkal; ?></td>
        </tr>
        <tr class="procent_day table-danger">
            <td colspan="2">Процентное соотношение БЖУ за день</td>
            <td class="text-center"><?= $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'protein').'%'; ?></td>
            <td class="text-center"><?= $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'fat').'%'; ?></td>
            <td class="text-center"><?= $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'carbohydrates_total').'%'; ?></td>
        </tr>
        <tr class="table-danger-2">
            <td colspan="2">Процент от общей массы пищевых веществ	</td>
            <td class="text-center"><?= ($itog_fat!=0 ||$itog_protein!=0 ||$itog_carbohydrates_total!=0) ? round($itog_protein/($itog_fat+$itog_protein+$itog_carbohydrates_total), 4) * 100 .'%' : '-';?></td>
            <td class="text-center"><?= ($itog_fat!=0 ||$itog_protein!=0 ||$itog_carbohydrates_total!=0) ? round($itog_fat/($itog_fat+$itog_protein+$itog_carbohydrates_total), 4) * 100 .'%' : '-';?></td>
            <td class="text-center"><?= ($itog_fat!=0 ||$itog_protein!=0 ||$itog_carbohydrates_total!=0) ? round($itog_carbohydrates_total/($itog_fat+$itog_protein+$itog_carbohydrates_total), 4) * 100 .'%' : '-';?></td>
        </tr>
    </table>
<?php } ?>

<?php
$script = <<< JS
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>