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

$this->title = 'Отчет о повторяемости';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->one();

$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];

if(!empty($post)){
    $my_menus = Menus::findOne($post['menu_id']);
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
}

?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?if(empty($my_menus) && (!(Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr') || Yii::$app->user->can('minobr')))){?>
        <p class="text-center" style="color: red"><b>У Вас не созданы меню и не добавлены блюда.(Перейдите в раздел "Архив меню" или "Настройка меню")</b></p>
    <?}?>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="container mb-30">
        <div class="row justify-content-center">
            <div class="col-11 col-md-6">
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
                <?= Html::submitButton('Посмотреть', ['class' => 'btn main-button-3 mb-3 beforeload']) ?>
                <button class="btn main-button-3 load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Посмотреть...
                </button>
            </div>
        </div>

        <?php ActiveForm::end(); ?>


<?php if($post){ $menus_copy_dishes = $menus_dishes; $used_ids = [];  $menu_id = $post['menu_id'];?>

    <?= Html::a('<span class="glyphicon glyphicon-download"></span> Скачать в PDF "Отчет о повторяемости"',
        ['repeat-report-export?id=' . $menu_id],
    [
        'class'=>'btn btn-outline-secondary',
        'title' => Yii::t('yii', 'Вы можете скачать технологическую карту в PDF формате'),
        'data-toggle'=>'tooltip',
    ])
    ?>
    <br>
    <br>
    <div class="row justify-content-center">
    <div class="col-auto">
    <table class="table_th0 table-responsive">
        <tr class="">
            <th class="text-center main-info-see">№</th>
            <th class="text-center main-info-see">Наименование блюда</th>
            <th class="text-center main-info-see">Дни совпадений</th>
            <th class="text-center main-info-see">Количество совпадений</th>
        </tr>
    <?$number_row=1;?>
    <?php foreach($menus_dishes as $m_dish){ $count = 0;  ?>
        <? if (!in_array($m_dish->dishes_id, $used_ids)) { $used_ids[] = $m_dish->dishes_id; ?>
        <tr>
            <td class="text-center main-info-see"><?=$number_row?></td>
            <td class="text-center"><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
            <td class="text-left">
                <?php $cycle_days_ids = []; foreach($menus_copy_dishes as $m_copy_dish){ ?>
                    <? if($m_copy_dish->dishes_id == $m_dish->dishes_id && !in_array($m_dish->get_days($m_copy_dish->days_id).'_'. $m_copy_dish->cycle, $cycle_days_ids)){ ?>

                        <? echo $m_dish->get_days($m_copy_dish->days_id).' '. $m_copy_dish->cycle .' недели <br>';
                        $cycle_days_ids[] = $m_dish->get_days($m_copy_dish->days_id).'_'. $m_copy_dish->cycle?>
                    <? } ?>
                <? } ?>
            </td>
            <td class="text-center">
                <?php foreach($menus_copy_dishes as $m_copy_dish){ ?>
                    <? if($m_copy_dish->dishes_id == $m_dish->dishes_id){ $count++;?>
                    <? } ?>
                <? } ?>
                <? echo $count; ?>
            </td>
        </tr>
            <?$number_row++;?>
            <? } ?>
    <?php } ?>
    </table>
    </div>
    </div>

<?php } ?>
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
