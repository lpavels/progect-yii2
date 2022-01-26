<?php

namespace backend\controllers;

use common\models\DailyRoutine;
use common\models\Days;
use common\models\Menus;
use common\models\MenusDays;
use common\models\MenusDishes;
use common\models\MenusNutrition;
use common\models\Organization;
use common\models\QuestionsResponse;
use common\models\SportsSectionName;
use DateTime;
use Yii;
use common\models\Kids;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class IndependentWorkController extends Controller
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

    public function actionGeneralInfo()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу общей информации запрещён");
            return $this->goHome();
        }

        if (empty(QuestionsResponse::find()->where(['user_id' => Yii::$app->user->id, 'status' => 1])->exists()))
        {
            Yii::$app->session->setFlash('error', "Необходимо пройти входной тест.");
            return $this->goHome();
        }

        if (!empty(Kids::find()->where(['user_id' => Yii::$app->user->id])->count()))
        {
            return $this->redirect('general-info-u');
        }

        $model = new Kids();
        //if (Yii::$app->request->post())
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $post = Yii::$app->request->post()['Kids'];

            if ($post['sex'] < 0 || $post['sex'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка при выборе пола ребёнка");
                return $this->goHome();
            }
            if ($post['height'] < 1 || $post['height'] > 250)
            {
                Yii::$app->session->setFlash('error', "Ошибка в росте ребёнка, не может быть меньше, чем 45 и больше, чем 250");
                return $this->goHome();
            }
            if ($post['mass'] < 7 || $post['mass'] > 200)
            {
                Yii::$app->session->setFlash('error', "Ошибка в массе ребёнка, не может быть меньше, чем 7 и больше, чем 200");
                return $this->goHome();
            }
            if ($post['age'] < 0 || $post['age'] > 18)
            {
                Yii::$app->session->setFlash('error', "Ошибка в возрасте ребёнка, не может быть меньше, чем 0 и больше, чем 18");
                return $this->goHome();
            }
            if ($post['class'] != '' && ($post['class'] < 1 || $post['class'] > 11))
            {
                Yii::$app->session->setFlash('error', "Ошибка при выборе класса ребенка, не может быть меньше 1 и больше 11");
                return $this->goHome();
            }
            if ($post['days_id'] < 1 || $post['days_id'] > 5)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['charging'] < 0 || $post['charging'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['additional_education'] < 0 || $post['additional_education'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['sports_section'] < 0 || $post['sports_section'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['walk'] < 0 || $post['walk'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['sleep_day'] < 0 || $post['sleep_day'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['use_telephone'] < 0 || $post['use_telephone'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['food_intake'] < 1 || $post['food_intake'] > 6)
            {
                Yii::$app->session->setFlash('error', "Ошибка в пункте 'Сколько раз вы кушали?'. Не может быть менее 1 и более 7.");
                return $this->goHome();
            }

            if ($post['sports_section1'] != '' && ($post['sports_section1'] < 1 || $post['sports_section1'] > 21))
            {
                Yii::$app->session->setFlash('error', "Ошибка в пункте 'Вид спорта'. Выбран не существующий вид деятельности.");
                return $this->goHome();
            }
            if ($post['sports_section2'] != '' && ($post['sports_section2'] < 1 || $post['sports_section2'] > 21))
            {
                Yii::$app->session->setFlash('error', "Ошибка в пункте 'Вид спорта (если необходимо)?'. Выбран не существующий вид деятельности.");
                return $this->goHome();
            }
            if ($post['sports_section'] == 1 && $post['sports_section1'] == '' && $post['sports_section2'] != '')
            {
                $post['sports_section'] = 1;
                $post['sports_section1'] = $post['sports_section2'];
                $post['sports_section2'] = '';
            }
            if ($post['sports_section'] == 1 && $post['sports_section1'] == '' && $post['sports_section2'] == '')
            {
                $post['sports_section'] = 0;
                $post['sports_section1'] = '';
                $post['sports_section2'] = '';
            }
            if ($post['sports_section'] == 0)
            {
                $post['sports_section'] = 0;
                $post['sports_section1'] = null;
                $post['sports_section2'] = null;
            }

            $model->user_id = Yii::$app->user->id;
            $model->organization_id = Yii::$app->user->identity->organization_id;
            $model->sex = $post['sex'];
            $model->height = $post['height'];
            $model->mass = $post['mass'];
            $model->age = $post['age'];
            $model->class = $post['class'];
            $model->days_id = $post['days_id'];
            $model->charging = $post['charging'];
            $model->additional_education = $post['additional_education'];
            $model->sports_section = $post['sports_section'];
            $model->sports_section1 = $post['sports_section1'];
            $model->sports_section2 = $post['sports_section2'];
            $model->walk = $post['walk'];
            $model->sleep_day = $post['sleep_day'];
            $model->use_telephone = $post['use_telephone'];
            $model->food_intake = $post['food_intake'];
            if ($model->save())
            {
                if ($post['age'] < 3)
                {
                    $age_info_id = 2;
                }
                elseif ($post['age'] > 2 && $post['age'] < 7)
                {
                    $age_info_id = 5;
                }
                elseif ($post['age'] > 6 && $post['age'] < 11)
                {
                    $age_info_id = 6;
                }
                elseif ($post['age'] > 10 && $post['age'] < 19)
                {
                    $age_info_id = 7;
                }

                $model_menu = new Menus();
                $model_menu->parent_id = 0;
                $model_menu->food_director = 0;
                $model_menu->feeders_characters_id = 0;
                $model_menu->age_info_id = $age_info_id;
                $model_menu->name = 'Меню';
                $model_menu->cycle = 1;
                $model_menu->date_start = strtotime(date("Y-m-d H:i:s"));
                $model_menu->date_end = strtotime(date("Y-m-d H:i:s"));
                $model_menu->status_archive = 0;
                $model_menu->show_indicator = 2;
                $model_menu->organization_id = Yii::$app->user->identity->organization_id;
                $model_menu->user_id = Yii::$app->user->id;
                $model_menu->save();

                $count = 0;
                while ($count < $post['food_intake'])
                {
                    $count++;
                    $model_menu_nutrition = new MenusNutrition();
                    $model_menu_nutrition->menu_id = $model_menu->id;
                    $model_menu_nutrition->nutrition_id = $count;
                    $model_menu_nutrition->save();
                }

                $model_menu_days = new MenusDays();
                $model_menu_days->menu_id = $model_menu->id;
                $model_menu_days->days_id = 1;
                $model_menu_days->save();


                Yii::$app->session->setFlash('success', "Общая информация сохранена");
                return $this->redirect('daily-routine');
            }
            else
            {
                Yii::$app->session->setFlash('error', "Ошибка при сохранении общей информации");
                return $this->redirect('general-info');
            }
        }

        $type_org = Organization::findOne(Yii::$app->user->identity->organization_id)->type_org;
        $sex = array('' => '', 'женский', 'мужской'); //пол
        $m_days = Days::find()->where(['<', 'id', 6])->all();
        $m_days = ArrayHelper::map($m_days, 'id', 'name');
        $m_days = ArrayHelper::merge(['' => ''], $m_days); //описываемый день
        $num_class = ['' => '', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11']; //номер класса
        $num_eat = ['' => '', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6']; //кол-во приемов пищи
        $ar_YoN = ['' => '', 'Нет', 'Да'];

        $sport_sel_name = SportsSectionName::find()->all();
        $sport_sel_name = ArrayHelper::map($sport_sel_name, 'id', 'name');
        $sport_sel_name = ArrayHelper::merge(['' => ''], $sport_sel_name); //спорт секции

        return $this->render('general-info', [
            'm_days' => $m_days,
            'sex' => $sex,
            'num_class' => $num_class,
            'num_eat' => $num_eat,
            'ar_YoN' => $ar_YoN,
            'sport_sel_name' => $sport_sel_name,
            'model' => $model,
            'type_org' => $type_org,
        ]);
    }

    public function actionGeneralInfoU()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу общей информации запрещён");
            return $this->goHome();
        }
        if (empty(Kids::find()->where(['user_id' => Yii::$app->user->id])->count()))
        {
            return $this->redirect('general-info');
        }

        $model = Kids::find()->where(['user_id' => Yii::$app->user->id])->one();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $post = Yii::$app->request->post()['Kids'];

            if ($post['sex'] < 0 || $post['sex'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка при выборе пола ребёнка");
                return $this->goHome();
            }
            if ($post['height'] < 1 || $post['height'] > 250)
            {
                Yii::$app->session->setFlash('error', "Ошибка в росте ребёнка, не может быть меньше, чем 45 и больше, чем 250");
                return $this->goHome();
            }
            if ($post['mass'] < 7 || $post['mass'] > 200)
            {
                Yii::$app->session->setFlash('error', "Ошибка в массе ребёнка, не может быть меньше, чем 7 и больше, чем 200");
                return $this->goHome();
            }
            if ($post['age'] < 0 || $post['age'] > 18)
            {
                Yii::$app->session->setFlash('error', "Ошибка в возрасте ребёнка, не может быть меньше, чем 0 и больше, чем 18");
                return $this->goHome();
            }
            if ($post['class'] != '' && ($post['class'] < 1 || $post['class'] > 11))
            {
                Yii::$app->session->setFlash('error', "Ошибка при выборе класса ребенка, не может быть меньше 1 и больше 11");
                return $this->goHome();
            }
            if ($post['days_id'] < 1 || $post['days_id'] > 5)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['charging'] < 0 || $post['charging'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['additional_education'] < 0 || $post['additional_education'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['sports_section'] < 0 || $post['sports_section'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['walk'] < 0 || $post['walk'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['sleep_day'] < 0 || $post['sleep_day'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['use_telephone'] < 0 || $post['use_telephone'] > 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка, выбран несуществующий вариант.");
                return $this->goHome();
            }
            if ($post['food_intake'] < 1 || $post['food_intake'] > 6)
            {
                Yii::$app->session->setFlash('error', "Ошибка в пункте 'Сколько раз вы кушали?'. Не может быть менее 1 и более 7.");
                return $this->goHome();
            }

            if ($post['sports_section1'] != '' && ($post['sports_section1'] < 1 || $post['sports_section1'] > 21))
            {
                Yii::$app->session->setFlash('error', "Ошибка в пункте 'Вид спорта'. Выбран не существующий вид деятельности.");
                return $this->goHome();
            }
            if ($post['sports_section2'] != '' && ($post['sports_section2'] < 1 || $post['sports_section2'] > 21))
            {
                Yii::$app->session->setFlash('error', "Ошибка в пункте 'Вид спорта (если необходимо)?'. Выбран не существующий вид деятельности.");
                return $this->goHome();
            }
            if ($post['sports_section'] == 1 && $post['sports_section1'] == '' && $post['sports_section2'] != '')
            {
                $post['sports_section'] = 1;
                $post['sports_section1'] = $post['sports_section2'];
                $post['sports_section2'] = '';
            }
            if ($post['sports_section'] == 1 && $post['sports_section1'] == '' && $post['sports_section2'] == '')
            {
                $post['sports_section'] = 0;
                $post['sports_section1'] = '';
                $post['sports_section2'] = '';
            }
            if ($post['sports_section'] == 0)
            {
                $post['sports_section'] = 0;
                $post['sports_section1'] = null;
                $post['sports_section2'] = null;
            }

            $model->user_id = Yii::$app->user->id;
            $model->organization_id = Yii::$app->user->identity->organization_id;
            $model->sex = $post['sex'];
            $model->height = $post['height'];
            $model->mass = $post['mass'];
            $model->age = $post['age'];
            $model->class = $post['class'];
            $model->days_id = $post['days_id'];
            $model->charging = $post['charging']; //зарядка
            $model->additional_education = $post['additional_education'];//занимались ли в кружках доп обр? (0-нет,1-да)
            $model->sports_section = $post['sports_section']; //занимались ли в спорт.секции? (0-нет,1-да)
            $model->sports_section1 = $post['sports_section1'];
            $model->sports_section2 = $post['sports_section2'];
            $model->walk = $post['walk'];//гуляли ли? (0-нет, 1-да)
            $model->sleep_day = $post['sleep_day'];//спали ли днем? (0-нет,1-да)
            $model->use_telephone = $post['use_telephone'];//Пользуется ли ребенок сотовым телефоном во время перемен 0-нет, 1-да
            $model->food_intake = $post['food_intake'];
            if ($model->save())
            {
                if (!empty(DailyRoutine::find()->where(['user_id' => Yii::$app->user->id])->count()))
                {
                    $model2 = DailyRoutine::find()->where(['user_id' => Yii::$app->user->id])->one();

                    $field1 = $model2->field1;
                    $field2 = $model2->field2;
                    if ($post['charging'] != $model2->field1)
                    {
                        if ($post['charging'] == 1)
                        {
                            $field1 = 1;
                            $field2 = 0;
                        }
                        elseif ($post['charging'] == 0)
                        {
                            $field1 = 0;
                            $field2 = null;
                        }
                    } //++

                    $field3 = $model2->field3;
                    $field4 = $model2->field4;
                    if ($post['additional_education'] != $model2->field3)
                    {
                        if ($post['additional_education'] == 1)
                        {
                            $field3 = 1;
                            $field4 = 0;
                        }
                        elseif ($post['additional_education'] == 0)
                        {
                            $field3 = 0;
                            $field4 = null;
                        }
                    }

                    $field5 = $model2->field5;
                    $field6 = $model2->field6;
                    $field7 = $model2->field7;
                    if ($post['sports_section'] == 0)
                    {
                        $field5 = 0;
                        $field6 = null;
                        $field7 = null;
                    }
                    elseif ($post['sports_section'] == 1)
                    {
                        $field5 = 1;
                        $field6 = 0;
                        $field7 = 0;
                        if (!empty($post['sports_section1']) && !empty($model2->field6))
                        {
                            $field6 = $model2->field6;
                        }
                        if (!empty($post['sports_section2']) && !empty($model2->field7))
                        {
                            $field7 = $model2->field7;
                        }
                    }

                    $field14 = $model2->field14;
                    $field15 = $model2->field15;
                    if ($post['walk'] != $model2->field14)
                    {
                        if ($post['walk'] == 1)
                        {
                            $field14 = 1;
                            $field15 = 0;
                        }
                        elseif ($post['walk'] == 0)
                        {
                            $field14 = 0;
                            $field15 = null;
                        }
                    }

                    $field16 = $model2->field16;
                    $field17 = $model2->field17;
                    if ($post['sleep_day'] != $model2->field16)
                    {
                        if ($post['sleep_day'] == 1)
                        {
                            $field16 = 1;
                            $field17 = 0;
                        }
                        elseif ($post['sleep_day'] == 0)
                        {
                            $field16 = 0;
                            $field17 = null;
                        }
                    } //+

                    $model2->field1 = $field1;
                    $model2->field2 = $field2;
                    $model2->field3 = $field3;
                    $model2->field4 = $field4;
                    $model2->field5 = $field5;
                    $model2->field6 = $field6;
                    $model2->field7 = $field7;
                    $model2->field14 = $field14;
                    $model2->field15 = $field15;
                    $model2->field16 = $field16;
                    $model2->field17 = $field17;
                    if ($model2->save(false))
                    {
                        if ($post['age'] < 3)
                        {
                            $age_info_id = 2;
                        }
                        elseif ($post['age'] > 2 && $post['age'] < 7)
                        {
                            $age_info_id = 5;
                        }
                        elseif ($post['age'] > 6 && $post['age'] < 11)
                        {
                            $age_info_id = 6;
                        }
                        elseif ($post['age'] > 10 && $post['age'] < 19)
                        {
                            $age_info_id = 7;
                        }

                        $model_menu = Menus::find()->where(['user_id' => Yii::$app->user->id])->one();
                        $model_menu->age_info_id = $age_info_id;
                        $model_menu->save();

                        MenusNutrition::deleteAll(['menu_id' => $model_menu->id]);
                        $count = 0;
                        while ($count < $post['food_intake'])
                        {
                            $count++;
                            $model_menu_nutrition = new MenusNutrition();
                            $model_menu_nutrition->menu_id = $model_menu->id;
                            $model_menu_nutrition->nutrition_id = $count;
                            $model_menu_nutrition->save();
                        }

                        MenusDishes::deleteAll(['AND', ['>', 'nutrition_id', $count], ['menu_id' => $model_menu->id]]);

                        Yii::$app->session->setFlash('success', "Общая информация обновлена. При необходимости внесите изменения в данные об учебном дне.");
                        return $this->redirect('daily-routine');
                    }
                    else
                    {
                        print_r(3434);
                        die();
                    }
                }
                else
                {
                    Yii::$app->session->setFlash('success', "Общая информация обновлена. При необходимости внесите изменения в данные об учебном дне.");
                    return $this->redirect('daily-routine');
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', "Ошибка при сохранении общей информации");
                return $this->redirect('general-info');
            }
        }
        elseif (!empty(Kids::find()->where(['user_id' => Yii::$app->user->id])->count())) //если есть записи в таблице у юзера и нет поста
        {
            $model = Kids::find()->where(['user_id' => Yii::$app->user->id])->one();
            $type_org = Organization::findOne(Yii::$app->user->identity->organization_id)->type_org;
            $sex = array('' => '', 'женский', 'мужской'); //пол
            $m_days = Days::find()->where(['<', 'id', 6])->all();
            $m_days = ArrayHelper::map($m_days, 'id', 'name');
            $m_days = ArrayHelper::merge(['' => ''], $m_days); //описываемый день
            $num_class = ['' => '', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11']; //номер класса
            $num_eat = ['' => '', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6']; //кол-во приемов пищи
            $ar_YoN = ['' => '', 'Нет', 'Да'];

            $sport_sel_name = SportsSectionName::find()->all();
            $sport_sel_name = ArrayHelper::map($sport_sel_name, 'id', 'name');
            $sport_sel_name = ArrayHelper::merge(['' => ''], $sport_sel_name); //спорт секции

            return $this->render('general-info', [
                'm_days' => $m_days,
                'sex' => $sex,
                'num_class' => $num_class,
                'num_eat' => $num_eat,
                'ar_YoN' => $ar_YoN,
                'sport_sel_name' => $sport_sel_name,
                'model' => $model,
                'type_org' => $type_org,
            ]);
        }
    }

    public function actionDailyRoutine()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу общей информации об учебном дне запрещён");
            return $this->goHome();
        }
        if (empty(Kids::find()->where(['user_id' => Yii::$app->user->id])->count()))
        {
            return $this->redirect('general-info');
        }
        if (!empty(DailyRoutine::find()->where(['user_id' => Yii::$app->user->id])->count()))
        {
            return $this->redirect('daily-routine-u');
        }

        $model = new DailyRoutine();
        //if (Yii::$app->request->post())
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $post = Yii::$app->request->post()['DailyRoutine'];

            $start_date = new DateTime('2021-05-02 ' . $post['field19']);
            $since_start = $start_date->diff(new DateTime('2021-05-03 ' . $post['field18']));
            $sleep_min = $since_start->i + ($since_start->h * 60); //время сна в минутах

            $start_date2 = new DateTime('2021-05-02 ' . $post['field20']);
            $since_start2 = $start_date2->diff(new DateTime('2021-05-03 ' . $post['field21']));
            $school_min = $since_start2->i + ($since_start2->h * 60); //время нахождения в школе в минутах

            $total = $sleep_min + $school_min + $post['field2'] + $post['field4'] + $post['field6'] + $post['field7'] + $post['field8'] + $post['field9'] + $post['field15'] + $post['field17'];

            if ($total > 1440)
            {
                Yii::$app->session->setFlash('error', "Ошибка, вы ввели более 24 часов.");
                return $this->redirect('daily-routine');
            }

            $model = new DailyRoutine();
            $u_id = Yii::$app->user->id;
            $model2 = Kids::find()->where(['user_id' => $u_id])->one();

            $model->user_id = $u_id;
            $model->kids_id = $model2->id;
            $model->field1 = $model2->charging; //Делали ли Вы зарядку? (0-нет,1-да)
            $model->field2 = $post['field2'];
            $model->field3 = $model2->additional_education; //Занимались ли Вы вчера в кружках? (0-нет,1-да)
            $model->field4 = $post['field4'];
            $model->field5 = $model2->sports_section; //Занимались ли Вы в спортивной секции?
            $model->field6 = $post['field6'];
            $model->field7 = $post['field7'];
            $model->field8 = $post['field8'];
            $model->field9 = $post['field9'];
            $model->field14 = $model2->walk; //Гуляли ли Вы вчера на улице? (0-нет,1-да)
            $model->field15 = $post['field15'];
            $model->field16 = $model2->sleep_day; //Спали ли Вы днем? (0-нет,1-да)
            $model->field17 = $post['field17'];
            $model->field18 = $post['field18'];
            $model->field19 = $post['field19'];
            $model->field20 = $post['field20'];
            $model->field21 = $post['field21'];
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', "Общая информация об учебном дне сохранена");
                return $this->redirect('day-report');
            }
            else
            {
                Yii::$app->session->setFlash('error', "Ошибка");
                return $this->redirect('daily-routine');
            }
        }

        $model2 = Kids::find()->where(['user_id' => Yii::$app->user->id])->one();
        return $this->render('daily-routine', [
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionDailyRoutineU()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу общей информации об учебном дне запрещён");
            return $this->goHome();
        }
        $u_id = Yii::$app->user->id;
        if (empty(Kids::find()->where(['user_id' => $u_id])->count()))
        {
            return $this->redirect('general-info');
        }
        if (empty(DailyRoutine::find()->where(['user_id' => $u_id])->count()))
        {
            return $this->redirect('daily-routine');
        }

        $model = DailyRoutine::find()->where(['user_id' => $u_id])->one();

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $post = Yii::$app->request->post()['DailyRoutine'];

            $start_date = new DateTime('2021-05-02 ' . $post['field19']);
            $since_start = $start_date->diff(new DateTime('2021-05-03 ' . $post['field18']));
            $sleep_min = $since_start->i + ($since_start->h * 60); //время сна в минутах

            $start_date2 = new DateTime('2021-05-02 ' . $post['field20']);
            $since_start2 = $start_date2->diff(new DateTime('2021-05-03 ' . $post['field21']));
            $school_min = $since_start2->i + ($since_start2->h * 60); //время нахождения в школе в минутах

            $total = $sleep_min + $school_min + $post['field2'] + $post['field4'] + $post['field6'] + $post['field7'] + $post['field8'] + $post['field9'] + $post['field15'] + $post['field17'];

            if ($total > 1440)
            {
                Yii::$app->session->setFlash('error', "Ошибка. Вы ввели более 24 часов.");
                return $this->redirect('daily-routine');
            }

            $model = DailyRoutine::find()->where(['user_id' => $u_id])->one();
            $model2 = Kids::find()->where(['user_id' => $u_id])->one();

            //$model->user_id = $u_id;
            //$model->kids_id = $model2->id;
            $model->field1 = $model2->charging; //Делали ли Вы зарядку? (0-нет,1-да)
            $model->field2 = $post['field2'];
            $model->field3 = $model2->additional_education; //Занимались ли Вы вчера в кружках? (0-нет,1-да)
            $model->field4 = $post['field4'];
            $model->field5 = $model2->sports_section; //Занимались ли Вы в спортивной секции?
            $model->field6 = $post['field6'];
            $model->field7 = $post['field7'];
            $model->field8 = $post['field8'];
            $model->field9 = $post['field9'];
            $model->field14 = $model2->walk; //Гуляли ли Вы вчера на улице? (0-нет,1-да)
            $model->field15 = $post['field15'];
            $model->field16 = $model2->sleep_day; //Спали ли Вы днем? (0-нет,1-да)
            $model->field17 = $post['field17'];
            $model->field18 = $post['field18'];
            $model->field19 = $post['field19'];
            $model->field20 = $post['field20'];
            $model->field21 = $post['field21'];
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', "Общая информация об учебном дне сохранена");
                return $this->redirect('day-report');
            }
            else
            {
                Yii::$app->session->setFlash('error', "Ошибка");
                return $this->redirect('daily-routine-u');
            }
        }

        $model2 = Kids::find()->where(['user_id' => Yii::$app->user->id])->one();
        return $this->render('daily-routine-u', [
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionDayReport()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу отчёта за день запрещён");
            return $this->goHome();
        }
        if (empty(DailyRoutine::find()->where(['user_id' => Yii::$app->user->id])->count()))
        {
            return $this->redirect('daily-routine');
        }

        $u_id = Yii::$app->user->id;
        $model = DailyRoutine::find()->where(['user_id' => $u_id])->one();
        $model2 = Kids::find()->where(['user_id' => $u_id])->one();

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
            print_r('Ошибка (IWC-ADR #1). Не выбран пол ребёнка в общей информации. Если после заполнения ошибка осталась - напишите на edu@niig.su приложив данную ошибку и свой идентификационный номер.');;
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
        elseif ($queteletIndexVal >= $BoysOrGirls[$model2['age']][1] && $queteletIndexVal <= $BoysOrGirls[$model2['age']][2])
        {
            $arrayChildNum = 2;
        }
        elseif ($queteletIndexVal >= $BoysOrGirls[$model2['age']][2])
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

        $start_date = new DateTime('2021-05-02' . $model['field19']);
        $since_start = $start_date->diff(new DateTime('2021-05-03' . $model['field18']));
        $sleep_min = $since_start->i + ($since_start->h * 60); //время сна в минутах
        //print_r($sleep_min);die();

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


        return $this->render('day-report', [
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
        ]);
    }
}
