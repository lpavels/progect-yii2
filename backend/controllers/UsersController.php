<?php

namespace backend\controllers;

use common\models\ChangePersonalData;
use common\models\DailyRoutine;
use common\models\Kids;
use common\models\Menus;
use common\models\MenusDishes;
use common\models\MenusNutrition;
use common\models\Municipality;
use common\models\NutritionInfo;
use common\models\Organization;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use common\models\SportsSectionName;
use common\models\TrainingProgram;
use common\models\UsersSearch;
use DateTime;
use Yii;
use Mpdf\Mpdf;
use common\models\FederalDistrict;
use common\models\Region;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class UsersController extends Controller
{
    const YAER_2021 = 2021;
    const YAER_2022 = 2022;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->can('admin'))
        {
            $dataProvider = new ActiveDataProvider(
                [
                    'query' => User::find(),
                    'pagination' => [
                        'pageSize' => 12,
                    ],
                ]
            );

            return $this->render(
                'index',
                [
                    'dataProvider' => $dataProvider,
                ]
            );
        }
        else
        {
            return $this->goHome();
        }
    }

    public function actionSearch()
    {
        if (Yii::$app->user->can('admin'))
        {
            $model = new UsersSearch();
            $dataProvider = new ActiveDataProvider(
                [
                    'query' => User::find()->where(['!=', 'id', 1]),
                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]
            );

            return $this->render(
                'search',
                [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                ]
            );
        }
        else
        {
            return $this->goHome();
        }
    }

    public function actionInfo()
    {
        $model = User::findOne(Yii::$app->user->id);

        if (Yii::$app->request->post())
        {
            if (ChangePersonalData::find()->select(['user_id'])->where(['user_id' => Yii::$app->user->id])->count() > 0)
            {
                Yii::$app->session->setFlash('error', "Ошибка. Отредактировать ФИО можно один раз.");
                return $this->render(
                    'info',
                    [
                        '$model' => $model,
                    ]
                );
            }
            else
            {
                if (Yii::$app->request->post()['User']['name'] == '')
                {
                    Yii::$app->session->setFlash('error', "ФИО не может быть пустым.");
                    return $this->redirect('info');
                }
                $change = new ChangePersonalData();
                $change->user_id = Yii::$app->user->id;
                $change->name_change = Yii::$app->user->identity->name;
                $change->changed_to = Yii::$app->request->post()['User']['name'];
                $change->change_user = 'user';

                if ($change->save())
                {
                    $model->name = Yii::$app->request->post()['User']['name'];
                    $model->save(false);

                    $ReportTbl21 = ReportTbl21::findOne(['user_id' => Yii::$app->user->id]);
                    if ($ReportTbl21)
                    {
                        $ReportTbl21->name = Yii::$app->request->post()['User']['name'];
                        $ReportTbl21->change_name = 1;
                        $ReportTbl21->save(false);
                    }
                    $ReportTbl22 = ReportTbl22::findOne(['user_id' => Yii::$app->user->id]);
                    if ($ReportTbl22)
                    {
                        $ReportTbl22->name = Yii::$app->request->post()['User']['name'];
                        $ReportTbl22->change_name = 1;
                        $ReportTbl22->save(false);
                    }

                    Yii::$app->session->setFlash('success', "ФИО изменено, повторное измение невозможно.");
                    return $this->redirect('info');
                }
            }
        }

        return $this->render(
            'info',
            [
                '$model' => $model,
            ]
        );
    }

    public function actionChangeNameAdmin($id)
    {
        if (Yii::$app->user->id != 1)
        {
            return $this->goHome();
        }

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Chat'];

            $model = User::findOne($id);
            $change = new ChangePersonalData();
            $change->user_id = $id;
            $change->name_change = $model->name;
            $change->changed_to = $post['fio'];
            $change->change_user = 'admin';

            if ($change->save(false))
            {
                $model->name = $post['fio'];
                $model->save(false);

                $ReportTbl21 = ReportTbl21::findOne(['user_id' => $id]);
                if ($ReportTbl21)
                {
                    $ReportTbl21->name = $post['fio'];
                    $ReportTbl21->save(false);
                }
                $ReportTbl22 = ReportTbl22::findOne(['user_id' => $id]);
                if ($ReportTbl22)
                {
                    $ReportTbl22->name = $post['fio'];
                    $ReportTbl22->save(false);
                }

                Yii::$app->session->setFlash('success', "ФИО изменено.");
                return $this->redirect(Yii::$app->request->referrer);
                //return $this->redirect(['/chat/index', 'id' => $id]);
            }
        }

        Yii::$app->session->setFlash('error', "ФИО не изменено");
        return $this->redirect(Yii::$app->request->referrer);
        //return $this->redirect(['/chat/index', 'id' => $id]);
    }

    public function actionEducation()
    {
        $model = new User();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['User']['form_training_id'];
            $model = User::findOne(Yii::$app->user->id);
            $model->form_training_id = $post;
            $model->save();

            Yii::$app->session->setFlash('success', "Информация о форме обучения была сохранена");
            return $this->redirect(['education']);
        }

        return $this->render(
            'education',
            [
                'model' => $model,
            ]
        );
    }

    public function actionExport($id)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        /*$id_u = MenusNutrition::find()->where(['menu_id' => $id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ*/
        $queru = User::find()->where(['key_login' => Yii::$app->user->identity->key_login])->one();
        $training = TrainingProgram::find()->where(['id' => $queru->training_id])->one()->name;
        //$form = \common\models\FormTraining::find()->where(['user_id' => $queru->id])->one();
        $organization = Organization::find()->where(['id' => $queru->organization_id])->one();
        $District = FederalDistrict::find()->where(['id' => $organization->federal_district_id])->one();
        $Region = Region::find()->where(['id' => $organization->region_id])->one();
        $Municipality = Municipality::find()->where(['id' => $organization->municipality_id])->one();
        $rest = substr($queru->created_at, 0, 10);

        $html = '
            <h3 align="center">Личные данные:</h3>
            <p><b>Уникальный номер:</b> ' . $queru->key_login . '</p>
            <p><b>Программа обучения:</b> ' . $training . '</p>
            <p><b>ФИО:</b> ' . $queru->name . ', ' . $queru->year_birth . ' года рождения</p>
            <p><b>Организация:</b> ' . $organization->title . '</p>
            <p><b>Федеральный округ:</b> ' . $District->name . '</p>
            <p><b>Регион:</b> ' . $Region->name . '</p>
            <p><b>Муниципальное образование:</b> ' . $Municipality->name . '</p>
            <p><b>Тип слушателя:</b> ' . $queru->type_listener . '</p>
            <p><b>Дата регистрации:</b> ' . date("d.m.Y", $rest) . '</p>';

        $mpdf = new Mpdf (
            ['margin_left' => '15', 'margin_right' => '5', 'margin_top' => '10', 'margin_bottom' => '5']
        );;
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter(
            '<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'
        ); //номер страницы {PAGENO}
        $mpdf->Output('ПС "Основы здорового питания".pdf', 'D'); //D - скачает файл!
    }

    public function actionCertificatetarget($id)
    {
        if (Yii::$app->user->id == 1)
        {
            $ReportTbl = ReportTbl21::findOne(['user_id' => $id])->training_completed;
            if ($ReportTbl == 1)
            {
                $today = 2021;
            }
            else
            {
                $ReportTbl = ReportTbl22::findOne(['user_id' => $id])->training_completed;
                if ($ReportTbl == 1)
                {
                    $today = 2022;
                }
            }
            goto download;
        }
        if ($id != Yii::$app->user->id)
        {
            Yii::$app->session->setFlash(
                'error',
                "Произошла ошибка при скачивании сертификата. Напишите в чат о данной ошибке."
            );
            return $this->goHome();
        }

        $ReportTbl = ReportTbl21::findOne(['user_id' => $id])->training_completed;
        if ($ReportTbl == 1)
        {
            $today = 2021;
        }
        else
        {
            $ReportTbl = ReportTbl22::findOne(['user_id' => $id])->training_completed;
            if ($ReportTbl == 1)
            {
                $today = 2022;
            }
        }

        if ($ReportTbl != 1)
        {
            Yii::$app->session->setFlash(
                'error',
                "Произошла ошибка при скачивании сертификата (2). Напишите на почту edu@niig.su"
            );
            return $this->goHome();
        }

        download:
        require_once __DIR__ . '/../../vendor/autoload.php';
        $queru = User::findOne($id);
        $training = TrainingProgram::find()->where(['id' => $queru->training_id])->one();
        $html = '
           <br>
           <div align="center" style="width: 850px; margin-left: 82px; margin-top: 285px; margin-bottom: 0px;">
                <div align="center" style="font-size: 18px; margin-top: 20px;">№ ' . $queru->key_login . '</div>
               </div>
               <div align="center" style="text-decoration: none;font-size: 38px; font-weight: 500px; margin-top: 10px;">' . $queru->name . '</div>
               <div align="center" style="font-size: 24px; margin-top: 10px;">прошел(а) обучение по санитарно-просветительской программе</div>
               <div align="center" style="width: 850px; margin-left: 82px; margin-bottom: 0px; margin-top: 5px;">
               <div align="center" style="font-weight: normal; text-decoration: none;font-size: 24px;margin-top: 0px;" >«' . mb_strtoupper(
                $training->name
            ) . '»</div>
               <div align="center" style="font-size: 14px; margin-top: 5px;">В объеме 15 часов</div>
               
               <div align="center" style="font-size: 15px;font-weight: bold; margin-top: 113px; color: #725745">НОВОСИБИРСК, ' . $today . ' г.</div>
           </div>
       ';

        $mpdf = new Mpdf (['format' => 'A4-L']);
        $mpdf->SetDefaultBodyCSS('background', "url('../web/images/certificate_2207.jpg')");
        $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
        $mpdf->WriteHTML($html);

        $name = 'Сертификат ' . $queru->name;
        if (Yii::$app->user->id == 1)
        {
            $mpdf->Output($name . '.pdf', 'D'); //D - скачает файл!
        }
        else
        {
            $mpdf->Output($name . '.pdf', 'I'); //D - скачает файл!
        }
    }

    public function actionCertificate2() #JS скачивание сертификата
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post()['id'];
        //это мы проверяем id
        if ($id != Yii::$app->user->id)
        {
            Yii::$app->session->setFlash(
                'error',
                "Произошла ошибка при скачивании сертификата. Напишите на почту edu@niig.su"
            );
            return $this->goHome();
        }

        $ReportTbl = ReportTbl21::findOne(['user_id' => $id])->training_completed;
        if ($ReportTbl == 1)
        {
            $today = 2021;
        }
        else
        {
            $ReportTbl = ReportTbl22::findOne(['user_id' => $id])->training_completed;
            if ($ReportTbl == 1)
            {
                $today = 2022;
            }
        }

        if ($ReportTbl != 1)
        {
            Yii::$app->session->setFlash(
                'error',
                "Произошла ошибка при скачивании сертификата (2). Напишите на почту edu@niig.su"
            );
            return $this->goHome();
        }

        $queru = User::findOne($id);
        $training = TrainingProgram::find()->where(['id' => $queru->training_id])->one();
        //$today = date("d.m.Y");
        $result = [
            'key_login' => $queru->key_login,
            'user_name' => $queru->name,
            'training_name' => mb_strtoupper(
                $training->name
            ),
            'today' => $today,
        ];

        return $result;
    }

    public function actionReport()
    {
        /*Двигательная активность*/
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can(
                'RPN'
            ) || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу отчёта за день запрещён");
            return $this->goHome();
        }
        if (!empty(DailyRoutine::find()->where(['user_id' => Yii::$app->user->id])->count()))
        {
            $daily = 1;
        }

        $u_id = Yii::$app->user->id;
        $model = DailyRoutine::find()->where(['user_id' => $u_id])->one();
        $model2 = Kids::find()->where(['user_id' => $u_id])->one();

        if (empty($model2)) //проверка на заполнение информации о ребёнка для исключения ошибки на странице
        {
            Yii::$app->session->setFlash(
                'error',
                "Для просмотра отчета по самостоятельной работе необходимо запонить общую информацию, информацию об учебном дне и о съеденной за день пище."
            );
            return $this->goHome();
        }
        $queteletIndexVal = $model2['mass'] / (pow($model2['height'] / 100, 2)); // ИМТ (число)

        /* Физическое развитие */
        $imt_arrayVal = [
            'отмечается дефицит массы тела',
            'гармоничное', // Нормальный вес
            'отмечается избыток массы тела',
            'отмечается ожирение'
        ];
        $imt_boys = [
            0 => [14.5, 18.1, 18.5],
            1 => [14.5, 18.1, 18.5],
            2 => [14.5, 18.1, 18.5],
            3 => [14, 17.5, 17.8],
            4 => [13.9, 17, 17.3],
            5 => [13.8, 16.9, 17.2],
            6 => [13.5, 17, 17.5],
            7 => [13.6, 17.5, 18],
            8 => [13.8, 18, 18.5],
            9 => [13.9, 18.5, 19.5],
            10 => [14, 19.2, 20.4],
            11 => [14.3, 20, 21.3],
            12 => [14.7, 21, 22.1],
            13 => [15.1, 21.8, 23],
            14 => [15.6, 22.5, 23.9],
            15 => [16.3, 23.5, 24.7],
            16 => [16.9, 24, 25.4],
            17 => [17.3, 25, 26.1],
            18 => [17.9, 25.6, 26.9],
            19 => [18.2, 26.2, 27.8],
            20 => [18.6, 27, 28.4],
            21 => [18.6, 27, 28.4],
            22 => [18.6, 27, 28.4],
            23 => [18.6, 27, 28.4],
            24 => [18.6, 27, 28.4],
            25 => [18.6, 27, 28.4],
            26 => [18.6, 27, 28.4]
        ];
        $imt_girls = [
            0 => [14, 18, 18.2],
            1 => [14, 18, 18.2],
            2 => [14, 18, 18.2],
            3 => [13.8, 17.2, 17.4],
            4 => [13.5, 16.8, 17.1],
            5 => [13.3, 16.9, 17.2],
            6 => [13.2, 17, 17.3],
            7 => [13.2, 17.9, 18.4],
            8 => [13.2, 18.5, 18.7],
            9 => [13.5, 19, 19.6],
            10 => [13.9, 20, 21],
            11 => [14, 21, 22],
            12 => [14.5, 21.6, 23],
            13 => [15, 22.5, 24],
            14 => [15.5, 23.5, 24.8],
            15 => [16, 24, 25.5],
            16 => [16.5, 24.8, 26],
            17 => [16.9, 25.1, 26.8],
            18 => [17, 25.8, 27.3],
            19 => [17.2, 25.8, 27.8],
            20 => [17.5, 25.5, 28.2],
            21 => [17.5, 25.5, 28.2],
            22 => [17.5, 25.5, 28.2],
            23 => [17.5, 25.5, 28.2],
            24 => [17.5, 25.5, 28.2],
            25 => [17.5, 25.5, 28.2],
            26 => [17.5, 25.5, 28.2]
        ];
        $recommended_value_boys = [
            0 => [110, 59.4, 8.9, 41.7],
            1 => [110, 59.4, 8.9, 41.7],
            2 => [88.2, 47.6, 7.1, 33.4],
            3 => [88.9, 48.0, 7.2, 33.7],
            4 => [96.0, 51.9, 7.8, 36.4],
            5 => [90.5, 48.8, 7.3, 34.3],
            6 => [85.2, 46.0, 6.9, 32.3],
            7 => [80.5, 43.5, 6.5, 30.5],
            8 => [74.1, 40.0, 6.0, 28.1],
            9 => [69.9, 37.8, 5.7, 26.5],
            10 => [65.7, 35.5, 5.3, 24.9],
            11 => [64.9, 35.0, 5.3, 24.6],
            12 => [61.9, 33.4, 5.0, 23.5],
            13 => [55.9, 30.2, 4.5, 21.2],
            14 => [51.1, 27.6, 4.1, 19.4],
            15 => [49.0, 26.4, 4.0, 18.6],
            16 => [47.4, 25.6, 3.8, 18.0],
            17 => [46.3, 25.0, 3.8, 17.5],
            18 => [46.3, 25.0, 3.8, 17.5],
            19 => [46.3, 25.0, 3.8, 17.5],
            20 => [46.3, 25.0, 3.8, 17.5],
        ];
        $recommended_value_girls = [
            0 => [110.526, 59.7, 9.0, 41.9],
            1 => [110.526, 59.7, 9.0, 41.9],
            2 => [91.954, 49.7, 7.4, 34.9],
            3 => [90.615, 48.9, 7.3, 34.3],
            4 => [96.045, 51.9, 7.8, 36.4],
            5 => [91.371, 49.3, 7.4, 34.6],
            6 => [86.560, 46.7, 7.0, 32.8],
            7 => [81.301, 43.9, 6.6, 30.8],
            8 => [74.275, 40.1, 6.0, 28.2],
            9 => [68.8, 37.2, 5.6, 26.1],
            10 => [66.066, 35.7, 5.4, 25.0],
            11 => [59.459, 32.1, 4.8, 22.5],
            12 => [56.312, 30.4, 4.6, 21.3],
            13 => [47.938, 25.9, 3.9, 18.2],
            14 => [46.332, 25.0, 3.8, 17.6],
            15 => [46.226, 25.0, 3.7, 17.5],
            16 => [45.455, 24.5, 3.7, 17.2],
            17 => [45.213, 24.4, 3.7, 17.1],
            18 => [45.213, 24.4, 3.7, 17.1],
            19 => [45.213, 24.4, 3.7, 17.1],
            20 => [45.213, 24.4, 3.7, 17.1]
        ];

        if ($model2['sex'] == 0)
        {
            $BoysOrGirls = $imt_girls;
            $calculationRecValue = $recommended_value_girls[$model2['age']];
        }
        elseif ($model2['sex'] == 1)
        {
            $BoysOrGirls = $imt_boys;
            $calculationRecValue = $recommended_value_boys[$model2['age']];
        }
        else
        {
            print_r(
                'Ошибка (IWC-ADR #1). Не выбран пол ребёнка в общей информации. Если после заполнения ошибка осталась - напишите на edu@niig.su приложив данную ошибку и свой идентификационный номер.'
            );;
            die();
        }

        if ($queteletIndexVal < $BoysOrGirls[$model2['age']][0])
        {
            $arrayChildNum = 0;
        }
        elseif ($queteletIndexVal >= $BoysOrGirls[$model2['age']][0] && $queteletIndexVal <= $BoysOrGirls[$model2['age']][1])
        {
            $arrayChildNum = 1;
        }
        elseif ($queteletIndexVal > $BoysOrGirls[$model2['age']][1] && $queteletIndexVal <= $BoysOrGirls[$model2['age']][2])
        {
            $arrayChildNum = 2;
        }
        elseif ($queteletIndexVal > $BoysOrGirls[$model2['age']][2])
        {
            $arrayChildNum = 3;
        }

        $queteletIndexText = $imt_arrayVal[$arrayChildNum]; //физическое развитие словами с учётом возраста
        /*---------------------*/

        $totalEnergy_rec = $model2['mass'] * $calculationRecValue[0]; //рекомендуюмые энерготраты для ребенка (масса ребёнка * )
        $OO = $totalEnergy_rec * 0.54; //основной обмен (фактический = рекомендуемому)

        $SDDP = $OO * 0.16; //СДДП (фактический = рекомендуемому)

        /* Расчёт энерготрат на физическую активность*/
        $unrecordedTime = 1440; //неучтенное время
        $timeStreet = 0; //время нахождения на улице

        $start_date = new DateTime('2021-05-02 ' . $model['field19']);
        $since_start = $start_date->diff(new DateTime('2021-05-03 ' . $model['field18']));
        $sleep_min = $since_start->i + ($since_start->h * 60); //время сна в минутах

        $total_sleep = $sleep_min; //общее время сна в минутах за день
        if ($model['field16'] == 1) //если спал днем, то добавляем время дневного сна
        {
            $total_sleep = $sleep_min + $model['field17']; //общее время сна в минутах за день
        }
        $unrecordedTime = $unrecordedTime - $total_sleep;

        /*расчет энерготрат на учтенную физическую активность*/

        $DA = 0; //Энерготраты на двигательную активность за день
        if ($model['field1'] == 1) // если была зарядка
        {
            $DA += $model['field2'] * 0.061;
            $unrecordedTime = $unrecordedTime - $model['field2'];
        }

        if ($model['field14'] == 1) // если гуляли в этот день
        {
            $DA += $model['field15'] * 0.061;
            $unrecordedTime = $unrecordedTime - $model['field15'];
            $timeStreet = $timeStreet + $model['field15'];
        }
        if ($model['field3'] == 1) // если занимались ли в кружках доп. образования
        {
            $DA += $model['field4'] * 0.0220;
            $unrecordedTime = $unrecordedTime - $model['field4'];
        }
        if ($model['field5'] == 1) // занимались ли в спорт секциях
        {
            $sport_section1 = SportsSectionName::findOne($model2['sports_section1'])->val;
            $DA += $model['field6'] * $sport_section1;
            $unrecordedTime = $unrecordedTime - $model['field6'];
            if (!empty($model2['sports_section2']))
            {
                $sport_section2 = SportsSectionName::findOne($model2['sports_section2'])->val;
                $DA += $model['field7'] * $sport_section2;
                $unrecordedTime = $unrecordedTime - $model['field7'];
            }
        }

        $DA_rec = $totalEnergy_rec - $SDDP - $OO; //двигательная активность рекомендуемая


        $DA = $DA + (($model['field8']) * 0.061); //добавление в пути в школу и из нее пешком + секции
        $unrecordedTime = $unrecordedTime - $model['field8'];
        $timeStreet = $timeStreet + $model['field8'];
        $DA = $DA + (($model['field9']) * 0.015); //добавление в пути в школу и из нее на транспорте + секции
        $unrecordedTime = $unrecordedTime - $model['field9'];
        $timeStreet = $timeStreet + $model['field9'];
        $DA = $DA * $model2['mass'];

        $start_date2 = new DateTime('2021-05-02 ' . $model['field20']);
        $since_start2 = $start_date2->diff(new DateTime('2021-05-03 ' . $model['field21']));
        $school_min = $since_start2->i + ($since_start2->h * 60); //время нахождения в школе в минутах

        $unrecordedTime = $unrecordedTime - $school_min;

        if ($model2['use_telephone'] == 0)//расчет при учете неиспользования сотового телефона
        {
            //75% с умственной нагрузкой
            //25% с умеренная двигательная активность
            $DA_temp = (($school_min * 0.75 * 0.022) + ($school_min * 0.25 * 0.061)) * $model2['mass'];
        }
        elseif ($model2['use_telephone'] == 1) //расчет при учете использования сотового телефона
        {
            //75% с умственной нагрузкой
            //15% без умственной нагрузкой
            //5% в положении стоя
            //5% с умеренная двигательная активность
            $DA_temp = (($school_min * 0.75 * 0.022) + ($school_min * 0.15 * 0.015) + ($school_min * 0.05 * 0.0397) + ($school_min * 0.05 * 0.061)) * $model2['mass'];
        }
        else
        {
            print_r('Ошибка определения использования телефона в школе. Напишите на edu@niig.su');
            die();
        }

        $DA += $DA_temp;
        /**/

        /*Неучтенное время*/
        if ($unrecordedTime > 0) // если есть неучтенное время
        {
            //25% в положении лежа
            //25% в положении стоя
            //20% в положении сидя без умственной нагрузки
            //30% в положении сидя с умственной нагрузкой
            $DA_temp2 = (($unrecordedTime * 0.25 * 0.01) + ($unrecordedTime * 0.25 * 0.0397) + ($unrecordedTime * 0.2 * 0.015) + ($unrecordedTime * 0.3 * 0.022)) * $model2['mass'];
        }

        $DA += $DA_temp2;

        $totalEnergy = $DA + $SDDP + $OO; //энерготраты за сутки
        /**/

        $check_recomend = 0;
        if ($totalEnergy < $totalEnergy_rec)
        {
            $check_recomend = 1;
            $text1 = 'Необходимо пересмотреть двигательную активность';

            if ($model2['charging'] == 0)
            {
                $text3 = 'Ежедневно делать утреннюю гимнастику.';
            }
            if ($model2['sports_section'] == 0)
            {
                $text4 = 'Заниматься спортом.';
            }
        }
        if ($model2['use_telephone'] == 1)
        {
            $check_recomend = 1;
            $text2 = 'Не пользоваться сотовым телефоном во время перемен в школе (в дет.саду).';
        }
        if ($timeStreet < 120) //!!!!!!!!!!!!!!!!
        {
            $check_recomend = 1;
            $text5 = 'Увеличить время прогулок и гулять ежедневно.';
        }
        if ($sleep_min < 600)
        {
            $check_recomend = 1;
            $text6 = 'Увеличить продолжительность ночного сна (не менее 10 ч.).';
        }
        if ($check_recomend == 0)
        {
            $text7 = 'У вас рациональная двигательная активность, вы делаете утреннюю зарядку, занимаетесь в спортивной секции. Продолжайте в том же духе.';
        }
        /*END двигательная активность*/

        /*Меню*/
        $menu_id = Menus::find()->where(['user_id' => $u_id])->one()->id;
        if (!empty($menu_id))
        {
            $menu = 1;
        }
        $cycle = 1;
        $days_id = 1;
        $nutrition_id = 0;

        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }

        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

        //$id - id приема пищи
        if ($nutrition_id > 0)
        {
            $menus_dishes = MenusDishes::find()->where(
                [
                    'date_fact_menu' => 0,
                    'menu_id' => $menu_id,
                    'cycle' => $cycle,
                    'days_id' => $days_id,
                    'nutrition_id' => $nutrition_id
                ]
            )->all();
        }
        else
        {
            $menus_dishes = MenusDishes::find()->where(
                ['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $days_id]
            )->all();
        }
        $indicator_page = $nutrition_id;
        $model = new MenusDishes();
        /*END меню*/

        $independentWork = ReportTbl22::find()->where(['user_id' => $u_id, 'independent_work' => 1])->one()->independent_work;
        if (!$independentWork)
        {
            $independentWork = ReportTbl21::find()->where(['user_id' => $u_id, 'independent_work' => 1])->one()->independent_work;
        }

        ($independentWork == 1) ? $indWork = '<span style="color: green">выполнена</span>' : $indWork = '<span style="color: firebrick">не выполнена</span>. <br>Пожалуйста, внесите больше блюд, так как внесённая калорийность не удовлетворяет минимальным потребностям для ребёнка. ';

        return $this->render(
            'report',
            [
                /*Двигательная активность*/
                'daily' => $daily,
                'queteletIndexVal' => round($queteletIndexVal, 1),
                'queteletIndexText' => $queteletIndexText,
                'totalEnergy' => round($totalEnergy, 1),
                'totalEnergy_rec' => round($totalEnergy_rec, 1),
                'OO' => round($OO, 1),
                'SDDP' => round($SDDP, 1),
                'DA' => round($DA, 1),
                'DA_rec' => round($DA_rec, 1),
                'text1' => $text1,
                'text2' => $text2,
                'text3' => $text3,
                'text4' => $text4,
                'text5' => $text5,
                'text6' => $text6,
                'text7' => $text7,

                /*Меню*/
                'menu' => $menu,
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'indicator_page' => $indicator_page,
                'model' => $model,

                /*Итог по самостоятельной*/
                'indWork' => $indWork
            ]
        );
    }
}
