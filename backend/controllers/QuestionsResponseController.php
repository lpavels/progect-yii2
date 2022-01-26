<?php

namespace backend\controllers;

use common\models\AuthAssignment;
use common\models\Organization;
use common\models\Questions;
use common\models\QuestionsForm;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use common\models\SiteSettings;
use common\models\User;
use Yii;
use common\models\QuestionsResponse;
use yii\web\Controller;

class QuestionsResponseController extends Controller
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

    public function actionCreate()
    {
        if (QuestionsResponse::find()->select([
                'user_id',
                'status',
                'number_trying'
            ])->where(['user_id' => Yii::$app->user->identity->id, 'status' => 1])->count() > 0)
        {
            Yii::$app->session->setFlash('error', "Для прохождения входного теста предоставляется одна попытка!");
            return $this->goHome();
        }
        else
        {
            $model = new QuestionsForm();
            $post = Yii::$app->request->post();
            if ($post)
            {
                if (count($post) == 11)
                {
                    $type_question = 1;

                    $AuthAssignment = AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name;
                    if ($AuthAssignment == 'school14')
                    {
                        $type_question = 14;
                    }
                    elseif ($AuthAssignment == 'school511')
                    {
                        $type_question = 511;
                    }
                    elseif ($AuthAssignment == 'school56')
                    {
                        $type_question = 56;
                    }
                    elseif ($AuthAssignment == 'school79')
                    {
                        $type_question = 79;
                    }
                    elseif ($AuthAssignment == 'school1011')
                    {
                        $type_question = 1011;
                    }
                    elseif (Yii::$app->user->identity->training_id == 2)
                    {
                        $type_question = 1;
                    }

                    $questions = Questions::find()->where([
                        'training_program_id' => Yii::$app->user->identity->training_id,
                        'type_questions' => $type_question
                    ])->all();
                    $questions_id = [];
                    foreach ($questions as $question)
                    {
                        $i = $question->id;
                        $post_question = Yii::$app->request->post()[$i];
                        if (!empty($post_question))
                        {
                            $questions_id[$question->id] = $post_question;
                        }
                    }

                    foreach ($questions_id as $key => $q1)
                    {
                        $model2 = new QuestionsResponse();
                        $model2->user_id = Yii::$app->user->identity->id;
                        $model2->questions_id = $key;
                        $model2->questions_variant_id = $q1;
                        $model2->number_trying = 1;
                        $model2->status = 1;
                        $model2->save(false);
                    }

                    $organization = Organization::findOne(Yii::$app->user->identity->organization_id);
                    $queru = User::findOne(Yii::$app->user->id);
                    $ball = $model->ball_response($queru->id);


                    if (date('Y') == self::YAER_2021)
                    {
                        $report_tbl = new ReportTbl21();
                    }
                    elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
                    {
                        $report_tbl = new ReportTbl22();
                    }
                    elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
                    {
                        $report_tbl = new ReportTbl22();
                    }
                    else
                    {
                        print_r('Ошибка. Обратиесь к администратору');
                        die();
                    }

                    $report_tbl->user_id = Yii::$app->user->id;
                    $report_tbl->federal_district_id = $organization->federal_district_id;
                    $report_tbl->region_id = $organization->region_id;
                    $report_tbl->municipality_id = $organization->municipality_id;
                    $report_tbl->organization_id = Yii::$app->user->identity->organization_id;
                    $report_tbl->training_id = Yii::$app->user->identity->training_id;
                    $report_tbl->type_training = Yii::$app->user->identity->type_training;
                    $report_tbl->key_login = Yii::$app->user->identity->key_login;
                    $report_tbl->name = Yii::$app->user->identity->name;
                    $report_tbl->type_listener = Yii::$app->user->identity->type_listener;
                    $report_tbl->class_number = Yii::$app->user->identity->class;
                    $report_tbl->letter_number = Yii::$app->user->identity->bukva_klassa;
                    $report_tbl->input_test = $ball;
                    $report_tbl->created_ip = Yii::$app->userHelp->ip();
                    $report_tbl->save();

                    Yii::$app->session->setFlash('success',
                        "Благодарим за внесение данных, теперь вы можете приступить к изучению материалов - вкладка Обучающие материалы");
                    return $this->goHome();
                }
                else
                {
                    Yii::$app->session->setFlash('error',
                        "Тест не засчитан. При повторной попытке нечестного прохождения теста доступ будет заблокирован.");
                    return $this->goHome();
                }
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionFinal()
    {
        $date_last_attempt = QuestionsResponse::find()->select([
            'user_id',
            'status',
            'number_trying',
            'creat_at'
        ])->where(['user_id' => Yii::$app->user->identity->id, 'status' => 2])->orderBy([
            'number_trying' => SORT_DESC,
            'creat_at' => SORT_DESC
        ])->one()->creat_at; //дата прохождения последней попытки итогового теста
        $allowed_attempt = date("Y-m-d H:i:s", strtotime(SiteSettings::findOne(['name' => 'retry_test'])->value,
            strtotime($date_last_attempt))); // дата резрешенной новой попытки прохождения итогового теста
        $today = date(date("Y-m-d H:i:s")); // дата сегодня

        /* Проверка даты и данных пользователя в таблице отчетов */
        if (date('Y') == self::YAER_2021)
        {
            $modelReport = ReportTbl21::find()->where(['user_id' => Yii::$app->user->id])->one();
        }
        elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
        {
            $modelReport = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();

            if (empty($modelReport))
            {
                Yii::$app->userHelp->checkDataReportTable();
                $modelReport = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
        }
        elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
        {
            $modelReport = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
        }
        else
        {
            print_r('Ошибка. Обратиесь к администратору');
            die();
        }
        /* Проверка даты и данных пользователя в таблице отчетов (END) */

        if ($modelReport->training_id == 1 && ($modelReport->input_test == null && $modelReport->independent_work != 1 && $modelReport->theme1 != 1 && $modelReport->theme2 != 1 && $modelReport->theme3 != 1 && $modelReport->theme4 != 1 && $modelReport->theme5 != 1))
        {
            Yii::$app->session->setFlash('error',
                "Вы не допущены к итоговому тесту. Перейдите в раздел \"Общая информация\"->\"Данные по обучению\".");
            return $this->goHome();
        }
        elseif ($modelReport->training_id == 2 && ($modelReport->input_test == null && $modelReport->independent_work != 1 && $modelReport->theme1 != 1 && $modelReport->theme2 != 1 && $modelReport->theme3 != 1 && $modelReport->theme4 != 1 && $modelReport->theme5 != 1 && $modelReport->theme6 != 1))
        {
            Yii::$app->session->setFlash('error',
                "Вы не допущены к итоговому тесту. Перейдите в раздел \"Общая информация\"->\"Данные по обучению\".");
            return $this->goHome();
        }


        $number = QuestionsResponse::find()->where([
            'user_id' => Yii::$app->user->identity->id,
            'status' => 2
        ])->orderBy(['number_trying' => SORT_DESC])->offset(1)->one()->number_trying;
        $questions_itog = QuestionsResponse::find()->select([
            'questions_response.user_id',
            'questions_response.questions_id',
            'questions_response.questions_variant_id',
            'questions_response.number_trying',
            'questions_response.`status`',
            'questions_variant.questions_id',
            'questions_variant.correct',
            'COUNT(questions_variant.correct)'
        ])
            ->leftJoin('questions_variant', 'questions_variant.id = questions_response.questions_variant_id')
            ->where([
                'questions_response.user_id' => Yii::$app->user->identity->id,
                'status' => 2,
                'questions_response.number_trying' => $number,
                'questions_variant.correct' => 1
            ])
            ->groupBy('questions_variant.correct')
            ->asArray()
            ->all();
        $count_correct_itog = $questions_itog[0]['COUNT(questions_variant.correct)'];

        if ($today < $allowed_attempt && $count_correct_itog < 7)
        {
            Yii::$app->session->setFlash('error',
                "Дата следующей попытки: " . date_create($allowed_attempt)->format('d.m.Y H:i:s') . ' (UTC+7)');
            return $this->goHome();
        }
        elseif ($count_correct_itog > 6)
        {
            Yii::$app->session->setFlash('success',
                "Итоговый тест был выполнен ранее. Вы можете скачать сертификат в личном кабинете. (" . $count_correct_itog . "0%)");
            return $this->goHome();
        }
        else
        {
            $model = new QuestionsForm();
            $post = Yii::$app->request->post();
            if ($post)
            {
                if (count($post) == 12)
                {
                    $type_question = 1;
                    $AuthAssignment = AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name;
                    if ($AuthAssignment == 'school14')
                    {
                        $type_question = 14;
                    }
                    elseif ($AuthAssignment == 'school511')
                    {
                        $type_question = 511;
                    }
                    elseif ($AuthAssignment == 'school56')
                    {
                        $type_question = 56;
                    }
                    elseif ($AuthAssignment == 'school79')
                    {
                        $type_question = 79;
                    }
                    elseif ($AuthAssignment == 'school1011')
                    {
                        $type_question = 1011;
                    }

                    $questions = Questions::find()->where([
                        'training_program_id' => '' . Yii::$app->user->identity->training_id,
                        'type_questions' => $type_question
                    ])->all();
                    $questions_id = [];
                    $number = QuestionsResponse::find()->where([
                        'user_id' => Yii::$app->user->identity->id,
                        'status' => 2
                    ])->orderBy(['number_trying' => SORT_DESC])->offset(1)->one()->number_trying;
                    if (empty($questions))
                    {
                        Yii::$app->session->setFlash('error',
                            "Ваш итоговый тест не защитан, программа обучения при регистрации была указана неправильно. Вам необходимо перерегистрироваться.");
                        return $this->goHome();
                    }

                    if (empty($number))
                    {
                        $number = 1;
                    }
                    else
                    {
                        $number++;
                    }

                    foreach ($questions as $question)
                    {
                        $i = $question->id;
                        $post_question = Yii::$app->request->post()[$i];
                        if (!empty($post_question))
                        {
                            $questions_id[$question->id] = $post_question;
                        }
                    }
                    foreach ($questions_id as $key => $q1)
                    {
                        $model2 = new QuestionsResponse();
                        $model2->user_id = Yii::$app->user->identity->id;
                        $model2->questions_id = $key;
                        $model2->questions_variant_id = $q1;
                        $model2->number_trying = $number;
                        $model2->status = 2;
                        if ($model2->save(false))
                        {
                            Yii::$app->session->setFlash('success',
                                'Благодарим за прохождение обучения, результаты представлены в личном кабинете. <br> Результат считается действительным, если количество положительных ответов 70% или более');
                        }
                        else
                        {
                            Yii::$app->session->setFlash('error',
                                'Итоговый тест не засчитан, напишите на edu@niigsu о данной проблеме.');
                            return $this->goHome();
                        }
                    }

                    $queru = User::find()->where(['key_login' => Yii::$app->user->identity->key_login])->one();
                    $id_u = Yii::$app->user->identity->id;
                    $ball = $queru->ball_response_fin($id_u);
                    $number_typing = QuestionsResponse::find()->select(['number_trying'])->where([
                        'user_id' => $id_u,
                        'status' => 2
                    ])->orderBy(['number_trying' => SORT_DESC])->one()->number_trying;

                    $modelReport->final_test = $ball;
                    if ($ball > 6)
                    {
                        $modelReport->training_completed = 1;
                        if ($number_typing == 1)
                        {
                            $modelReport->final_test_1st = 1;
                            $modelReport->final_test_2st = 0;
                        }
                        else
                        {
                            $modelReport->final_test_1st = 0;
                            $modelReport->final_test_2st = 1;
                        }
                    }
                    else
                    {
                        $modelReport->final_test_1st = 0;
                        $modelReport->final_test_2st = 0;
                        $modelReport->training_completed = 0;
                    }
                    $modelReport->save();

                    return $this->goHome();
                }
                else
                {
                    Yii::$app->session->setFlash('error',
                        "Тест не засчитан. При повторной попытке нечестного прохождения теста доступ будет заблокирован.");
                    return $this->goHome();
                }
            }
            return $this->render('final', [
                'model' => $model,
            ]);
        }
    }

    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /*protected function findModel($id)
    {
        if (($model = QuestionsResponse::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }*/
}