<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\RecipesCollection;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Информация о съеденной за день пище';
$this->params['breadcrumbs'][] = $this->title;

$recipes_collections = RecipesCollection::find()->where(['organization_id' => 1])->all();
$recipes_collection = ArrayHelper::map($recipes_collections, 'id', 'name');

$type_food = [
    '' => 'Не указано',
    '0' => 'Дом',
    '1' => 'Школа/дет.сад',
    '2' => 'Иное'
];
?>
<div class="menus-dishes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <? if (!empty($nutritions)) { ?>
        <div class="hide-select2">
            <?
            echo '<div class="d-none">2, 4, 717</div>';
            echo Select2::widget([
                'name' => 'recipes_collection',
                'value' => ['2', '4','717'],
                'data' => $recipes_collection,
                'options' => [
                    //'placeholder' => 'Выберите сборники...',
                    'multiple' => true,
                ],
                'pluginEvents' => [
                    "change" => 'function() { 
                        var data_id = $(this).val();
                        $(".select2-selection__choice").attr("data-info", data_id);
                    }',
                ],
            ]);
            ?>
        </div>

        <? foreach ($nutritions as $nutrition) { ?>
            <div class="block container-fluid mt-0 pl-0 pr-0" style="margin-top: 10px;">
                <? echo '<p class="text-center" style="font-size: 26px; font-weight: 500;">' . $nutrition->name2 . '</p>' ?>
                <table id="nutri_<?php echo $nutrition->id ?>" class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>№</th>
                        <th class="text-center w-40">Блюдо</th>
                        <th class="text-center">Место употребления</th>
                        <th class="text-center">Выход, грамм</th>
                        <th class="text-center">Изменить выход<i class="fa fa-info-circle" data-toggle="tooltip"></i>
                        </th>
                        <th class="text-center">Удалить блюдо</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 0;
                    $indicator = 0; ?>
                    <? foreach ($menus_dishes as $key => $m_dish) { ?>
                        <? if ($nutrition->id == $m_dish->nutrition_id) { ?>

                            <? $count++; ?>
                            <tr data-id="<?= $m_dish->id; ?>">
                                <td class="number text-center"><?= $count ?></td>
                                <td class="dish text-left"><?= $m_dish->get_dishes($m_dish->dishes_id) ?></td>
                                <td class="food text-center"><?= $type_food[$m_dish->type_food] ?></td>
                                <td class="yield text-center"><?= $m_dish->yield ?></td>

                                <td class="pencil text-center">
                                    <?php $param = $m_dish->get_dishes($m_dish->dishes_id); ?>
                                    <?= Html::button('<span class="glyphicon glyphicon-pencil"></span>', [
                                        'title' => Yii::t('yii', 'Редактирование'),
                                        'data-toggle' => 'tooltip',
                                        'class' => 'btn btn-sm main-button-edit',
                                        "onclick" => "editDishes('$m_dish->dishes_id','$param ','$m_dish->yield','$m_dish->id')"
                                    ]); ?>
                                </td>
                                <td class="text-center">
                                    <?= Html::button('<span class="glyphicon glyphicon-trash"></span>', [
                                        'title' => Yii::t('yii', 'Удалить'),
                                        'data-toggle' => 'tooltip',
                                        'class' => 'btn btn-sm main-button-delete',
                                        'data' => ['confirm' => 'Вы уверены что хотите удалить это блюдо из меню?'],
                                        'onclick' => 'deleteDishes(' . $m_dish->id . ')'
                                    ]);
                                    unset($menus_dishes[$key]);
                                    ?>
                                </td>
                            </tr>

                        <? }
                        else
                        {
                            break;
                        } ?>

                    <? } ?>
                    <tr id="add_dish_<?php echo $nutrition->id ?>">
                        <td></td>
                        <td><?= Html::textInput('dishes_id', '', ['placeholder' => "Начните вводить блюдо", 'class' => 'form-control dishes_auto dishes_id_' . $nutrition->id]); ?></td>
                        <td><?= Html::dropDownList('type_food', 'id', $type_food, ['class' => 'form-control type_food_' . $nutrition->id]); ?></td>
                        <td colspan="2"><?= Html::textInput('yield', '', ['placeholder' => "Введите выход блюда (только число)", 'class' => 'form-control yield_' . $nutrition->id]); ?></td>
                        <td colspan="2"
                            class="text-center"><?= Html::button('Добавить в меню', ['class' => 'btn main-button-3', 'onclick' => 'saveDish(' . $menu_id . ',"' . $nutrition->id . '")']); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <? } ?>

        <!--Посмотреть состав за день-->
        <div class="text-center">
            <?= Html::button('Посмотреть состав за день', [
                'title' => Yii::t('yii', 'Посмотреть состав за день'),
                'data-toggle' => 'tooltip',
                'class' => 'btn main-button-3',
                'data-menu_id' => $menu_id,
                'data-cycle' => 1,
                'data-days_id' => 1,
                'data-nutrition_id' => 0,
                'onclick' => '
                $.get("../menus-dishes/show_composition?menu_id=" + $(this).attr("data-menu_id") + "&cycle=" + $(this).attr("data-cycle") + "&days_id="  + $(this).attr("data-days_id") + "&nutrition_id=" + $(this).attr("data-nutrition_id"), function(data){
                $("#showComposition .modal-body").empty();
                  $("#showComposition .modal-body").append(data);
                  //console.log(data);
                  $("#showComposition").modal("show");
                });'
            ]); ?>
        </div>
                <!-- -->
    <?}?>

    <script type="text/javascript">
        function editDishes(id, name, yield, menusdishes_id)
        {
            $('#editDishes').find('input.dishes_id').val(name);
            $('#editDishes').find('input.dishes_id').attr('data-dishes', id);
            $('#editDishes').find('input.yield').val(yield);
            $('#editDishes').find('input.menusdishes_id').val(menusdishes_id);
            $('#editDishes').modal('show');
        }
    </script>

    <script type="text/javascript">
        function updateDishes()
        {
            var dish = {};
            //СОБИРАЕМ ДАННЫЕ ИЗ ФОРМ
            dish.menusdishes_id = $('#editDishes').find('input.menusdishes_id').val();
            //dish.dishes_id = $('#editDishes').find('input.dishes_id').val();
            dish.dishes_id = $('#editDishes').find('input.dishes_id').data('dishes');
            //console.log(dish.dishes_id);
            dish.yield = $('#editDishes').find('input.yield').val();
            dish.date = 0;

            if (dish.yield > 501){
                return alert('Проверьте выход блюда');
            }
            if (dish.yield < 1){
                return alert('Проверьте выход блюда');
            }

            //console.log(dish);
            $.ajax({
                url: 'updating-user',
                data: dish,
                method: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    //console.log(data);
                    if (data == 'error3'){
                        alert('Некорректно введен выход блюда');
                    }

                    if (data)
                    {
                        $('tr[data-id="' + data.id + '"]').find('td.dish').text(data.created_at);
                        $('tr[data-id="' + data.id + '"]').find('td.yield').text(data.yield);
                        //ПРИ ПОВТОРНОМ РЕДАКТИРОВАНИИ ДАННЫЕ С ФУНКЦИИ ОНЧЕК СТАРЫЕ МЫ ЕЕ ОБНОВЛЯЕМ, ЧТОБЫ ДАННЫЕ БЫЛИ АКТУАЛЬНЫМИ
                        $('tr[data-id="' + data.id + '"]').find('td.pencil').html('<button type="button" class="btn btn-sm main-button-edit" title="Редактирование" data-toggle="tooltip" onclick="editDishes(\'' + data.dishes_id + '\', \'' + data.created_at + '\', \'' + data.yield + '\', \'' + data.id + '\')"><span class="glyphicon glyphicon-pencil"></span></button>');
                        /*в кнопке просто меняет текст мы ее не заменяем и не удаляем, а просто меняем текст и спан и онклик остается не изменным*/
                        $('tr[data-id="' + data.id + '"]').find('td.check_card button').html('<span class="glyphicon glyphicon-list-alt"></span>  на ' + data.yield + ' грамм ');
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
        function deleteDishes(id)
        {
            $.ajax({
                url: 'del?id=' + id,
                data: id,
                method: 'GET',
                dataType: 'HTML',
                success: function (data) {
                    //console.log(data);
                    $('tr[data-id="' + data + '"]').remove();
                },
                error: function (err) {
                    console.log('error')
                }
            });
        }
    </script>

    <script type="text/javascript">
        function saveDish(menu_id,nutrition_id)
        {
            var typeFood_arr = [
                'Дом',
                'Школа/дет.сад',
                'Иное'
            ];

            var dish = {};
            dish.menu_id = menu_id;
            dish.nutrition_id = nutrition_id;
            dish.dishes_id = $('#add_dish_' + nutrition_id).find('input.dishes_id_' + nutrition_id).data('dishes');
            dish.type_food = $('.type_food_' + nutrition_id).val();
            dish.yield = $('#add_dish_' + nutrition_id).find('input.yield_' + nutrition_id).val();

            if (dish.type_food == ''){
                return alert('Выберите место употребления');
            }
            if (dish.yield > 501){
                return alert('Проверьте выход блюда');
            }
            if (dish.yield < 1){
                return alert('Проверьте выход блюда');
            }

            $.ajax({
                url: '/menus-dishes/saving-user',
                data: dish,
                method: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if (data)
                    {
                        if (data == 'error2'){
                            alert('Введено не существующее блюдо');
                        }
                        if (data == 'error1'){
                            alert('Блюдо и выход блюда должны быть заполнены');
                        }
                        if (data == 'error3'){
                            alert('Некорректно введен выход блюда');
                        }

                        $('#add_dish_' + data.nutrition_id).before('<tr data-id="' + data.id + '">' +
                            '<td class="number"></td>' +
                            '<td class="text-left dish">' + data.created_at + '</td>' +
                            '<td class="text-left text-center">' + typeFood_arr[data.type_food] + '</td>' + //
                            '<td class="yield text-center">' + data.yield + '</td>' +
                            '<td class="pencil text-center"><button type="button" class="btn btn-sm main-button-edit" title="Редактирование" data-toggle="tooltip" onclick="editDishes(\'' + data.dishes_id + '\', \'' + data.created_at + '\', \'' + data.yield + '\', \'' + data.id + '\')"><span class="glyphicon glyphicon-pencil"></span></button></td>' +
                            '<td class="text-center"><a class="btn btn-sm btn-danger " title="Удалить" data-toggle="tooltip" data-confirm="Вы уверены что хотите удалить это блюдо из меню ?" onclick="deleteDishes(' + data.id + ')"><span class="glyphicon glyphicon-trash" style="color:white"></span></a></td>' +
                            '</tr>'
                        );
                        //обнуление инпутов
                        dish.dishes_id = $('#add_dish_' + nutrition_id).find('input.dishes_id_' + nutrition_id).val('');
                        dish.yield = $('#add_dish_' + nutrition_id).find('input.yield_' + nutrition_id).val('');
                        dish.type_food = $('.type_food_' + nutrition_id).val('');
                    }
                },
                error: function (err) {
                    console.log('error')
                }
            })
        }
    </script>

    <!--МОДАЛЬНОЕ ОКНО ДЛЯ РЕДАКТИРОВАНИЯ БЛЮДА-->
    <div id="editDishes" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header-p3">
                    <h4 class="modal-title">Изменение выхода в блюде</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= Html::hiddenInput('nutrition_id', '', ['class' => 'form-control menusdishes_id']); ?>
                            <?= $model->getAttributeLabel('dishes_id') ?>
                            <?= Html::textInput('dishes_id', '', ['class' => 'form-control dishes_id', 'disabled' => 'disabled']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $model->getAttributeLabel('yield') ?>
                            <?= Html::textInput('yield', '', ['class' => 'form-control yield']); ?>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn main-button-delete" data-dismiss="modal">Отмена</button>

                        <?= Html::submitButton('Сохранить', ['class' => 'btn main-button-3 pull-right', 'onclick' => 'updateDishes()']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--МОДАЛЬНОЕ ОКНО ДЛЯ ТЕХКАРТ-->
    <div id="showTechmup" class="modal fade">
        <div class="modal-dialog modal-lg" style="">
            <div class="modal-content">
                <div class="modal-header-p3">
                    <h4 class="modal-title">Технологическая карта
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

    <!--МОДАЛЬНОЕ ОКНО ДЛЯ 'ПОКАЗАТЬ СОСТАВ ЗА <ПРИЕМ ПИЩИ>'-->
    <div id="showComposition" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header-p3">
                    <h4 class="modal-title">Состав за прием пищи</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?
$script = <<< JS
$('.hide-select2').hide();
//автоподстановка блюд
$( document ).ready(function(){
    $('.dishes_auto').autocomplete({
        autoFocus: true,
        minLength: 1,
        delay: 300,      
        source: function( request, response ) {
            //получаем символы при вводе
            var symbol_recipes = this.term;
            var recipes_collections, recipes_collections2;
            //id блюда храним в data
            recipes_collections = $( ".select2-selection__choice" ).data("info");
            //console.log(recipes_collections);
            if(typeof(recipes_collections) != "undefined") {
                if(recipes_collections.length > 1) {
                    recipes_collections2 = recipes_collections.split(',');
                } else {
                    recipes_collections2 = recipes_collections;
                }
            }
            else {
                recipes_collections3 = $( ".d-none" ).text();
                if(recipes_collections3.length > 1) {
                    recipes_collections2 = recipes_collections3.split(',');
                }
                //console.log(recipes_collections2);
                //сборники по умолчанию в выборке
                //recipes_collections2 = ["1", "2", "4"];
            }
            $.ajax({
                url: "../menus-dishes/searchfulltextuser",
                notUseImage: true,
                type: "POST",      // тип запроса
                data: { // действия
                    'e' : symbol_recipes, //$('.dishes_id_1').val(),
                    'recipes_collections' : recipes_collections2
                },
                // Данные пришли
                success: function( data ) {  
                    var json = $.parseJSON(data);
                    response($.map(json.field, function (item) {
                        //$('.dishes_id_1').attr('data-id',item.id);
                        //console.log(item.id);
                        return {
                            label: item.short_name,
                            value: item.short_name,
                            id: item.id,
                            yield: item.yield
                        }
                    }));
                },
                error: function (err) {
                    console.log(err);
                }
          })
        },
        select: function( event, ui ) {
            var classes = $(this).closest('.dishes_auto').prop('class').split(' ');
            //console.log(1111);
            //console.log(classes[2].toString().slice(-1));
            //classes.splice( classes.indexOf('box'), 1 );
            $(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('dishes',ui.item.id).attr('data-dishes',ui.item.id);
            //$('#my').text(function(){
            //    return $(this).text() == '' ? 'You selected: ' + ui.item.value : $(this).text()+ ', '+ui.item.value;
            //});
            //console.log(ui.item.yield);
            $(".yield_"+classes[2].toString().slice(-1)).val(ui.item.yield);
            return false;
        },
        change: function( event, ui ) {
            $(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('dishes',ui.item.id).attr('data-dishes',ui.item.id);
            return false;
        },
    });
    $('.dishes_id').autocomplete({
        autoFocus: true,
        minLength: 1,
        delay: 300,      
        source: function( request, response ) {
            //получаем символы при вводе
            var symbol_recipes = this.term;
            var recipes_collections, recipes_collections2;
            //id блюда храним в data
            recipes_collections = $( ".select2-selection__choice" ).data("info");
            if(typeof(recipes_collections) != "undefined") {
                if(recipes_collections.length > 1) {
                    recipes_collections2 = recipes_collections.split(',');
                } else {
                recipes_collections2 = recipes_collections;
                }
            }
            else {
                //сборники по умолчанию в выборке
                recipes_collections3 = $( ".d-none" ).text();
                if(recipes_collections3.length > 1) {
                    recipes_collections2 = recipes_collections3.split(',');
                }
            }
            $.ajax({
                url: "../menus-dishes/searchfulltext",
                notUseImage: true,
                type: "POST",      // тип запроса
                data: { // действия
                    'e' : symbol_recipes, //$('.dishes_id_1').val(),
                    'recipes_collections' : recipes_collections2
                },
                // Данные пришли
                success: function( data ) {  
                    var json = $.parseJSON(data);
                    response($.map(json.field, function (item) {
                        //$('.dishes_id_1').attr('data-id',item.id);
                        //console.log(item.id);
                        $('dishes_id').html('asd');
                        return {
                            label: item.name +': '+ item.techmup_number +' ('+ item.recipes_collections +')',
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
            //$(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('dishes',ui.item.id).attr('data-dishes',ui.item.id);
            $('#my').text(function(){
                return $(this).text() == '' ? 'You selected: ' + ui.item.value : $(this).text()+ ', '+ui.item.value;
            });
            return false;
        },
        change: function( event, ui ) {
            //$(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('dishes',ui.item.id).attr('data-dishes',ui.item.id);
            return false;
        },
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
