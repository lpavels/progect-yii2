<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Добавление продуктов в блюдо';
$this->params['breadcrumbs'][] = $this->title;
$get = Yii::$app->request->get()['id'];

?>

<?= Html::a('Вернуться к списку блюд', ['/dishes'], ['class' => 'profile-link']) ?>
<div class="container">
<div class="dishes-index">
    <h1 class="text-center"><?= Html::encode($this->title).' ('.$model->get_dishes($get).')'; ?> </h1>

        <div class="row justify-content-center">
            <div class="">
        <?= GridView::widget([
            'options' => [
                'class' => 'menus-table table-responsive'],
            'tableOptions' => [
                'class' => 'table table-bordered table-responsive'
            ],
            'dataProvider' => $dataProvider,
            'rowOptions' => ['class' => 'grid_table_tr'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'grid_table_th'],],

                //'id',
                //'dishes_id',
                [
                    'attribute' => 'products_id',
                    'value' => function($model){

                        return $model->get_products($model->products_id)->name;
                    },
                    'filter' => $categories,
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                    //'visible' => Yii::$app->user->can('admin'),
                ],
               // 'products_id',
                [
                    'attribute' => 'gross_weight',
                    'value' => 'gross_weight',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'align-middle text-center'],
                ],
                [
                    'attribute' => 'net_weight',
                    'value' => 'net_weight',
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => 'align-middle text-center'],
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model){
                        return $model->get_date($model->created_at);
                    },
                    'filter' => $categories,
                    'headerOptions' => ['class' => 'grid_table_th'],
                    'contentOptions' => ['class' => ''],
                    //'visible' => Yii::$app->user->can('admin'),
                ],
               // 'created_at',
                [
                    'header' => 'Редактировать/Удалить продукт',
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{dishes-products/update} {delete-product}',
                    'headerOptions' => ['class' => 'grid_table_th text-nowrap'],
                    'contentOptions' => ['class' => 'action-column align-middle text-center'],
                    'buttons' => [

                        'dishes-products/update' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => Yii::t('yii', 'Редактировать'),
                                'data-toggle' => 'tooltip',
                                'class' => 'btn btn-sm btn-primary'
                            ]);
                        },
                        'delete-product' => function ($url, $model, $key) {

                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Удалить'),
                                'data-toggle' => 'tooltip',
                                'class' => 'btn btn-sm btn-danger',
                                'data' => ['confirm' => 'Вы уверены что хотите удалить продукт из блюда?'],
                            ]);

                        },
                    ],
                ]

            ],
        ]); ?>
        <?= Html::a('Автоматически пересчитать Брутто', ['dishes/brutto-netto-count', 'id' => $get], ['class' => 'btn btn-primary']) ?>
            </div>

    </div>

    <div class="dishes-products-form">

<!--        --><?php //$form = ActiveForm::begin(); ?>
<!---->
<!--        --><?//= $form->field($model, 'dishes_id')->hiddenInput()->label(false) ?>
<!---->
<!--        --><?//= $form->field($model, 'products_id')->textInput() ?>
<!---->
<!--        --><?//= $form->field($model, 'net_weight')->textInput() ?>
<!---->
<!--        --><?//= $form->field($model, 'gross_weight')->textInput() ?>
<!---->
<!--        --><?// //= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--        <div class="form-group">-->
<!--            --><?//= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
<!--        </div>-->
<!---->
<!--        --><?php //ActiveForm::end(); ?>
        <div class="mt-5 mt-5">
            <b class="text-center"><p>Начните вводить продукт в поле "Продукт", а после введите его массу нетто</p></b>
			<p class="text-danger text-center">Если число не является целым, то нужно указывать дробную часть через точку(например: 25.9).</p>
            <div class="row justify-content-center" id="add_product">
                <div class="col-11 col-md-4" id="products_id"><b>Продукт</b><?= Html::textInput('products_id', '', ['placeholder' => "Начните вводить продукт", 'class'=>'form-control products_auto products_id']);?></div>
<!--                <div class="col-11 col-md-4" id="brutto"><b>Брутто</b>--><?//= Html::textInput('brutto', '', ['class'=>'form-control brutto']);?><!--</div>-->
                <div class="col-11 col-md-4" id="netto"><b>Нетто(в граммах)</b><?= Html::textInput('netto', '', ['class'=>'form-control netto']);?></div>
                <div class="col-11 col-md-4"><?= Html::button('Добавить в блюдо', ['class' => 'btn main-button-3 mt-4', 'onclick'=> 'saveProduct()']); ?></div>
            </div>
        </div>
    </div>

</div>
</div>

<script type="text/javascript">
    function saveProduct(){
        var product = {};
        var key = getUrlParameter('id');
        product.dishes_id = key;
        //product.products_id = $('#products_id').find('input.products_id').val();
        product.products_id = $('#products_id').find('input.products_id').data('products');
        product.netto = $('#netto').find('input.netto').val();
        //product.brutto = $('#brutto').find('input.brutto').val();

        $.ajax({
            url: '/menus-dishes/saving-product',
            data: product,
            method: 'POST',
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
                if (data) {
                    if(data == 'error2'){
                        alert('Введен несуществующий продукт');
                    }
                    if(data == 'error1'){
                        alert('Все поляля должны быть обязательно заполнены');
                    }
                    location.reload();
                }

            },
            error: function (err) {
                console.log('error')
            }
        })

    }

/*получение продукта из урл*/
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };
</script>

<?
$script = <<< JS
//автоподстановка продуктов
$( document ).ready(function(){
    $('.products_auto').autocomplete({
        autoFocus: true,
        minLength: 1,
        delay: 300,      
        source: function( request, response ) {
            $.ajax({
                url: "../dishes/searchfulltext",
                notUseImage: true,
                type: "POST",      // тип запроса
                data: { // действия
                    'e' : $('.products_auto').val(),
                },
                // Данные пришли
                success: function( data ) {  
                    var json = $.parseJSON(data);
                    //console.log(json.field);
                    response($.map(json.field, function (item) {
                        //$('.dishes_id_1').attr('data-id',item.id);
                        //console.log(item.id);
                        return {
                            label: item.name,
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
            $(this).data('products',ui.item.id).attr('data-products',ui.item.id);
            $('#my').text(function(){
                return $(this).text() == '' ? 'You selected: ' + ui.item.value : $(this).text()+ ', '+ui.item.value;
            });
            return false;
        },
        change: function( event, ui ) {
            //console.log(ui.item.id);
            $(this).val(ui.item.value);
            $(this).addClass('green');
            $(this).data('products',ui.item.id).attr('data-products',ui.item.id);
            return false;
        },
    });
});

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
