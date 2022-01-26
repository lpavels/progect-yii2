<?php

namespace backend\controllers;

use common\models\ChangePersonalData;
use common\models\DailyRoutine;
use common\models\DailyRoutineFormActivity21;
use common\models\DailyRoutineNumberActivity21;
use common\models\Group;
use common\models\Kids;
use common\models\KidsActivity21;
use common\models\KidsQ;
use common\models\Menus;
use common\models\MenusActivity21;
use common\models\MenusDays;
use common\models\MenusDishes;
use common\models\MenusDishesActivity21;
use common\models\MenusNutrition;
use common\models\MenusNutritionActivity21;
use common\models\NutritionInfoActivity21;
use common\models\NutritionProcentActivity21;
use common\models\Organization;
use common\models\QuestionsResponse;
use common\models\Recalculation;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use common\models\Trainings;
use common\models\TrainingThemes;
use common\models\User;
use common\models\UsersActivity21;
use Yii;
use yii\web\Controller;
use function Symfony\Component\String\b;

class RecalculationController extends Controller
{
    public function actionCalculationSchoolVersionFirst($a, $b) //обновление информации в отчете (школьная программа(1), старая версия (1) (с двумя сайтами))
    {
        $start = microtime(true);
        $ip = Yii::$app->userHelp->ip();

        $training_ids = ['school' => 1, 'preschool' => 2]; //массив обучающих программ

        if (Yii::$app->user->can('admin')/* && ($ip == '127.0.0.1' || $ip == '87.103.250.194')*/)
        {
            ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '13312M');

            $model_recalculation = new Recalculation();
            // все юзеры старой версии школьной программы
            $users = User::find()->where(['>=', 'id', $a])->andWhere(['<=', 'id', $b])->andWhere(['version' => 1, 'training_id' => $training_ids['school']])->all();
            $usersCount = User::find()->count();
            $u_ids = [];
            foreach ($users as $user)
            {
                $u_ids[] = $user->id;
            }

            $u_editCount = 0; //количество пользователей с информацией
            $u_skipCount = 0; //количество пользователей без входного теста

            foreach ($u_ids as $u_id)
            {
                $changeName = ChangePersonalData::findOne(['user_id' => $u_id]); //Редактировалось ли ФИО

                $numberTypingStart = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 1])->orderBy(['id' => SORT_DESC])->one()->number_trying; //номер последней попытки входного теста
                $questionsResponseStart = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 1]); //входной тест

                $numberTypingFinal = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 2])->orderBy(['number_trying' => SORT_DESC])->one()->number_trying; //номер последней попытки итогового теста
                $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $numberTypingFinal]); //итоговый тест, последняя попытка

                $trainingsThemes = TrainingThemes::findAll(['training_program_id' => $training_ids['school']]); //из поля theme_program_id берем id тем, которые должны быть изучены
                $trainingsThemeCompletes = Trainings::findAll(['user_id' => $u_id]); //отмеченные темы, как изученные

                $groups = Group::findAll(['user_id' => $u_id]); #создана ли группа для дошкольников

                $ball_start = null;
                if (!empty($questionsResponseStart))//если проходили входной тест
                {
                    if ($numberTypingStart > 1)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 1 попытки во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) > 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) < 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов во входном тесте');
                        die();
                    }

                    $ball_start = $model_recalculation->ball_response_start($u_id); //количество правильных ответов во входном тесте
                }
                else
                {
                    $u_skipCount++;
                    continue;
                    //print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Не пройден входной тест');die();
                }

                $theme1 = null;
                $theme2 = null;
                $theme3 = null;
                $theme4 = null;
                $theme5 = null;
                $theme6 = null;
                if (!empty($trainingsThemeCompletes)) //если есть пройденнные темы
                {
                    $trainingsTheme_ids = []; //id тем, которые должны быть изучены
                    foreach ($trainingsThemes as $trainingsTheme) //собираем в массив темы, которые должны быть изучены
                    {
                        $trainingsTheme_ids[] = $trainingsTheme->theme_program_id; //массив id темы, которые должны быть изучены
                    }

                    $trainingsThemeComplete_ids = []; //id тем, которые были изучены пользователем
                    foreach ($trainingsThemeCompletes as $trainingsThemeComplete)//собираем в массив темы, которые были изучены
                    {
                        $trainingsThemeComplete_ids[] = $trainingsThemeComplete->theme_program_id; //массив id тем, которые были изучены

                        if ($trainingsThemeComplete->theme_program_id == 10)
                        {
                            $theme1 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 1)
                        {
                            $theme2 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 2)

                        {
                            $theme3 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 3)
                        {
                            $theme4 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 4)
                        {
                            $theme5 = 1;
                        }
                    }
                    foreach ($trainingsThemeComplete_ids as $trainingsThemeComplete_id) //проверка, на соответсвие пройденых тем выбранной программе обучения
                    {
                        $key = in_array($trainingsThemeComplete_id, $trainingsTheme_ids);
                        if ($key == false)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Пройдена несуществующая тема для данной программы обучения');
                            die();
                        }
                    }
                }

                $independent_work = null;
                $ball_end = array(null, null);
                $final_test_1st = null;
                $final_test_2st = null;
                $training_completed = null;
                $kidsq_count = null;

                if (!empty($groups))
                {
                    $array_group = [];
                    foreach ($groups as $group)
                    {
                        $array_group[] += $group->id;
                    }
                    $kidsq_count = KidsQ::find()->where(['group_id' => $array_group])->count();
                }

                $user = User::findOne($u_id);
                $org = Organization::findOne($user->organization_id);

                #Самостоятельная работа на втором сайте
                $u_activity = UsersActivity21::findOne(['key_login' => $user->key_login]);
                $kid_activity = KidsActivity21::findAll(['user_id' => $u_activity->id]);
                $menus_activity = MenusActivity21::findAll(['user_id' => $u_activity->id]);

                $indicator_activity = 0;
                $indicator_menu = 0;

                if (count($kid_activity) == 0 || count($menus_activity) == 0)
                {
                    $independent_work = 0;
                }
                elseif (count($kid_activity) == 1 && count($menus_activity) == 1)
                {
                    $menu_id = $menus_activity[0]['id'];
                    $modelMenusDishes = new MenusDishesActivity21();
                    $model_form = new DailyRoutineFormActivity21();

                    $super_total_kkal = $modelMenusDishes->get_super_total_kkal($menu_id, 'super_total'); //ккал общая
                    $energy = $model_form->get_total(DailyRoutineNumberActivity21::find()->where(['kids_id' => KidsActivity21::findOne(['user_id' => $u_activity->id])->id])->one()->id, 'check');
                    $minKkal = $energy - ($energy / 100 * 20);
                    $maxKkal = $energy + ($energy / 100 * 20);

                    if ($super_total_kkal >= $minKkal || $super_total_kkal <= $maxKkal)
                    {
                        $indicator_activity = 1;
                        $menus_nutritions = MenusNutritionActivity21::find()->where(['menu_id' => $menu_id])->all();
                        $ids = [];
                        foreach ($menus_nutritions as $m_nutrition)
                        {
                            $ids[] = $m_nutrition->nutrition_id;
                        }
                        $nutritions = NutritionInfoActivity21::find()->where(['id' => $ids])->all();
                        $second_zavtrak = MenusNutritionActivity21::find()->where(['menu_id' => $menu_id, 'nutrition_id' => 2])->count();
                        $second_uzhin = MenusNutritionActivity21::find()->where(['menu_id' => $menu_id, 'nutrition_id' => 6])->count();

                        foreach ($nutritions as $nutrition)
                        {
                            $kkal_nutrition = 0;
                            $indicator = 0;
                            if ($nutrition->id == 1 && $second_zavtrak == 0)
                            {
                                $indicator = NutritionProcentActivity21::find()->where(['nutrition_id' => 2])->one()->procent;
                            }
                            if ($nutrition->id == 5 && $second_uzhin == 0)
                            {
                                $indicator = NutritionProcentActivity21::find()->where(['nutrition_id' => 6])->one()->procent;
                            }
                            $norma = round((NutritionProcentActivity21::find()->where(['nutrition_id' => $nutrition->id])->one()->procent + $indicator) * $energy / 100);

                            $norma_minus = $norma * 0.8;
                            $norma_plus = $norma * 1.2;
                            $kkal_nutrition = round($modelMenusDishes->get_kkal_nutrition($menu_id, $nutrition->id), 1);

                            if ($kkal_nutrition > $norma_minus && $kkal_nutrition < $norma_plus)
                            {
                                $indicator_menu++;
                            }
                        }
                        if ($indicator_activity == 1 && $indicator_menu == count($ids))
                        {
                            $independent_work = 1;
                        }
                        else
                        {
                            $independent_work = 0;
                        }
                    }
                }

                if (!empty($questionsResponseFinal)) //если проходили итоговый тест
                {
                    for ($i = 1; $i <= $numberTypingFinal; $i++)
                    {
                        $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $i]); //итоговый тест
                        if (count($questionsResponseFinal) > 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                        if (count($questionsResponseFinal) < 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                    }

                    $ball_end = $model_recalculation->ball_response_end($u_id); //номер последней попытки и количество правильных ответов в итоговом тесте (массив)

                    if ($ball_end[0] != $numberTypingFinal)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Номера последних попыток не совпадают');
                        die();
                    }

                    if ($ball_end[1] > 6)
                    {
                        if ($ball_end[0] == 1)
                        {
                            $final_test_1st = 1;
                            $final_test_2st = 0;
                        }
                        elseif ($ball_end[0] > 1)
                        {
                            $final_test_1st = 0;
                            $final_test_2st = 1;
                        }

                        $training_completed = 1;
                    }
                    elseif ($ball_end[1] < 7)
                    {
                        $final_test_1st = 0;
                        $final_test_2st = 0;
                        $training_completed = 0;
                    }

                    $independent_work = 1;
                }

                $model_ReportTbl21 = ReportTbl21::findOne(['user_id' => $u_id]);
                if (empty($model_ReportTbl21))
                {
                    $checkReport = 1;
                    $model_ReportTbl21 = new ReportTbl21();
                }
                $model_ReportTbl21->user_id = $u_id;
                $model_ReportTbl21->training_id = $user->training_id;
                $model_ReportTbl21->key_login = $user->key_login;
                $model_ReportTbl21->name = $user->name;
                $model_ReportTbl21->class_number = $user->class;
                $model_ReportTbl21->letter_number = $user->bukva_klassa;
                $model_ReportTbl21->organization_id = $user->organization_id;
                $model_ReportTbl21->type_listener = $user->type_listener;
                $model_ReportTbl21->type_training = $user->type_training;
                $model_ReportTbl21->federal_district_id = $org->federal_district_id;
                $model_ReportTbl21->region_id = $org->region_id;
                $model_ReportTbl21->municipality_id = $org->municipality_id;
                $model_ReportTbl21->input_test = $ball_start;
                $model_ReportTbl21->theme1 = $theme1;
                $model_ReportTbl21->theme2 = $theme2;
                $model_ReportTbl21->theme3 = $theme3;
                $model_ReportTbl21->theme4 = $theme4;
                $model_ReportTbl21->theme5 = $theme5;
                $model_ReportTbl21->theme6 = null;
                $model_ReportTbl21->independent_work = $independent_work;
                $model_ReportTbl21->final_test = $ball_end[1];
                $model_ReportTbl21->final_test_1st = $final_test_1st;
                $model_ReportTbl21->final_test_2st = $final_test_2st;
                $model_ReportTbl21->training_completed = $training_completed;
                $model_ReportTbl21->number_children = $kidsq_count;
                $model_ReportTbl21->change_name = (!empty($changeName)) ? 1 : 0;
                if ($checkReport == 1)
                {
                    $model_ReportTbl21->created_ip = 'Автодобавление';
                }
                $model_ReportTbl21->save(false);

                $u_editCount++;
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', "Доступ запрещен");
            return $this->goHome();
        }
        $finish = microtime(true);

        return $this->render('calculation-school-version-first', [
            'a' => $a,
            'b' => $b,
            'u_editCount' => $u_editCount,
            'u_skipCount' => $u_skipCount,
            'timeStart' => $start,
            'timeEnd' => $finish,
            'usersCount' => $usersCount,
            'timeComplete' => $finish - $start,
        ]);
    }

    public function actionCalculationPreschoolVersionFirst($a, $b) //обновление информации в отчете (дошкольная программа(2), старая версия (1) (с двумя сайтами))
    {
        $start = microtime(true);
        $ip = Yii::$app->userHelp->ip();

        $training_ids = ['school' => 1, 'preschool' => 2]; //массив

        if (Yii::$app->user->can('admin')/* && ($ip == '127.0.0.1' || $ip == '87.103.250.194')*/)
        {
            ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '13312M');

            $model_recalculation = new Recalculation();
            // все юзеры старой версии школьной программы
            $users = User::find()->where(['>=', 'id', $a])->andWhere(['<=', 'id', $b])->andWhere(['version' => 1, 'training_id' => $training_ids['preschool']])->all();
            $usersCount = User::find()->count();
            $u_ids = [];
            foreach ($users as $user)
            {
                $u_ids[] = $user->id;
            }

            $u_editCount = 0; //количество пользователей с информацией
            $u_skipCount = 0; //количество пользователей без входного теста

            foreach ($u_ids as $u_id)
            {
                $changeName = ChangePersonalData::findOne(['user_id' => $u_id]); //Редактировалось ли ФИО

                $numberTypingStart = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 1])->orderBy(['id' => SORT_DESC])->one()->number_trying; //номер последней попытки входного теста
                $questionsResponseStart = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 1]); //входной тест

                $numberTypingFinal = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 2])->orderBy(['number_trying' => SORT_DESC])->one()->number_trying; //номер последней попытки итогового теста
                $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $numberTypingFinal]); //итоговый тест, последняя попытка

                $trainingsThemes = TrainingThemes::findAll(['training_program_id' => $training_ids['preschool']]); //из поля theme_program_id берем id тем, которые должны быть изучены
                $trainingsThemeCompletes = Trainings::findAll(['user_id' => $u_id]); //отмеченные темы, как изученные

                $groups = Group::findAll(['user_id' => $u_id]); #создана ли группа для дошкольников

                $ball_start = null;
                if (!empty($questionsResponseStart))//если проходили входной тест
                {
                    if ($numberTypingStart > 1)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 1 попытки во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) > 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) < 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов во входном тесте');
                        die();
                    }

                    $ball_start = $model_recalculation->ball_response_start($u_id); //количество правильных ответов во входном тесте
                }
                else
                {
                    $u_skipCount++;
                    continue;
                    //print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Не пройден входной тест');die();
                }

                $theme1 = null;
                $theme2 = null;
                $theme3 = null;
                $theme4 = null;
                $theme5 = null;
                $theme6 = null;
                if (!empty($trainingsThemeCompletes)) //если есть пройденнные темы
                {
                    $trainingsTheme_ids = []; //id тем, которые должны быть изучены
                    foreach ($trainingsThemes as $trainingsTheme) //собираем в массив темы, которые должны быть изучены
                    {
                        $trainingsTheme_ids[] = $trainingsTheme->theme_program_id; //массив id темы, которые должны быть изучены
                    }

                    $trainingsThemeComplete_ids = []; //id тем, которые были изучены пользователем
                    foreach ($trainingsThemeCompletes as $trainingsThemeComplete)//собираем в массив темы, которые были изучены
                    {
                        $trainingsThemeComplete_ids[] = $trainingsThemeComplete->theme_program_id; //массив id тем, которые были изучены

                        if ($trainingsThemeComplete->theme_program_id == 9)
                        {
                            $theme1 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 5)
                        {
                            $theme2 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 6)
                        {
                            $theme3 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 7)
                        {
                            $theme4 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 8)
                        {
                            $theme5 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 11)
                        {
                            $theme6 = 1;
                        }
                    }
                    foreach ($trainingsThemeComplete_ids as $trainingsThemeComplete_id) //проверка, на соответсвие пройденых тем выбранной программе обучения
                    {
                        $key = in_array($trainingsThemeComplete_id, $trainingsTheme_ids);
                        if ($key == false)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Пройдена несуществующая тема для данной программы обучения');
                            die();
                        }
                    }
                }

                $independent_work = null;
                $ball_end = array(null, null);
                $final_test_1st = null;
                $final_test_2st = null;
                $training_completed = null;
                $kidsq_count = null;
                if (!empty($groups))
                {
                    $array_group = [];
                    foreach ($groups as $group)
                    {
                        $array_group[] += $group->id;
                    }
                    $kidsq_count = KidsQ::find()->where(['group_id' => $array_group])->count();
                }


                $user = User::findOne($u_id);
                $org = Organization::findOne($user->organization_id);

                #Самостоятельная работа на втором сайте
                $u_activity = UsersActivity21::findOne(['key_login' => $user->key_login]);
                $kid_activity = KidsActivity21::findAll(['user_id' => $u_activity->id]);
                $menus_activity = MenusActivity21::findAll(['user_id' => $u_activity->id]);

                $indicator_activity = 0;
                $indicator_menu = 0;

                if (count($kid_activity) == 0 || count($menus_activity) == 0)
                {
                    $independent_work = 0;
                }
                elseif (count($kid_activity) == 1 && count($menus_activity) == 1)
                {
                    $menu_id = $menus_activity[0]['id'];
                    $modelMenusDishes = new MenusDishesActivity21();
                    $model_form = new DailyRoutineFormActivity21();

                    $super_total_kkal = $modelMenusDishes->get_super_total_kkal($menu_id, 'super_total'); //ккал общая
                    $energy = $model_form->get_total(DailyRoutineNumberActivity21::find()->where(['kids_id' => KidsActivity21::findOne(['user_id' => $u_activity->id])->id])->one()->id, 'check');
                    $minKkal = $energy - ($energy / 100 * 20);
                    $maxKkal = $energy + ($energy / 100 * 20);

                    if ($super_total_kkal >= $minKkal || $super_total_kkal <= $maxKkal)
                    {
                        $indicator_activity = 1;
                        $menus_nutritions = MenusNutritionActivity21::find()->where(['menu_id' => $menu_id])->all();
                        $ids = [];
                        foreach ($menus_nutritions as $m_nutrition)
                        {
                            $ids[] = $m_nutrition->nutrition_id;
                        }
                        $nutritions = NutritionInfoActivity21::find()->where(['id' => $ids])->all();
                        $second_zavtrak = MenusNutritionActivity21::find()->where(['menu_id' => $menu_id, 'nutrition_id' => 2])->count();
                        $second_uzhin = MenusNutritionActivity21::find()->where(['menu_id' => $menu_id, 'nutrition_id' => 6])->count();

                        foreach ($nutritions as $nutrition)
                        {
                            $kkal_nutrition = 0;
                            $indicator = 0;
                            if ($nutrition->id == 1 && $second_zavtrak == 0)
                            {
                                $indicator = NutritionProcentActivity21::find()->where(['nutrition_id' => 2])->one()->procent;
                            }
                            if ($nutrition->id == 5 && $second_uzhin == 0)
                            {
                                $indicator = NutritionProcentActivity21::find()->where(['nutrition_id' => 6])->one()->procent;
                            }
                            $norma = round((NutritionProcentActivity21::find()->where(['nutrition_id' => $nutrition->id])->one()->procent + $indicator) * $energy / 100);

                            $norma_minus = $norma * 0.8;
                            $norma_plus = $norma * 1.2;
                            $kkal_nutrition = round($modelMenusDishes->get_kkal_nutrition($menu_id, $nutrition->id), 1);

                            if ($kkal_nutrition > $norma_minus && $kkal_nutrition < $norma_plus)
                            {
                                $indicator_menu++;
                            }
                        }
                        if ($indicator_activity == 1 && $indicator_menu == count($ids))
                        {
                            $independent_work = 1;
                        }
                        else
                        {
                            $independent_work = 0;
                        }
                    }
                }

                if (!empty($questionsResponseFinal)) //если проходили итоговый тест
                {
                    for ($i = 1; $i <= $numberTypingFinal; $i++)
                    {
                        $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $i]); //итоговый тест
                        if (count($questionsResponseFinal) > 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                        if (count($questionsResponseFinal) < 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                    }

                    $ball_end = $model_recalculation->ball_response_end($u_id); //номер последней попытки и количество правильных ответов в итоговом тесте (массив)

                    if ($ball_end[0] != $numberTypingFinal)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Номера последних попыток не совпадают');
                        die();
                    }

                    if ($ball_end[1] > 6)
                    {
                        if ($ball_end[0] == 1)
                        {
                            $final_test_1st = 1;
                            $final_test_2st = 0;
                        }
                        elseif ($ball_end[0] > 1)
                        {
                            $final_test_1st = 0;
                            $final_test_2st = 1;
                        }

                        $training_completed = 1;
                    }
                    elseif ($ball_end[1] < 7)
                    {
                        $final_test_1st = 0;
                        $final_test_2st = 0;
                        $training_completed = 0;
                    }

                    $independent_work = 1;
                }

                $model_ReportTbl21 = ReportTbl21::findOne(['user_id' => $u_id]);
                if (empty($model_ReportTbl21))
                {
                    $checkReport = 1;
                    $model_ReportTbl21 = new ReportTbl21();
                }
                $model_ReportTbl21->user_id = $u_id;
                $model_ReportTbl21->training_id = $user->training_id;
                $model_ReportTbl21->key_login = $user->key_login;
                $model_ReportTbl21->name = $user->name;
                $model_ReportTbl21->class_number = $user->class;
                $model_ReportTbl21->letter_number = $user->bukva_klassa;
                $model_ReportTbl21->organization_id = $user->organization_id;
                $model_ReportTbl21->type_listener = $user->type_listener;
                $model_ReportTbl21->type_training = $user->type_training;
                $model_ReportTbl21->federal_district_id = $org->federal_district_id;
                $model_ReportTbl21->region_id = $org->region_id;
                $model_ReportTbl21->municipality_id = $org->municipality_id;
                $model_ReportTbl21->input_test = $ball_start;
                $model_ReportTbl21->theme1 = $theme1;
                $model_ReportTbl21->theme2 = $theme2;
                $model_ReportTbl21->theme3 = $theme3;
                $model_ReportTbl21->theme4 = $theme4;
                $model_ReportTbl21->theme5 = $theme5;
                $model_ReportTbl21->theme6 = $theme6;
                $model_ReportTbl21->independent_work = $independent_work;
                $model_ReportTbl21->final_test = $ball_end[1];
                $model_ReportTbl21->final_test_1st = $final_test_1st;
                $model_ReportTbl21->final_test_2st = $final_test_2st;
                $model_ReportTbl21->training_completed = $training_completed;
                $model_ReportTbl21->number_children = $kidsq_count;
                $model_ReportTbl21->change_name = (!empty($changeName)) ? 1 : 0;
                if ($checkReport == 1)
                {
                    $model_ReportTbl21->created_ip = 'Автодобавление';
                }
                $model_ReportTbl21->save(false);

                $u_editCount++;
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', "Доступ запрещен");
            return $this->goHome();
        }
        $finish = microtime(true);

        return $this->render('calculation-preschool-version-first', [
            'a' => $a,
            'b' => $b,
            'u_editCount' => $u_editCount,
            'u_skipCount' => $u_skipCount,
            'timeStart' => $start,
            'timeEnd' => $finish,
            'usersCount' => $usersCount,
            'timeComplete' => $finish - $start,
        ]);
    }

    public function actionCalculationSchoolVersionSecond($a, $b) //обновление информации в отчете (школьная программа(1), новая версия (2) (с одним сайтом))
    {
        $start = microtime(true);
        $ip = Yii::$app->userHelp->ip();

        $training_ids = ['school' => 1, 'preschool' => 2]; //массив

        if (Yii::$app->user->can('admin')/* && ($ip == '127.0.0.1' || $ip == '87.103.250.194')*/)
        {
            $model_recalculation = new Recalculation();
            // все юзеры старой версии школьной программы
            $users = User::find()->where(['>=', 'id', $a])->andWhere(['<=', 'id', $b])->andWhere(['version' => 2, 'training_id' => $training_ids['school']])->all();
            $usersCount = User::find()->count();
            $u_ids = [];
            foreach ($users as $user)
            {
                $u_ids[] = $user->id;
            }

            $u_editCount = 0; //количество пользователей с информацией
            $u_skipCount = 0; //количество пользователей без входного теста

            foreach ($u_ids as $u_id)
            {
                $changeName = ChangePersonalData::findOne(['user_id' => $u_id]); //Редактировалось ли ФИО

                $numberTypingStart = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 1])->orderBy(['id' => SORT_DESC])->one()->number_trying; //номер последней попытки входного теста
                $questionsResponseStart = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 1]); //входной тест

                $numberTypingFinal = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 2])->orderBy(['number_trying' => SORT_DESC])->one()->number_trying; //номер последней попытки итогового теста
                $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $numberTypingFinal]); //итоговый тест, последняя попытка

                $trainingsThemes = TrainingThemes::findAll(['training_program_id' => $training_ids['school']]); //из поля theme_program_id берем id тем, которые должны быть изучены
                $trainingsThemeCompletes = Trainings::findAll(['user_id' => $u_id]); //отмеченные темы, как изученные

                $groups = Group::findAll(['user_id' => $u_id]); #созданные группы детей пользователем

                #проверка самостоятельной работы
                $kids = Kids::findOne(['user_id' => $u_id]); #информация о ребенке
                $dailyRoutine = DailyRoutine::findOne(['user_id' => $u_id]); #режим дня
                $menus = Menus::findOne(['user_id' => $u_id]); #созданное меню
                $menusDays = MenusDays::findOne(['menu_id' => $menus->id]); #у пользователей одная запись (1), у директоров больше
                $menusNutrition = MenusNutrition::findAll(['menu_id' => $menus->id]); #количество приемов пищи
                $menusDishes = MenusDishes::findAll(['menu_id' => $menus->id]); #внесенные блюда в приемы пищи
                #проверка самостоятельной работы END

                $ball_start = null;
                if (!empty($questionsResponseStart))//если проходили входной тест
                {
                    if ($numberTypingStart > 1)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 1 попытки во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) > 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) < 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов во входном тесте');
                        die();
                    }

                    $ball_start = $model_recalculation->ball_response_start($u_id); //количество правильных ответов во входном тесте
                }
                else
                {
                    $u_skipCount++;
                    continue;
                    //print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Не пройден входной тест');die();
                }

                $theme1 = null;
                $theme2 = null;
                $theme3 = null;
                $theme4 = null;
                $theme5 = null;
                $theme6 = null;
                if (!empty($trainingsThemeCompletes)) //если есть пройденнные темы
                {
                    $trainingsTheme_ids = []; //id тем, которые должны быть изучены
                    foreach ($trainingsThemes as $trainingsTheme) //собираем в массив темы, которые должны быть изучены
                    {
                        $trainingsTheme_ids[] = $trainingsTheme->theme_program_id; //массив id темы, которые должны быть изучены
                    }

                    $trainingsThemeComplete_ids = []; //id тем, которые были изучены пользователем
                    foreach ($trainingsThemeCompletes as $trainingsThemeComplete)//собираем в массив темы, которые были изучены
                    {
                        $trainingsThemeComplete_ids[] = $trainingsThemeComplete->theme_program_id; //массив id тем, которые были изучены

                        if ($trainingsThemeComplete->theme_program_id == 10)
                        {
                            $theme1 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 1)
                        {
                            $theme2 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 2)
                        {
                            $theme3 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 3)
                        {
                            $theme4 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 4)
                        {
                            $theme5 = 1;
                        }
                    }
                    foreach ($trainingsThemeComplete_ids as $trainingsThemeComplete_id) //проверка, на соответсвие пройденых тем выбранной программе обучения
                    {
                        $key = in_array($trainingsThemeComplete_id, $trainingsTheme_ids);
                        if ($key == false)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Пройдена несуществующая тема для данной программы обучения');
                            die();
                        }
                    }
                }

                $independent_work = null;
                if (!empty($kids) && !empty($dailyRoutine) && !empty($menus) && !empty($menusDays) && !empty($menusNutrition) && !empty($menusDishes)) #если заполнены все данные в самостоятельной работе
                {
                    $menu = new MenusDishes();
                    $total_kkal = $menu->get_super_total_kkal($menus->id, 1, 1, 'super_total');
                    if ($total_kkal > 1200)
                    {
                        $independent_work = 1;
                    } #если внесено блюд более, чем на 1200 ккалорий, то самостоятельная выполнена
                }

                $ball_end = array(null, null);
                $final_test_1st = null;
                $final_test_2st = null;
                $training_completed = null;
                $kidsq_count = null;
                if (!empty($groups))
                {
                    $array_group = [];
                    foreach ($groups as $group)
                    {
                        $array_group[] += $group->id;
                    }
                    $kidsq_count = KidsQ::find()->where(['group_id' => $array_group])->count();
                }

                if (!empty($questionsResponseFinal)) //если проходили итоговый тест
                {
                    for ($i = 1; $i <= $numberTypingFinal; $i++)
                    {
                        $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $i]); //итоговый тест
                        if (count($questionsResponseFinal) > 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                        if (count($questionsResponseFinal) < 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                    }

                    $ball_end = $model_recalculation->ball_response_end($u_id); //номер последней попытки и количество правильных ответов в итоговом тесте (массив)

                    if ($ball_end[0] != $numberTypingFinal)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Номера последних попыток не совпадают');
                        die();
                    }

                    if ($ball_end[1] > 6)
                    {
                        if ($ball_end[0] == 1)
                        {
                            $final_test_1st = 1;
                            $final_test_2st = 0;
                        }
                        elseif ($ball_end[0] > 1)
                        {
                            $final_test_1st = 0;
                            $final_test_2st = 1;
                        }

                        $training_completed = 1;
                    }
                    elseif ($ball_end[1] < 7)
                    {
                        $final_test_1st = 0;
                        $final_test_2st = 0;
                        $training_completed = 0;
                    }

                    $independent_work = 1;
                }

                $user = User::findOne($u_id);
                $org = Organization::findOne($user->organization_id);

                $model_ReportTbl21 = ReportTbl21::findOne(['user_id' => $u_id]);
                if (empty($model_ReportTbl21))
                {
                    $checkReport = 1;
                    $model_ReportTbl21 = new ReportTbl21();
                }
                $model_ReportTbl21->user_id = $u_id;
                $model_ReportTbl21->training_id = $user->training_id;
                $model_ReportTbl21->key_login = $user->key_login;
                $model_ReportTbl21->name = $user->name;
                $model_ReportTbl21->class_number = $user->class;
                $model_ReportTbl21->letter_number = $user->bukva_klassa;
                $model_ReportTbl21->organization_id = $user->organization_id;
                $model_ReportTbl21->type_listener = $user->type_listener;
                $model_ReportTbl21->type_training = $user->type_training;
                $model_ReportTbl21->federal_district_id = $org->federal_district_id;
                $model_ReportTbl21->region_id = $org->region_id;
                $model_ReportTbl21->municipality_id = $org->municipality_id;
                $model_ReportTbl21->input_test = $ball_start;
                $model_ReportTbl21->theme1 = $theme1;
                $model_ReportTbl21->theme2 = $theme2;
                $model_ReportTbl21->theme3 = $theme3;
                $model_ReportTbl21->theme4 = $theme4;
                $model_ReportTbl21->theme5 = $theme5;
                $model_ReportTbl21->theme6 = null;
                $model_ReportTbl21->independent_work = $independent_work;
                $model_ReportTbl21->final_test = $ball_end[1];
                $model_ReportTbl21->final_test_1st = $final_test_1st;
                $model_ReportTbl21->final_test_2st = $final_test_2st;
                $model_ReportTbl21->training_completed = $training_completed;
                $model_ReportTbl21->number_children = $kidsq_count;
                $model_ReportTbl21->change_name = (!empty($changeName)) ? 1 : 0;
                if ($checkReport == 1)
                {
                    $model_ReportTbl21->created_ip = 'Автодобавление';
                }
                $model_ReportTbl21->save(false);

                $u_editCount++;
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', "Доступ запрещен");
            return $this->goHome();
        }
        $finish = microtime(true);

        return $this->render('calculation-school-version-second', [
            'a' => $a,
            'b' => $b,
            'u_editCount' => $u_editCount,
            'u_skipCount' => $u_skipCount,
            'timeStart' => $start,
            'timeEnd' => $finish,
            'usersCount' => $usersCount,
            'timeComplete' => $finish - $start,
        ]);
    }

    public function actionCalculationPreschoolVersionSecond($a, $b) //обновление информации в отчете (дошкольная программа(2), новая версия (2) (с одним сайтом))
    {
        $start = microtime(true);
        $ip = Yii::$app->userHelp->ip();

        $training_ids = ['school' => 1, 'preschool' => 2]; //массив

        if (Yii::$app->user->can('admin')/* && ($ip == '127.0.0.1' || $ip == '87.103.250.194')*/)
        {
            $model_recalculation = new Recalculation();
            // все юзеры старой версии школьной программы
            $users = User::find()->where(['>=', 'id', $a])->andWhere(['<=', 'id', $b])->andWhere(['version' => 2, 'training_id' => $training_ids['preschool']])->all();
            $usersCount = User::find()->count();
            $u_ids = [];
            foreach ($users as $user)
            {
                $u_ids[] = $user->id;
            }

            $u_editCount = 0; //количество пользователей с информацией
            $u_skipCount = 0; //количество пользователей без входного теста

            foreach ($u_ids as $u_id)
            {
                $changeName = ChangePersonalData::findOne(['user_id' => $u_id]); //Редактировалось ли ФИО

                $numberTypingStart = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 1])->orderBy(['id' => SORT_DESC])->one()->number_trying; //номер последней попытки входного теста
                $questionsResponseStart = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 1]); //входной тест

                $numberTypingFinal = QuestionsResponse::find()->where(['user_id' => $u_id, 'status' => 2])->orderBy(['number_trying' => SORT_DESC])->one()->number_trying; //номер последней попытки итогового теста
                $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $numberTypingFinal]); //итоговый тест, последняя попытка

                $trainingsThemes = TrainingThemes::findAll(['training_program_id' => $training_ids['preschool']]); //из поля theme_program_id берем id тем, которые должны быть изучены
                $trainingsThemeCompletes = Trainings::findAll(['user_id' => $u_id]); //отмеченные темы, как изученные

                $groups = Group::findAll(['user_id' => $u_id]); #создана ли группа для дошкольников

                #проверка самостоятельной работы
                $kids = Kids::findOne(['user_id' => $u_id]); #информация о ребенке
                $dailyRoutine = DailyRoutine::findOne(['user_id' => $u_id]); #режим дня
                $menus = Menus::findOne(['user_id' => $u_id]); #созданное меню
                $menusDays = MenusDays::findOne(['menu_id' => $menus->id]); #у пользователей одная запись (1), у директоров больше
                $menusNutrition = MenusNutrition::findAll(['menu_id' => $menus->id]); #количество приемов пищи
                $menusDishes = MenusDishes::findAll(['menu_id' => $menus->id]); #внесенные блюда в приемы пищи
                #проверка самостоятельной работы END

                $ball_start = null;
                if (!empty($questionsResponseStart))//если проходили входной тест
                {
                    if ($numberTypingStart > 1)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 1 попытки во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) > 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов во входном тесте');
                        die();
                    }
                    if (count($questionsResponseStart) < 10)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов во входном тесте');
                        die();
                    }

                    $ball_start = $model_recalculation->ball_response_start($u_id); //количество правильных ответов во входном тесте
                }
                else
                {
                    $u_skipCount++;
                    continue;
                    //print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Не пройден входной тест');die();
                }

                $theme1 = null;
                $theme2 = null;
                $theme3 = null;
                $theme4 = null;
                $theme5 = null;
                $theme6 = null;
                if (!empty($trainingsThemeCompletes)) //если есть пройденнные темы
                {
                    $trainingsTheme_ids = []; //id тем, которые должны быть изучены
                    foreach ($trainingsThemes as $trainingsTheme) //собираем в массив темы, которые должны быть изучены
                    {
                        $trainingsTheme_ids[] = $trainingsTheme->theme_program_id; //массив id темы, которые должны быть изучены
                    }

                    $trainingsThemeComplete_ids = []; //id тем, которые были изучены пользователем
                    foreach ($trainingsThemeCompletes as $trainingsThemeComplete)//собираем в массив темы, которые были изучены
                    {
                        $trainingsThemeComplete_ids[] = $trainingsThemeComplete->theme_program_id; //массив id тем, которые были изучены

                        if ($trainingsThemeComplete->theme_program_id == 9)
                        {
                            $theme1 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 5)
                        {
                            $theme2 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 6)
                        {
                            $theme3 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 7)
                        {
                            $theme4 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 8)
                        {
                            $theme5 = 1;
                        }
                        elseif ($trainingsThemeComplete->theme_program_id == 11)
                        {
                            $theme6 = 1;
                        }
                    }
                    foreach ($trainingsThemeComplete_ids as $trainingsThemeComplete_id) //проверка, на соответсвие пройденых тем выбранной программе обучения
                    {
                        $key = in_array($trainingsThemeComplete_id, $trainingsTheme_ids);
                        if ($key == false)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Пройдена несуществующая тема для данной программы обучения');
                            die();
                        }
                    }
                }

                $independent_work = null;
                if (!empty($kids) && !empty($dailyRoutine) && !empty($menus) && !empty($menusDays) && !empty($menusNutrition) && !empty($menusDishes)) #если заполнены все данные в самостоятельной работе
                {
                    $menu = new MenusDishes();
                    $total_kkal = $menu->get_super_total_kkal($menus->id, 1, 1, 'super_total');
                    if ($total_kkal > 1200)
                    {
                        $independent_work = 1;
                    } #если внесено блюд более, чем на 1200 ккалорий, то самостоятельная выполнена
                }

                $ball_end = array(null, null);
                $final_test_1st = null;
                $final_test_2st = null;
                $training_completed = null;
                $kidsq_count = null;
                if (!empty($groups))
                {
                    $array_group = [];
                    foreach ($groups as $group)
                    {
                        $array_group[] += $group->id;
                    }
                    $kidsq_count = KidsQ::find()->where(['group_id' => $array_group])->count();
                }

                if (!empty($questionsResponseFinal)) //если проходили итоговый тест
                {
                    for ($i = 1; $i <= $numberTypingFinal; $i++)
                    {
                        $questionsResponseFinal = QuestionsResponse::findAll(['user_id' => $u_id, 'status' => 2, 'number_trying' => $i]); //итоговый тест
                        if (count($questionsResponseFinal) > 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Более 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                        if (count($questionsResponseFinal) < 10)
                        {
                            print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Меньше 10 ответов в итоговом тесте. Попытка №' . $i);
                            die();
                        }
                    }

                    $ball_end = $model_recalculation->ball_response_end($u_id); //номер последней попытки и количество правильных ответов в итоговом тесте (массив)

                    if ($ball_end[0] != $numberTypingFinal)
                    {
                        print_r('user_id: ' . $u_id . '<br>' . 'key_login: ' . User::findOne($u_id)->key_login . '<br>Номера последних попыток не совпадают');
                        die();
                    }

                    if ($ball_end[1] > 6)
                    {
                        if ($ball_end[0] == 1)
                        {
                            $final_test_1st = 1;
                            $final_test_2st = 0;
                        }
                        elseif ($ball_end[0] > 1)
                        {
                            $final_test_1st = 0;
                            $final_test_2st = 1;
                        }

                        $training_completed = 1;
                    }
                    elseif ($ball_end[1] < 7)
                    {
                        $final_test_1st = 0;
                        $final_test_2st = 0;
                        $training_completed = 0;
                    }

                    $independent_work = 1;
                }

                $user = User::findOne($u_id);
                $org = Organization::findOne($user->organization_id);

                $model_ReportTbl21 = ReportTbl21::findOne(['user_id' => $u_id]);
                if (empty($model_ReportTbl21))
                {
                    $checkReport = 1;
                    $model_ReportTbl21 = new ReportTbl21();
                }
                $model_ReportTbl21->user_id = $u_id;
                $model_ReportTbl21->training_id = $user->training_id;
                $model_ReportTbl21->key_login = $user->key_login;
                $model_ReportTbl21->name = $user->name;
                $model_ReportTbl21->class_number = $user->class;
                $model_ReportTbl21->letter_number = $user->bukva_klassa;
                $model_ReportTbl21->organization_id = $user->organization_id;
                $model_ReportTbl21->type_listener = $user->type_listener;
                $model_ReportTbl21->type_training = $user->type_training;
                $model_ReportTbl21->federal_district_id = $org->federal_district_id;
                $model_ReportTbl21->region_id = $org->region_id;
                $model_ReportTbl21->municipality_id = $org->municipality_id;
                $model_ReportTbl21->input_test = $ball_start;
                $model_ReportTbl21->theme1 = $theme1;
                $model_ReportTbl21->theme2 = $theme2;
                $model_ReportTbl21->theme3 = $theme3;
                $model_ReportTbl21->theme4 = $theme4;
                $model_ReportTbl21->theme5 = $theme5;
                $model_ReportTbl21->theme6 = $theme6;
                $model_ReportTbl21->independent_work = $independent_work;
                $model_ReportTbl21->final_test = $ball_end[1];
                $model_ReportTbl21->final_test_1st = $final_test_1st;
                $model_ReportTbl21->final_test_2st = $final_test_2st;
                $model_ReportTbl21->training_completed = $training_completed;
                $model_ReportTbl21->number_children = $kidsq_count;
                $model_ReportTbl21->change_name = (!empty($changeName)) ? 1 : 0;
                if ($checkReport == 1)
                {
                    $model_ReportTbl21->created_ip = 'Автодобавление';
                }
                $model_ReportTbl21->save(false);

                $u_editCount++;
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', "Доступ запрещен");
            return $this->goHome();
        }
        $finish = microtime(true);

        return $this->render('calculation-preschool-version-second', [
            'a' => $a,
            'b' => $b,
            'u_editCount' => $u_editCount,
            'u_skipCount' => $u_skipCount,
            'timeStart' => $start,
            'timeEnd' => $finish,
            'usersCount' => $usersCount,
            'timeComplete' => $finish - $start,
        ]);
    }
}
