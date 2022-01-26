<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\MenusDays;
use common\models\Days;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Редактирование действующего цикличного меню';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->one();
$menu_cycle_count = $first_menu->cycle;
$menu_cycle = [];
for($i=1;$i<=$menu_cycle_count;$i++){
    $menu_cycle[$i] = $i;//массив из подходящи циклов
}

$my_days = MenusDays::find()->where(['menu_id' => $first_menu->id])->all();
foreach($my_days as $m_day){
    $ids[] = $m_day->days_id;
}
$days = Days::find()->where(['id' => $ids])->all();
$my_days_items = ArrayHelper::map($days, 'id', 'name');

$params_menu = ['class' => 'form-control', 'options' => [21 => ['Selected' => true]]];
$params_cycle = ['class' => 'form-control', 'options' => [1 => ['Selected' => true]]];
$params_days = ['class' => 'form-control', 'options' => [1 => ['Selected' => true]]];
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
    $my_days_items = ArrayHelper::map($days, 'id', 'name');
    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_cycle = ['class' => 'form-control', 'options' => [$post['cycle'] => ['Selected' => true]]];
    $params_days = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];

}
?>
<div class="menus-dishes-index">
    <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>
    <div class="container mb-30">
        <div class="row">
            <div class="col">
                <?= $form->field($model, 'menu_id')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
                    'onchange' => '
                  $.get("../menus-dishes/cyclelistday?id="+$(this).val(), function(data){
                    $("select#menusdishes-cycle").html(data);
                  });
                  $.get("../menus-dishes/daylist?id="+$(this).val(), function(data){
                    $("select#menusdishes-days_id").html(data);
                  });'
                ])->label('Варианты меню'); ?>
            </div>

            <div class="col">
                <?= $form->field($model, 'cycle')->dropDownList($menu_cycle, $params_cycle) ?>
            </div>

            <div class="col">
                <?= $form->field($model, 'days_id')->dropDownList($my_days_items, $params_days) ?>
            </div>
        </div>


        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['class' => 'btn btn-success mb-3']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?if(!empty($nutritions)){?>
<? foreach($nutritions as $nutrition){?>
    <div class="block container-fluid mt-0 pl-0 pr-0" style="margin-top: 10px;">
    <? echo '<p class="" style="font-size: 26px; font-weight: 500;">'. $nutrition->name .'</p>'?>
    <table id="nutri_<?php echo $nutrition->id?>" class="table table-hover table-responsive table-bordered" >
        <thead class="">
        <tr class="">
            <th >№<?// echo '<p style="font-size: 20px;">'. $nutrition->name .'</p>'?></th>
            <th class="text-center  w-40">Блюдо</th>
            <th class="text-center">Просмотр технологической карты</th>
            <th class="text-center">Выход, грамм</th>
            <th class="text-center">Просмотр технологической карты с текущим выходом<i class="fa fa-info-circle" title="Длительность сезона в месяцах и в днях. Ежемесячный платеж формируется от делеления стоимости сезона на количество месяцев" data-toggle="tooltip"></i></th>
            <th class="text-center">Изменить блюдо или выход<i class="fa fa-info-circle" title="Здесь нужно выбрать месяц, в котором сумма ежемесячного платежа будет уменьшена на сумму первого взноса. Так как участник уже вносил 500 рублей на участие, соответственно один из платежей нужно уменьшить на эту сумму. По умолчанию это первый месяц" data-toggle="tooltip"></i></th>
            <th class="text-center">Удалить блюдо</th>
        </tr>
        </thead>
        <tbody>

        <? $count = 0;
        $indicator = 0;?>
        <?foreach($menus_dishes as $key => $m_dish){?>
            <?//echo $nutrition->id. ' ==='.$m_dish->nutrition_id;?>
                <? if($nutrition->id == $m_dish->nutrition_id){?>

                <? $count++;?>
                <tr data-id="<?= $m_dish->id;?>">
                <td class="number text-center"><?= $count?></td>
                <td class="dish text-left"><?= $m_dish->get_dishes($m_dish->dishes_id)?></td>
                <td class="check text-center">
                    <?= Html::button('<span class="glyphicon glyphicon-list-alt"></span> на 100 грамм', [
                        'title' => Yii::t('yii', 'Посмотреть тех. карту'),
                        'data-toggle'=>'tooltip',
                        'data-dishes_id' => $m_dish->dishes_id,
                        'class'=>'btn btn-sm btn-secondary',
                        'onclick' => '
                          $.get("../menus-dishes/showtechmup?id=" + $(this).attr("data-dishes_id"), function(data){
                            $("#showTechmup .modal-body").append(data);
                            console.log(data);
                            $("#showTechmup").modal("show");
                          });'
                    ]);?>
                </td>
                <td class="yield text-center"><?= $m_dish->yield?></td>
                <td class="check_card text-center">
                    <?= Html::a('<span class="glyphicon glyphicon-list"></span> на '.$m_dish->yield.' грамм', ['settings/delseasson', 'id'=> $item->id], [
                        'title' => Yii::t('yii', 'Посмотреть тех. карту с текущ. выходом'),
                        'data-toggle'=>'tooltip',
                        'class'=>'btn btn-sm btn-secondary',
                    ]);?>
                </td>

                <td class="pencil text-center">
                    <?= Html::button('<span class="glyphicon glyphicon-pencil"></span>', [
                        'title' => Yii::t('yii', 'Редактирование'),
                        'data-toggle'=>'tooltip',
                        'class'=>'btn btn-sm btn-primary',
                        'onclick'=> 'editDishes('.$m_dish->dishes_id.',"'.$m_dish->yield.'","'.$m_dish->id.'")'
                    ]);?>
                </td>
                <td class="text-center">
                    <?= Html::button('<span class="glyphicon glyphicon-trash"></span>', [
                        'title' => Yii::t('yii', 'Удалить'),
                        'data-toggle'=>'tooltip',
                        'class'=>'btn btn-sm btn-danger',
                        'data'=>['confirm'=>'Вы уверены что хотите удалить это блюдо из меню ?'],
                        'onclick'=> 'deleteDishes('.$m_dish->id.')'
                    ]);
                    unset($menus_dishes[$key]);
                    ?>
                </td>
                </tr>

                <?}else{
                break;
                }?>

        <?}?>
        <tr id="add_dish_<?php echo $nutrition->id?>" >


                <td></td>
                <td><?= Html::textInput('dishes_id', '', ['placeholder' => "Начните вводить блюдо", 'class'=>'form-control dishes_auto dishes_id_'.$nutrition->id]);?></td>
                <td colspan="3"><?= Html::textInput('yield', '', ['placeholder' => "Введите выход блюда", 'class'=>'form-control yield_'.$nutrition->id]);?></td>
                <td colspan="2" class="text-center"><?= Html::button('Добавить в меню', ['class' => 'btn btn-success', 'onclick'=> 'saveDish('.$post['menu_id'].',"'.$post['cycle'].'","'.$post['days_id'].'","'.$nutrition->id.'")']); ?></td>


        </tr>
    </div>
    </tbody>
    </table>
</div>
<?}?>

<?}?>
    <script type="text/javascript">
        function editDishes(id, yield, menusdishes_id){
            $('#editDishes').find('input.dishes_id').val(id);
            $('#editDishes').find('input.yield').val(yield);
            $('#editDishes').find('input.menusdishes_id').val(menusdishes_id);
            $('#editDishes').modal('show');
        }
    </script>

    <script type="text/javascript">
        function updateDishes(){
            /*console.log('ok');
            $('#editDishes').find('input.dishes_id').val();
            $('#editDishes').find('input.yield').val();
            $('#editDishes').find('input.nutrition').val();*/
            //console.log($('#editDishes').find('input.nutrition').val());
           var dish = {};
           //СОБИРАЕМ ДАННЫЕ ИЗ ФОРМ
            dish.menusdishes_id = $('#editDishes').find('input.menusdishes_id').val();
            dish.dishes_id = $('#editDishes').find('input.dishes_id').val();
            dish.yield = $('#editDishes').find('input.yield').val();
            console.log(dish);

            $.ajax({
                url: 'updating',
                data: dish,
                method: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    console.log(data);
                    if (data) {
                        $('tr[data-id="'+data.id+'"]').find('td.dish').text(data.created_at);
                        $('tr[data-id="'+data.id+'"]').find('td.yield').text(data.yield);
                        //ПРИ ПОВТОРНОМ РЕДАКТИРОВАНИИ ДАННЫЕ С ФУНКЦИИ ОНЧЕК СТАРЫЕ МЫ ЕЕ ОБНОВЛЯЕМ, ЧТОБЫ ДАННЫЕ БЫЛИ АКТУАЛЬНЫМИ
                        $('tr[data-id="'+data.id+'"]').find('td.pencil').html('<button type="button" class="btn btn-sm btn-primary" title="Редактирование" data-toggle="tooltip" onclick="editDishes('+ data.dishes_id+', '+ data.yield +', '+ data.id +')"><span class="glyphicon glyphicon-pencil"></span></button>');

                    }

                },
                error: function (err) {
                    console.log('error')
                }
            });
             $('#editDishes').modal('toggle');
        }
    </script>

    <script type="text/javascript">
        function deleteDishes(id){
            $.ajax({
                url: 'delete?id='+id,
                data: id,
                method: 'GET',
                dataType: 'HTML',
                success: function (data) {
                    console.log(data);
                    $('tr[data-id="'+data+'"]').remove();

                },
                error: function (err) {


                    console.log('error')
                }
            });
        }
    </script>

    <script type="text/javascript">
        function saveDish(menu_id, cycle, days_id, nutrition_id){
            /*if($('input.yield_'+nutrition_id).val() != '' || $('input.dishes_id_'+nutrition_id).val()!= ''){
               alert('Пустые поля должны быть заполнены');
               //exit();
            }*/
            //else {
                var dish = {};
                dish.menu_id = menu_id;
                dish.cycle = cycle;
                dish.days_id = days_id;
                dish.nutrition_id = nutrition_id;
                //val
                console.log("nutr id",nutrition_id);
                //dish.dishes_id = $('#add_dish_' + nutrition_id).find('input.dishes_id_' + nutrition_id).val();
                dish.dishes_id = $('#add_dish_' + nutrition_id ).find('input.dishes_id_' + nutrition_id).data('dishes');
                console.log("dish id",dish.dishes_id);
                dish.yield = $('#add_dish_' + nutrition_id).find('input.yield_' + nutrition_id).val();
                //console.log(dish);

                $.ajax({
                    url: 'saving',
                    data: dish,
                    method: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        //console.log(data);
                        if (data) {
                            if(data == 'error2'){
                                alert('Введено не существующее блюдо');
                            }
                            if(data == 'error1'){
                                alert('Блюдо и выход блюда должны быть заполнены');
                            }

                            $('#add_dish_' + data.nutrition_id).before('<tr data-id="' + data.id + '">' +
                                '<td class="number"></td>' +
                                '<td class="dish">' + data.created_at + '</td>' +
                                //'<td><button type="button" class="btn btn-sm btn-secondary" title="Посмотреть тех. карту"  data-toggle="tooltip"  onclick='$.get('../menus-dishes/showtechmup?id=' + $(this).attr('data-dishes_id'), function(data){$('#showTechmup .modal-body').append(data);$("#showTechmup").modal("show")});" ><span class="glyphicon glyphicon-list-alt"></span> на 100 грамм</button></td>' +
                                '<td><button type="button" class="btn btn-sm btn-secondary" title="Посмотреть тех. карту"  data-toggle="tooltip"  onclick='$.get('../menus-dishes/showtechmup?id=' + $(this).attr('data-dishes_id'), function(data){$('#showTechmup .modal-body').append(data);$("#showTechmup").modal("show")});" ><span class="glyphicon glyphicon-list-alt"></span> на 100 грамм</button></td>' +
                                '<td class="yield">' + data.yield + '</td>' +
                                '<td><button type="button" class="btn btn-sm btn-secondary" title="Посмотреть тех. карту с текущим выходом" data-toggle="tooltip"><span class="glyphicon glyphicon-list"></span> Посмотреть тех. карту с текущим выходом</button></td>' +
                                '<td class="pencil"><button type="button" class="btn btn-sm btn-primary" title="Редактирование" data-toggle="tooltip" onclick="editDishes('+ data.dishes_id+', '+ data.yield +', '+ data.id +')"><span class="glyphicon glyphicon-pencil"></span></button></td>' +
                                '<td><a class="btn btn-sm btn-danger" title="Удалить" data-toggle="tooltip" data-confirm="Вы уверены что хотите удалить это блюдо из меню ?" onclick="deleteDishes(' + data.id + ')"><span class="glyphicon glyphicon-trash" style="color:white"></span></a></td>' +
                                '</tr>');
                            //обнуление инпутов
                            dish.dishes_id = $('#add_dish_' + nutrition_id).find('input.dishes_id_' + nutrition_id).val('');
                            dish.yield = $('#add_dish_' + nutrition_id).find('input.yield_' + nutrition_id).val('');

                        }

                    },
                    error: function (err) {
                        console.log('error')
                    }
                })
            //}
        }
    </script>

    <div id="editDishes" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Редактирование блюда в меню</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                        <?= Html::hiddenInput('nutrition_id', '', ['class'=>'form-control menusdishes_id']);?>
                            <?= $model->getAttributeLabel('dishes_id')?>
                            <?= Html::textInput('dishes_id', '', ['class'=>'form-control dishes_id']);?>
                        </div>
                        <div class="col-sm-6">
                            <?= $model->getAttributeLabel('yield')?>
                            <?= Html::textInput('yield', '', ['class'=>'form-control yield']);?>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>

                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success pull-right','onclick'=>'updateDishes()']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="showTechmup" class="modal fade">
        <div class="modal-dialog" style="width:80%">
            <div class="modal-content" style="width:400px">
                <div class="header" style="width:400px">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Технологическая карта</h4>
                </div>
                <div class="modal-body">
                    <div class="row">



                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>

<?
$script = <<< JS
//автоподстановка блюд
$( document ).ready(function(){
    $('.dishes_auto').autocomplete({
        autoFocus: true,
        minLength: 1,
        delay: 300,      
        source: function( request, response ) {
            $.ajax({
                url: "../menus-dishes/searchfulltext",
                notUseImage: true,
                type: "POST",      // тип запроса
                data: { // действия
                    'e' : $('.dishes_id_1').val()
                },
                // Данные пришли
                success: function( data ) {  
                    var json = $.parseJSON(data);
                    //console.log(json.field);
                    response($.map(json.field, function (item) {
                        //$('.dishes_id_1').attr('data-id',item.id);
                        //console.log(item.id);
                        return {
                            label: item.name +': '+ item.techmup_number,
                            value: item.name,
                            id: item.id
                        }
                    }));
                },
                error: function (err) {
                    console.log(err);
                }
          })
        },
        select: function( event, ui ) {
            $(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('dishes',ui.item.id).attr('data-dishes',ui.item.id);
            $('#my').text(function(){
                return $(this).text() == '' ? 'You selected: ' + ui.item.value : $(this).text()+ ', '+ui.item.value;
            });
            return false;
        },
        change: function( event, ui ) {
            //console.log(ui.item.id);
            $(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('dishes',ui.item.id).attr('data-dishes',ui.item.id);
            return false;
        },
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>