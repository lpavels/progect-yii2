<?php

namespace backend\controllers;

use common\models\Anket;
use common\models\QuestionsResponse;
use common\models\ReportTbl22;
use common\models\ReportTbl21;
use common\models\ThemeProgram;
use common\models\TrainingThemes;
use common\models\Trainings;
use Yii;
use yii\web\Controller;

class TrainingsController extends Controller
{
    const YAER_2021 = 2021;
    const YAER_2022 = 2022;

    //public function behaviors() {
    //    return [
    //        'access' => [
    //            'class' => \yii\filters\AccessControl::className(),
    //            'rules' => [
    //                [
    //                    'allow' => true,
    //                    'roles' => ['@'],
    //                ],
    //            ],
    //        ],
    //    ];
    //}

    public function actionEducation()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun') || Yii::$app->user->can('director'))
        {
            Yii::$app->session->setFlash('error', "Доступ к просмотру тем запрещен.");
            return $this->goHome();
        }

        if (QuestionsResponse::find()->where(['user_id' => Yii::$app->user->id, 'status' => 1])->count() != 10)
        {
            Yii::$app->session->setFlash('error', "У вас не пройден входной тест.");
            return $this->goHome();
        }

        $themes = ThemeProgram::find()
            ->select(['theme_program.id as id', 'theme_program.name as name', 'theme_program.short_name as short_name', 'training_themes.training_program_id as training_program_id', 'training_themes.theme_program_id as theme_program_id', 'training_themes.sort as sort'])
            ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
            ->where(['training_program_id' => Yii::$app->user->identity->training_id])
            ->orderby(['sort' => SORT_ASC])
            ->all();

        $model1 = new TrainingThemes();
        $model2 = new Anket();

        if (Yii::$app->request->post())
        {
            if ($model2->load(Yii::$app->request->post()) && !$model2->validate())
            {
                Yii::$app->session->setFlash('error', "Укажите даты проведенных по теме организационных мероприятий");
                return $this->render('education', [
                    'model2' => $model2,
                    'model1' => $model1,
                    'themes' => $themes,
                ]);
            }

            $u_id = Yii::$app->user->identity->id; //user_id
            $theme_program = Yii::$app->request->post()['TrainingThemes']['theme_program_id'];
            if (Trainings::find()->where(['user_id' => $u_id, 'theme_program_id' => $theme_program])->count() > 0)
            {
                Yii::$app->session->setFlash('success', 'Данная тема уже успешна пройдена.');
                return $this->redirect(['education']);
            }

            $anket = Yii::$app->request->post()['Anket'];
            $model2->user_id = $u_id;
            $model2->informativity = $anket['informativity'];
            $model2->informativity_comment = $anket['informativity_comment'];
            $model2->utility = $anket['utility'];
            $model2->utility_comment = $anket['utility_comment'];
            $model2->visualization = $anket['visualization'];
            $model2->visualization_comment = $anket['visualization_comment'];
            $model2->class_chas = $anket['class_chas'];
            $model2->class_chas_date = strtotime($anket['class_chas_date']);
            $model2->parent_meet = $anket['parent_meet'];
            $model2->parent_meet_date = strtotime($anket['parent_meet_date']);
            $model2->inoe = $anket['inoe'];
            if ($model2->save())
            {
                $model = new Trainings();
                $model->user_id = $u_id;
                $model->theme_program_id = $theme_program;
                if ($model->save())
                {
                    /*Таблица отчета*/
                    if (date('Y') == self::YAER_2021)
                    {
                        $report_tbl = ReportTbl21::find()->where(['user_id' => $u_id])->one();
                    }
                    elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
                    {
                        $report_tbl = ReportTbl22::find()->where(['user_id' => $u_id])->one();

                        if (empty($report_tbl))
                        {
                            Yii::$app->userHelp->checkDataReportTable();
                            $report_tbl = ReportTbl22::find()->where(['user_id' => $u_id])->one();
                        }
                        if (!$report_tbl)
                        {
                            Yii::$app->session->setFlash('error', "Ошибка при сохранении. Обратиесь к администратору сайта используя чат.");
                            $model2->delete();
                            $model->delete();

                            return $this->goHome();
                        }
                    }
                    elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
                    {
                        $report_tbl = ReportTbl22::find()->where(['user_id' => $u_id])->one();
                    }
                    else
                    {
                        Yii::$app->session->setFlash('error', "Ошибка. Обратиесь к администратору сайта используя чат.");
                        return $this->redirect(['chat']);
                    }

                    if ($theme_program == 10 || $theme_program == 9)
                    {
                        $report_tbl->theme1 = 1;
                    }
                    elseif ($theme_program == 1 || $theme_program == 5)
                        $report_tbl->theme2 = 1;
                    elseif ($theme_program == 2 || $theme_program == 6)
                        $report_tbl->theme3 = 1;
                    elseif ($theme_program == 3 || $theme_program == 7)
                        $report_tbl->theme4 = 1;
                    elseif ($theme_program == 4 || $theme_program == 8)
                        $report_tbl->theme5 = 1;
                    elseif ($theme_program == 11)
                        $report_tbl->theme6 = 1;
                    $report_tbl->updated_ip = Yii::$app->userHelp->ip();
                    $report_tbl->save();
                    /*Таблица отчета (END)*/
                    Yii::$app->session->setFlash('success', "Тема пройдена.");
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', "Ошибка сохранения, введите корректные данные");
            }

            return $this->redirect(['education']);
        }

        return $this->render('education', [
            'model2' => $model2,
            'model1' => $model1,
            'themes' => $themes,
        ]);
    }
}
