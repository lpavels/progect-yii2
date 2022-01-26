<?php

namespace backend\controllers;

use common\models\ChangePersonalData;
use Yii;
use common\models\Chat;
use yii\web\Controller;
use yii\filters\VerbFilter;

class ChatController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex($id = false)
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model = new Chat();
        if (Yii::$app->user->can('admin'))
        {
            # $countQuestion = Chat::find()->select(['chat.sender_user_id'])->where(['status' => 0])
            #     ->andWhere(['!=','chat.sender_user_id',1])->groupBy(['chat.sender_user_id'])->count(); #Кол-во неотвеченных вопросов

            $dates = date('Y-m-d H:m:s', strtotime('-10 days')); //список чатов за последние 1 месяца

            if ($id == false)
            {
                $show = false;

                #Список пользователей
                $sender_users = Chat::find()
                    ->select(['chat.sender_user_id', 'chat.status', 'chat.created_at', 'user.name'])
                    ->innerJoin('user', 'user.id=chat.sender_user_id')
                    ->where(['>', 'chat.created_at', $dates])
                    ->andWhere(['!=', 'chat.sender_user_id', 1])
                    ->orderBy(['chat.status' => SORT_DESC])
                    /*->groupBy(['chat.sender_user_id'])*/
                    ->asArray()
                    ->all();

                $sender_user_ids = [];
                foreach ($sender_users as $sender_user)
                {
                    if ($sender_user['status'] == 0)
                    {
                        $sender_user_ids['new'][$sender_user['sender_user_id']] = $sender_user['sender_user_id'] . '|' . $sender_user['name'] . '|new|' . $sender_user['created_at'];
                    }
                    else
                    {
                        $sender_user_ids['nonew'][$sender_user['sender_user_id']] = $sender_user['sender_user_id'] . '|' . $sender_user['name'] . '||' . $sender_user['created_at'];
                    }
                }

                $messages = false;
            }
            else
            {
                $show = true;

                Chat::updateAll(['status' => 1], ['sender_user_id' => $id]);
                #Список пользователей
                $sender_users = Chat::find()
                    ->select(['chat.sender_user_id', 'chat.status', 'chat.created_at', 'user.name'])
                    ->innerJoin('user', 'user.id=chat.sender_user_id')
                    ->where(['>', 'chat.created_at', $dates])
                    ->andWhere(['!=', 'chat.sender_user_id', 1])
                    ->orderBy(['chat.status' => SORT_DESC])
                    /*->groupBy(['chat.sender_user_id'])*/
                    ->asArray()
                    ->all();

                $sender_user_ids = [];
                foreach ($sender_users as $sender_user)
                {
                    if ($sender_user['status'] == 0)
                    {
                        $sender_user_ids['new'][$sender_user['sender_user_id']] = $sender_user['sender_user_id'] . '|' . $sender_user['name'] . '|new|' . $sender_user['created_at'];
                    }
                    else
                    {
                        $sender_user_ids['nonew'][$sender_user['sender_user_id']] = $sender_user['sender_user_id'] . '|' . $sender_user['name'] . '||' . $sender_user['created_at'];
                    }
                }

                #Сообщения от пользователя
                $messages_user = Chat::find()
                    ->select(
                        [
                            'chat.id as chat_id',
                            'chat.sender_user_id',
                            'chat.receiver_user_id',
                            'chat.message',
                            'chat.status',
                            'chat.created_at',
                            'user.type_listener',
                            'user.training_id',
                            'user.name as u_name',
                            'user.key_login as u_login',
                            'organization.id as org_id',
                            'organization.title as org_title',

                            'report_tbl21.input_test as input_test',
                            'report_tbl21.theme1 as theme1',
                            'report_tbl21.theme2 as theme2',
                            'report_tbl21.theme3 as theme3',
                            'report_tbl21.theme4 as theme4',
                            'report_tbl21.theme5 as theme5',
                            'report_tbl21.theme6 as theme6',
                            'report_tbl21.independent_work as independent_work',
                            'report_tbl21.final_test as final_test',
                            'report_tbl21.number_children as number_children',
                            'report_tbl21.training_completed as training_completed',
                        ]
                    )
                    ->leftJoin('user', 'user.id=chat.sender_user_id')
                    ->leftJoin('organization', 'organization.id=user.organization_id')
                    ->leftJoin('report_tbl21', 'report_tbl21.user_id=user.id')
                    ->where(['chat.sender_user_id' => $id])
                    ->orWhere(['chat.receiver_user_id' => $id])
                    ->asArray()
                    ->all();

                foreach ($messages_user as $m_user)
                {
                    $messages[] = $m_user['sender_user_id'] . '|' . $m_user['receiver_user_id'] . '|' . $m_user['message'] . '|' . $m_user['status'] . '|' . $m_user['created_at'] . '|' . $m_user['title'] . '|' . $m_user['chat_id'];
                }

                $org = $messages_user[0]['org_title'] . ' (' . $messages_user[0]['org_id'] . ')';
                $u_name = $messages_user[0]['u_name'] . ' (' . $messages_user[0]['u_login'] . ')' . ' (' . $id . ')';
                $u_type = ($messages_user[0]['training_id'] == 1) ? $messages_user[0]['type_listener'] . '|школьная' : $messages_user[0]['type_listener'] . '|дошкольная';

                $training = [
                    $messages_user[0]['input_test'],
                    $messages_user[0]['theme1'],
                    $messages_user[0]['theme2'],
                    $messages_user[0]['theme3'],
                    $messages_user[0]['theme4'],
                    $messages_user[0]['theme5'],
                    $messages_user[0]['theme6'],
                    $messages_user[0]['independent_work'],
                    $messages_user[0]['final_test'],
                    $messages_user[0]['number_children'],
                    $messages_user[0]['training_completed']
                ];

                $changeNameHistorys = ChangePersonalData::findAll(['user_id' => $id]);
                foreach ($changeNameHistorys as $changeNameHistory)
                {
                    $nameHistorys[] = $changeNameHistory->name_change . '->' . $changeNameHistory->changed_to . ' | ' . $changeNameHistory->change_user;
                }
            }

            return $this->render(
                'index-admin',
                [
                    'model' => $model,
                    'sender_user_ids' => $sender_user_ids,
                    'show' => $show,
                    'messages' => $messages,
                    'u_id' => $id,
                    'org' => $org,
                    'u_name' => $u_name,
                    'u_type' => $u_type,
                    'training' => $training,
                    'nameHistorys' => $nameHistorys
                ]
            );
        }
        else
        {
            $u_id = Yii::$app->user->id;
            Chat::updateAll(['status' => 1], ['receiver_user_id' => $u_id]);
            #Сообщения от пользователя
            $messages_user = Chat::find()
                ->select(
                    [
                        'chat.sender_user_id',
                        'chat.receiver_user_id',
                        'chat.message',
                        'chat.status',
                        'chat.created_at',
                        'user.name'
                    ]
                )
                ->leftJoin('user', 'user.id=chat.sender_user_id')
                //->groupBy(['chat.sender_user_id'])
                ->where(['sender_user_id' => $u_id])
                ->orWhere(['receiver_user_id' => $u_id])
                ->asArray()
                ->all();
            foreach ($messages_user as $m_user)
            {
                $messages[] = $m_user['sender_user_id'] . '|' . $m_user['receiver_user_id'] . '|' . $m_user['message'] . '|' . $m_user['status'] . '|' . $m_user['created_at'] . '|' . $m_user['created_at'];
            }

            return $this->render(
                'index',
                [
                    'model' => $model,
                    'messages' => $messages,
                ]
            );
        }
    }

    public function actionCreate()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Chat'];

            $model = new Chat();
            if (Yii::$app->user->can('admin'))
            {
                $model->sender_user_id = Yii::$app->user->id;
                $model->receiver_user_id = $post['receiver_user_id'];
                $model->message = 'Здравствуйте. <br>' . $post['message'];
                $model->ip = Yii::$app->userHelp->ip();
                $model->save(false);
                return $this->redirect(['index', 'id' => $post['receiver_user_id']]);
            }
            else
            {
                $model->sender_user_id = Yii::$app->user->id;
                $model->receiver_user_id = 1;
                $model->message = $post['message'];
                $model->ip = Yii::$app->userHelp->ip();
                if ($model->save(false))
                {
                    #Новый ответ - новый массив. при добавлении массива, добавить в массив ответов строку с таким же номером ключа.
                    #Добавить $arr с номером массива, скопировать for изменив $i, $arr_message
                    #Добавить if изменив $arr и $arr_answer на номер массива
                    #Если нужно добавить ключевые слова - добавлять в массив. Ищется только точное повторение фразы
                    #массивы вопросов

                    /*Как скачать сертификат*/
                    $arr_message1 = [
                        'выдать сертификат',
                        'взять сертификат',
                        'где мой сертификат',
                        'найти сертификат',
                        'не выходит сертификат',
                        'посмотреть сертификат',
                        'получить сертификат',
                        'распечатать сертификат',
                        'сделать сертификат',
                        'скачать сертификат',
                        'сертификат найти',
                        'сертификата нет',
                    ];

                    /*Где найти идентификационный номер*/
                    $arr_message2 = [
                        'как узнать свой пароль',
                        'забыла свой ключ',
                        'забыл свой ключ',
                        'забыла идентификационный номер',
                        'забыла код индификации',
                        'забыл идентификационный номер',
                    ];

                    /*Как выполнить самостоятельную*/
                    $arr_message3 = [
                        'найти самостоятельную работу',
                        'пройти самостоятельную работу',
                    ];

                    /*Как пройти/не отображается итоговый тест*/
                    $arr_message4 = [
                        'где итоговый тест',
                        'вкладка итоговый тест',
                        'как пройти итоговый тест',
                        'не выходит итоговый тест',
                    ];

                    /*Как пройти итоговый тест повторно*/
                    $arr_message5 = [
                        'пройти итоговый тест',
                        'пройти итоговую работу',
                        'проходить итоговый тест',
                        'пересдать итоговый тест',
                    ];

                    /*Изменить программу обучения*/
                    $arr_message6 = [
                        #'',
                    ];

                    /*Основные задачи программы*/
                    $arr_message7 = [
                        #'',
                    ];

                    /*Как внести детей*/
                    $arr_message8 = [
                        #'',
                    ];

                    /*Где личный кабинет*/
                    $arr_message9 = [
                        'зайти в личный кабинет',
                        'где находится личный кабинет',
                    ];

                    /*$arr_message10 = [
                        #'',
                    ];*/

                    $arr_answer = [
                        1 => 'Для скачивания сертификата нажмите на раздел "Общая информация" и выберите пункт "Данные по обучению" и нажмите на кнопку "Сертификат в PDF формате".
                            <br>Скачать вы его сможете при условии, что у Вас пройдено: 1)входной тест; 2)все темы отмечены изученными; 3)выполнена самостоятельная работа; 4)пройден итоговый тест (70% или более).
                            <br> будет отображена кнопка "Сертификат в PDF формате".
                            <br>Если при скачивании ошибка, напишите свою электронную почту, отправим сертификат на неё.',
                        2 => 'Если вы видите раздел "Общая информация", то необходимо нажать на данный раздел и выбрать пункт "Личные данные" и, лучше всего, нажать "Сохранить данные в PDF формате".
                            <br>Если вы зарегистриролись повторно, но уже все проходили, то необходимо написать  на электронную почту edu@niig.su следующие данные: 1. ФИО, 2. год рождения, 3. год регистрации.',
                        3 => 'Если Вы зарегистрировались 14.07.2021 и позже, то у Вас отображается раздел "Самостоятельная работа" в котором и необходимо её выполнять. Переходить по ссылкам дополнительно не нужно. Видеоурок по выполнению самостоятельной работы можно посмотреть по <a href="https://youtu.be/SPhpKN4w74o?t=28" target="_blank">ссылке (нажмите)</a>.
                            <br> Если Вы были зарегистрированы до 14.07.2021, то для прохождения самостоятельной работы перейдите по <a target="_blank" href="https://individ.demography.site/">ссылке (нажмите)</a>. Видеоурок по выполнению самостоятельной работы можно посмотреть по <a href="https://youtu.be/uWQXD855PYM" target="_blank">ссылке (нажмите)</a>.',
                        4 => 'Нажмите на раздел "Общая информация" и выберите пункт "Данные по обучению". У Вас должны отображаться пройденными все разделы (входной тест, обучающие материалы и самостоятельная работа).
                            <br>Если все выполнено - будет отображен раздел "Итоговый тест" после выполнения которого на 70% или более вы сможете скачать сертификат.',
                        5 => 'Итоговый тест возможно пройти неограниченное количество раз с интервалом в 1 час и только если не было набрано достаточное количество правильных ответов (70%).
                            <br>Входной тест пересдать не возможно.',
                        6 => 'При регистрации предлагается еще раз проверить введённую информацию, после чего согласиться с тем, что вся информация корректна.
                            <br>Вы можете зарегистрироваться повторно указав верные данные.',
                        7 => 'Для прохождения Вам необходимо зайти в раздел «Обучающие материалы» - «Основные задачи программы» и внизу нажать кнопку подтвердить прохождение материала.',
                        8 => 'Программа обучения предназначена для сотрудников дошкольных и школьных учреждений, рекомендуем Вам указать данные ребенка из Вашей группы детского сада или школьного класса или в крайнем случае указать данные вымышленного ребенка, и указать чем бы Вы его кормили в течении дня.',
                        9 => 'Вы входите в личный кабинет после того, как вводите свой идентификационный номер и нажимаете кнопку входа. Уточните вопрос.',
                    ]; //массив ответов

                    $arrsCount = [];
                    for ($i1 = 0; $i1 < count($arr_message1); $i1++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message1[$i1]))
                            $arrsCount['1']++;
                    }
                    for ($i2 = 0; $i2 < count($arr_message2); $i2++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message2[$i2]))
                            $arrsCount['2']++;
                    }
                    for ($i3 = 0; $i3 < count($arr_message3); $i3++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message3[$i3]))
                            $arrsCount['3']++;
                    }
                    for ($i4 = 0; $i4 < count($arr_message4); $i4++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message4[$i4]))
                            $arrsCount['4']++;
                    }
                    for ($i5 = 0; $i5 < count($arr_message5); $i5++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message5[$i5]))
                            $arrsCount['5']++;
                    }
                    for ($i6 = 0; $i6 < count($arr_message6); $i6++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message6[$i6]))
                            $arrsCount['6']++;
                    }
                    for ($i7 = 0; $i7 < count($arr_message7); $i7++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message7[$i7]))
                            $arrsCount['7']++;
                    }
                    for ($i8 = 0; $i8 < count($arr_message8); $i8++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message8[$i8]))
                            $arrsCount['8']++;
                    }
                    for ($i9 = 0; $i9 < count($arr_message9); $i9++)
                    {
                        if (substr_count(mb_strtolower($post['message']), $arr_message9[$i9]))
                            $arrsCount['9']++;
                    }

                    for ($count = 1; $count <= 9; $count++)
                    {
                        if ($arrsCount[$count] > 0)
                        {
                            $model_answer = new Chat();
                            $model_answer->sender_user_id = 1;
                            $model_answer->receiver_user_id = Yii::$app->user->id;
                            $model_answer->message = $arr_answer[$count];
                            $model_answer->ip = 'auto answer';
                            $model_answer->save(false);
                        }
                    }
                }
            }
        }
        return $this->redirect(['index']);
    }

    public function actionNotRead($id)
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }
        $model = Chat::findOne($id);
        $model->status = 0;
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Вопрос №' . $id . ' отмечен не прочитанным.');
        return $this->redirect(['index']);
    }

    public function actionCreateAdm($id, $status)
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }

        if (!empty($id) && !empty($status))
        {
            $text = [
                1 => 'Здравствуйте.
                        <br>Для скачивания сертификата нажмите на раздел "Общая информация" и выберите пункт "Данные по обучению" и нажмите на кнопку "Сертификат в PDF формате".
                        <br>Скачать вы его сможете при условии, что у Вас пройдено: 1)входной тест; 2)все темы отмечены изученными; 3)выполнена самостоятельная работа; 4)пройден итоговый тест (70% или более).
                        <br> будет отображена кнопка "Сертификат в PDF формате".
                        <br>Если при скачивании ошибка, напишите свою электронную почту, отправим сертификат на неё.',
                2 => 'Здравствуйте.
                        <br>Если вы видите раздел "Общая информация", то необходимо нажать на данный раздел и выбрать пункт "Личные данные" и, лучше всего, нажать "Сохранить данные в PDF формате".
                        <br>Если вы зарегистриролись повторно, но уже все проходили, то необходимо написать  на электронную почту edu@niig.su следующие данные: 1. ФИО, 2. год рождения, 3. год регистрации.',
                3 => 'Здравствуйте.
                        <br>Если Вы зарегистрировались 14.07.2021 и позже, то у Вас отображается раздел "Самостоятельная работа" в котором и необходимо её выполнять. Переходить по ссылкам дополнительно не нужно. Видеоурок по выполнению самостоятельной работы можно посмотреть по <a href="https://youtu.be/SPhpKN4w74o?t=28" target="_blank">ссылке (нажмите)</a>.
                        <br> Если Вы были зарегистрированы до 14.07.2021, то для прохождения самостоятельной работы перейдите по <a target="_blank" href="https://individ.demography.site/">ссылке (нажмите)</a>. Видеоурок по выполнению самостоятельной работы можно посмотреть по <a href="https://youtu.be/uWQXD855PYM" target="_blank">ссылке (нажмите)</a>.',
                4 => 'Здравствуйте.
                        <br>Нажмите на раздел "Общая информация" и выберите пункт "Данные по обучению". У Вас должны отображаться пройденными все разделы (входной тест, обучающие материалы и самостоятельная работа).
                        <br>Если все выполнено - будет отображен раздел "Итоговый тест" после выполнения которого на 70% или более вы сможете скачать сертификат.',
                5 => 'Здравствуйте.
                        <br>Итоговый тест возможно пройти неограниченное количество раз с интервалом в 1 час и только если не было набрано достаточное количество правильных ответов (70%).
                        <br>Входной тест пересдать не возможно.',
                6 => 'Здравствуйте.
                        <br>При регистрации предлагается еще раз проверить введённую информацию, после чего согласиться с тем, что вся информация корректна.
                        <br>Вы можете зарегистрироваться повторно указав верные данные.',
                7 => 'Здравствуйте.
                        <br>Для прохождения Вам необходимо зайти в раздел «Обучающие материалы» - «Основные задачи программы» и внизу нажать кнопку подтвердить прохождение материала.',
                8 => 'Здравствуйте.
                        <br>Программа обучения предназначена для сотрудников дошкольных и школьных учреждений, рекомендуем Вам указать данные ребенка из Вашей группы детского сада или школьного класса или в крайнем случае указать данные вымышленного ребенка, и указать чем бы Вы его кормили в течении дня.',
                9 => 'Здравствуйте.
                        <br>Вы входите в личный кабинет после того, как вводите свой идентификационный номер и нажимаете кнопку входа. Уточните вопрос.',


                98 => 'Видеоурок по прохождению входного тестирования и доступу к обучающим материалам можно посмотреть <a target="_blank" href="https://youtu.be/AKL9y95cumo">по ссылке (нажмите)</a>.',
                99 => 'Видеоурок по выполнению самостоятельной работы (для зарегистрированных с 14.07.2021), прохождению итогового теста и скачиванию сертификата можно посмотреть <a target="_blank" href="https://youtu.be/SPhpKN4w74o">по ссылке (нажмите)</a>.'
            ];

            $model = new Chat();
            $model->sender_user_id = Yii::$app->user->id;
            $model->receiver_user_id = $id;
            $model->message = $text[$status];
            $model->ip = Yii::$app->userHelp->ip();
            $model->save(false);
            return $this->redirect(['index', 'id' => $id]);
        }
        return $this->redirect(['index']);
    }

}
