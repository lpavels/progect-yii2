<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\Menus;
use common\models\MenusDays;
use common\models\Days;
use common\models\RecipesCollection;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Фактическое меню за день';
$this->params['breadcrumbs'][] = $this->title;

$my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
$my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
$first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();

$params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
/*$indicator_fact_menu - переменная, в значении 1 - показывает что фактическое меню для этого дня существует, 0 - не существует
нужно для отображения кнопки "Удалить изменения" и для перезагрузки страницы аяксом в тегах серипт функциях*/
if(!empty($post)){

    $post_date = $post['date'];

    $my_menus = Menus::findOne($post['menu_id']);

    $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
    $params_days = ['class' => 'form-control', 'options' => [$post['date'] => ['Selected' => true]]];
}
//print_r($indicator_fact_menu);
?>
<div class="menus-dishes-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <div class="container mb-30">
        <div class="row">
            <div class="col">
                <?= $form->field($model2, 'menu_id')->dropDownList($my_menus_items, [
                    'class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]],
                    'onchange' => '
                  $.get("../menus-dishes/insertcharacters?id="+$(this).val(), function(data){
                  console.log(data);
                    $("input#characters").val(data);
                  });
                  $.get("../menus-dishes/insertage?id="+$(this).val(), function(data){
                  console.log(data);
                    $("input#age").val(data);
                  });
                  $.get("../menus-dishes/insertdays?id="+$(this).val(), function(data){
                  console.log(data);
                    $("#insert-days").text(data);
                  });
                  $.get("../menus-dishes/insertsrok?id="+$(this).val(), function(data){
                  console.log(data);
                    $("#insert-srok").val(data);
                  });'


                ]); ?>
            </div>

            <div class="col">
                <?= $form->field($model2, 'date')->textInput(['class'=>'datepicker-here form-control', 'value' => $post['date']])->label('Выберите дату') ?>
            </div>

        </div>
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
        <div class="row">
            <div class="col">
                <b>Дни меню: </b> <p id="insert-days"><?=$model->insert_info($menu_id, 'days');?></p>
            </div>
        </div>
        <div class="row">
            <div class="form-group" style="margin: 0 auto">
                <?= Html::submitButton('Посмотреть', ['class' => 'btn main-button-3 beforeload']) ?>
                <!--                <button class="btn main-button-3 load" type="button" disabled style="display: none">-->
                <!--                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>-->
                <!--                    Посмотреть...-->
                <!--                </button>-->
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <? if(!empty($post)){ echo '<br><br><b><p class="text-center" style="font-size: 20px;">'.$cycle.' неделя '.$model->get_days($day_of_week).' '.$post['date'].'</p></b>';} ?>
<!--    $indicator_fact_menu - переменная, в значении 1 - показывает что фактическое меню для этого дня существует,
0 - не существует нужно для отображения кнопки "Удалить изменения" и для перезагрузки страницы аяксом в тегах серипт функциях-->
    <? if($indicator_fact_menu == 1){?>
        <p><b>По этой дате были внесены изменения в меню</b></p>
        <?= Html::a('Удалить изменнения из фактического меню по этой дате', [ 'menus-dishes/fact-delete', 'menus_id' => $post['menu_id'], 'cycle' => $cycle, 'day_of_week' => $day_of_week, 'date' => strtotime($post_date)], ['data-confirm' => 'Вы уверены? Данные о фактическом меню будут удалены!', 'class' => 'btn btn-danger']); ?>
        <br><br>

    <? } ?>
    <?if(!empty($nutritions)){?>
    <?
    $recipes_collections = RecipesCollection::find()->all();
    $recipes_collection = ArrayHelper::map($recipes_collections, 'id', 'name');

    echo '<label class="control-label">Сборники технологических карт</label>';
    echo Select2::widget([
        'name' => 'recipes_collection',
        'data' => $recipes_collection,
        'options' => [
            'placeholder' => 'Выберите сборники...',
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
<!--            $menus_dishes взависимости от условий хранит либо циклическое, либо фактическое меню. По умолчанию циклическое но если есть фактическое, то его
                определение $menus_dishes происходит в контроллере-->
            <?foreach($menus_dishes as $key => $m_dish){?>
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
                                'class'=>'btn btn-sm main-button-see',
                                'onclick' => '
                          $.get("../menus-dishes/showtechmup?id=" + $(this).attr("data-dishes_id"), function(data){
                          $("#showTechmup .modal-body").empty();
                            $("#showTechmup .modal-body").append(data);
                            //console.log(data);
                            $("#showTechmup").modal("show");
                          });'
                            ]);?>
                        </td>
                        <td class="yield text-center"><?= $m_dish->yield?></td>
                        <td class="check_card text-center">
                            <?= Html::button('<span class="glyphicon glyphicon-list-alt"></span> на '. $m_dish->yield .' грамм ', [
                                'title' => Yii::t('yii', 'Посмотреть тех. карту с текущ. выходом'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm main-button-see',
                                'data-id' => $m_dish->id,
                                'onclick' => '
                          $.get("../menus-dishes/showtechmup_current_yield?id=" + $(this).attr("data-id"), function(data){
                          $("#showTechmup .modal-body").empty();
                            $("#showTechmup .modal-body").append(data);
                            //console.log(data);
                            $("#showTechmup").modal("show");
                          });'
                            ]);?>
                        </td>

                        <td class="pencil text-center">
                            <?= Html::button('<span class="glyphicon glyphicon-pencil"></span>', [
                                'title' => Yii::t('yii', 'Редактирование'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm main-button-edit',
                                'onclick'=> 'editDishes('.$m_dish->dishes_id.',"'.$m_dish->yield.'","'.$m_dish->id.'")'
                            ]);?>
                        </td>
                        <td class="text-center">
                            <?= Html::button('<span class="glyphicon glyphicon-trash"></span>', [
                                'title' => Yii::t('yii', 'Удалить'),
                                'data-toggle'=>'tooltip',
                                'class'=>'btn btn-sm main-button-delete',
                                'data'=>['confirm'=>'Вы уверены что хотите удалить это блюдо из меню ?'],
                                'onclick'=> 'deleteDishes('.$m_dish->id.',"'.strtotime($post_date).'")'
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
                <td colspan="2" class="text-center"><?= Html::button('Добавить в меню', ['class' => 'btn main-button-3', 'onclick'=> 'saveDish('.$post['menu_id'].',"'.$cycle.'","'.$day_of_week.'","'.$nutrition->id.'")']); ?></td>


            </tr>
    </div>
    </tbody>
    </table>
</div>
    <div class="text-center">
        <?= Html::button('Посмотреть состав за '. $nutrition->name, [
            'title' => Yii::t('yii', 'Посмотреть состав за '. $nutrition->name),
            'data-toggle'=>'tooltip',
            'class'=>'btn main-button-3',
            'data-menu_id' => $post['menu_id'],
            'data-cycle' => $cycle,
            'data-days_id' => $day_of_week,
            'data-nutrition_id' => $nutrition->id,
            'onclick' => '
                          $.get("../menus-dishes/show_composition?menu_id=" + $(this).attr("data-menu_id") + "&cycle=" + $(this).attr("data-cycle") + "&days_id="  + $(this).attr("data-days_id") + "&nutrition_id=" + $(this).attr("data-nutrition_id"), function(data){
                          $("#showComposition .modal-body").empty();
                            $("#showComposition .modal-body").append(data);
                            //console.log(data);
                            $("#showComposition").modal("show");
                          });'
        ]);?>
    </div>
<?}?>


<br>
    <div class="text-center">
        <?= Html::button('Посмотреть состав за день', [
            'title' => Yii::t('yii', 'Посмотреть состав за день'),
            'data-toggle'=>'tooltip',
            'class'=>'btn main-button-3',
            'data-menu_id' => $post['menu_id'],
            'data-cycle' => $cycle,
            'data-days_id' => $day_of_week,
            'data-nutrition_id' => 0,
            'onclick' => '
                          $.get("../menus-dishes/show_composition?menu_id=" + $(this).attr("data-menu_id") + "&cycle=" + $(this).attr("data-cycle") + "&days_id="  + $(this).attr("data-days_id") + "&nutrition_id=" + $(this).attr("data-nutrition_id"), function(data){
                          $("#showComposition .modal-body").empty();
                            $("#showComposition .modal-body").append(data);
                            //console.log(data);
                            $("#showComposition").modal("show");
                          });'

        ]);?>
    </div>


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

        var dish = {};
        //СОБИРАЕМ ДАННЫЕ ИЗ ФОРМ
        //indicator(либо 0 либо 1) Если 0, то фактического меню еще нет, и после первого аякса произойдут изменения и создасться копия циклического. Поэтому необходимо перезагрузить страницу и показать новые кнопки и отобразить фактич меню
        indicator = '<?php echo $indicator_fact_menu; ?>';
        dish.menusdishes_id = $('#editDishes').find('input.menusdishes_id').val();
        dish.dishes_id = $('#editDishes').find('input.dishes_id').val();
        dish.yield = $('#editDishes').find('input.yield').val();
        dish.date = '<?php echo strtotime($post_date); ?>';
        //console.log(indicator);

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
                    $('tr[data-id="'+data.id+'"]').find('td.pencil').html('<button type="button" class="btn btn-sm main-button-edit" title="Редактирование" data-toggle="tooltip" onclick="editDishes('+ data.dishes_id+', '+ data.yield +', '+ data.id +')"><span class="glyphicon glyphicon-pencil"></span></button>');
                    //$('tr[data-id="'+data.id+'"]').find('td.check_card').html('<button type="button" class="btn btn-sm btn-secondary" title="Посмотреть тех. карту с текущим выходом" data-toggle="tooltip"><span class="glyphicon glyphicon-list-alt"></span>  на '+ data.yield + ' грамм </button>');
                    /*в кнопке просто меняет текст мы ее не заменяем и не удаляем, а просто меняем текст и спан и онклик остается не изменным*/
                    $('tr[data-id="'+data.id+'"]').find('td.check_card button').html('<span class="glyphicon glyphicon-list-alt"></span>  на '+ data.yield + ' грамм ');


                }

            },
            error: function (err) {
                console.log('error')
            }
        });
        $('#editDishes').modal('toggle');
        //перезагрузка страницыдля самого первого момента изменений
        if(indicator == 0){
            location.reload();//перезагркзка
        }
        //console.log(indicator);

    }
</script>

<script type="text/javascript">

    function deleteDishes(id, date){
        //indicator(либо 0 либо 1) Если 0, то фактического меню еще нет, и после первого аякса произойдут изменения и
        // создасться копия циклического. Поэтому необходимо перезагрузить страницу и показать новые кнопки и отобразить
        // фактич меню

        indicator = '<?php echo $indicator_fact_menu; ?>';
        $.ajax({
            url: 'delete?id='+id+'&date='+ date,
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
        if(indicator == 0){
            location.reload();// перезагрузка страницы
        }
    }
</script>

<script type="text/javascript">
    //indicator(либо 0 либо 1) Если 0, то фактического меню еще нет, и после первого аякса произойдут изменения и создасться
    // копия циклического. Поэтому необходимо перезагрузить страницу и показать новые кнопки и отобразить фактич меню

    function saveDish(menu_id, cycle, days_id, nutrition_id){
        indicator = '<?php echo $indicator_fact_menu; ?>';
        var dish = {};
        dish.menu_id = menu_id;
        dish.cycle = cycle;
        dish.days_id = days_id;
        dish.nutrition_id = nutrition_id;
        console.log("nutr id",nutrition_id);
        dish.dishes_id = $('#add_dish_' + nutrition_id ).find('input.dishes_id_' + nutrition_id).data('dishes');
        console.log("dish id",dish.dishes_id);
        dish.yield = $('#add_dish_' + nutrition_id).find('input.yield_' + nutrition_id).val();
        dish.date = '<?php echo strtotime($post_date); ?>';
        $.ajax({
            url: '/menus-dishes/saving',
            data: dish,
            method: 'POST',
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
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
                        "<td class=\"text-center\"><button type=\"button\" class=\"btn btn-sm main-button-see\" title=\"Посмотреть тех. карту\"  data-toggle=\"tooltip\"  onclick='$.get(\"../menus-dishes/showtechmup?id="+data.dishes_id+"\", function(data){$(\"#showTechmup .modal-body\").empty(); $(\"#showTechmup .modal-body\").append(data);$(\"#showTechmup\").modal(\"show\")});'><span class=\"glyphicon glyphicon-list-alt\"></span> на 100 грамм</button></td>" +

                        '<td class="yield text-center">' + data.yield + '</td>' +
                        "<td class=\"text-center\"><button type=\"button\" class=\"btn btn-sm main-button-see\" title=\"Посмотреть тех. карту с текущим выходом\" data-toggle=\"tooltip\" onclick='$.get(\"../menus-dishes/showtechmup_current_yield?id="+data.id+"\", function(data){$(\"#showTechmup .modal-body\").empty(); $(\"#showTechmup .modal-body\").append(data);$(\"#showTechmup\").modal(\"show\")});\'><span class=\"glyphicon glyphicon-list-alt\"></span>  на "+ data.yield + " грамм </button></td>" +
                        '<td class="pencil text-center"><button type="button" class="btn btn-sm main-button-edit" title="Редактирование" data-toggle="tooltip" onclick="editDishes('+ data.dishes_id+', '+ data.yield +', '+ data.id +')"><span class="glyphicon glyphicon-pencil"></span></button></td>' +
                        '<td class="text-center"><a class="btn btn-sm btn-danger " title="Удалить" data-toggle="tooltip" data-confirm="Вы уверены что хотите удалить это блюдо из меню ?" onclick="deleteDishes(' + data.id + ', '+ data.date_fact_menu +')"><span class="glyphicon glyphicon-trash" style="color:white"></span></a></td>' +
                        '</tr>'
                    );
                    //обнуление инпутов
                    dish.dishes_id = $('#add_dish_' + nutrition_id).find('input.dishes_id_' + nutrition_id).val('');
                    dish.yield = $('#add_dish_' + nutrition_id).find('input.yield_' + nutrition_id).val('');

                }


            },
            error: function (err) {
                console.log('error')
            }
        })
        if(indicator == 0){
            location.reload();
        }
    }
</script>
<!--МОДАЛЬНОЕ ОКНО ДЛЯ РЕДАКТИРОВАНИЯ БЛЮДА-->
<div id="editDishes" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header-p3">
                <h4 class="modal-title">Редактирование блюда в меню</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
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
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn main-button-delete" data-dismiss="modal">Отмена</button>

                    <?= Html::submitButton('Сохранить', ['class' => 'btn main-button-3 pull-right','onclick'=>'updateDishes()']); ?>
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

<?
$script = <<< JS
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
            if(typeof(recipes_collections) != "undefined" && variable !== null) {
                if(recipes_collections.length > 1) {
                    recipes_collections2 = recipes_collections.split(',');
                } else {
                recipes_collections2 = recipes_collections;
                }
            }
            else {
                //сборники по умолчанию в выборке
                recipes_collections2 = ["1", "2", "4"];
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
            if(typeof(recipes_collections) != "undefined" && variable !== null) {
                if(recipes_collections.length > 1) {
                    recipes_collections2 = recipes_collections.split(',');
                } else {
                recipes_collections2 = recipes_collections;
                }
            }
            else {
                //сборники по умолчанию в выборке
                recipes_collections2 = ["1", "2", "4"];
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
