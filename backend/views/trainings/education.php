<?php

use common\models\ThemesContent;
use common\models\Trainings;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */

$two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => ' col-sm-12 col-lg-6 col-form-label font-weight-bold']];
$params = ['class' => 'form-control  col-sm-12 col-lg-4'];

$informativity_items = [
    '' => 'Выберите вариант',
    1 => 'Да, информативна',
    0 => 'Нет, не информативна',

];
$utility_items = [
    '' => 'Выберите вариант',
    1 => 'Да, полезна',
    0 => 'Нет, не полезна',

];
$visualization_items = [
    '' => 'Выберите вариант',
    1 => 'Да, наглядна',
    0 => 'Нет, не наглядна',
];
$yesOrNo = [
    '' => 'Выберите вариант',
    0 => 'Нет',
    1 => 'Да',
];
?>
    <!--ТАБЫ СО СПИСКОМ ТЕМ-->
    <div class="users-education-form container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <? $count_li = 0;
        foreach ($themes as $thema)
        {
            $count_li++; ?>
            <li class="nav-item">
                <a class="nav-link nav-link1 <? if ($count_li == 1)
                {
                    echo 'active';
                } ?>" id="home-tab" data-toggle="tab" href="#tema<?= $thema->id ?>" role="tab"
                   aria-controls="home" aria-selected="true"><?= $thema->short_name ?></a>
            </li>
        <? } ?>

        <li class="nav-item">
            <a class="nav-link nav-link1" id="profile-tab" data-toggle="tab" href="#tema_sr" role="tab"
               aria-controls="profile" aria-selected="false">Самостоятельная работа</a>
        </li>
    </ul>

    <!--КОНТЕНТ ДЛЯ ТАБОВ-->
    <div class="tab-content" id="myTabContent">
        <? $count_cont = 0;
        foreach ($themes as $thema)
        {
            $count_cont++; ?>
            <div class="tab-pane fade <? if ($count_cont == 1)
            {
                echo 'show active';
            } ?>" id="tema<?= $thema->id; ?>" role="tabpanel" aria-labelledby="home-tab">
                <br>
                <? $themes_content = ThemesContent::find()->where(['theme_program_id' => $thema->id])->one()->content;
                echo $themes_content; ?>

                <div class="form-group ">
                    <?
                    $Trainings = Trainings::find()->where(['user_id' => Yii::$app->user->identity->id, 'theme_program_id' => $thema->id])->count();
                    if ($Trainings > 0)
                    {
                        echo '<div class="alert alert-success alert-dismissible mt-3" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Вы отметили тему как пройденную</div>';
                    }
                    else
                    { ?>
                        <h5 class="text-center">Оцените предложенную к изучению информацию по следующим критериям:</h5>

                        <?php $form = ActiveForm::begin(); ?>

                        <div class="anket-form container">
                            <?= $form->field($model1, 'theme_program_id')->hiddenInput(['value' => $thema->id])->label(false); ?>
                            <?= $form->field($model2, 'informativity', $two_column)->dropDownList($informativity_items, $params) ?>
                            <?= $form->field($model2, 'informativity_comment', $two_column)->textarea(['class' => 'form-control col-sm-12 col-lg-4', 'rows' => 2]) ?>
                            <?= $form->field($model2, 'utility', $two_column)->dropDownList($utility_items, $params) ?>
                            <?= $form->field($model2, 'utility_comment', $two_column)->textarea(['class' => 'form-control col-sm-12 col-lg-4', 'rows' => 2]) ?>
                            <?= $form->field($model2, 'visualization', $two_column)->dropDownList($visualization_items, $params) ?>
                            <?= $form->field($model2, 'visualization_comment', $two_column)->textarea(['class' => 'form-control col-sm-12 col-lg-4', 'rows' => 2]) ?>


                            <? if (Yii::$app->user->can('school1011') || Yii::$app->user->can('school14') || Yii::$app->user->can('school511') || Yii::$app->user->can('school56') || Yii::$app->user->can('school59') || Yii::$app->user->can('school79'))
                            {
                                ?>
                                <h5 class="text-center">Перечень проведенных по теме организационных мероприятий:</h5>
                                <?= $form->field($model2, 'class_chas', $two_column)->dropDownList($yesOrNo, $params) ?>
                                <?= $form->field($model2, 'class_chas_date', $two_column)->textInput(['type' => 'date', 'class' => 'form-control col-sm-7 col-lg-4', 'rows' => 2]) ?>
                                <?= $form->field($model2, 'parent_meet', $two_column)->dropDownList($yesOrNo, $params) ?>
                                <?= $form->field($model2, 'parent_meet_date', $two_column)->textInput(['type' => 'date', 'class' => 'form-control col-sm-7 col-lg-4', 'rows' => 2]) ?>
                                <?= $form->field($model2, 'inoe', $two_column)->textInput(['class' => 'form-control col-sm-7 col-lg-4', 'rows' => 2]) ?>
                            <? } ?>

                            <div class="form-group text-center">
                                <?= Html::submitButton('Подтвердить прохождение материала', ['class' => 'btn btn-sm main-button-3 form-control mt-3 col-sm-7 col-lg-4', 'value' => 'test', 'name' => 'save_test']) ?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    <? } ?>
                </div>
            </div>
        <? } ?>

        <div class="tab-pane fade" id="tema_sr" role="tabpanel" aria-labelledby="profile-tab">
            <br>
            <h5 class="text-center">Cборники рецептур блюд и кулинарных изделий для детского питания, разработанные ФБУН
                "Новосибирский НИИ гигиены" Роспотребнадзора:</h5>
            <table>
                <tbody>
                <tr>
                    <td>
                        <a class="btn btn-sm main-button-2" target="_blank" href="../images/pdf/school/hol1.pdf"
                           title="Скачать"
                           data-toggle="tooltip"> Скачать "Сборник №1.pdf" <span
                                    class="glyphicon glyphicon-arrow-down"></span></a>
                    </td>
                    <td>
                        <a class="btn btn-sm main-button-2" target="_blank" href="../images/pdf/school/hol2.pdf"
                           title="Скачать"
                           data-toggle="tooltip">Скачать "Сборник №2.pdf" <span
                                    class="glyphicon glyphicon-arrow-down"></span></a>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <p>Совместная работа родителей с детьми по составлению режима дня и
                меню позволит получить новые навыки, а также существенно сократить
                риски здоровью, обусловленные нерациональным режимом дня и
                нездоровым питанием.</p>

            <p>Полученные навыки рекомендуется укрепить в ходе практического занятия</p>

            <div class="alert alert-primary" role="alert">
                Если Вы зарегистрировались 14.07.2021 и позже, то у Вас отображается раздел "Самостоятельная работа" в котором и необходимо её выполнять. Переходить по ссылкам дополнительно не нужно. Видеоурок по выполнению самостоятельной работы можно посмотреть по <a href="https://youtu.be/uWQXD855PYM?t=37" target="_blank">ссылке (нажмите)</a>.
            </div>
            <div class="alert alert-danger" role="alert">
                Если Вы были зарегистрированы до 14.07.2021, то для прохождения самостоятельной работы перейдите по <a target="_blank" href="https://individ.demography.site/">ссылке (нажмите)</a>. Видеоурок по выполнению самостоятельной работы можно посмотреть по <a href="https://youtu.be/uWQXD855PYM" target="_blank">ссылке (нажмите)</a>.
            </div>

        </div>
    </div>

<?
$script = <<< JS
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>