<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \common\models\SignupForm */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;
use kartik\select2\Select2;

$this->title = 'Регистрация в ПС "Основы здорового питания"';
?>

    <div class="site-signup m-4">
        <!--<p class="text-left" style="color: red"><br>
            <strong>Видеоуроки по темам:
                <br> 1. Как зарегистрироваться в программном средстве?
                <br> 2. Как получить личный логин ответственного по обучению в учреждении?
                <br> 3. Что делать, если Вашей организации нет в выпадающем списке при регистрации?
                <br>Можно посмотреть
                <a target="_blank" href="https://www.youtube.com/watch?v=2sUIHtlCvgs">по ссылке</a>
            </strong>
        </p>-->

        <span class="title"><?= Html::encode($this->title) ?></span>

                <p style="font-size: 13px"><span style="color: red; ">*</span> - поля обязательные для заполнения</p>
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">Федеральный округ<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= Select2::widget([
                            'name' => 'federal_district_id',
                            'data' => $district_items,
                            'options' => [
                                'required' => true,
                                'placeholder' => 'Выберите федеральный округ',
                                'options' => [
                                    //3 => ['disabled' => true],
                                ],
                                'onchange' => "
                                    $.get(\"../site/subjectslist?id=\"+$(this).val(), function(data){
                                        $(\"#w1\").html(data);
                                    });
                                    $.get(\"../site/municipalitylist?id=0\", function(data){
                                      $(\"#w2\").html(data);
                                    });
                                    $.get(\"../site/organization-name?id=0\", function(data){
                                      $(\"#w3\").html(data);
                                    });
                                    $.get(\"../site/listen-type?id=0\", function(data){
                                      $(\"#w4\").html(data);
                                    });
                                    $.get(\"../site/training-program?name=0\", function(data){
                                      $(\"#w5\").html(data);
                                    });
                                "
                            ],
                        ]); ?></div>
                </div>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">Субъект федерации<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= Select2::widget([
                            'name' => 'region_id',
                            'data' => [],
                            'options' => [
                                'required' => true,
                                'placeholder' => 'Выберите регион',
                                'onchange' => "
                                    $.get(\"../site/municipalitylist?id=\"+$(this).val(), function(data){
                                      $(\"#w2\").html(data);
                                    });
                                    $.get(\"../site/organization-name?id=0\", function(data){
                                      $(\"#w3\").html(data);
                                    });
                                    $.get(\"../site/listen-type?id=0\", function(data){
                                      $(\"#w4\").html(data);
                                    });
                                    $.get(\"../site/training-program?name=0\", function(data){
                                      $(\"#w5\").html(data);
                                    });
                                "
                            ],
                        ]); ?></div>
                </div>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">Муниципальное образование<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= Select2::widget([
                            'name' => 'municipality',
                            'data' => [],
                            'options' => [
                                'required' => true,
                                'placeholder' => 'Выберите муниципальное образование',
                                'onchange' => "          
                                    $.get(\"../site/organization-name?id=\"+$(this).val(), function(data){
                                      $(\"#w3\").html(data);
                                    });
                                    $.get(\"../site/listen-type?id=0\", function(data){
                                      $(\"#w4\").html(data);
                                    });
                                    $.get(\"../site/training-program?name=0\", function(data){
                                      $(\"#w5\").html(data);
                                    });
                                "
                            ],
                        ]); ?></div>
                </div>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">Наименование организации<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= Select2::widget([
                            'name' => 'name_organization',
                            'data' => [],
                            'options' => [
                                'required' => true,
                                'placeholder' => 'Выберите организацию',
                                'onchange' => "          
                                    $.get(\"../site/listen-type?id=\"+$(this).val(), function(data){
                                      $(\"#w4\").html(data);
                                    });
                                    $.get(\"../site/training-program?name=0\", function(data){
                                      $(\"#w5\").html(data);
                                    });
                                "
                            ],
                        ]); ?></div>
                </div>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">Тип слушателя<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= Select2::widget([
                            'name' => 'listener_type',
                            'data' => [],
                            'options' => [
                                'required' => true,
                                'placeholder' => 'Выберите тип слушателя',
                                'onchange' => "          
                                    $.get(\"../site/training-program?name=\"+$(this).val(), function(data){
                                      $(\"#w5\").html(data);
                                    });
                                    
                                    if($(this).val()=='school14' || $(this).val() == 'school511')
                                    {
                                        $(\".num_class\").show();
                                        $(\".let_class\").show();
                                    }
                                    else
                                    {
                                        $(\".num_class\").hide();
                                        $(\".let_class\").hide();
                                    }
                                "
                            ],
                        ]); ?></div>
                </div>

                <div class="row mt-2">
                    <div class="col-sm-7 col-lg-2">Обучающая программа<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= Select2::widget([
                            'name' => 'training_id',
                            'data' => [],
                            'options' => [
                                'required' => true,
                                'placeholder' => 'Выберите обучающую программу',
                                //'onchange' => ""
                            ],
                        ]); ?></div>
                </div>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">ФИО (на кого будет сертификат)<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= $form->field($model, 'name')->textInput(['class' => 'form-control'])->label(false) ?></div>
                </div>

                <div class="row mt-1 num_class">
                    <div class="col-sm-7 col-lg-2">Класс<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= $form->field($model, 'class')->textInput(['type' => 'number', 'class' => 'form-control'])->label(false) ?></div>
                </div>

                <div class="row let_class">
                    <div class="col-sm-7 col-lg-2">Буква класса<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= $form->field($model, 'bukva_klassa')->textInput(['class' => 'form-control'])->label(false) ?></div>
                </div>

                <div class="row mt-1">
                    <div class="col-sm-7 col-lg-2">Год рождения<span style="color: red; ">*</span></div>
                    <div class="col-sm-7 col-lg-3"><?= $form->field($model, 'year_birth')->textInput(['type' => 'number', 'class' => 'form-control'])->label(false) ?></div>
                </div>

                <div class="row approval">
                    <div class="col-sm-7 col-lg-6"><?= $form->field($model, 'check2')->checkbox(['checked' => false])->label('Данные(организация, тип слушателя, обучающая программа, год рождения) введены корректно. Изменение этих данных в процессе обучения невозможно<span style="color: red; ">*</span>') ?></div>
                </div>

                <div class="row approval">
                    <div class="col-sm-7 col-lg-6"><?= $form->field($model, 'check')->checkbox(['checked' => false])->label(HTML::a('Согласие ','@web/approval.pdf',['target'=>'_blank']). 'на обработку персональных данных<span style="color: red; ">*</span>') ?></div>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn main-button-3 col-sm-7 col-lg-5 mt-3', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

<?php
$js = <<< JS
$('form').on('beforeSubmit', function(){
    var form = $(this);
    var submit = form.find(':submit');
    submit.html('<span class="fa fa-spin fa-spinner"></span> Пожалуйста, подождите...');
    submit.prop('disabled', true);
});

$(".num_class").hide();
$(".let_class").hide();
JS;
$this->registerJs($js, View::POS_READY);

$this->registerCssFile('@web/css/adaptive.css');