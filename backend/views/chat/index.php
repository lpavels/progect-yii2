<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Чат';
$u_id = Yii::$app->user->id;
?>

    <!--<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">-->

    <style>
        .chat-right .chat-text {
            background: #0B93F6 !important;
            color: white !important;
            max-width: 70%;
        }

        .chat-right .chat-text a {
            color: #ffbc97;
            text-decoration: underline
        }

        .chat-left .chat-text {
            max-width: 70%;
        }

        .chat-div {
            /*width: 100%;*/
            height: 100%;
        }

        .chat-search-box {
            -webkit-border-radius: 3px 0 0 0;
            -moz-border-radius: 3px 0 0 0;
            border-radius: 3px 0 0 0;
            padding: .75rem 1rem;
        }

        .chat-search-box .input-group .form-control {
            -webkit-border-radius: 2px 0 0 2px;
            -moz-border-radius: 2px 0 0 2px;
            border-radius: 2px 0 0 2px;
            border-right: 0;
        }

        .chat-search-box .input-group .form-control:focus {
            border-right: 0;
        }

        .chat-search-box .input-group .input-group-btn .btn {
            -webkit-border-radius: 0 2px 2px 0;
            -moz-border-radius: 0 2px 2px 0;
            border-radius: 0 2px 2px 0;
            margin: 0;
        }

        .chat-search-box .input-group .input-group-btn .btn i {
            font-size: 1.2rem;
            line-height: 100%;
            vertical-align: middle;
        }

        @media (max-width: 767px) {
            .chat-search-box {
                display: none;
            }
        }


        /************************************************
            ************************************************
                                            Users Container
            ************************************************
        ************************************************/

        .users-container {
            position: relative;
            padding: 1rem 0;
            border-right: 1px solid #e6ecf3;
            height: 100%;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
        }


        /************************************************
            ************************************************
                                                    Users
            ************************************************
        ************************************************/

        .users {
            padding: 0;
        }

        .users .person {
            position: relative;
            width: 100%;
            padding: 10px 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f0f4f8;
        }

        .users .person:hover {
            background-color: #ffffff;
            /* Fallback Color */
            background-image: -webkit-gradient(linear, left top, left bottom, from(#e9eff5), to(#ffffff));
            /* Saf4+, Chrome */
            background-image: -webkit-linear-gradient(right, #e9eff5, #ffffff);
            /* Chrome 10+, Saf5.1+, iOS 5+ */
            background-image: -moz-linear-gradient(right, #e9eff5, #ffffff);
            /* FF3.6 */
            background-image: -ms-linear-gradient(right, #e9eff5, #ffffff);
            /* IE10 */
            background-image: -o-linear-gradient(right, #e9eff5, #ffffff);
            /* Opera 11.10+ */
            background-image: linear-gradient(right, #e9eff5, #ffffff);
        }

        .users .person.active-user {
            background-color: #ffffff;
            /* Fallback Color */
            background-image: -webkit-gradient(linear, left top, left bottom, from(#f7f9fb), to(#ffffff));
            /* Saf4+, Chrome */
            background-image: -webkit-linear-gradient(right, #f7f9fb, #ffffff);
            /* Chrome 10+, Saf5.1+, iOS 5+ */
            background-image: -moz-linear-gradient(right, #f7f9fb, #ffffff);
            /* FF3.6 */
            background-image: -ms-linear-gradient(right, #f7f9fb, #ffffff);
            /* IE10 */
            background-image: -o-linear-gradient(right, #f7f9fb, #ffffff);
            /* Opera 11.10+ */
            background-image: linear-gradient(right, #f7f9fb, #ffffff);
        }

        .users .person:last-child {
            border-bottom: 0;
        }

        .users .person .user {
            display: inline-block;
            position: relative;
            margin-right: 10px;
        }

        .users .person .user img {
            width: 48px;
            height: 48px;
            -webkit-border-radius: 50px;
            -moz-border-radius: 50px;
            border-radius: 50px;
        }

        .users .person .user .status {
            width: 10px;
            height: 10px;
            -webkit-border-radius: 100px;
            -moz-border-radius: 100px;
            border-radius: 100px;
            background: #e6ecf3;
            position: absolute;
            top: 0;
            right: 0;
        }

        .users .person .user .status.online {
            background: #9ec94a;
        }

        .users .person .user .status.offline {
            background: #c4d2e2;
        }

        .users .person .user .status.away {
            background: #f9be52;
        }

        .users .person .user .status.busy {
            background: #fd7274;
        }

        .users .person p.name-time {
            font-weight: 600;
            font-size: .85rem;
            display: inline-block;
        }

        .users .person p.name-time .time {
            font-weight: 400;
            font-size: .7rem;
            text-align: right;
            color: #8796af;
        }

        .users .person p.name-time .new-message {
            font-weight: 400;
            font-size: .7rem;
            text-align: right;
            color: red;
        }

        @media (max-width: 767px) {
            .users .person .user img {
                width: 30px;
                height: 30px;
            }

            .users .person p.name-time {
                display: none;
            }

            .users .person p.name-time .time {
                display: none;
            }

            .users .person p.name-time .new-message {
                display: none;
            }
        }


        /************************************************
            ************************************************
                                            Chat right side
            ************************************************
        ************************************************/

        .selected-user {
            width: 100%;
            padding: 0 15px;
            min-height: 64px;
            line-height: 64px;
            border-bottom: 1px solid #e6ecf3;
            -webkit-border-radius: 0 3px 0 0;
            -moz-border-radius: 0 3px 0 0;
            border-radius: 0 3px 0 0;
        }

        .selected-user span {
            line-height: 100%;
        }

        .selected-user span.name {
            font-weight: 700;
        }

        .chat-container {
            position: relative;
            padding: 1rem;
        }

        .chat-container li.chat-left,
        .chat-container li.chat-right {
            display: flex;
            flex: 1;
            flex-direction: row;
            margin-bottom: 10px;
        }

        .chat-container li img {
            width: 48px;
            height: 48px;
            -webkit-border-radius: 30px;
            -moz-border-radius: 30px;
            border-radius: 30px;
        }

        .chat-container li .chat-avatar {
            margin-right: 20px;
        }

        .chat-container li.chat-right {
            justify-content: flex-end;
        }

        .chat-container li.chat-right > .chat-avatar {
            margin-left: 20px;
            margin-right: 0;
        }

        .chat-container li .chat-name {
            font-size: .75rem;
            color: #999999;
            text-align: center;
        }

        .chat-container li .chat-text {
            padding: .4rem 1rem;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            background: #ffffff;
            font-weight: 300;
            line-height: 150%;
            position: relative;
        }

        .chat-container li .chat-text:before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            top: 10px;
            left: -20px;
            border: 10px solid;
            border-color: transparent #ffffff transparent transparent;
        }

        .chat-container li.chat-right > .chat-text {
            text-align: right;
        }

        .chat-container li.chat-right > .chat-text:before {
            right: -20px;
            border-color: transparent transparent transparent #0B93F6;
            left: inherit;
        }

        .chat-container li .chat-hour {
            padding: 0;
            margin-bottom: 10px;
            font-size: .75rem;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            margin: 0 0 0 15px;
        }

        /*.chat-container li .chat-hour > span {
            font-size: 16px;
            color: #9ec94a;
        }*/

        .fa-check-circle {
            font-size: 16px;
            color: #9ec94a;
        }

        .fa-times-circle {
            font-size: 16px;
            color: #ff1012;
        }

        .chat-container li.chat-right > .chat-hour {
            margin: 0 15px 0 0;
        }

        @media (max-width: 767px) {
            .chat-container li.chat-left,
            .chat-container li.chat-right {
                flex-direction: column;
                margin-bottom: 30px;
            }

            .chat-container li img {
                width: 32px;
                height: 32px;
            }

            .chat-container li.chat-left .chat-avatar {
                margin: 0 0 5px 0;
                display: flex;
                align-items: center;
            }

            .chat-container li.chat-left .chat-hour {
                justify-content: flex-end;
            }

            .chat-container li.chat-left .chat-name {
                margin-left: 5px;
            }

            .chat-container li.chat-right .chat-avatar {
                order: -1;
                margin: 0 0 5px 0;
                align-items: center;
                display: flex;
                justify-content: right;
                flex-direction: row-reverse;
            }

            .chat-container li.chat-right .chat-hour {
                justify-content: flex-start;
                order: 2;
            }

            .chat-container li.chat-right .chat-name {
                margin-right: 5px;
            }

            .chat-container li .chat-text {
                font-size: .8rem;
            }
        }

        .chat-form {
            padding: 15px;
            width: 100%;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ffffff;
            border-top: 1px solid white;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .card {
            border: 0;
            background: #f4f5fb;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
            margin-bottom: 2rem;
            box-shadow: none;
        }

        .chat-text {
            position: relative;
            padding-bottom: 20px !important;
            margin: 0;
        }

        .chat-text .chat-hour {
            font-size: 10px !important;
            position: absolute;
            bottom: -3px;
            right: 2px;
        }
    </style>


    <div class="chat-div container">
        <!-- Content wrapper start -->
        <div class="content-wrapper">
            <!-- Row start -->
            <div class="row gutters">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card m-0">

                        <!-- Row start -->
                        <div class="row no-gutters">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="selected-user text-center">
                                    <span class="name">ФБУН "Новосибирский НИИ гигиены" Роспотребнадзора</span>
                                </div>
                                <div class="chat-container">
                                    <ul class="chat-box chatContainerScroll" style="height: 500px; overflow-x: hidden">
                                        <?php
                                        if (is_array($messages)) {
                                            foreach ($messages as $message) {
                                                $expMes = explode('|', $message);
                                                //print_r($expMes);die();
                                                if ($expMes[0] == 1 && $expMes[1] == $u_id) {
                                                    ?>
                                                    <li class="chat-left">
                                                        <div class="chat-avatar">
                                                            <img src="../images/icons/from-adm.png" alt="Retail User">
                                                            <div class="chat-name">admin</div>
                                                        </div>
                                                        <div class="chat-text"><?= $expMes[2] ?>
                                                            <div class="chat-hour ml-1"><?= ($expMes[3] == 1) ? '<span class="fa fa-check-circle mr-1"></span>' : '<span class="fa fa-times-circle mr-1"></span>' ?> <?= date(
                                                                    'd.m.Y H:i:s',
                                                                    strtotime($expMes[4])
                                                                ) ?></div>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }

                                                if ($expMes[0] == $u_id && $expMes[1] == 1) {
                                                    ?>
                                                    <li class="chat-right">
                                                        <div class="chat-text"><?= $expMes[2] ?>
                                                            <div class="chat-hour mr-1"><?= ($expMes[3] == 1) ? '<span class="fa fa-check-circle mr-1"></span>' : '<span class="fa fa-times-circle mr-1"></span>' ?> <?= date(
                                                                    'd.m.Y H:i:s',
                                                                    strtotime($expMes[4])
                                                                ) ?></div>
                                                        </div>
                                                        <div class="chat-avatar">
                                                            <img src="../images/icons/user.png" alt="Retail Admin">
                                                            <div class="chat-name">Вы</div>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>

                                    <div class="form-group mt-3 mb-0">
                                        <?php
                                        $form = ActiveForm::begin(['action' => '/chat/create']); ?>
                                        <?= $form->field($model, 'receiver_user_id')->hiddenInput(
                                            ['value' => $u_id]
                                        )->label(false) ?>
                                        <?= $form->field($model, 'message')->textarea(
                                            ['rows' => 3, 'placeholder' => 'Введите сообщение...']
                                        )->label(false) ?>
                                        <div class="form-group">
                                            <?= Html::submitButton(
                                                'Отправить',
                                                ['class' => 'btn btn-secondary form-control']
                                            ) ?>
                                        </div>
                                        <?php
                                        ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>


<?php
$js = <<< JS
$("ul.chatContainerScroll").scrollTop(999999);

$('form').on('beforeSubmit', function(){
    var form = $(this);
    var submit = form.find(':submit');
    submit.html('<span class="fa fa-spin fa-spinner"></span> Пожалуйста, подождите...');
    submit.prop('disabled', true);
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);