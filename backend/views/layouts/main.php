<?php
$access_ip = ['87.103.250.194','127.0.0.1'];
if (!in_array(Yii::$app->userHelp->ip(),$access_ip))
{
    //print_r('Внимание! 13.12.2021 c 14:00 до 15:00 (UTC +7) будут проводиться технические работы. Программа работать не будет в течение этого времени.');
    //die();
}

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use common\models\Chat;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use xtetis\bootstrap4glyphicons\assets\GlyphiconAsset;

GlyphiconAsset::register($this);
AppAsset::register($this);

$u_id = Yii::$app->user->id;
if (date('Y')===2021){
    $report_tbl = ReportTbl21::find()->where(['user_id' => $u_id])->one();
}elseif (date('Y')==2022){
    $report_tbl = ReportTbl22::find()->where(['user_id' => $u_id])->one();
    if (empty($report_tbl)){
        $report_tbl = ReportTbl21::find()->where(['user_id' => $u_id])->one();
    }
}

if (Yii::$app->user->can('admin')){
    $countQuestion = Chat::find()->select(['chat.sender_user_id'])->where(['status' => 0])
        ->andWhere(['!=','chat.sender_user_id',1])->groupBy(['chat.sender_user_id'])->count(); #Кол-во неотвеченных вопросов
}
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/loadingio/ldLoader@v1.0.0/dist/ldld.min.css">
        <script src="https://cdn.jsdelivr.net/gh/loadingio/ldLoader@v1.0.0/dist/ldld.min.js"></script>

        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
        <title><?= Html::encode($this->title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <style>
        i.chat-count {
            width: 90px;
            height: 90px;
            /*line-height: 50px;*/
            background: white;
            margin: 0;
            border-radius: 40%;
            text-align: center;
            font-size: 12px;
            color: red;
            font-weight: bold;
        }
    </style>
    <div class="wrap">
        <?php
        NavBar::begin([
            'options' => [
                'class' => 'navbar-dark main-color navbar-expand-lg p-0',
            ],
        ]);
        if (Yii::$app->user->isGuest)
        {
            $menuItems[] = ['label' => 'Регистрация в ПС "Обучение"', 'url' => ['/site/signup'], 'options' => ['class' => 'btn btn-outline-light ml-4 mr-4 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Авторизация', 'url' => ['/site/login'], 'options' => ['class' => 'btn btn-outline-light ml-4 mr-4 mt-2 mb-2']];
            $logout = '';
        }

        elseif (Yii::$app->user->can('vospitatel') || Yii::$app->user->can('vospitatel_help'))
        {
            $menuItems = [
                ['label' => 'Общая информация', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                    'items' => [
                        ['label' => 'Личные данные', 'url' => ['/users/info'], 'options' => ['class' => '']],
                        ['label' => 'Данные по обучению', 'url' => ['/users/education'], 'options' => ['class' => '']],
                    ],
                ],
                ['label' => 'Входной тест', 'url' => ['questions-response/create'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Обучающие материалы', 'url' => ['trainings/education'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Самостоятельная работа', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                    'items' => [
                        ['label' => 'Общая информация', 'url' => ['/independent-work/general-info'], 'options' => ['class' => '']],
                        ['label' => 'Заполнение информации об учебном дне', 'url' => ['/independent-work/daily-routine'], 'options' => ['class' => '']],
                        ['label' => 'Информация о съеденной за день пище', 'url' => ['/menus-dishes/index-user'], 'options' => ['class' => '']],
                        ['label' => 'Отчёт по меню за день', 'url' => ['/menus-dishes/menus-days'], 'options' => ['class' => '']],
                        ['label' => 'Отчёт по самостоятельной работе', 'url' => ['/users/report'], 'options' => ['class' => '']],
                    ]
                ],
                ['label' => 'Итоговый тест', 'url' => ['questions-response/final'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Планируемое и фактическое обучение', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                    'items' => [
                        ['label' => 'Список групп, добавление групп', 'url' => ['/group/index'], 'options' => ['class' => '']],
                        ['label' => 'Планируемая информация  по группе', 'url' => ['/training-plan/index'], 'options' => ['class' => '']],
                        ['label' => 'Добавление ребенка в группу', 'url' => ['/kids-q/create'], 'options' => ['class' => '']],
                        ['label' => 'Список детей', 'url' => ['/kids-q/index'], 'options' => ['class' => '']],
                        //['label' => 'Отчет по внесенной информации в группе', 'url' => ['/training-actual/index2'], 'options' => ['class' => '']],
                    ]
                ],
                ['label' => 'ЗАДАТЬ ВОПРОС (ЧАТ)', 'url' => ['chat/index'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Видеоуроки', 'url' => ['/'], 'options' => ['class' => 'mr-3 p-2']],
            ];

            if (Yii::$app->user->identity->version == 1)
            {
                unset($menuItems[3]);
            }

            if (empty($report_tbl))
            {
                unset($menuItems[2]);
                unset($menuItems[3]);
                unset($menuItems[4]);
                unset($menuItems[5]);
            }
            elseif ($report_tbl->training_id == 1 && $report_tbl->independent_work == 1 && $report_tbl->theme1 && $report_tbl->theme2 && $report_tbl->theme3 && $report_tbl->theme4 && $report_tbl->theme5)
            {
                unset($menuItems[1]);
            }
            elseif ($report_tbl->training_id == 2 && $report_tbl->independent_work == 1 && $report_tbl->theme1 && $report_tbl->theme2 && $report_tbl->theme3 && $report_tbl->theme4 && $report_tbl->theme5 && $report_tbl->theme6)
            {
                unset($menuItems[1]);
            }
            elseif ($report_tbl->input_test >= 0)
            {
                unset($menuItems[1]);
                unset($menuItems[4]);
            }

            $logout = Html::begintag('div', ['class' => 'row'])
                . Html::begintag('div', ['class' => 'col-4'])
                . Html::endtag('div')
                . Html::tag('div', 'Пользователь:(' . Yii::$app->user->identity->name . ')', ['class' => 'col-6 text-right'])
                . Html::begintag('div', ['class' => 'col-2 text-right'])
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(' Выход', ['class' => 'btn main-button-2-outline logout'])
                . Html::endForm()
                . Html::endtag('div')
                . Html::endtag('div');
        }

        elseif (Yii::$app->user->can('director'))
        {
            $menuItems[] = ['label' => 'Отчёт по организации (школьный)', 'url' => ['/report/rep-director'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Отчёт по организации (дошкольный)', 'url' => ['/report/rep-director-p'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'ЗАДАТЬ ВОПРОС (ЧАТ)', 'url' => ['/chat/index'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Контроль', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                'items' => [
                    ['label' => 'Родительский контроль', 'url' => ['anket-parent-control/create'], 'options' => ['class' => '']],
                    ['label' => 'Внутренний контроль', 'url' => ['anket-parent-control/inside'], 'options' => ['class' => '']],
                    ['label' => 'Отчет по контролю', 'url' => ['anket-parent-control/report'], 'options' => ['class' => '']],
                ]
            ];
            //$menuItems[] = ['label' => 'Организация питания', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
            //    'items' => [
            //        //['label' => 'Архив меню', 'url' => ['menus/archive'], 'options' => ['class' => 'm-5']],
            //        ['label' => 'Настройки меню', 'url' => ['menus/index'], 'options' => ['class' => 'm-5']],
            //        ['label' => 'Разработка (редактирование) цикличного меню', 'url' => ['menus-dishes/index'], 'options' => ['class' => '']],
            //        ['label' => 'Просмотр действующего меню по дням', 'url' => ['menus-dishes/menus-days'], 'options' => ['class' => '']],
            //        ['label' => 'Просмотр действующего меню за период', 'url' => ['menus-dishes/menus-period'], 'options' => ['class' => '']],
            //        ///['label' => 'Корректировка фактического меню по дате', 'url' => ['menus-dishes/fact-day-index'], 'options' => ['class' => '']],
            //        //['label' => 'Фактическое меню за день', 'url' => ['menus-dishes/fact-date'], 'options' => ['class' => '']],
            //        ['label' => 'Отчет о повторяемости', 'url' => ['menus-dishes/repeat-report'], 'options' => ['class' => '']],
            //        ['label' => 'Отчет о перечне продуктов', 'url' => ['menus-dishes/products-list'], 'options' => ['class' => '']],
            //        //['label' => 'Отчет о перечне продуктов за указанный период', 'url' => ['menus-dishes/fact-date-products-list'], 'options' => ['class' => '']],
            //        ['label' => 'Прогнозная накопительная ведомость', 'url' => ['menus-dishes/prognos-storage'], 'options' => ['class' => '']],
            //        ///['label' => 'Фактическая накопительная ведомость', 'url' => ['menus-dishes/fact-storage'], 'options' => ['class' => '']],
            //        ///['label' => 'Фактическое меню за день', 'url' => ['menus-dishes/fact-day-index'], 'options' => ['class' => '']],
            //        ['label' => 'Меню раскладка за день', 'url' => ['menus-dishes/raskladka'], 'options' => ['class' => '']],
            //        ['label' => 'Меню раскладка за день по приемам пищи', 'url' => ['menus-dishes/raskladka-nutrition'], 'options' => ['class' => '']],
            //        ['label' => 'Проверка меню на аллергены', 'url' => ['/menus-dishes/show-allergen'], 'options' => ['class' => '']],
            //        ['label' => 'Проверка меню на хлебные единицы', 'url' => ['/menus-dishes/show-sahar'], 'options' => ['class' => '']],
            //        ///['label' => 'Технологические карты с учетом количества питающихся (пересчет)', 'url' => ['menus-dishes/techmup-page'], 'options' => ['class' => '']],
            //        ['label' => 'Формирование собственных сборников рецептур', 'url' => ['/settings/recipes-index'], 'options' => ['class' => '']],
            //        ['label' => 'Добавить блюдо в сборник', 'url' => ['/dishes'], 'options' => ['class' => '']],
            //        ['label' => 'База данных блюд', 'url' => ['dishes/dishes-base'], 'options' => ['class' => '']],
            //        ['label' => 'База данных продуктов', 'url' => ['products/products-base'], 'options' => ['class' => '']],
            //        //['label' => 'Формирование документов(печать меню)', 'url' => ['/menus-dishes/report-document'], 'options' => ['class' => '']],
            //        ['label' => 'Замена продуктов в меню', 'url' => ['/products-change-organization/index'], 'options' => ['class' => '']],
            //        ['label' => 'Отчет по витаминам и микроэлементам', 'url' => ['/menus-dishes/report-vitamin'], 'options' => ['class' => '']],
            //    ]
            //];
            $menuItems[] = ['label' => 'Видеоуроки', 'url' => ['/'], 'options' => ['class' => 'mr-3 p-2']];

            $logout = Html::begintag('div', ['class' => 'row'])
                . Html::begintag('div', ['class' => 'col-4'])
                . Html::endtag('div')
                . Html::tag('div', 'Пользователь:(' . Yii::$app->user->identity->name . ')', ['class' => 'col-6 text-right'])
                . Html::begintag('div', ['class' => 'col-2 text-right'])
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(' Выход', ['class' => 'btn main-button-2-outline logout'])
                . Html::endForm()
                . Html::endtag('div')
                . Html::endtag('div');
        }

        elseif (Yii::$app->user->can('RPN_mun'))
        {
            $menuItems[] = ['label' => 'Краткий отчёт по школьной программе', 'url' => ['/report/rep-education-dep'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Краткий отчёт по дошкольной программе', 'url' => ['/report/rep-education-dep-p'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Организации', 'url' => ['/organizations/searcho'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'ЗАДАТЬ ВОПРОС (ЧАТ)', 'url' => ['/chat/index'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Результаты проведения мероприятий внутр/род контроля', 'url' => ['/menus-dishes/report-school-little3'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Видеоуроки', 'url' => ['/'], 'options' => ['class' => 'mr-3 p-2']];
            $logout = Html::begintag('div', ['class' => 'row'])
                . Html::begintag('div', ['class' => 'col-4'])
                . Html::endtag('div')
                . Html::tag('div', 'Пользователь:(' . Yii::$app->user->identity->name . ')', ['class' => 'col-6 text-right'])
                . Html::begintag('div', ['class' => 'col-2 text-right'])
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(' Выход', ['class' => 'btn main-button-2-outline logout'])
                . Html::endForm()
                . Html::endtag('div')
                . Html::endtag('div');
        }

        elseif (Yii::$app->user->can('RPN'))
        {
            $menuItems[] = ['label' => 'Краткий отчёт по школьной программе', 'url' => ['/report/rep-rpn'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Краткий отчёт по дошкольной программе', 'url' => ['/report/rep-rpn-p'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Организации', 'url' => ['/organizations/searcho'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'ЗАДАТЬ ВОПРОС (ЧАТ)', 'url' => ['/chat/index'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Видеоуроки', 'url' => ['/'], 'options' => ['class' => 'mr-3 p-2']];
            $menuItems[] = ['label' => 'Результаты проведения мероприятий внутр/род контроля', 'url' => ['/menus-dishes/report-school-little3'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $logout = Html::begintag('div', ['class' => 'row'])
                . Html::begintag('div', ['class' => 'col-4'])
                . Html::endtag('div')
                . Html::tag('div', 'Пользователь:(' . Yii::$app->user->identity->name . ')', ['class' => 'col-6 text-right'])
                . Html::begintag('div', ['class' => 'col-2 text-right'])
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(' Выход', ['class' => 'btn main-button-2-outline logout'])
                . Html::endForm()
                . Html::endtag('div')
                . Html::endtag('div');
        }

        elseif (Yii::$app->user->can('admin'))
        {
            $menuItems[] = ['label' => 'Краткий отчёт по школьной программе', 'url' => ['/report/rep-adm'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Краткий отчёт по дошкольной программе', 'url' => ['/report/rep-adm-p'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = '<a href="/chat/index"><button class="btn btn-outline-light mr-3 mt-2 mb-2">Чат <i class="chat-count">'.$countQuestion.'</i></button></a>';
            //$menuItems[] = ['label' => 'ЗАДАТЬ ВОПРОС (ЧАТ)', 'url' => ['/chat/index'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2 chatCount']];
            $menuItems[] = ['label' => 'Поиск организаций', 'url' => ['/organizations/searcho'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Поиск пользователей', 'url' => ['/users/search'], 'options' => ['class' => 'btn btn-outline-light mr-3 mt-2 mb-2']];
            $menuItems[] = ['label' => 'Видеоуроки', 'url' => ['/'], 'options' => ['class' => 'mr-3 p-2']];
            $menuItems[] = ['label' => 'дубли', 'url' => ['/site/transferring-users-and-del-organization'], 'options' => ['class' => 'mr-3 p-2']];
            $menuItems[] = ['label' => 'Работа с блюдами и продуктами', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                'items' => [
                    ['label' => 'Добавление продуктов', 'url' => ['/products/index'], 'options' => ['class' => '']],
                    ['label' => 'Добавление блюд', 'url' => ['/dishes/index'], 'options' => ['class' => '']],
                ],
            ];
            $logout = Html::begintag('div', ['class' => 'row'])
                . Html::begintag('div', ['class' => 'col-4'])
                . Html::endtag('div')
                . Html::tag('div', 'Пользователь:(' . Yii::$app->user->identity->name . ')', ['class' => 'col-6 text-right'])
                . Html::begintag('div', ['class' => 'col-2 text-right'])
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(' Выход', ['class' => 'btn main-button-2-outline logout'])
                . Html::endForm()
                . Html::endtag('div')
                . Html::endtag('div');
        }

        else
        {
            $menuItems = [
                ['label' => 'Общая информация', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                    'items' => [
                        ['label' => 'Личные данные', 'url' => ['/users/info'], 'options' => ['class' => '']],
                        ['label' => 'Данные по обучению', 'url' => ['/users/education'], 'options' => ['class' => '']],
                    ],
                ],
                ['label' => 'Входной тест', 'url' => ['questions-response/create'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Обучающие материалы', 'url' => ['trainings/education'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Самостоятельная работа', 'url' => ['#'], 'options' => ['class' => 'mr-3 p-2'],
                    'items' => [
                        ['label' => 'Общая информация', 'url' => ['/independent-work/general-info'], 'options' => ['class' => '']],
                        ['label' => 'Заполнение информации об учебном дне', 'url' => ['/independent-work/daily-routine'], 'options' => ['class' => '']],
                        ['label' => 'Информация о съеденной за день пище', 'url' => ['/menus-dishes/index-user'], 'options' => ['class' => '']],
                        ['label' => 'Отчёт по меню за день', 'url' => ['/menus-dishes/menus-days'], 'options' => ['class' => '']],
                        ['label' => 'Отчёт по самостоятельной работе', 'url' => ['/users/report'], 'options' => ['class' => '']],
                    ]
                ],
                ['label' => 'Итоговый тест', 'url' => ['questions-response/final'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'ЗАДАТЬ ВОПРОС (ЧАТ)', 'url' => ['chat/index'], 'options' => ['class' => 'mr-3 p-2']],
                ['label' => 'Видеоуроки', 'url' => ['/'], 'options' => ['class' => 'mr-3 p-2']],
            ];

            if (Yii::$app->user->identity->version == 1)
            {
                unset($menuItems[3]);
            }

            if (empty($report_tbl))
            {
                unset($menuItems[2]);
                unset($menuItems[3]);
                unset($menuItems[4]);
            }
            elseif ($report_tbl->training_id == 1 && $report_tbl->independent_work == 1 && $report_tbl->theme1 && $report_tbl->theme2 && $report_tbl->theme3 && $report_tbl->theme4 && $report_tbl->theme5)
            {
                unset($menuItems[1]);
            }
            elseif ($report_tbl->training_id == 2 && $report_tbl->independent_work == 1 && $report_tbl->theme1 && $report_tbl->theme2 && $report_tbl->theme3 && $report_tbl->theme4 && $report_tbl->theme5 && $report_tbl->theme6)
            {
                unset($menuItems[1]);
            }
            elseif ($report_tbl->input_test >= 0)
            {
                unset($menuItems[1]);
                unset($menuItems[4]);
            }

            $logout = Html::begintag('div', ['class' => 'row'])
                . Html::begintag('div', ['class' => 'col-11 col-md-4'])
                . Html::endtag('div')
                . Html::tag('div', 'Пользователь:(' . Yii::$app->user->identity->name . ')', ['class' => 'col-11 col-md-6 text-right'])
                . Html::begintag('div', ['class' => 'col-11 col-md-2 text-right'])
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(' Выход', ['class' => 'btn main-button-2-outline logout'])
                . Html::endForm()
                . Html::endtag('div')
                . Html::endtag('div');
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left main-color'],
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>
        <div class="container-fluid mt-3">
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <?php echo Yii::$app->session->getFlash('success'); ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <?php echo Yii::$app->session->getFlash('error'); ?>
                </div>
            <?php endif; ?>

            <?= $logout ?>

<!--            <p class="text-center text-danger" style="font-size: 20px;"><b>Внимание! 19.11.2021 будут проводиться технические работы. Программа работать не будет в течение этого времени.</b></p>-->

            <?= $content ?>
        </div>
    </div>

    <footer class="footer main-color">
        <p class="text-light ml-3 font-weight-bold"><?=date('Y') ?> год</p>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>