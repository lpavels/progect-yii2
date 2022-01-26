<?php

use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\TrainingProgram;
use common\models\User;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Личные данные';
?>

<?php
$queru = User::find()->where(['key_login' => Yii::$app->user->identity->key_login])->one();
$training = TrainingProgram::find()->where(['id' => $queru->training_id])->one();
$organization = Organization::find()->where(['id' => $queru->organization_id])->one();
$District = FederalDistrict::find()->where(['id' => $organization->federal_district_id])->one();
$Region = Region::find()->where(['id' => $organization->region_id])->one();
$Municipality = Municipality::find()->where(['id' => $organization->municipality_id])->one();
$rest = substr($queru->created_at, 0, 10);
$model_user = User::findOne(Yii::$app->user->id);

$two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-sm-7 col-lg-3 col-form-label font-weight-bold']];
?>

<div class="user-info">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Уникальный номер:</div>
            <div class="col-sm-7 col-lg-4"><?= $queru->key_login ?> </div>
        </div>

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model_user, 'name', $two_column)->textInput(['data-toggle'=>"tooltip",'title'=>'Исправьте опечатку и нажмите "Сохранить"','class' => 'form-control col-sm-7 col-lg-4'])->label('Фамилия Имя Отчество:') ?>
        <div class="mt-2"><?= Html::submitButton('Сохранить изменения в графе "ФИО"', ['data-toggle'=>"tooltip",'title'=>'Исправьте опечатку в графе ФИО ','class' => 'text-center btn btn-sm main-button-3']) ?></div>
        <?php ActiveForm::end(); ?>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Год рождения:</div>
            <div class="col-sm-7 col-lg-4"><?= $queru->year_birth ?> </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Федеральный округ:</div>
            <div class="col-sm-7 col-lg-4"><?= $District->name ?> </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Регион:</div>
            <div class="col-sm-7 col-lg-4"><?= $Region->name ?> </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Муниципальное образование:</div>
            <div class="col-sm-7 col-lg-4"><?= $Municipality->name ?> </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Организация:</div>
            <div class="col-sm-7 col-lg-4"><?= $organization->short_title ?> </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Тип слушателя:</div>
            <div class="col-sm-7 col-lg-4"><?= $queru->type_listener ?> </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3 font-weight-bold">Дата регистрации:</div>
            <div class="col-sm-7 col-lg-4"><?= date('d.m.Y', $rest) ?> </div>
        </div>

        <div class="mt-3">
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> Сохранить данные в PDF формате',
                ['export?id=' . $queru->id],
                [
                    'class' => 'btn btn-outline-secondary btn-sm',
                    'title' => Yii::t('yii', 'Вы можете сохранить данные в PDF формате'),
                    'data-toggle' => 'tooltip',
                ])
            ?>
        </div>
    </div>
</div>

