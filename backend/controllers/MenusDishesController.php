<?php

namespace backend\controllers;

use common\models\Allergen;
use common\models\AllergenForm;
use common\models\Dishes;
use common\models\Kids;
use common\models\ProductsAllergen;
use common\models\RaskladkaForm;
use common\models\RecipesCollection;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\
{Font, Border, Alignment
};
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Yii;
use common\models\MenusDishes;
use common\models\Menus;
use common\models\AgeInfo;
use common\models\NutritionInfo;
use common\models\MenusNutrition;
use common\models\MenusDays;
use common\models\DishesProducts;
use common\models\FactdateForm;
use common\models\Days;
use common\models\Products;
use common\models\ProductsCategory;
use common\models\FeedersCharacters;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;


class MenusDishesController extends Controller
{
    const YAER_2021 = 2021;
    const YAER_2022 = 2022;

    // public $enableCsrfValidation = false;
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
        if (!(Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun')))
        {
            Yii::$app->session->setFlash('error', "Доступ к данному разделу вам запрещен.");
            return $this->goHome();
        }
        $model = new MenusDishes();
        //$menus_dishes = MenusDishes::find()->where(['menu_id' => 21, 'cycle' => 2, 'days_id' => 1])->orderby(['nutrition_id'=>SORT_ASC])->all();


        if (Yii::$app->request->post())
        {
            //$nutritions = NutritionInfo::find()->all();
            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

            $menus_dishes = MenusDishes::find()->where(['menu_id' => $post['menu_id'], 'cycle' => $post['cycle'], 'days_id' => $post['days_id'], 'date_fact_menu' => 0])->orderby(['nutrition_id' => SORT_ASC])->all();

            return $this->render('index', [
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'model' => $model,
                'post' => $post,
            ]);
        }


        return $this->render('index', [
            //'menus_dishes' => $menus_dishes,
            //'nutritions' => $nutritions,
            'model' => $model,
        ]);
    }

    public function actionIndexUser()
    {
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('director') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            Yii::$app->session->setFlash('error', "Доступ к данному разделу вам запрещен.");
            return $this->goHome();
        }

        if (empty(Kids::find()->where(['user_id' => Yii::$app->user->id])->exists()))
        {
            Yii::$app->session->setFlash('error', "Заполните общую информацию о ребёнке.");
            return $this->redirect('/independent-work/general-info');
        }

        $model = new MenusDishes();

        $menu_id = Menus::find()->where(['user_id' => Yii::$app->user->id])->one()->id;
        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }

        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

        $menus_dishes = MenusDishes::find()->where(['menu_id' => Menus::find()->where(['user_id' => Yii::$app->user->id])->one()->id, 'date_fact_menu' => 0])->orderby(['nutrition_id' => SORT_ASC])->all();

        return $this->render('index-user', [
            'menu_id' => $menu_id,
            'menus_dishes' => $menus_dishes,
            'nutritions' => $nutritions,
            'model' => $model,
        ]);
    }

    //МЕТОД ДДЯ ДОБАВЛЕНИЯ БЛЮДА В ЦИКЛИЧЕСКОЕ И ФАКТИЧЕСКОЕ МЕНЮ
    public function actionSaving()
    {
        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $dishes = Dishes::findOne($post['dishes_id']);
        if (empty($post['yield']))
        {
            return 'error1';
        }

        if (empty($dishes))
        {
            return 'error2';
        }
        /*СОЗДАЛИ ЧАСТЬ НОВОГО БЛЮДА, НО МЫ ЕЩЕ НЕ ЗНАЕМ ДЛЯ КАКОГО ОНО МЕНЮ(ФАКТИЧ ИЛИ ЦИКЛИЧ)*/
        $menu = new MenusDishes();
        $menu->menu_id = $post['menu_id'];
        $menu->cycle = $post['cycle'];
        $menu->days_id = $post['days_id'];
        $menu->nutrition_id = $post['nutrition_id'];
        $menu->dishes_id = $post['dishes_id'];
        $menu->yield = $post['yield'];
        /*КОНЕЦ ЧАСТИ СОЗД*/
        /*ЕСЛИ ДАТА БОЛЬШЕ НУЛЯ, ТО БЛЮДО ДЛЯ ФАКТИЧЕСКОГО МЕНЮ*/
        if ($post['date'] > 0)
        {
            /*ПОИСК ФАКТИЧЕСКОГО МЕНЮ*/
            $m_dish_fact = MenusDishes::find()->where(['date_fact_menu' => $post['date'], 'menu_id' => $post['menu_id']])->all();
            /*ЕСЛИ НЕ НАШЛОСЬ, ТОГДА ПЕРЕБИРАЕМ ЦИКЛИЧЕСКОЕ И ПО ЕГО ПОДОБИЮ СОЗДАЕМ ФАКТИЧЕСКОГО*/
            if (empty($m_dish_fact))
            {
                $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $post['cycle'], 'days_id' => $post['days_id']])->all();
                foreach ($menus_dishes as $m_dish)
                {
                    $model = new MenusDishes();
                    $model->menu_id = $post['menu_id'];
                    $model->cycle = $post['cycle'];
                    $model->days_id = $post['days_id'];
                    $model->nutrition_id = $m_dish->nutrition_id;
                    $model->dishes_id = $m_dish->dishes_id;
                    $model->yield = $m_dish->yield;
                    $model->date_fact_menu = $post['date'];
                    $model->save();
                }
            }
        }
        /*А ЕСЛИ ДАТА БЫЛО <> 0 МЫ ЗАКАНЧИВАЕМ С СОЗДАНИЕМ НОВОГО БЛЮДА И СОХРАНЯЕМ ЕГО. ЗА ОТЛИЧИЕ МЕЖДУ ФАКТИЧ И ЦИКЛ
        МЕНЮ ИСПОЛЬЗУЮТСЯ ПОЛЕ date_fact_menu (0 ЦИКЛИЧЕСКОЕ, В ИНОМ СЛУЧ - ФАКТИЧ)*/


        $menu->date_fact_menu = $post['date'];
        $menu->save();
        /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ
        НЕНУЖЕНОЕ ПОЛЕ created_at*/
        $menu->created_at = $menu->get_dishes($post['dishes_id']);
        return $menu;
    } //для директоров

    public function actionSavingUser() //для самостоятельной работы
    {
        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $dishes = Dishes::findOne($post['dishes_id']);
        if (empty($post['yield']))
        {
            return 'error1';
        }
        if (empty($dishes))
        {
            return 'error2';
        }

        if ($post['yield'] > 1000 || $post['yield'] < 1)
        {
            Yii::$app->session->setFlash('error', "Произошла ошибка.");
            return 'error3';
        }
        /*СОЗДАЛИ ЧАСТЬ НОВОГО БЛЮДА, НО МЫ ЕЩЕ НЕ ЗНАЕМ ДЛЯ КАКОГО ОНО МЕНЮ(ФАКТИЧ ИЛИ ЦИКЛИЧ)*/
        $menu = new MenusDishes();
        $menu->menu_id = $post['menu_id'];
        $menu->nutrition_id = $post['nutrition_id'];
        $menu->dishes_id = $post['dishes_id'];
        $menu->type_food = $post['type_food'];
        $menu->yield = $post['yield'];

        $menu->date_fact_menu = '0'; //не нужно
        $menu->cycle = '1'; //не нужно
        $menu->days_id = '1'; //не нужно
        $menu->save();
        /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ
        НЕНУЖЕНОЕ ПОЛЕ created_at*/
        $menu->created_at = $menu->get_dishes($post['dishes_id']);

        $total_kkal = $menu->get_super_total_kkal($post['menu_id'], 1, 1, 'super_total');
        if ($total_kkal > 1200)
        {
            if (date('Y') == self::YAER_2021)
            {
                $report_tbl = ReportTbl21::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
            {
                $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();

                if (empty($report_tbl))
                {
                    Yii::$app->userHelp->checkDataReportTable();
                    $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
                }
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
            {
                $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
            else
            {
                print_r('Ошибка. Обратиесь к администратору');
                die();
            }

            $report_tbl->independent_work = '1';
            $report_tbl->save();
        }
        else
        {
            if (date('Y') == self::YAER_2021)
            {
                $report_tbl = ReportTbl21::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
            {
                $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();

                if (empty($report_tbl))
                {
                    Yii::$app->userHelp->checkDataReportTable();
                    $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
                }
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
            {
                $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
            else
            {
                print_r('Ошибка. Обратиесь к администратору');
                die();
            }

            $report_tbl->independent_work = '0';
            $report_tbl->save();
        }

        return $menu;
    }

    //МЕТОД ДЛЯ ОБНОВЛЕНИЯ БЛЮДА ДЛЯ ЦИКЛИЧЕСКОГО И ФАКТИЧЕСКОГО МЕНЮ
    public function actionUpdating()
    {
        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $menu = MenusDishes::findOne($post['menusdishes_id']);
        //ЕСЛИ ПРИШЕДШАЯ ДАТА РАВНА НУЛЮ, ЭТО ЗНАЧИТ ЧТО МЫ РАБОТАЕМ С ЦИКЛИЧЕСКИМ МЕНЮ И ЗНАЧИТ ПРОСТО МЕНЯЕМ ЕГО ПОЛЯ НА НОВЫЕ

        if ($post['date'] == 0)
        {
            //$menu->dishes_id = $post['dishes_id'];
            $menu->yield = $post['yield'];
            $menu->date_fact_menu = $post['date'];
            $menu->save();
            /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ ВО ВЬЮШКЕ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ НЕНУЖЕНОЕ ПОЛЕ created_at*/
            $menu->created_at = $menu->get_dishes($menu->dishes_id);

            return $menu;
        }
        //ЕСЛИ ПРИШЕДШАЯ ДАТА БОЛЬШЕ НУЛЯ, ЭТО ЗНАЧИТ ЧТО МЫ РАБОТАЕМ С ФАКТИЧЕСКИМ МЕНЮ. пОЭТОМУ
        //ПЕРЕД ОБНОВЛЕНИЕМ НУЖНО ПРОВЕРИТЬ СУЩЕСТВУЕТ ОНОН ИЛИ НЕТ. ЕСЛИ НЕТ, ТО ДЕЛАЕМ КОПИЮ ЦИКЛИЧЕСКОГО И ОБНОВЛЯЕМ
        //УКАЗАННОЕ БЛЮДО. а ЕСЛИ ДА, ТО ПРОСТО ОБНОВЛЯЕМ БЛЮДО ФАКТИЧЕСКОГО МКЕНЮ
        if ($post['date'] > 0)
        {
            $m_dish_fact = MenusDishes::find()->where(['date_fact_menu' => $post['date'], 'menu_id' => $menu->menu_id])->all();
            if (empty($m_dish_fact))
            {
                $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu->menu_id, 'cycle' => $menu->cycle, 'days_id' => $menu->days_id])->all();
                foreach ($menus_dishes as $m_dish)
                {   //ТАК МЫ ПОЙМЕМ, ЧТО ЭТО БЛЮДО КОТОРОЕ НУЖНО ОБНОВИТЬ(НО ОНО ЦИКЛИЧЕСКОЕ, ПОЭТОМУ ПЕРЕСОЗДАЕМ ЕГО КАК ФАКТИЧЕСКОЕ
                    // И СРАЗУ ЖЕ ОБНОВЛЯЕМ И СОХРАНЯЕМ. А В ЭЛСЕ МЫ ПЕРЕСОЗДАЕМ ВСЕ ОСТАЛЬНЫЕ БЛЮДА ПО ПОДОБИЮ ЦИКЛИЧЕСКОГО)
                    if ($m_dish->id == $post['menusdishes_id'])
                    {
                        $model_upd = new MenusDishes();
                        $model_upd->menu_id = $m_dish->menu_id;
                        $model_upd->cycle = $m_dish->cycle;
                        $model_upd->days_id = $m_dish->days_id;
                        $model_upd->nutrition_id = $m_dish->nutrition_id;
                        $model_upd->dishes_id = $post['dishes_id'];
                        $model_upd->yield = $post['yield'];
                        $model_upd->date_fact_menu = $post['date'];
                        $model_upd->save(false);
                        /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ ВО ВЬЮШКЕ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ
                        НЕНУЖЕНОЕ ПОЛЕ created_at*/
                        $model_upd->created_at = $model_upd->get_dishes($post['dishes_id']);
                    }
                    else
                    {
                        $model = new MenusDishes();
                        $model->menu_id = $m_dish->menu_id;
                        $model->cycle = $m_dish->cycle;
                        $model->days_id = $m_dish->days_id;
                        $model->nutrition_id = $m_dish->nutrition_id;
                        $model->dishes_id = $m_dish->dishes_id;
                        $model->yield = $m_dish->yield;
                        $model->date_fact_menu = $post['date'];
                        $model->save(false);
                    }
                }
                return $model_upd;
            }
            //ЭТО НА СЛУЧАЙ ТОГО ЧТО ФАКТИЧЕСКОЕ МЕНЮ УЖЕ СОЗДАНО И МЫ ПРОСТО ОБНОВЛЯЕМ ЭТО БЛЮДО ИЗ ФАКТИЧЕСКОЕ МЕНЮ
            else
            {
                $menu->dishes_id = $post['dishes_id'];
                $menu->yield = $post['yield'];
                $menu->date_fact_menu = $post['date'];
                $menu->save();
                /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ ВО ВЬЮШКЕ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ
                НЕНУЖЕНОЕ ПОЛЕ created_at*/
                $menu->created_at = $menu->get_dishes($post['dishes_id']);
                return $menu;
            }
        }
    }

    public function actionUpdatingUser()
    {
        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $menu = MenusDishes::findOne($post['menusdishes_id']);
        //ЕСЛИ ПРИШЕДШАЯ ДАТА РАВНА НУЛЮ, ЭТО ЗНАЧИТ ЧТО МЫ РАБОТАЕМ С ЦИКЛИЧЕСКИМ МЕНЮ И ЗНАЧИТ ПРОСТО МЕНЯЕМ ЕГО ПОЛЯ НА НОВЫЕ
        if ($post['yield'] > 501 || $post['yield'] < 1)
        {
            return 'error3';
        }
        if ($post['date'] == 0)
        {
            //$menu->dishes_id = $post['dishes_id'];
            $menu->yield = $post['yield'];
            $menu->date_fact_menu = $post['date'];
            $menu->save();
            /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ ВО ВЬЮШКЕ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ НЕНУЖЕНОЕ ПОЛЕ created_at*/
            $menu->created_at = $menu->get_dishes($menu->dishes_id);

            $total_kkal = $menu->get_super_total_kkal(Menus::find()->where(['user_id' => Yii::$app->user->id])->one()->id, 1, 1, 'super_total');
            if ($total_kkal > 1200)
            {
                if (date('Y') == self::YAER_2021)
                {
                    $report_tbl = ReportTbl21::find()->where(['user_id' => Yii::$app->user->id])->one();
                }
                elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
                {
                    $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();

                    if (empty($report_tbl))
                    {
                        Yii::$app->userHelp->checkDataReportTable();
                        $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
                    }
                }
                elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
                {
                    $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
                }
                else
                {
                    print_r('Ошибка. Обратиесь к администратору');
                    die();
                }
                $report_tbl->independent_work = '1';
                $report_tbl->save();
            }
            else
            {
                if (date('Y') == self::YAER_2021)
                {
                    $report_tbl = ReportTbl21::find()->where(['user_id' => Yii::$app->user->id])->one();
                }
                elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
                {
                    $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();

                    if (empty($report_tbl))
                    {
                        Yii::$app->userHelp->checkDataReportTable();
                        $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
                    }
                }
                elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
                {
                    $report_tbl = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
                }
                else
                {
                    print_r('Ошибка. Обратиесь к администратору');
                    die();
                }
                $report_tbl->independent_work = '0';
                $report_tbl->save();
            }

            return $menu;
        }
        //ЕСЛИ ПРИШЕДШАЯ ДАТА БОЛЬШЕ НУЛЯ, ЭТО ЗНАЧИТ ЧТО МЫ РАБОТАЕМ С ФАКТИЧЕСКИМ МЕНЮ. пОЭТОМУ
        //ПЕРЕД ОБНОВЛЕНИЕМ НУЖНО ПРОВЕРИТЬ СУЩЕСТВУЕТ ОНОН ИЛИ НЕТ. ЕСЛИ НЕТ, ТО ДЕЛАЕМ КОПИЮ ЦИКЛИЧЕСКОГО И ОБНОВЛЯЕМ
        //УКАЗАННОЕ БЛЮДО. а ЕСЛИ ДА, ТО ПРОСТО ОБНОВЛЯЕМ БЛЮДО ФАКТИЧЕСКОГО МКЕНЮ
        if ($post['date'] > 0)
        {
            $m_dish_fact = MenusDishes::find()->where(['date_fact_menu' => $post['date'], 'menu_id' => $menu->menu_id])->all();
            if (empty($m_dish_fact))
            {
                $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu->menu_id, 'cycle' => $menu->cycle, 'days_id' => $menu->days_id])->all();
                foreach ($menus_dishes as $m_dish)
                {   //ТАК МЫ ПОЙМЕМ, ЧТО ЭТО БЛЮДО КОТОРОЕ НУЖНО ОБНОВИТЬ(НО ОНО ЦИКЛИЧЕСКОЕ, ПОЭТОМУ ПЕРЕСОЗДАЕМ ЕГО КАК ФАКТИЧЕСКОЕ
                    // И СРАЗУ ЖЕ ОБНОВЛЯЕМ И СОХРАНЯЕМ. А В ЭЛСЕ МЫ ПЕРЕСОЗДАЕМ ВСЕ ОСТАЛЬНЫЕ БЛЮДА ПО ПОДОБИЮ ЦИКЛИЧЕСКОГО)
                    if ($m_dish->id == $post['menusdishes_id'])
                    {
                        $model_upd = new MenusDishes();
                        $model_upd->menu_id = $m_dish->menu_id;
                        $model_upd->cycle = $m_dish->cycle;
                        $model_upd->days_id = $m_dish->days_id;
                        $model_upd->nutrition_id = $m_dish->nutrition_id;
                        $model_upd->dishes_id = $post['dishes_id'];
                        $model_upd->yield = $post['yield'];
                        $model_upd->date_fact_menu = $post['date'];
                        $model_upd->save(false);
                        /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ ВО ВЬЮШКЕ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ
                        НЕНУЖЕНОЕ ПОЛЕ created_at*/
                        $model_upd->created_at = $model_upd->get_dishes($post['dishes_id']);
                    }
                    else
                    {
                        $model = new MenusDishes();
                        $model->menu_id = $m_dish->menu_id;
                        $model->cycle = $m_dish->cycle;
                        $model->days_id = $m_dish->days_id;
                        $model->nutrition_id = $m_dish->nutrition_id;
                        $model->dishes_id = $m_dish->dishes_id;
                        $model->yield = $m_dish->yield;
                        $model->date_fact_menu = $post['date'];
                        $model->save(false);
                    }
                }
                return $model_upd;
            }
            //ЭТО НА СЛУЧАЙ ТОГО ЧТО ФАКТИЧЕСКОЕ МЕНЮ УЖЕ СОЗДАНО И МЫ ПРОСТО ОБНОВЛЯЕМ ЭТО БЛЮДО ИЗ ФАКТИЧЕСКОЕ МЕНЮ
            else
            {
                $menu->dishes_id = $post['dishes_id'];
                $menu->yield = $post['yield'];
                $menu->date_fact_menu = $post['date'];
                $menu->save();
                /*created_at ТАК КАК В ТАБЛИЦЕ НЕТ ПОЛЯ ДЛЯ НАЗВАНИЯ БЛЮДА, А НАЗВАНИЕ ВЕРНУТЬ НУЖНО В НАШУ ТАБЛИЦУ ВО ВЬЮШКЕ, ТО БУДЕМ ИСПОЛЬЗОВАТЬ
                НЕНУЖЕНОЕ ПОЛЕ created_at*/
                $menu->created_at = $menu->get_dishes($post['dishes_id']);
                return $menu;
            }
        }
    }


    public function actionArchiveIndex()
    {
        $model = new MenusDishes();
        //$menus_dishes = MenusDishes::find()->where(['menu_id' => 21, 'cycle' => 2, 'days_id' => 1])->orderby(['nutrition_id'=>SORT_ASC])->all();


        if (Yii::$app->request->post())
        {
            //$nutritions = NutritionInfo::find()->all();
            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
            $menus_dishes = MenusDishes::find()->where(['menu_id' => $post['menu_id'], 'cycle' => $post['cycle'], 'days_id' => $post['days_id'], 'date_fact_menu' => 0])->orderby(['nutrition_id' => SORT_ASC])->all();
            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();
            return $this->render('archive-dishes', [
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'model' => $model,
                'post' => $post,
            ]);
        }
        return $this->render('archive-dishes', [
            //'menus_dishes' => $menus_dishes,
            //'nutritions' => $nutritions,
            'model' => $model,
        ]);
    }

    public function actionFactDayIndex()
    {
        $model2 = new FactdateForm();
        $model = new MenusDishes();
        //$menus_dishes = MenusDishes::find()->where(['menu_id' => 21, 'cycle' => 2, 'days_id' => 1])->orderby(['nutrition_id'=>SORT_ASC])->all();


        if (Yii::$app->request->post())
        {

            //$nutritions = NutritionInfo::find()->all();
            $post = Yii::$app->request->post()['FactdateForm'];
            //print_r(strtotime($post['date']));
            $my_menus = Menus::findOne($post['menu_id']);

            if ($my_menus->date_end < strtotime($post['date']) || $my_menus->date_start > strtotime($post['date']))
            {
                Yii::$app->session->setFlash('error', "Указанная дата не входит в диапозон даты начала или даты окончания меню");
                return $this->redirect(['menus-dishes/fact-day-index']);
            }

            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                if ($day_id->days_id != 7)
                {
                    $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
                }
                /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ФУНКЦИИ DATE() ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                else
                {
                    $days_ids[] = 0;
                }

            }
            /*print_r(Yii::$app->request->post());
            exit;*/
            if (!in_array(date("w", strtotime($post['date'])), $days_ids))
            {
                Yii::$app->session->setFlash('error', "Этот день недели отсутсвует в меню");
                return $this->redirect(['menus-dishes/fact-day-index']);
            }


            $start_date = date('d.m.Y', $my_menus->date_start);//Дата старта меню
            $day_of_week = date("w", strtotime($post['date']));//День недели выбранной даты
            $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
            /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
            if ($day_of_week_start_date == 0)
            {
                $day_of_week_start_date = 7;
            }
            if ($day_of_week == 0)
            {
                $day_of_week = 7;
            }
            /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
            $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

            $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
            $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
            $count_week = ceil((((strtotime($post['date']) - $my_menus->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

            $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
            /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
            if ($day_of_week == 1)
            {
                $cycle = $count_week - 1;
            }
            /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
            if ($count_week == 0)
            {
                $cycle = 1;
            }

            /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
            while ($cycle > $my_menus->cycle)
            {
                $cycle = $cycle - $my_menus->cycle;
            }
            if ($cycle == 0)
            {
                $cycle = $my_menus->cycle;
            }
            /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }

            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

//            //Если фактическое меню по этой дате уже создавалось -показать его, если нет то показать циклическое меню
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => strtotime($post['date']), 'menu_id' => $post['menu_id']])->orderby(['nutrition_id' => SORT_ASC])->all();
            $indicator_fact_menu = 1;

            if (empty($menus_dishes))
            {
                $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle, 'days_id' => $day_of_week])->orderby(['nutrition_id' => SORT_ASC])->all();
                $indicator_fact_menu = 0;
            }


            return $this->render('fact-day-index', [
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'day_of_week' => $day_of_week,
                'cycle' => $cycle,
                'model' => $model,
                'model2' => $model2,
                'post' => $post,
                'indicator_fact_menu' => $indicator_fact_menu,
            ]);
        }


        return $this->render('fact-day-index', [
            //'menus_dishes' => $menus_dishes,
            //'nutritions' => $nutritions,
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionRaskladka()
    {
        $model_menus_dishes = new MenusDishes();
        $model = new Menus();
        $model_form = new RaskladkaForm();
        $menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
        //подгрузка меню к роспотребнадзору

        if (Yii::$app->request->post())
        {

            $post = Yii::$app->request->post();
            $post_date = Yii::$app->request->post()['RaskladkaForm']['data'];
            $post_brutto_netto = Yii::$app->request->post()['RaskladkaForm']['brutto_netto'];
            /*ключ масива айди меню, значение массива - количество питающихся*/
            $menus_count_ids = [];
            /* массив айдишников меню */
            $ids = [];
            foreach ($menus as $menu)
            {
                $p_m = 'menu' . $menu->id;
                $p_c = 'count' . $menu->id;
                $post_menu = Yii::$app->request->post()[$p_m];
                $post_count = Yii::$app->request->post()[$p_c];
                if ($post_menu == 1)
                {
                    //Если поля не были заполнены, то меню сюда не придут
                    if (!empty($post_count))
                    {
                        $menus_count_ids[$menu->id] = $post_count;
                        $ids[] = $menu->id;
                    }
                }
            }
            //print_r($menus_count_ids);
            //exit();
            /*меню перед которыми была нажата галочка*/
            $post_menus = Menus::find()->where(['id' => $ids])->all();
            /*поиск приемов пищи по всем меню перед которыми была нажата галочка*/
            /*$menus_nutritions = MenusNutrition::find()->where(['menu_id' => $ids])->all();
            $nutrition_ids = [];

            foreach ($menus_nutritions as $m_nutrition)
            {
                $nutrition_ids[] = $m_nutrition->nutrition_id;
            }

            $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();*/
            /*конец блока приемов пищи*/
            $nutrition_ids = [];

            $menus_dishes = [];
            $menus_dishes2 = [];
            $menus_dishes2[] = [];
            $i = 0;
            foreach ($post_menus as $p_menu)
            {
                /*get_cycle_from_date() - функция по определения цикла меню по дате*/
                $cycle = $model_menus_dishes->get_cycle_from_date($p_menu->id, $post_date);
                $day_of_week_start_date = date("w", strtotime($post_date));//День недели даты
                /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
                if ($day_of_week_start_date == 0)
                {
                    $day_of_week_start_date = 7;
                }
                /*$cycle - цикл даты данного меню, $day_of_week_start_date - день недели*/
                $current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => strtotime($post_date), 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date])->asArray()->all();
                //print_r($current_menus_dishes);exit;
                if (empty($current_menus_dishes))
                {
                    $current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date])->asArray()->all();
                }


                //print_r(count($current_menus_dishes));exit;
                /*объединение массив по разным меню*/
                $menus_dishes = ArrayHelper::merge($menus_dishes, $current_menus_dishes);
                $menus_dishes2[$i] = $current_menus_dishes;
                $i++;
            }
            //print_r($menus_dishes);exit;


            $dishes_ids = [];
            foreach ($menus_dishes as $m_dish)
            {
                $nutrition_ids[] = $m_dish['nutrition_id'];
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish['dishes_id']])->all();
                foreach ($dishes_products as $d_product)
                {
                    if (!in_array($d_product->products_id, $dishes_ids))
                    {
                        $dishes_ids[] = $d_product->products_id;
                    }
                }
            }

            /*поиск приемов пищи по всем меню перед которыми была нажата галочка*/
            $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();
            /*конец блока приемов пищи*/
            //print_r($dishes_ids);exit;


            //print_r($menus_dishes);exit;
            //$products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
            $products = Products::find()->
            select(['products.id as id', 'products_category.id as pc_id', 'products.name as name', 'products.products_category_id as products_category_id', 'products_category.sort as sort'])->
            leftJoin('products_category', 'products.products_category_id = products_category.id')->
            where(['products.id' => $dishes_ids])->
            orderby(['products_category.sort' => SORT_ASC])->
            asArray()->
            all();


            return $this->render('raskladka', [
                'post' => $post,
                'menus' => $menus,
                'post_menus' => $post_menus,
                'menus_count_ids' => $menus_count_ids,
                'nutritions' => $nutritions,
                'model' => $model,
                'model_form' => $model_form,
                'menus_dishes' => $menus_dishes,
                'menus_dishes2' => $menus_dishes2,
                'products' => $products,
                'model_menus_dishes' => $model_menus_dishes,
                'post_brutto_netto' => $post_brutto_netto,
                'post_date' => $post_date,
            ]);
        }


        return $this->render('raskladka', [
            'menus' => $menus,
            'model' => $model,
            'model_form' => $model_form,
        ]);
    }

    public function actionRaskladkaNutrition()
    {
        $model_menus_dishes = new MenusDishes();
        $model = new Menus();
        $model_form = new RaskladkaForm();
        $menus = Menus::find()->where(['user_id' => Yii::$app->user->id, 'status_archive' => 0])->all();
        //подгрузка меню к роспотребнадзору

        if (Yii::$app->request->post())
        {

            $post = Yii::$app->request->post();
            $post_date = Yii::$app->request->post()['RaskladkaForm']['data'];
            $post_brutto_netto = Yii::$app->request->post()['RaskladkaForm']['brutto_netto'];
            /*ключ масива айди меню, значение массива - количество питающихся*/
            $menus_count_ids = [];
            $nutrition_ids = [];
            /* массив айдишников меню */
            $ids = [];
            foreach ($menus as $menu)
            {
                $p_m = 'menu' . $menu->id;
                $post_menu = Yii::$app->request->post()[$p_m];

                if ($post_menu == 1)
                {
                    $menus_nutrition = MenusNutrition::find()->where(['menu_id' => $menu->id])->all();
                    foreach ($menus_nutrition as $m_nutrition)
                    {
                        $p_c = 'count' . $menu->id . '_' . $m_nutrition->nutrition_id;
                        $post_count = Yii::$app->request->post()[$p_c];

                        //Если поля не были заполнены, то меню сюда не придут
                        if (!empty($post_count))
                        {
                            $menus_count_ids[$menu->id . '_' . $m_nutrition->nutrition_id] = $post_count;
                            $ids[] = $menu->id;
                            $nutrition_ids[] = $m_nutrition->nutrition_id;
                        }
                    }
                }
            }

            /*меню перед которыми была нажата галочка*/
            $post_menus = Menus::find()->where(['id' => $ids])->all();


            $menus_dishes = [];
            $menus_dishes2 = [];
            $menus_dishes2[] = [];
            $i = 0;
            foreach ($post_menus as $p_menu)
            {

                /*get_cycle_from_date() - функция по определения цикла меню по дате*/
                $cycle = $model_menus_dishes->get_cycle_from_date($p_menu->id, $post_date);
                $day_of_week_start_date = date("w", strtotime($post_date));//День недели даты
                /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
                if ($day_of_week_start_date == 0)
                {
                    $day_of_week_start_date = 7;
                }
                /*$cycle - цикл даты данного меню, $day_of_week_start_date - день недели*/
                $current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => strtotime($post_date), 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date, 'nutrition_id' => $nutrition_ids])->asArray()->all();
                //print_r($current_menus_dishes);exit;
                if (empty($current_menus_dishes))
                {
                    $current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date, 'nutrition_id' => $nutrition_ids])->asArray()->all();
                }


                //print_r(count($current_menus_dishes));exit;
                /*объединение массив по разным меню*/
                $menus_dishes = ArrayHelper::merge($menus_dishes, $current_menus_dishes);
                $menus_dishes2[$i] = $current_menus_dishes;
                $i++;
            }


            $dishes_ids = [];
            foreach ($menus_dishes as $m_dish)
            {
                //$nutrition_ids[] = $m_dish['nutrition_id'];
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish['dishes_id']])->all();
                foreach ($dishes_products as $d_product)
                {
                    if (!in_array($d_product->products_id, $dishes_ids))
                    {
                        $dishes_ids[] = $d_product->products_id;
                    }
                }
            }

            /*поиск приемов пищи по всем меню перед которыми была нажата галочка*/
            $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();
            /*конец блока приемов пищи*/
//            print_r($menus_dishes);exit;


            //print_r($menus_dishes);exit;
            //$products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
            $products = Products::find()->
            select(['products.id as id', 'products_category.id as pc_id', 'products.name as name', 'products.products_category_id as products_category_id', 'products_category.sort as sort'])->
            leftJoin('products_category', 'products.products_category_id = products_category.id')->
            where(['products.id' => $dishes_ids])->
            orderby(['products_category.sort' => SORT_ASC])->
            asArray()->
            all();


            return $this->render('raskladka-nutrition', [
                'post' => $post,
                'menus' => $menus,
                'post_menus' => $post_menus,
                'menus_count_ids' => $menus_count_ids,
                'nutritions' => $nutritions,
                'model' => $model,
                'model_form' => $model_form,
                'menus_dishes' => $menus_dishes,
                'menus_dishes2' => $menus_dishes2,
                'products' => $products,
                'model_menus_dishes' => $model_menus_dishes,
                'post_brutto_netto' => $post_brutto_netto,
                'post_date' => $post_date,
            ]);
        }


        return $this->render('raskladka-nutrition', [
            'menus' => $menus,
            'model' => $model,
            'model_form' => $model_form,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new MenusDishes();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /*МЕТОД ДЛЯ УДАЛЕНИЕ БЛЮД ИЗ ФАКТИЧЕСКОГО МЕНЮ*/
    public function actionDelete($id, $date)
    {
        $menus_for_delete = MenusDishes::findOne($id);
        $menus_id = $menus_for_delete->menu_id;
        $count = MenusDishes::find()->where(['date_fact_menu' => $date, 'menu_id' => $menus_id])->count();
        /*$COUNT ПОКАЗЫВАЕТ КОЛИЧЕСТВО БЛЮД В ФАКТИЧЕСКОМ МЕНЮ. еСЛИ БЛЮДА ЕСТЬ МЫ ПРОСТО УДАЛЯЕМ ОДНО ПО АЙДИШНИКУ*/
        if ($count > 0)
        {
            $m_dish = MenusDishes::find()->where(['date_fact_menu' => $date, 'id' => $id])->one();
            $m_dish->delete();
            return $id;
        }
        //ЕСЛИ БЛЮД НЕТ, ТО ФАКТИЧЕСКОГО МЕНЮ НЕТ.
        if ($count == 0)
        {
            //ПО ЦИКЛИЧЕСКОМУ СОЗДАЕМ ФАКТИЧЕСКОЕ
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menus_id, 'cycle' => $menus_for_delete->cycle, 'days_id' => $menus_for_delete->days_id])->all();
            foreach ($menus_dishes as $m_dish)
            {
                //МЫ НЕ СОЗДАЕМ БЛЮДО ПО ЦИКЛИЧЕСКОМУ АЙДИШНИКУ КОТОРОЕ НРУЖНО БЫЛО УДАЛИТЬ И ВОЗВРАЩАЕМ АЙДИ "УДАЛЕННОГО(НЕСОЗДАННОГО) БЛЮДА"
                if ($m_dish->id != $id)
                {
                    $model = new MenusDishes();
                    $model->menu_id = $menus_id;
                    $model->cycle = $menus_for_delete->cycle;
                    $model->days_id = $menus_for_delete->days_id;
                    $model->nutrition_id = $m_dish->nutrition_id;
                    $model->dishes_id = $m_dish->dishes_id;
                    $model->yield = $m_dish->yield;
                    $model->date_fact_menu = $date;
                    $model->save();
                }
            }
            return $id;
        }
    }

    /*МЕТОД ДЛЯ УДАЛЕНИЕ БЛЮД ИЗ ЦИКЛИЧЕСКОГО МЕНЮ*/
    public function actionDel($id)
    {

        $this->findModel($id)->delete();
        return $id;
    }

    /*МЕТОД ДЛЯ УДАЛЕНИЯ ВСЕХ БЛЮД ОПРЕДЕЛЕННОГО ДНЯ ФАКТ. МЕНЮ(ВОЗВРАТ К ЦИКЛИЧЕСКОМУ)*/
    public function actionFactDelete($menus_id, $cycle, $day_of_week, $date)
    {
        if ($date > 0)
        {
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => $date, 'menu_id' => $menus_id, 'cycle' => $cycle, 'days_id' => $day_of_week])->all();
            //ЕСЛИ В ФАКТИЧЕСКОМ МЕНЮ ЧТО ТО ЕСТЬ ТО УДАЛЯЕМ ВСЕ ЧТО ЕСТЬ
            if (!empty($menus_dishes))
            {
                foreach ($menus_dishes as $m_dish)
                {
                    $m_dish->delete();
                }
            }
            else
            {
                $menus_dishes = MenusDishes::find()->where(['menu_id' => $menus_id, 'cycle' => $cycle, 'days_id' => $day_of_week])->all();
                foreach ($menus_dishes as $m_dish)
                {
                    $model = new MenusDishes();
                    $model->menu_id = $menus_id;
                    $model->cycle = $cycle;
                    $model->days_id = $day_of_week;
                    $model->nutrition_id = $m_dish->nutrition_id;
                    $model->dishes_id = $m_dish->dishes_id;
                    $model->yield = $m_dish->yield;
                    $model->date_fact_menu = $date;
                    $model->save();
                }
            }
        }

        Yii::$app->session->setFlash('success', "Изменения удалены. По умолчанию подгружены значения циклического меню");
        return $this->redirect(['menus-dishes/fact-day-index']);

    }

    protected function findModel($id)
    {
        if (($model = MenusDishes::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionMenusDays()
    {
        $model = new MenusDishes();
        //$menus_dishes = MenusDishes::find()->where(['menu_id' => 21, 'cycle' => 2, 'days_id' => 1])->orderby(['nutrition_id'=>SORT_ASC])->all();
        if (Yii::$app->user->can('admin') || Yii::$app->user->can('RPN') || Yii::$app->user->can('RPN_mun'))
        {
            return $this->goHome();
        }
        elseif (Yii::$app->user->can('director'))
        {
            if (Yii::$app->request->post())
            {
                //$nutritions = NutritionInfo::find()->all();
                $post = Yii::$app->request->post()['MenusDishes'];

                $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
                $ids = [];
                foreach ($menus_nutrition_id as $m_id)
                {
                    $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
                }
                $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
                //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $post['cycle'], 'days_id' => $post['days_id']])->orderby(['nutrition_id' => SORT_ASC])->all();
                $menus_dishes = MenusDishes::find()->
                select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $post['cycle'], 'days_id' => $post['days_id']])->
                orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
                all();

                return $this->render('menus-days', [
                    'menus_dishes' => $menus_dishes,
                    'nutritions' => $nutritions,
                    'model' => $model,
                    'post' => $post,
                ]);
            }
            return $this->render('menus-days', [
                //'menus_dishes' => $menus_dishes,
                //'nutritions' => $nutritions,
                'model' => $model,
            ]);
        }
        else
        {
            //$nutritions = NutritionInfo::find()->all();
            //$post = Yii::$app->request->post()['MenusDishes'];
            $menu_id = Menus::find()->where(['user_id' => Yii::$app->user->id])->one()->id;

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
            //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $post['cycle'], 'days_id' => $post['days_id']])->orderby(['nutrition_id' => SORT_ASC])->all();
            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => 1, 'days_id' => 1])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();

            return $this->render('menus-days', [
                'menus_dishes' => $menus_dishes,
                'menu_id' => $menu_id,
                'nutritions' => $nutritions,
                'model' => $model,
            ]);
        }

    }

    public function actionMenusPeriod()
    {
        if (!Yii::$app->user->can('director'))
        {
            Yii::$app->session->setFlash('error', "Доступ к разделу запрещен");
            return $this->goHome();
        }

        $model = new MenusDishes();

        if (Yii::$app->request->post())
        {
            $identificator = Yii::$app->request->post()['identificator'];

            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
            //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']/*, 'cycle' => $post['cycle']*/])->orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC])->all();

            $my_menus = Menus::findOne($post['menu_id']);
            $menu_cycle_count = $my_menus->cycle;
            if ($post['cycle'] != 0)
            {
                $cycle_ids[$post['cycle']] = $post['cycle'];
            }
            else
            {
                for ($i = 1; $i <= $menu_cycle_count; $i++)
                {
                    $cycle_ids[$i] = $i;//массив из подходящи циклов
                }
            }

            $dishes_check = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->count();
            if ($dishes_check == 0)
            {
                Yii::$app->session->setFlash('error', "В меню не внесены блюда. Перейдите в раздел 'Разработка редактирование действующего цикличного меню' и добавьте блюда в меню");
                return $this->redirect(['menus-dishes/index']);
            }

            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();

            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }

            $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ


            return $this->render('menus-period', [
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'days' => $days,
                'model' => $model,
                'post' => $post,
                'identificator' => $identificator,
            ]);
        }

        return $this->render('menus-period', [
            'model' => $model,
        ]);
    }

    public function actionMenusPeriodDisable()
    {
        $model = new MenusDishes();


        if (Yii::$app->request->post())
        {
            $identificator = Yii::$app->request->post()['identificator'];

            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

            $my_menus = Menus::findOne($post['menu_id']);
            $menu_cycle_count = $my_menus->cycle;
            if ($post['cycle'] != 0)
            {
                $cycle_ids[$post['cycle']] = $post['cycle'];
            }
            else
            {
                for ($i = 1; $i <= $menu_cycle_count; $i++)
                {
                    $cycle_ids[$i] = $i;//массив из подходящи циклов
                }
            }
            /*select(['dishes_products.id','dishes_products.dishes_id','dishes_products.products_id', 'dishes_products.net_weight', 'dishes_products.gross_weight', 'dishes.yield as dishes_yield', 'menus_dishes.cycle', 'menus_dishes.days_id', 'menus_dishes.yield as menus_yield'])->
            leftJoin('products', 'dishes_products.products_id = products.id')->
            leftJoin('menus_dishes', 'dishes_products.dishes_id = menus_dishes.dishes_id')->
            leftJoin('dishes', 'dishes_products.dishes_id = dishes.id')->
            where(['products.products_category_id' => $product_category_id, 'menus_dishes.date_fact_menu' => '0','menus_dishes.menu_id' => $menu_id])->
            asArray()->
            all();*/
            //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']/*, 'cycle' => $post['cycle']*/])->orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC])->all();
            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();
            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }

            $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ


            return $this->render('menus-period-disable', [
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'days' => $days,
                'model' => $model,
                'post' => $post,
                'identificator' => $identificator,
            ]);
        }

        return $this->render('menus-period-disable', [
            'model' => $model,

        ]);
    }

    public function actionShowAllergen()
    {
        $model = new AllergenForm();
        $model2 = new MenusDishes();
        $allergens = Allergen::find()->all();

        if (Yii::$app->request->post())
        {
            //print_r(Yii::$app->request->post());exit;

            $post = Yii::$app->request->post()['AllergenForm'];
            $post_allergen = Yii::$app->request->post();

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

            $my_menus = Menus::findOne($post['menu_id']);
            $menu_cycle_count = $my_menus->cycle;
            if ($post['cycle'] != 0)
            {
                $cycle_ids[$post['cycle']] = $post['cycle'];
            }
            else
            {
                for ($i = 1; $i <= $menu_cycle_count; $i++)
                {
                    $cycle_ids[$i] = $i;//массив из подходящи циклов
                }
            }

            //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']/*, 'cycle' => $post['cycle']*/])->orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC])->all();
            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();
            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }

            $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ


            return $this->render('show-allergen', [
                'menus_dishes' => $menus_dishes,
                'allergens' => $allergens,
                'nutritions' => $nutritions,
                'days' => $days,
                'model' => $model,
                'model2' => $model2,
                'post' => $post,
                'post_allergen' => $post_allergen,
            ]);
        }

        return $this->render('show-allergen', [
            'allergens' => $allergens,
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    public function actionShowSahar()
    {
        $model = new AllergenForm();
        $model2 = new MenusDishes();
        //$allergens = Allergen::find()->all();

        if (Yii::$app->request->post())
        {
            //print_r(Yii::$app->request->post());exit;

            $post = Yii::$app->request->post()['AllergenForm'];
            $post_allergen = Yii::$app->request->post();

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

            $my_menus = Menus::findOne($post['menu_id']);
            $menu_cycle_count = $my_menus->cycle;
            if ($post['cycle'] != 0)
            {
                $cycle_ids[$post['cycle']] = $post['cycle'];
            }
            else
            {
                for ($i = 1; $i <= $menu_cycle_count; $i++)
                {
                    $cycle_ids[$i] = $i;//массив из подходящи циклов
                }
            }

            //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']/*, 'cycle' => $post['cycle']*/])->orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC])->all();
            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();
            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }

            $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ


            return $this->render('show-sahar', [
                'menus_dishes' => $menus_dishes,
                //'allergens' => $allergens,
                'nutritions' => $nutritions,
                'days' => $days,
                'model' => $model,
                'model2' => $model2,
                'post' => $post,
                'post_allergen' => $post_allergen,
            ]);
        }

        return $this->render('show-sahar', [
            //'allergens' => $allergens,
            'model' => $model,
            'model2' => $model2,
        ]);
    }

    //Отчет о повторяемости!
    public function actionRepeatReport()
    {
        $model = new MenusDishes();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']])->orderby(['dishes_id' => SORT_ASC])->all();

            return $this->render('repeat-report', [
                'menus_dishes' => $menus_dishes,
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('repeat-report', [
            'model' => $model,
        ]);
    }

    public function actionProductsList()
    {
        $model = new MenusDishes();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']])->orderby(['dishes_id' => SORT_ASC])->all();
            $dishes_ids = [];
            $categories_ids = [];

            foreach ($menus_dishes as $m_dish)
            {
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->all();
                foreach ($dishes_products as $d_product)
                {
                    if (!in_array($d_product->products_id, $dishes_ids))
                    {
                        $dishes_ids[] = $d_product->products_id;
                    }
                }
            }
            $products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
            foreach ($products as $product)
            {
                if (!in_array($product->products_category_id, $categories_ids))
                {
                    $categories_ids[] = $product->products_category_id;
                }
            }
            $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();

            return $this->render('products-list', [
                'products_categories' => $products_categories,
                'products' => $products,
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('products-list', [
            'model' => $model,
        ]);
    }

    public function actionPrognosStorage()
    {
        $model = new FactdateForm();


        $model4 = new ProductsCategory();
        $model3 = new MenusDishes();
        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['FactdateForm'];;
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']])->orderby(['nutrition_id' => SORT_ASC])->all();
            $dishes_ids = [];

            foreach ($menus_dishes as $m_dish)
            {
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->one();
                if (!in_array($dishes_products->dishes_id, $dishes_ids))
                {
                    /*Массив используемых продуктов. Продукты пока что не уникальных в 1м из 2х случаях*/
                    $dishes_ids[] = $dishes_products->dishes_id;
                }
            }

            $dishes_dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_ids])->orderby(['id' => SORT_ASC])->all();
            $categories_ids = [];

            foreach ($dishes_dishes_products as $d_d_product)
            {
                $product = Products::find()->where(['id' => $d_d_product->products_id])->one();
                $categories = ProductsCategory::find()->where(['id' => $product->products_category_id])->one();
                if (!in_array($product->products_category_id, $categories_ids))
                {
                    $categories_ids[] = $product->products_category_id;
                }
            }

            $categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();

            $menus_nutritions = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();
            $nutrition_ids = [];

            foreach ($menus_nutritions as $m_nutrition)
            {
                $nutrition_ids[] = $m_nutrition->nutrition_id;
            }

            $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();
            $model2 = new ProductsCategory;
            return $this->render('prognos-storage', [
                'categories' => $categories,
                'nutritions' => $nutritions,
                'model' => $model,
                'model2' => $model2,
                'model3' => $model3,
                'model4' => $model4,
                'post' => $post,
            ]);
        }

        return $this->render('prognos-storage', [
            'model' => $model,
            'model3' => $model3,
        ]);
    }

    public function actionFactDate()
    {
        $model2 = new FactdateForm();
        $model = new MenusDishes();

        if (Yii::$app->request->post())
        {
            //$nutritions = NutritionInfo::find()->all();
            $post = Yii::$app->request->post()['FactdateForm'];
            $my_menus = Menus::findOne($post['menu_id']);

            if ($my_menus->date_end < strtotime($post['date']) || $my_menus->date_start > strtotime($post['date']))
            {
                Yii::$app->session->setFlash('error', "Указанная дата не входит в диапозон даты начала или даты окончания меню");
                return $this->redirect(['menus-dishes/fact-date']);
            }

            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                if ($day_id->days_id != 7)
                {
                    $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
                }
                /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ФУНКЦИИ DATE() ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                else
                {
                    $days_ids[] = 0;
                }

            }
            if (!in_array(date("w", strtotime($post['date'])), $days_ids))
            {
                Yii::$app->session->setFlash('error', "Этот день недели отсутсвует в меню");
                return $this->redirect(['menus-dishes/fact-date']);
            }


            $start_date = date('d.m.Y', $my_menus->date_start);//Дата старта меню
            $day_of_week = date("w", strtotime($post['date']));//День недели выбранной даты
            $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
            /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
            if ($day_of_week_start_date == 0)
            {
                $day_of_week_start_date = 7;
            }
            if ($day_of_week == 0)
            {
                $day_of_week = 7;
            }
            /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
            $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

            $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
            $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
            $count_week = ceil((((strtotime($post['date']) - $my_menus->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

            $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
            /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
            if ($day_of_week == 1)
            {
                $cycle = $count_week - 1;
            }
            /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
            if ($count_week == 0)
            {
                $cycle = 1;
            }

            /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
            while ($cycle > $my_menus->cycle)
            {
                $cycle = $cycle - $my_menus->cycle;
            }
            if ($cycle == 0)
            {
                $cycle = $my_menus->cycle;
            }
            /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }

            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
            //$menus_dishes = MenusDishes::find()->where(['menu_id' => $post['menu_id'], 'cycle' => $cycle, 'days_id' => $day_of_week])->orderby(['nutrition_id' => SORT_ASC])->all();

            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => strtotime($post['date']), 'menu_id' => $post['menu_id'], 'cycle' => $cycle, 'days_id' => $day_of_week])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();


            if (empty($menus_dishes))
            {
                $menus_dishes = MenusDishes::find()->
                select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle, 'days_id' => $day_of_week])->
                orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
                all();
            }


            return $this->render('fact-date', [
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'model' => $model,
                'model2' => $model2,
                'post' => $post,
                'day_of_week' => $day_of_week,
                'cycle' => $cycle,
            ]);
        }


        return $this->render('fact-date', [
            'model2' => $model2,
            'model' => $model,
        ]);
    }

    public function actionFactStorage()
    {
        $model3 = new FactdateForm();
        $model = new MenusDishes();

        if (Yii::$app->request->post())
        {
            //$nutritions = NutritionInfo::find()->all();
            $post = Yii::$app->request->post()['FactdateForm'];


            $my_menus = Menus::findOne($post['menu_id']);


            if (strtotime($post['date']) > strtotime($post['date_end']))
            {
                Yii::$app->session->setFlash('error', "Дата начала не может быть больше даты окончания! Введите новые значения.");
                return $this->redirect(['menus-dishes/fact-storage']);
            }

            if (strtotime($post['date']) == strtotime($post['date_end']))
            {
                Yii::$app->session->setFlash('error', "Дата начала не может совпадать с датой окончания! Введите новые значения.");
                return $this->redirect(['menus-dishes/fact-storage']);
            }

            if ($my_menus->date_end < strtotime($post['date']) || $my_menus->date_start > strtotime($post['date']))
            {
                Yii::$app->session->setFlash('error', "Указанная дата начала не входит в диапозон даты начала или даты окончания меню");
                return $this->redirect(['menus-dishes/fact-storage']);
            }

            if ($my_menus->date_end < strtotime($post['date_end']) || $my_menus->date_start > strtotime($post['date_end']))
            {
                Yii::$app->session->setFlash('error', "Указанная дата окончанчания не входит в диапозон даты начала или даты окончания меню");
                return $this->redirect(['menus-dishes/fact-storage']);
            }

            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                if ($day_id->days_id != 7)
                {
                    $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
                }
                /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ФУНКЦИИ DATE() ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                else
                {
                    $days_ids[] = 0;
                }

            }
            if (!in_array(date("w", strtotime($post['date'])), $days_ids))
            {
                Yii::$app->session->setFlash('error', "Этот день недели даты начала отсутсвует в меню");
                return $this->redirect(['menus-dishes/fact-storage']);
            }

            if (!in_array(date("w", strtotime($post['date_end'])), $days_ids))
            {
                Yii::$app->session->setFlash('error', "Этот день недели даты окончания отсутсвует в меню");
                return $this->redirect(['menus-dishes/fact-storage']);
            }


            $start_date = date('d.m.Y', $my_menus->date_start);//Дата старта меню
            $day_of_week = date("w", strtotime($post['date']));//День недели выбранной даты
            $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
            /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
            if ($day_of_week_start_date == 0)
            {
                $day_of_week_start_date = 7;
            }
            if ($day_of_week == 0)
            {
                $day_of_week = 7;
            }
            /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
            $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

            $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
            $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
            $count_week = ceil((((strtotime($post['date']) - $my_menus->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

            $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
            /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
            while ($cycle > $my_menus->cycle)
            {
                $cycle = $cycle - $my_menus->cycle;
            }
            if ($cycle == 0)
            {
                $cycle = $my_menus->cycle;
            }
            /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }

            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
            $menus_dishes = MenusDishes::find()->where(['menu_id' => $post['menu_id']])->orderby(['nutrition_id' => SORT_ASC])->all();


            $dishes_ids = [];

            foreach ($menus_dishes as $m_dish)
            {
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->one();
                if (!in_array($dishes_products->dishes_id, $dishes_ids))
                {
                    /*Массив используемых продуктов. Продукты пока что не уникальных в 1м из 2х случаях*/
                    $dishes_ids[] = $dishes_products->dishes_id;
                }
            }
            //print_r($dishes_ids);

            $dishes_dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_ids])->orderby(['id' => SORT_ASC])->all();
            /*print_r($products);
            exit;*/
            $categories_ids = [];
            foreach ($dishes_dishes_products as $d_d_product)
            {
                $product = Products::find()->where(['id' => $d_d_product->products_id])->one();
                $categories = ProductsCategory::find()->where(['id' => $product->products_category_id])->one();
                if (!in_array($product->products_category_id, $categories_ids))
                {
                    $categories_ids[] = $product->products_category_id;
                }
            }

            $categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();
            /*print_r($categories);
                        exit;*/
            $menus_nutritions = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();
            $nutrition_ids = [];
            foreach ($menus_nutritions as $m_nutrition)
            {
                $nutrition_ids[] = $m_nutrition->nutrition_id;
            }
            $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();
            $model2 = new ProductsCategory;


            return $this->render('fact-storage', [
                'categories' => $categories,
                'menus_dishes' => $menus_dishes,
                'nutritions' => $nutritions,
                'model' => $model,
                'model2' => $model2,
                'model3' => $model3,
                'post' => $post,
            ]);
        }


        return $this->render('fact-storage', [
            'model3' => $model3,
            'model' => $model,
        ]);
    }

    /*МЕТОД ДЛЯ ДЕМОНСТРАЦИИ АНАЛОГОВ БЛЮД БЕЗ АЛЕРГЕНА*/
    public function actionAnalog_alergen($id, $alergen)
    {
        //$id - id блюда
        $this->layout = false;
        $alergens = explode("_", $alergen);
        $alergens_ids = [];
        foreach ($alergens as $alergen)
        {
            if ($alergen != '')
            {
                $alergen_ids[] = $alergen;
            }
        }
        $alergens = Allergen::find()->where(['id' => $alergen_ids])->all();


        $this_dishes = Dishes::findOne($id);
        //Находим блюда данной категории и того же сборника
        $dishes = Dishes::find()->where(['dishes_category_id' => $this_dishes->dishes_category_id, 'recipes_collection_id' => $this_dishes->recipes_collection_id])->all();
        //массив айдишников подходящих блюд
        $dishes_ids = [];

        foreach ($dishes as $dish)
        {
            $count = 0;
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $dish->id])->all();
            foreach ($dishes_products as $d_product)
            {
                $product = ProductsAllergen::find()->where(['products_id' => $d_product->products_id, 'allergen_id' => $alergen_ids])->count();
                if ($product > 0)
                {
                    $count++;
                }
            }

            if ($count == 0)
            {
                $dishes_ids[] = $dish->id;
            }
        }

        //Если аналогов по данной категории не было найдено начинаем поиск по подобным категориям
        if (count($dishes_ids) == 0)
        {
            //крупы и каши меняем на яйца и творог
            if ($this_dishes->dishes_category_id == 1)
            {
                $dishes2 = Dishes::find()->where(['dishes_category_id' => 4, 'recipes_collection_id' => $this_dishes->recipes_collection_id])->all();
            }
            //яйца и творог меняем на каши и гарниры
            if ($this_dishes->dishes_category_id == 4)
            {
                $dishes2 = Dishes::find()->where(['dishes_category_id' => [1, 5], 'recipes_collection_id' => $this_dishes->recipes_collection_id])->all();
            }
            //блюда из рыбы меняем на блюда из мяса
            if ($this_dishes->dishes_category_id == 3)
            {
                $dishes2 = Dishes::find()->where(['dishes_category_id' => 2, 'recipes_collection_id' => $this_dishes->recipes_collection_id])->all();
            }


            //Если аналоги есть начинаем перебор
            if (!empty($dishes2))
            {
                foreach ($dishes2 as $dish)
                {
                    $count = 0;
                    $dishes_products = DishesProducts::find()->where(['dishes_id' => $dish->id])->all();
                    foreach ($dishes_products as $d_product)
                    {
                        $product = ProductsAllergen::find()->where(['products_id' => $d_product->products_id, 'allergen_id' => $alergen_ids])->count();
                        if ($product > 0)
                        {
                            $count++;
                        }
                    }

                    if ($count == 0)
                    {
                        $dishes_ids[] = $dish->id;
                    }
                }
            }

        }

        $correct_dishes = Dishes::find()->where(['id' => $dishes_ids])->all();
        return $this->render('analog-alergen', [
            'this_dishes' => $this_dishes,
            'alergens' => $alergens,
            'correct_dishes' => $correct_dishes,
        ]);
    }

    /*МЕТОД ДЛЯ ДЕМОНСТРАЦИИ АНАЛОГОВ БЛЮД БЕЗ Сахара*/
    public function actionAnalog_sahar($menu_dishes_id)
    {
        $this->layout = false;
        $m_dish = MenusDishes::findOne($menu_dishes_id);
        $this_dishes = Dishes::findOne($m_dish->dishes_id);
        //Находим блюда данной категории и того же сборника
        $dishes = Dishes::find()->where(['dishes_category_id' => $this_dishes->dishes_category_id, 'recipes_collection_id' => $this_dishes->recipes_collection_id])->all();
        //массив айдишников подходящих блюд
        $dishes_ids = [];

        foreach ($dishes as $dish)
        {

            $dishes_ids[] = $dish->id;

        }

        $correct_dishes = Dishes::find()->where(['id' => $dishes_ids])->all();
        return $this->render('analog-sahar', [
            'this_dishes' => $this_dishes,
            'correct_dishes' => $correct_dishes,
            'm_dish' => $m_dish,
        ]);
    }

    /*МЕТОД ДЛЯ ДЕМОНСТРАЦИИ ТЕХКАРТЫ НА (НА НАШИ) КОЛИЧЕСТВО ГР*/
    public function actionShowtechmup($id)
    {
        //$id - id блюда
        $this->layout = false;
        $indicator = 1;
        $dishes = Dishes::findOne($id);
        //$dishes_products = DishesProducts::find()->where(['dishes_id' => $id])->orderBy(['created_at' => 'SORT_ASK'])->all();

        $dishes_products = DishesProducts::find()->
        select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
        leftJoin('products', 'dishes_products.products_id = products.id')->
        leftJoin('products_category', 'products.products_category_id = products_category.id')->
        where(['dishes_id' => $id])->
        orderby(['sort_techmup' => SORT_ASC])->
        all();


        return $this->render('techmup', [
            'indicator' => $indicator,
            'dishes' => $dishes,
            'dishes_products' => $dishes_products,
            'id' => $id,
        ]);
    }

    /*МЕТОД ДЛЯ ДЕМОНСТРАЦИИ ТЕХКАРТЫ С УЧЕТОМ ТЕКУЩ. ВЫВОДА*/
    public function actionShowtechmup_current_yield($id)
    {
        //$id - id из таблицы menus_dishes
        $this->layout = false;
        $menus_dishes = MenusDishes::findOne($id);
        $dishes = Dishes::findOne($menus_dishes->dishes_id);
        //$dishes_products = DishesProducts::find()->where(['dishes_id' => $menus_dishes->dishes_id])->all();

        $dishes_products = DishesProducts::find()->
        select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
        leftJoin('products', 'dishes_products.products_id = products.id')->
        leftJoin('products_category', 'products.products_category_id = products_category.id')->
        where(['dishes_id' => $menus_dishes->dishes_id])->
        orderby(['sort_techmup' => SORT_ASC])->
        all();

        $indicator = $menus_dishes->yield / $dishes->yield;
        return $this->render('techmup', [
            'indicator' => $indicator,
            'dishes' => $dishes,
            'dishes_products' => $dishes_products,
            'id' => $id,
        ]);
    }

    /*МЕТОД ДЛЯ ДЕМОНСТРАЦИИ 'ПОКАЗАТЬ СОСТАВ ЗА <ПРИЕМ ПИЩИ>' НА СТРАНИЦЕ menus-dishes/index*/
    public function actionShow_composition($menu_id, $cycle, $days_id, $nutrition_id)
    {
        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }

        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

        //$id - id приема пищи
        $this->layout = false;
        if ($nutrition_id > 0)
        {
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $days_id, 'nutrition_id' => $nutrition_id])->all();
        }
        else
        {
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $days_id])->all();
        }
        if (empty($menus_dishes))
        {
            return 'Необходимо заполнить информацию о съеденной за день пище.';
        }
        $indicator_page = $nutrition_id;

        $model = new MenusDishes();
        return $this->render('composition', [
            'menus_dishes' => $menus_dishes,
            'nutritions' => $nutritions,
            'indicator_page' => $indicator_page,
            'model' => $model,
        ]);
    }

    /*ВЫПАДАЮЩИЕ СПИСКИ ДЛЯ ФОРМ КАЖДОЙ СТРАНИЦЫ РАЗДЕЛА ЭТОГО КОНТРОЛЛЕРА*/
    //ФУНКЦИЯ ВЫВОДИТ СПИСОК C НУЛЕВОЙ ЗАПИСЬЮ ИСПОЛЬЗУЕТСЯ В MENUS-PERIOD
    public function actionCyclelist($id)
    {

        $menu = Menus::find()->where(['id' => $id])->one();
        $menu_cycle = $menu->cycle;
        echo '<option value="0">Показать за все недели</option>';
        for ($i = 1; $i <= $menu_cycle; $i++)
        {
            echo '<option value="' . $i . '">' . $i . '</option>';
        }
    }

    //ФУНКЦИЯ ВЫВОДИТ СПИСОК C НУЛЕВОЙ ЗАПИСЬЮ ИСПОЛЬЗУЕТСЯ В MENUS-PERIOD
    public function actionNutritionlist($id)
    {
        $menu_nutritions = MenusNutrition::find()->where(['menu_id' => $id])->all();

        echo '<option value="0">Показать по всем приемам пищи</option>';
        foreach ($menu_nutritions as $m_nutrition)
        {
            echo '<option value="' . $m_nutrition->nutrition_id . '">' . NutritionInfo::findOne($m_nutrition->nutrition_id)->name . '</option>';
        }
    }

    public function actionDayfulllist($id)
    {

        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $ids = [];
        foreach ($menus_days as $m_day)
        {
            $ids[] = $m_day->days_id;
        }
        $days = Days::find()->where(['id' => $ids])->all();
        echo '<option value="0">Показать за все дни</option>';
        foreach ($days as $day)
        {
            echo '<option value="' . $day->id . '">' . $day->name . '</option>';
        }
    }

    //ФУНКЦИЯ ВЫВОДИТ СПИСОК БЕЗ НУЛЕВОЙ ЗАПИСИ ИСПОЛЬЗУЕТСЯ В MENUS-DAYS
    public function actionCyclelistday($id)
    {

        $menu = Menus::find()->where(['id' => $id])->one();
        $menu_cycle = $menu->cycle;
        for ($i = 1; $i <= $menu_cycle; $i++)
        {
            echo '<option value="' . $i . '">' . $i . '</option>';
        }
    }

    /*МЕТОД ДЛЯ ДЕМОНСТРАЦИИ ТЕХКАРТЫ В РАЗДЕЛЕ*/
    public function actionTechmupTechmup($id, $netto, $count)
    {
        //$id - id блюда
        $this->layout = false;
        $dishes = Dishes::findOne($id);
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $id])->all();
        return $this->render('techmup-techmup', [
            'dishes' => $dishes,
            'netto' => $netto,
            'count' => $count,
            'dishes_products' => $dishes_products,
        ]);
    }

    public function actionDaylist($id)
    {

        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $ids = [];
        foreach ($menus_days as $m_day)
        {
            $ids[] = $m_day->days_id;
        }
        $days = Days::find()->where(['id' => $ids])->all();
        foreach ($days as $day)
        {
            echo '<option value="' . $day->id . '">' . $day->name . '</option>';
        }
    }

    /*МЕТОД ВСТАВКИ ХАРАКТЕРИСТИК*/
    public function actionInsertcharacters($id)
    {

        $menus = Menus::findOne($id);
        $feeders_characters = FeedersCharacters::findOne($menus->feeders_characters_id);

        return $feeders_characters->name;
    }

    /*МЕТОД ВСТАВКИ ХАРАКТЕРИСТИК*/
    public function actionInsertage($id)
    {

        $menus = Menus::findOne($id);
        $age = AgeInfo::findOne($menus->age_info_id);

        return $age->name;
    }

    /*МЕТОД ВСТАВКИ ДНЕЙ*/
    public function actionInsertdays($id)
    {

        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $days = '';
        foreach ($menus_days as $m_day)
        {
            $days .= Days::findOne($m_day->days_id)->name . ' ';
        }

        return $days;
    }

    public function actionInsertsrok($id)
    {

        $menus_days = Menus::findOne($id);
        return date("d.m.Y", $menus_days->date_start) . ' - ' . date("d.m.Y", $menus_days->date_end);
    }

    //МЕТОД ДЛЯ ПОДГРУЗКИ БЛЮД ПО ВЫБОРКЕ СБОРНИКА techmup-page
    public function actionDisheslist($id)
    {
        if ($id != 0)
        {
            $dishes = Dishes::find()->where(['recipes_collection_id' => $id])->orderBy(['recipes_collection_id' => SORT_ASC, 'name' => SORT_ASC])->all();
        }
        else
        {
            $dishes = Dishes::find()->orderBy(['recipes_collection_id' => SORT_ASC, 'name' => SORT_ASC])->all();
        }


        foreach ($dishes as $dish)
        {
            echo '<option value="' . $dish->id . '">' . $dish->name . '</option>';
        }
    }

    /*МЕТОД ДЛЯ СОХРАНЕНИЯ ПРОДУКТА ИЗ АЯКСА ИЗ АДПРОДУКТА*/
    public function actionSavingProduct()
    {
        Yii::$app->controller->enableCsrfValidation = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $products = Products::findOne($post['products_id']);
        if (empty($post['netto']) /*|| empty($post['brutto'])*/)
        {
            return 'error1';
        }

        if (empty($products))
        {
            return 'error2';
        }

        $menu = new DishesProducts();
        $menu->products_id = $post['products_id'];
        $menu->net_weight = $post['netto'];
        $menu->gross_weight = 0/*$post['brutto']*/
        ;
        $menu->dishes_id = $post['dishes_id'];
        $menu->save();
        return $menu;
        //return $post;
    }

    /*МЕТОД ЛЯ АВТОПОДСТАНОВКИ ТЕКСТА*/
    public function actionSearchfulltext()
    {
        $json = array();
        $json[] = array();
        $e = Yii::$app->request->post()['e'];
        $recipes_collections = Yii::$app->request->post()['recipes_collections'];
        $dishes = Dishes::find()->where(['like', 'name', $e])->andWhere(['in', 'recipes_collection_id', $recipes_collections])->orderBy(['techmup_number' => SORT_ASC])->all();

        $field = array();
        foreach ($dishes as $dish)
        {
            $short_title_recipes_collections = RecipesCollection::find()->where(['id' => $dish->recipes_collection_id])->one()->short_title;
            //$field[$i] = $dish->name;
            if ($dish->dishes_category_id == 7)
            {
                $field[] = array('id' => $dish->id, 'name' => $dish->name, 'techmup_number' => $dish->techmup_number, 'recipes_collections' => $short_title_recipes_collections, 'yield' => 200);
            }
            else
            {
                $field[] = array('id' => $dish->id, 'name' => $dish->name, 'techmup_number' => $dish->techmup_number, 'recipes_collections' => $short_title_recipes_collections, 'yield' => $dish->yield);
            }
        }
        $result = array("field" => $field);
        return json_encode($result);
    }

    public function actionSearchfulltextuser()
    {
        $json = array();
        $json[] = array();
        $e = Yii::$app->request->post()['e'];
        $recipes_collections = Yii::$app->request->post()['recipes_collections'];
        $dishes = Dishes::find()->where(['like', 'short_name', $e])->andWhere(['in', 'recipes_collection_id', $recipes_collections])->orderBy(['techmup_number' => SORT_ASC])->all();

        $field = array();
        foreach ($dishes as $dish)
        {
            $short_title_recipes_collections = RecipesCollection::find()->where(['id' => $dish->recipes_collection_id])->one()->short_title;
            //$field[$i] = $dish->name;
            if ($dish->dishes_category_id == 7)
            {
                $field[] = array('id' => $dish->id, 'short_name' => $dish->short_name, 'techmup_number' => $dish->techmup_number, 'recipes_collections' => $short_title_recipes_collections, 'yield' => 200);
            }
            else
            {
                $field[] = array('id' => $dish->id, 'short_name' => $dish->short_name, 'techmup_number' => $dish->techmup_number, 'recipes_collections' => $short_title_recipes_collections, 'yield' => $dish->yield);
            }
        }
        $result = array("field" => $field);
        return json_encode($result);
    }

    //ЭТО ПЕЧАТЬ
    public function actionTechmupExport($id, $indicator)
    {

        require_once __DIR__ . '/../../vendor/autoload.php';

        if ($indicator != 1)
        {
            $this->layout = false;
            $menus_dishes = MenusDishes::findOne($id);
            $dishes = Dishes::findOne($menus_dishes->dishes_id);
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $menus_dishes->dishes_id])->all();
            //$indicator = $menus_dishes->yield / 100;
        }
        else
        {
            $this->layout = false;
            $indicator = 1;
            $dishes = Dishes::findOne($id);
            if (empty($dishes))
            {
                $dishes = Dishes::findOne(MenusDishes::findOne($id)->dishes_id);
                $id = $dishes->id;
            }

            $dishes_products = DishesProducts::find()->
            select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
            leftJoin('products', 'dishes_products.products_id = products.id')->
            leftJoin('products_category', 'products.products_category_id = products_category.id')->
            where(['dishes_id' => $id])->
            orderby(['sort' => SORT_ASC])->
            all();
        }

        $super_total_yield = 0;
        $super_total_protein = 0;
        $super_total_fat = 0;
        $super_total_carbohydrates_total = 0;
        $super_total_energy_kkal = 0;
        $super_total_vitamin_a = 0;
        $super_total_vitamin_c = 0;
        $super_total_vitamin_b1 = 0;
        $super_total_vitamin_b2 = 0;
        $super_total_vitamin_d = 0;
        $super_total_vitamin_pp = 0;
        $super_total_na = 0;
        $super_total_k = 0;
        $super_total_ca = 0;
        $super_total_f = 0;
        $super_total_se = 0;
        $super_total_i = 0;
        $super_total_fe = 0;
        $super_total_p = 0;
        $super_total_mg = 0;
        $number_row = 1;
        $html = '
            <p class="mb-0"><b>Технологическая карта кулинарного изделия (блюда):</b> ' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование изделия:</b>' . $dishes->name . '</p>
            <p class="mb-0"><b>Номер рецептуры:</b>' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование сборника рецептур, год выпуска, автор:</b>' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->name . ', ' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->year . ' </p>
            <b>Пищевые вещества:</b><br>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Наименование сырья</th>
                    <th class="text-center">Брутто, г.</th>
                    <th class="text-center">Нетто, г.</th>
                    <th class="text-center">Белки, г.</th>
                    <th class="text-center">Жиры, г.</th>
                    <th class="text-center">Углеводы, г.</th>
                    <th class="text-center">Энергетическая ценность, ккал.</th>
                </tr>
        ';

        foreach ($dishes_products as $d_product)
        {

            $html .= '
            <tr>
                <td class="text-center">' . $number_row . '</td>
                <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->gross_weight * $indicator) . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->net_weight * $indicator) . '</td>
                <td class="text-center">' . $protein = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'protein') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $fat = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'fat') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $carbohydrates_total = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'carbohydrates_total') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $energy_kkal = sprintf("%.1f", $d_product->get_kkal($d_product->products_id, $d_product->dishes_id) * (($d_product->net_weight) / 100) * $indicator) . '</td>
            </tr>';
            $super_total_protein = $super_total_protein + $protein;
            $super_total_fat = $super_total_fat + $fat;
            $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
            $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;
            $number_row++;
        }
        $html .= '
        <tr>
            <td colspan="3"><b>Выход:</b></td>
            <td class="text-center"><b>' . round(($dishes->yield * $indicator), 1) . '</b></td>
            <td class="text-center"><b>' . $super_total_protein . '</b></td>
            <td class="text-center"><b>' . $super_total_fat . '</b></td>
            <td class="text-center"><b>' . $super_total_carbohydrates_total . '</b></td>
            <td class="text-center"><b>' . $super_total_energy_kkal . '</b></td>
        </tr>
        ';
        $html .= '</table>';
        $html .= ' <br><b>Витамины и минеральные вещества</b>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr class="">
                    <th class="text-center">№</th>
                    <th class="text-center">Продукт</th>
                    <th class="text-center">B1, мг</th>
                    <th class="text-center">B2, мг</th>
                    <th class="text-center">А, мкг. рет. экв.</th>
                    <th class="text-center">РР, мг.</th>
                    <th class="text-center">C, мг.</th>
                    <th class="text-center">Na, мг.</th>
                    <th class="text-center">K, мг.</th>
                    <th class="text-center">Ca, мг.</th>
                    <th class="text-center">Mg, мг.</th>
                    <th class="text-center">P, мг.</th>
                    <th class="text-center">FE, мг.</th>
                    <th class="text-center">I, мкг.</th>
                    <th class="text-center">Se, мкг.</th>
            </tr>
        ';

        $number_row = 1;
        foreach ($dishes_products as $d_product)
        {
            $html .= '
           <tr>
               <td class="text-center">' . $number_row . '</td>
               <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
               <td class="text-center">' . $vitamin_b1 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b1') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_b2 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b2') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_a = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_a') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_pp = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_pp') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_c = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_c') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $na = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'na') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $k = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'k') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $ca = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'ca') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $mg = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'mg') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $p = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'p') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $fe = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'fe') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $i = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'i') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $se = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'se') * (($d_product->net_weight) / 100) * $indicator) . '</td>
           </tr>';

            $number_row++;
            $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
            $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
            $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
            $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
            $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
            $super_total_na = $super_total_na + $na;
            $super_total_k = $super_total_k + $k;
            $super_total_ca = $super_total_ca + $ca;
            $super_total_mg = $super_total_mg + $mg;
            $super_total_p = $super_total_p + $p;
            $super_total_fe = $super_total_fe + $fe;
            $super_total_i = $super_total_i + $i;
            $super_total_se = $super_total_se + $se;
        }
        $html .= ' 
            <tr>
                <td colspan="2"><b>Итого</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b1 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b2 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_a . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_pp . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_c . '</b></td>
                <td class="text-center"><b>' . $super_total_na . '</b></td>
                <td class="text-center"><b>' . $super_total_k . '</b></td>
                <td class="text-center"><b>' . $super_total_ca . '</b></td>
                <td class="text-center"><b>' . $super_total_mg . '</b></td>
                <td class="text-center"><b>' . $super_total_p . '</b></td>
                <td class="text-center"><b>' . $super_total_fe . '</b></td>
                <td class="text-center"><b>' . $super_total_i . '</b></td>
                <td class="text-center"><b>' . $super_total_se . '</b></td>
            </tr>
        ';
        $html .= '</table>';
        $html .= '
            <p class="mb-0"><b>Способ обработки:</b>' . $dishes->get_culinary_processing($dishes->culinary_processing_id) . '</p>
            <p class="mb-0"><b>Технология приготовления:</b> ' . $dishes->description . '</p>
            <b>Характеристика блюда на выходе:</b>
            <p class="mb-0">' . $dishes->dishes_characters . '</p>
        ';
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'); //номер страницы {PAGENO}
        $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
        //$mpdf->Output('MyPDF.pdf', 'I'); //I - откроет в томже окне файл!
        //$mpdf->Output('MyPDF123123.pdf', 'F'); //F - гененирует ссылку на файл и сохранить его на сервере путь сохр backend\web!


    }

    //ЭТО ПЕЧАТЬ В ПДФ !!!!
    public function actionMenusDaysExport($menu_id, $cycle, $days_id)
    {
        //print_r(Yii::$app->request->post());
        //print_r($post);
        $model = new MenusDishes();
        require_once __DIR__ . '/../../vendor/autoload.php';
        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }
        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
        //$menus_dishes = MenusDishes::find()->where(['menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $days_id])->orderby(['nutrition_id' => SORT_ASC])->all();
        $menus_dishes = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $days_id])->
        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
        all();
        $super_total_yield = 0;
        $super_total_protein = 0;
        $super_total_fat = 0;
        $super_total_carbohydrates_total = 0;
        $super_total_energy_kkal = 0;
        $super_total_vitamin_a = 0;
        $super_total_vitamin_c = 0;
        $super_total_vitamin_b1 = 0;
        $super_total_vitamin_b2 = 0;
        $super_total_vitamin_d = 0;
        $super_total_vitamin_pp = 0;
        $super_total_na = 0;
        $super_total_k = 0;
        $super_total_ca = 0;
        $super_total_f = 0;
        $super_total_se = 0;

        $html = '
            <div class="block" style="margin-top: 10px;">';
        foreach ($nutritions as $nutrition)
        {
            //print_r($nutrition->name);
            //exit();

            $html .= '
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                 /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                ">
                    <thead>
                    <tr class="text-center"><td colspan="17" align="center"><p  style="font-size: 20px; ">' . $nutrition->name . '</p></td></tr>
                    <tr>
                        <th class="text-center align-middle" rowspan="2">Название блюда</th>
                        <th class="text-center align-middle" rowspan="2">Выход</th>
                        <th class="text-center align-middle" rowspan="2">Белки</th>
                        <th class="text-center align-middle" rowspan="2">Жиры</th>
                        <th class="text-center align-middle" rowspan="2">Углеводы</th>
                        <th class="text-center align-middle" rowspan="2">Эн. ценность</th>
                        <th class="text-center" colspan="6">Витамины</th>
                        <th class="text-center" colspan="6">Минеральные вещества</th>
                    </tr>
                    <tr>
                        <th class="text-center">A, мкг рет.экв</th>
                        <th class="text-center">C, мг</th>
                        <th class="text-center">B1, мг</th>
                        <th class="text-center">B2, мг</th>
                        <th class="text-center">D, мкг</th>
                        <th class="text-center">PP, мг</th>
                        <th class="text-center">Na, мг</th>
                        <th class="text-center">K, мг</th>
                        <th class="text-center">Ca, мг</th>
                        <th class="text-center">F, мг</th>
                        <th class="text-center">Se, мкг</th>
                    </tr>
                    </thead>
                    <!--<tbody>-->
                ';
            $count = 0;
            $indicator = 0;
            $energy_kkal = 0;
            $protein = 0;
            $fat = 0;
            $carbohydrates_total = 0;

            foreach ($menus_dishes as $key => $m_dish)
            {
                // print_r($m_dish->id);
                // exit();
                if ($nutrition->id == $m_dish->nutrition_id)
                {
                    $count++;

                    $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'), 1);
                    $protein = $protein_dish + $protein;
                    $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'), 1);
                    $fat = $fat_dish + $fat;
                    $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'), 1);
                    $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total;
                    $kkal = round($m_dish->get_kkal_dish($m_dish->id), 1);
                    $energy_kkal = $energy_kkal + $kkal;

                    $html .= '
                        <tr data-id="' . $m_dish->id . '">
                            <td>' . $m_dish->get_dishes($m_dish->dishes_id) . '</td>
                            <td class="text-center">' . $m_dish->yield . '</td>
                            <td class="text-center">' . $protein_dish . '</td>
                            <td class="text-center">' . $fat_dish . '</td>
                            <td class="text-center">' . $carbohydrates_total_dish . '</td>
                            <td class="text-center">' . $kkal . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'), 3) . '</td>
                        </tr>';
                    unset($menus_dishes[$key]);
                }
                else
                {
                    break;
                }
            }
            if ($count > 0)
            {
                $html .= '
                    <tr class="table-primary" style="background: lightskyblue;">
                        <td>Итого за <b>' . $nutrition->name . '</b></td>
                        <td class="text-center">' . $yield = $model->get_total_yield($menu_id, $cycle, $days_id, $nutrition->id) . '</td>
                        <td class="text-center">' . $protein . '</td>
                        <td class="text-center">' . $fat . '</td>
                        <td class="text-center">' . $carbohydrates_total . '</td>
                        <td class="text-center">' . $energy_kkal . '</td>
                        <td class="text-center">' . $vitamin_a = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'vitamin_a'), 2) . '</td>
                        <td class="text-center">' . $vitamin_c = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'vitamin_c'), 2) . '</td>
                        <td class="text-center">' . $vitamin_b1 = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'vitamin_b1'), 2) . ' </td>
                        <td class="text-center">' . $vitamin_b2 = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'vitamin_b2'), 2) . '</td>
                        <td class="text-center">' . $vitamin_d = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'vitamin_d'), 2) . '</td>
                        <td class="text-center">' . $vitamin_pp = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'vitamin_pp'), 2) . '</td>
                        <td class="text-center">' . $na = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'na'), 3) . '</td>
                        <td class="text-center">' . $k = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'k'), 3) . '</td>
                        <td class="text-center">' . $ca = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'ca'), 3) . '</td>
                        <td class="text-center">' . $f = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'f'), 3) . '</td>
                        <td class="text-center">' . $se = round($model->get_total_vitamin($menu_id, $cycle, $days_id, $nutrition->id, 'se'), 3) . '</td>
                    </tr>
                    <tr class="table-success" >
                        <td style="background: aquamarine;" colspan="2">Рекомендуемая величина</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'protein_middle_weight') . '</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'fat_middle_weight') . '</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'carbohydrates_middle_weight') . '</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'middle_kkal') . '</td>
                    </tr>
                    <tr class="table-warning" >
                        <td colspan="2" style="background: moccasin;">Процент от общей массы пищевых веществ</td>
                        <td class="text-center" style="background: moccasin;">' . $model->get_procent($menu_id, $cycle, $days_id, $nutrition->id, 'protein') . '%</td>
                        <td class="text-center" style="background: moccasin;">' . $model->get_procent($menu_id, $cycle, $days_id, $nutrition->id, 'fat') . '%</td>
                        <td class="text-center" style="background: moccasin;">' . $model->get_procent($menu_id, $cycle, $days_id, $nutrition->id, 'carbohydrates_total') . '%</td>
                    </tr>
                    <tr class="table-info">
                        <td style="background: aquamarine;">Процент от суток</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_yield($menu_id, $cycle, $days_id, $nutrition->id) . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $days_id, $nutrition->id, 'protein') . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $days_id, $nutrition->id, 'fat') . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $days_id, $nutrition->id, 'carbohydrates_total') . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $days_id, $nutrition->id, 'energy_kkal') . '%</td>
                    </tr>
                ';
                $super_total_yield = $super_total_yield + $yield;
                $super_total_protein = $super_total_protein + $protein;
                $super_total_fat = $super_total_fat + $fat;
                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;
                $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d;
                $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                $super_total_na = $super_total_na + $na;
                $super_total_k = $super_total_k + $k;
                $super_total_ca = $super_total_ca + $ca;
                $super_total_f = $super_total_f + $f;
                $super_total_se = $super_total_se + $se;
            }

            //$html .= '</tbody>';
            $html .= '</table>';
            $html .= '<br>';
        }

        $html .= '
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                 /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                ">';
        $html .= '
            <thead>
                <tr class="text-center"><td colspan="16" align="center"><p  style="font-size: 20px; ">Итого за день</p></td></tr>
                    <tr>
                        <!--<th class="text-center align-middle" rowspan="2">                 </th>-->
                        <th class="text-center align-middle" rowspan="2">Выход</th>
                        <th class="text-center align-middle" rowspan="2">Белки</th>
                        <th class="text-center align-middle" rowspan="2">Жиры</th>
                        <th class="text-center align-middle" rowspan="2">Углеводы</th>
                        <th class="text-center align-middle" rowspan="2">Эн. ценность</th>
                        <th class="text-center" colspan="6">Витамины</th>
                        <th class="text-center" colspan="6">Минеральные вещества</th>
                    </tr>
                    <tr>
                        <th class="text-center">A, мкг рет.экв</th>
                        <th class="text-center">C, мг</th>
                        <th class="text-center">B1, мг</th>
                        <th class="text-center">B2, мг</th>
                        <th class="text-center">D, мкг</th>
                        <th class="text-center">PP, мг</th>
                        <th class="text-center">Na, мг</th>
                        <th class="text-center">K, мг</th>
                        <th class="text-center">Ca, мг</th>
                        <th class="text-center">F, мг</th>
                        <th class="text-center">Se, мкг</th>
                    </tr>
            </thead>
            <tr class="itog_day table-danger">
                <!--<td>Итого за день </td>-->
                <td class="text-center" style="background: lightcoral;">' . $super_total_yield . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_protein . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_fat . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_carbohydrates_total . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_energy_kkal . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_a . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_c . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_b1 . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_b2 . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_d . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_pp . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_na . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_k . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_ca . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_f . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_se . '</td>
            </tr>';
        $html .= '
            <tr class="procent_day table-danger">
                <td colspan="1" style="background: pink;">Процентное соотношение БЖУ за день</td>
                <td class="text-center" style="background: pink;">' . $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'protein') . '%</td>
                <td class="text-center" style="background: pink;">' . $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'fat') . '%</td>
                <td class="text-center" style="background: pink;">' . $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'carbohydrates_total') . '%</td>
            </tr>';

        $html .= '</table>';
        $html .= '</div>';
        $mpdf = new Mpdf (['margin_left' => '5', 'margin_right' => '5', 'margin_top' => '10', 'margin_bottom' => '5']);;
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"  {PAGENO}</div>'); //номер страницы {PAGENO}
        $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
    }

    public function actionExportExcel2($id, $indicator)
    {

        require_once Yii::$app->basePath . '\Excel\PHPExcel.php';
        require_once Yii::$app->basePath . '\Excel\PHPExcel\IOFactory.php';

        if ($indicator != 1)
        {
            $this->layout = false;
            $menus_dishes = MenusDishes::findOne($id);
            $dishes = Dishes::findOne($menus_dishes->dishes_id);
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $menus_dishes->dishes_id])->all();
            //$indicator = $menus_dishes->yield / 100;
        }
        else
        {
            $this->layout = false;
            $indicator = 1;
            $dishes = Dishes::findOne($id);
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $id])->all();
        }
        $super_total_yield = 0;
        $super_total_protein = 0;
        $super_total_fat = 0;
        $super_total_carbohydrates_total = 0;
        $super_total_energy_kkal = 0;
        $super_total_vitamin_a = 0;
        $super_total_vitamin_c = 0;
        $super_total_vitamin_b1 = 0;
        $super_total_vitamin_b2 = 0;
        $super_total_vitamin_d = 0;
        $super_total_vitamin_pp = 0;
        $super_total_na = 0;
        $super_total_k = 0;
        $super_total_ca = 0;
        $super_total_f = 0;
        $super_total_se = 0;
        $super_total_i = 0;
        $super_total_fe = 0;
        $super_total_p = 0;
        $super_total_mg = 0;
        $number_row = 1;
        $document = new \PHPExcel();
        //подгружаем готовый шаблон !!!!
        //$document = \PHPExcel_IOFactory::load('../web/images/generator.xlsx');
        ob_start();

        $sheet = $document->getActiveSheet();
        $sheet->setCellValue("A1", "Наименование продукта");
        //$sheet->setCellValue('A' . $numRow, $array_org[$i][2]);
        $sheet->setCellValue('A' . 2, $dishes->name);
        $filename = 'generator_' . date('Y_m_d_H_i', time()) . '.xlsx';
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    public function actionExemple2()
    {
        //print_r(123);
        //exit();
        //require '/../../vendor/autoload.php';
        require '../../vendor/autoload.php';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ауауа')
            ->setCellValue('B1', 'паапапа')
            ->setCellValue('C1', 'ааааа')
            ->setCellValue('D1', 'ииии')
            ->setCellValue('E1', 'ссс');

        $filename = 'Output.xlsx'; //save our workbook as this file name
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
        //print_r(123);

        /*//Создаем экземпляр класса электронной таблицы
        $spreadsheet = new Spreadsheet();
        //Получаем текущий активный лист
        $sheet = $spreadsheet->getActiveSheet();
        // Записываем в ячейку A1 данные
        $sheet->setCellValue('A1', 'Hello my Friend!');

        //$writer = new Xlsx($spreadsheet);
        //Сохраняем файл в текущей папке, в которой выполняется скрипт.
        //Чтобы указать другую папку для сохранения.
        //Прописываем полный путь до папки и указываем имя файла
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('hello.xlsx');*/
        /*
         $sOutFile = 'out.xlsx';

         $oSpreadsheet_Out = new Spreadsheet();

         $oSpreadsheet_Out->getProperties()->setCreator('Maarten Balliauw')
             ->setLastModifiedBy('Maarten Balliauw')
             ->setTitle('Office 2007 XLSX Test Document')
             ->setSubject('Office 2007 XLSX Test Document')
             ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
             ->setKeywords('office 2007 openxml php')
             ->setCategory('Test result file');
             // Add some data
         $oSpreadsheet_Out->setActiveSheetIndex(0)
             ->setCellValue('A1', 'Привет 123')
             ->setCellValue('B2', 'world!')
             ->setCellValue('C1', 'Hello')
             ->setCellValue('D2', 'world!');

         $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
         $oWriter->save($sOutFile);*/
    }

    //ЭТО ПЕЧАТЬ В ПДФ Отчета о поторяемости!!!!
    public function actionRepeatReportExport($id)
    {
        //print_r('fdfdfdf');
        //exit();
        require_once __DIR__ . '/../../vendor/autoload.php';
        $menus_dishes = MenusDishes::find()->where(['menu_id' => $id])->orderby(['dishes_id' => SORT_ASC])->all();
        $menus_copy_dishes = $menus_dishes;
        $used_ids = [];
        $array_num = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ',
            'EA',
            'EB',
            'EC',
            'ED',
            'EE',
            'EF',
            'EG',
            'EH',
            'EI',
            'EJ',
            'EK',
            'EL',
            'EM',
            'EN',
            'EO',
            'EP',
            'EQ',
            'ER',
            'ES',
            'ET',
            'EU',
            'EV',
            'EW',
            'EX',
            'EY',
            'EZ',
            'FA',
            'FB',
            'FC',
            'FD',
            'FE',
            'FF',
            'FG',
            'FH',
            'FI',
            'FJ',
            'FK',
            'FL',
            'FM',
            'FN',
            'FO',
            'FP',
            'FQ',
            'FR',
            'FS',
            'FT',
            'FU',
            'FV',
            'FW',
            'FX',
            'FY',
            'FZ',
        ];
        $html = '
           <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr class="">
                    <th class="text-center main-info-see">№</th>
                    <th class="text-center main-info-see">Наименование блюда</th>
                    <th class="text-center main-info-see">Дни совпадений</th>
                    <th class="text-center main-info-see">Количество совпадений</th>
                </tr>
          
        ';
        $number_row = 1;
        foreach ($menus_dishes as $m_dish)
        {
            $count = 0;
            if (!in_array($m_dish->dishes_id, $used_ids))
            {
                $used_ids[] = $m_dish->dishes_id;
                $html .= '<tr>
                <td class="text-center main-info-see">' . $number_row . '</td>
                <td class="text-center">' . $m_dish->get_dishes($m_dish->dishes_id) . '</td>
                <td class="text-left">';

                $cycle_days_ids = [];
                foreach ($menus_copy_dishes as $m_copy_dish)
                {
                    if ($m_copy_dish->dishes_id == $m_dish->dishes_id && !in_array($m_dish->get_days($m_copy_dish->days_id) . '_' . $m_copy_dish->cycle, $cycle_days_ids))
                    {

                        $html .= $m_dish->get_days($m_copy_dish->days_id) . ' ' . $m_copy_dish->cycle . ' недели <br>';
                        $cycle_days_ids[] = $m_dish->get_days($m_copy_dish->days_id) . '_' . $m_copy_dish->cycle;
                    }
                }
                $html .= '</td>';

                $html .= '<td class="text-center">';

                foreach ($menus_copy_dishes as $m_copy_dish)
                {
                    if ($m_copy_dish->dishes_id == $m_dish->dishes_id)
                    {
                        $count++;

                    }
                }
                $html .= $count . '</td>';
                $html .= '</tr>';
                $number_row++;
            }
        }
        $html .= '</table>';
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'); //номер страницы {PAGENO}
        $mpdf->Output('Отчет о повторяемости.pdf', 'D'); //D - скачает файл!
    }

    public function actionExportExcel4($id, $indicator)
    {

        require_once Yii::$app->basePath . '\Excel\PHPExcel.php';
        require_once Yii::$app->basePath . '\Excel\PHPExcel\IOFactory.php';


        $document = new \PHPExcel();
        //подгружаем готовый шаблон !!!!
        //$document = \PHPExcel_IOFactory::load('../web/images/generator.xlsx');
        ob_start();
        $num = 2;
        $sheet = $document->getActiveSheet();
        $num_st = 1;
        $this->layout = false;
        $indicator = 1;
        $dishes = Dishes::find()->all();
        $sheet->setCellValue('A' . $num_st, "Наименование продукта");
        $sheet->setCellValue('B' . $num_st, "Номер рецептуры");
        $sheet->setCellValue('C' . $num_st, "Наименование сборника рецептур, год выпуска, автор");
        $sheet->setCellValue('D' . $num_st, "№");
        $sheet->setCellValue('E' . $num_st, "Наименование сырья");
        $sheet->setCellValue('F' . $num_st, "Брутто, г.");
        $sheet->setCellValue('G' . $num_st, "Нетто, г.");
        $sheet->setCellValue('H' . $num_st, "Белки, г.");
        $sheet->setCellValue('I' . $num_st, "Жиры, г.");
        $sheet->setCellValue('J' . $num_st, "Углеводы, г.");
        $sheet->setCellValue('K' . $num_st, "Энергетическая ценность, ккал.");
        $sheet->setCellValue('L' . $num_st, "B1, мг");
        $sheet->setCellValue('M' . $num_st, "B2, мг");
        $sheet->setCellValue('N' . $num_st, "А, мкг. рет. экв.");
        $sheet->setCellValue('O' . $num_st, "РР, мг.");
        $sheet->setCellValue('P' . $num_st, "C, мг.");
        $sheet->setCellValue('Q' . $num_st, "Na, мг.");
        $sheet->setCellValue('R' . $num_st, "K, мг.");
        $sheet->setCellValue('S' . $num_st, "Ca, мг.");
        $sheet->setCellValue('T' . $num_st, "Mg, мг.");
        $sheet->setCellValue('U' . $num_st, "P, мг.");
        $sheet->setCellValue('V' . $num_st, "Fe, мг.");
        $sheet->setCellValue('W' . $num_st, "I, мкг.");
        $sheet->setCellValue('X' . $num_st, "Se, мкг.");
        foreach ($dishes as $dish)
        {
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $dish->id])->all();


            //$sheet->setCellValue('A' . $numRow, $array_org[$i][2]);
            $sheet->setCellValue('A' . $num, $dish->name);
            $sheet->setCellValue('B' . $num, $dish->techmup_number);
            $sheet->setCellValue('C' . $num, $dish->get_recipes_collection($dish->recipes_collection_id)->name);
            $super_total_yield = 0;
            $super_total_protein = 0;
            $super_total_fat = 0;
            $super_total_carbohydrates_total = 0;
            $super_total_energy_kkal = 0;
            $super_total_vitamin_a = 0;
            $super_total_vitamin_c = 0;
            $super_total_vitamin_b1 = 0;
            $super_total_vitamin_b2 = 0;
            $super_total_vitamin_d = 0;
            $super_total_vitamin_pp = 0;
            $super_total_na = 0;
            $super_total_k = 0;
            $super_total_ca = 0;
            $super_total_f = 0;
            $super_total_se = 0;
            $super_total_i = 0;
            $super_total_fe = 0;
            $super_total_p = 0;
            $super_total_mg = 0;
            $number_row = 1;
            foreach ($dishes_products as $d_product)
            {

                $sheet->setCellValue('D' . $num, $number_row);
                $sheet->setCellValue('E' . $num, $d_product->get_products($d_product->products_id)->name);
                $sheet->setCellValue('F' . $num, sprintf("%.1f", $d_product->gross_weight * $indicator));
                $sheet->setCellValue('G' . $num, sprintf("%.1f", $d_product->net_weight * $indicator));
                $protein = sprintf("%.1f", $d_product->get_products($d_product->products_id)->protein * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('H' . $num, $protein);
                $fat = sprintf("%.1f", $d_product->get_products($d_product->products_id)->fat * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('I' . $num, $fat);
                $carbohydrates_total = sprintf("%.1f", $d_product->get_products($d_product->products_id)->carbohydrates_total * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('J' . $num, $carbohydrates_total);
                $energy_kkal = sprintf("%.1f", $d_product->get_products($d_product->products_id)->energy_kkal * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('K' . $num, $energy_kkal);

                $vitamin_b1 = sprintf("%.2f", $d_product->get_products($d_product->products_id)->vitamin_b1 * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('L' . $num, $vitamin_b1);
                $vitamin_b2 = sprintf("%.2f", $d_product->get_products($d_product->products_id)->vitamin_b2 * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('M' . $num, $vitamin_b2);
                $vitamin_a = sprintf("%.2f", $d_product->get_products($d_product->products_id)->vitamin_a * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('N' . $num, $vitamin_a);
                $vitamin_pp = sprintf("%.2f", $d_product->get_products($d_product->products_id)->vitamin_pp * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('O' . $num, $vitamin_pp);
                $vitamin_c = round(($d_product->get_products($d_product->products_id)->vitamin_c * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('P' . $num, $vitamin_c);
                $na = round(($d_product->get_products($d_product->products_id)->na * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('Q' . $num, $na);
                $k = round(($d_product->get_products($d_product->products_id)->k * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('R' . $num, $k);
                $ca = round(($d_product->get_products($d_product->products_id)->ca * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('S' . $num, $ca);
                $mg = round(($d_product->get_products($d_product->products_id)->mg * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('T' . $num, $mg);
                $p = round(($d_product->get_products($d_product->products_id)->p * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('U' . $num, $p);
                $fe = round(($d_product->get_products($d_product->products_id)->fe * (($d_product->net_weight) / 100) * $indicator), 0);
                $sheet->setCellValue('V' . $num, $fe);
                $i = sprintf("%.1f", $d_product->get_products($d_product->products_id)->i * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('W' . $num, $i);
                $se = sprintf("%.1f", $d_product->get_products($d_product->products_id)->se * (($d_product->net_weight) / 100) * $indicator);
                $sheet->setCellValue('X' . $num, $se);

                $number_row++;
                $num++;
                $super_total_protein = $super_total_protein + $protein;
                $super_total_fat = $super_total_fat + $fat;
                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;

                $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                $super_total_na = $super_total_na + $na;
                $super_total_k = $super_total_k + $k;
                $super_total_ca = $super_total_ca + $ca;
                $super_total_mg = $super_total_mg + $mg;
                $super_total_p = $super_total_p + $p;
                $super_total_fe = $super_total_fe + $fe;
                $super_total_i = $super_total_i + $i;
                $super_total_se = $super_total_se + $se;
            }
            $sheet->setCellValue('G' . $num, $dish->yield * $indicator);
            $sheet->setCellValue('H' . $num, $super_total_protein);
            $sheet->setCellValue('I' . $num, $super_total_fat);
            $sheet->setCellValue('J' . $num, $super_total_carbohydrates_total);
            $sheet->setCellValue('K' . $num, $super_total_energy_kkal);

            $sheet->setCellValue('L' . $num, $super_total_vitamin_b1);
            $sheet->setCellValue('M' . $num, $super_total_vitamin_b2);
            $sheet->setCellValue('N' . $num, $super_total_vitamin_a);
            $sheet->setCellValue('O' . $num, $super_total_vitamin_pp);
            $sheet->setCellValue('P' . $num, $super_total_vitamin_c);
            $sheet->setCellValue('Q' . $num, $super_total_na);
            $sheet->setCellValue('R' . $num, $super_total_k);
            $sheet->setCellValue('S' . $num, $super_total_ca);
            $sheet->setCellValue('T' . $num, $super_total_mg);
            $sheet->setCellValue('U' . $num, $super_total_p);
            $sheet->setCellValue('V' . $num, $super_total_fe);
            $sheet->setCellValue('W' . $num, $super_total_i);
            $sheet->setCellValue('X' . $num, $super_total_se);
            $num++;
            $sheet->setCellValue('Y' . $num, "--");
            $num++;
        }
        $filename = 'generator_' . date('Y_m_d_H_i', time()) . '.xlsx';
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    public function actionExportMenusPeriod($menu_id, $cycle, $him)
    {

        require_once Yii::$app->basePath . '\Excel\PHPExcel.php';
        require_once Yii::$app->basePath . '\Excel\PHPExcel\IOFactory.php';


        $document = new \PHPExcel();
        //подгружаем готовый шаблон !!!!
        //$document = \PHPExcel_IOFactory::load('../web/images/generator.xlsx');
        ob_start();

        $model = new MenusDishes();
        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }
        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

        $menus_dishes = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->
        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
        all();

        $menus_days_id = MenusDays::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
        $days_ids = [];
        foreach ($menus_days_id as $day_id)
        {
            $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
        }

        $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
        $count_my_days = MenusDays::find()->where(['menu_id' => $menu_id])->count();
        $my_menus = Menus::findOne($menu_id);
        $normativ_vitamin_day_vitamin_a = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_a', 'age_info_id' => $my_menus->age_info_id])->one()->value;
        $normativ_vitamin_day_k = \common\models\NormativVitaminDay::find()->where(['name' => 'k', 'age_info_id' => $my_menus->age_info_id])->one()->value;

        $menu_cycle_count = $my_menus->cycle;

        if ($cycle == 0)
        {
            $count_my_days = $count_my_days * $menu_cycle_count;
        }

        $cycle_ids = [];
        if ($cycle != 0)
        {
            $cycle_ids[$cycle] = $cycle;
        }
        else
        {
            for ($i = 1; $i <= $menu_cycle_count; $i++)
            {
                $cycle_ids[$i] = $i;//массив из подходящи циклов
            }
        }


        $num = 3;
        $sheet = $document->getActiveSheet();
        $num_st = 2;
        $this->layout = false;

        $sheet->getColumnDimension('A')->setWidth("30");
        $sheet->getColumnDimension('B')->setWidth("50");
        $sheet->getColumnDimension('G')->setWidth("30");

        $sheet->getStyle("B1")->getFont()->setBold(true);
        $sheet->getStyle("A2:V2")->getFont()->setBold(true);

        $sheet->setCellValue('B' . 1, $my_menus->name);

        $sheet->setCellValue('A' . $num_st, "№ рецептуры");
        $sheet->setCellValue('B' . $num_st, "Название блюда");
        $sheet->setCellValue('C' . $num_st, "Масса, г");
        $sheet->setCellValue('D' . $num_st, "Белки, г");
        $sheet->setCellValue('E' . $num_st, "Жиры, г");
        $sheet->setCellValue('F' . $num_st, "Углеводы, г.");
        $sheet->setCellValue('G' . $num_st, "Калорийность, ккал.");

        if ($him == 1)
        {
            $sheet->setCellValue('H' . $num_st, "A, мкг рет.экв");
            $sheet->setCellValue('I' . $num_st, "C, мг");
            $sheet->setCellValue('J' . $num_st, "B1, мг");
            $sheet->setCellValue('K' . $num_st, "B2, мг");
            $sheet->setCellValue('L' . $num_st, "D, мкг");
            $sheet->setCellValue('M' . $num_st, "PP, мг");
            $sheet->setCellValue('N' . $num_st, "Na, мг");
            $sheet->setCellValue('O' . $num_st, "K, мг");
            $sheet->setCellValue('P' . $num_st, "Ca, мг");
            $sheet->setCellValue('Q' . $num_st, "F, мкг");
            $sheet->setCellValue('R' . $num_st, "Mg, мг");
            $sheet->setCellValue('S' . $num_st, "P, мг");
            $sheet->setCellValue('T' . $num_st, "Fe, мг");
            $sheet->setCellValue('U' . $num_st, "I, мкг");
            $sheet->setCellValue('V' . $num_st, "Se, мкг");
        }

        $count_cycle = 0;
        $count = 0;
        $data = [];
        foreach ($cycle_ids as $cycle_id)
        {
            $count++;
            foreach ($days as $day)
            {
                $sheet->setCellValue('B' . $num, $day->name . ', ' . $cycle_id . ' неделя');
                $sheet->getStyle("B" . $num)->getFont()->setBold(true);
                $sheet->getStyle("B" . $num)->getFont()->getColor()->setRGB('3d30cf');
                $num = $num + 1;

                $super_total_yield = 0;
                $super_total_protein = 0;
                $super_total_fat = 0;
                $super_total_carbohydrates_total = 0;
                $super_total_energy_kkal = 0;

                //vitamins
                $super_total_vitamin_a = 0;
                $super_total_vitamin_c = 0;
                $super_total_vitamin_b1 = 0;
                $super_total_vitamin_b2 = 0;
                $super_total_vitamin_d = 0;
                $super_total_vitamin_pp = 0;
                $super_total_na = 0;
                $super_total_k = 0;
                $super_total_ca = 0;
                $super_total_f = 0;
                $super_total_p = 0;
                $super_total_i = 0;
                $super_total_mg = 0;
                $super_total_fe = 0;
                $super_total_se = 0;
                //end vitamins
                foreach ($nutritions as $nutrition)
                {
                    $energy_kkal = 0;
                    $protein = 0;
                    $fat = 0;
                    $carbohydrates_total = 0;
                    $sheet->setCellValue('B' . $num, $nutrition->name);
                    $sheet->getStyle("B" . $num)->getFont()->setBold(true);
                    $num = $num + 1;
                    $vitamins = [];
                    unset($vitamins);
                    $vitamin_a = 0;
                    $vitamin_c = 0;
                    $vitamin_b1 = 0;
                    $vitamin_b2 = 0;
                    $vitamin_d = 0;
                    $vitamin_pp = 0;
                    $na = 0;
                    $k = 0;
                    $ca = 0;
                    $f = 0;
                    $p = 0;
                    $se = 0;
                    $i = 0;
                    $mg = 0;
                    $fe = 0;

                    foreach ($menus_dishes as $key => $m_dish)
                    {

                        if ($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id)
                        {
                            $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'), 1);
                            $protein = $protein_dish + $protein;
                            $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'), 1);
                            $fat = $fat_dish + $fat;
                            $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'), 1);
                            $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total;
                            $kkal = round($m_dish->get_kkal_dish($m_dish->id), 1);
                            $energy_kkal = $energy_kkal + $kkal;

                            //РАСЧЕТ ВИТАМИНА
                            $vitamins['vitamin_a'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'), 2);
                            $vitamin_a = $vitamin_a + $vitamins['vitamin_a'];
                            $vitamins['vitamin_c'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'), 2);
                            $vitamin_c = $vitamin_c + $vitamins['vitamin_c'];
                            $vitamins['vitamin_b1'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'), 2);
                            $vitamin_b1 = $vitamin_b1 + $vitamins['vitamin_b1'];
                            $vitamins['vitamin_b2'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'), 2);
                            $vitamin_b2 = $vitamin_b2 + $vitamins['vitamin_b2'];
                            $vitamins['vitamin_d'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'), 2);
                            $vitamin_d = $vitamin_d + $vitamins['vitamin_d'];
                            $vitamins['vitamin_pp'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'), 2);
                            $vitamin_pp = $vitamin_pp + $vitamins['vitamin_pp'];
                            $vitamins['na'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'), 2);
                            $na = $na + $vitamins['na'];
                            $vitamins['k'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'), 2);
                            $k = $k + $vitamins['k'];
                            $vitamins['ca'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'), 2);
                            $ca = $ca + $vitamins['ca'];
                            $vitamins['f'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'), 2);
                            $f = $f + $vitamins['f'];
                            $vitamins['mg'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'mg'), 2);
                            $mg = $mg + $vitamins['mg'];
                            $vitamins['p'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'p'), 2);
                            $p = $p + $vitamins['p'];
                            $vitamins['fe'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'fe'), 2);
                            $fe = $fe + $vitamins['fe'];
                            $vitamins['i'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'i'), 2);
                            $i = $i + $vitamins['i'];
                            $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'), 2);
                            $se = $se + $vitamins['se'];
                            //КОНЕЦ РАСЧЕТА


                            $sheet->setCellValue('A' . $num, $m_dish->get_techmup($m_dish->dishes_id));
                            $sheet->setCellValue('B' . $num, $m_dish->get_dishes($m_dish->dishes_id));
                            $sheet->setCellValue('C' . $num, $m_dish->yield);
                            $sheet->setCellValue('D' . $num, $protein_dish);
                            $sheet->setCellValue('E' . $num, $fat_dish);
                            $sheet->setCellValue('F' . $num, $carbohydrates_total_dish);
                            $sheet->setCellValue('G' . $num, $kkal);

                            if ($him == 1)
                            {
                                $sheet->setCellValue('H' . $num, round($vitamins['vitamin_a'], 2));
                                $sheet->setCellValue('I' . $num, round($vitamins['vitamin_c'], 2));
                                $sheet->setCellValue('J' . $num, round($vitamins['vitamin_b1'], 2));
                                $sheet->setCellValue('K' . $num, round($vitamins['vitamin_b2'], 2));
                                $sheet->setCellValue('L' . $num, round($vitamins['vitamin_d'], 2));
                                $sheet->setCellValue('M' . $num, round($vitamins['vitamin_pp'], 2));
                                $sheet->setCellValue('N' . $num, round($vitamins['na'], 2));
                                $sheet->setCellValue('O' . $num, round($vitamins['k'], 2));
                                $sheet->setCellValue('P' . $num, round($vitamins['ca'], 2));
                                $sheet->setCellValue('Q' . $num, round($vitamins['f'], 2));
                                $sheet->setCellValue('R' . $num, round($vitamins['mg'], 2));
                                $sheet->setCellValue('S' . $num, round($vitamins['p'], 2));
                                $sheet->setCellValue('T' . $num, round($vitamins['fe'], 2));
                                $sheet->setCellValue('U' . $num, round($vitamins['i'], 2));
                                $sheet->setCellValue('V' . $num, round($vitamins['se'], 2));
                            }

                            unset($menus_dishes[$key]);
                            $num = $num + 1;
                        }
                    }
                    $sheet->setCellValue('B' . $num, 'Итого за ' . $nutrition->name);

                    $yield = $model->get_total_yield($menu_id, $cycle_id, $day->id, $nutrition->id);
                    $data[$nutrition->id]['yield'] = $data[$nutrition->id]['yield'] + $yield;
                    $super_total_yield = $super_total_yield + $yield;
                    $data[$nutrition->id]['protein'] = $data[$nutrition->id]['protein'] + $protein;
                    $super_total_protein = $super_total_protein + $protein;
                    $data[$nutrition->id]['fat'] = $data[$nutrition->id]['fat'] + $fat;
                    $super_total_fat = $super_total_fat + $fat;
                    $data[$nutrition->id]['carbohydrates_total'] = $data[$nutrition->id]['carbohydrates_total'] + $carbohydrates_total;
                    $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                    $data[$nutrition->id]['energy_kkal'] = $data[$nutrition->id]['energy_kkal'] + $energy_kkal;
                    $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;


                    //РАСЧЕТ ВИТАМИНОВ
                    $data[$nutrition->id]['vitamin_a'] = $data[$nutrition->id]['vitamin_a'] + $vitamin_a;
                    $data[$nutrition->id]['vitamin_c'] = $data[$nutrition->id]['vitamin_c'] + $vitamin_c;
                    $data[$nutrition->id]['vitamin_b1'] = $data[$nutrition->id]['vitamin_b1'] + $vitamin_b1;
                    $data[$nutrition->id]['vitamin_b2'] = $data[$nutrition->id]['vitamin_b2'] + $vitamin_b2;
                    $data[$nutrition->id]['vitamin_d'] = $data[$nutrition->id]['vitamin_d'] + $vitamin_d;
                    $data[$nutrition->id]['vitamin_pp'] = $data[$nutrition->id]['vitamin_pp'] + $vitamin_pp;
                    $data[$nutrition->id]['vitamin_na'] = $data[$nutrition->id]['vitamin_na'] + $na;
                    $data[$nutrition->id]['vitamin_k'] = $data[$nutrition->id]['vitamin_k'] + $k;
                    $data[$nutrition->id]['vitamin_ca'] = $data[$nutrition->id]['vitamin_ca'] + $ca;
                    $data[$nutrition->id]['vitamin_f'] = $data[$nutrition->id]['vitamin_f'] + $f;
                    $data[$nutrition->id]['vitamin_mg'] = $data[$nutrition->id]['vitamin_mg'] + $mg;
                    $data[$nutrition->id]['vitamin_p'] = $data[$nutrition->id]['vitamin_p'] + $p;
                    $data[$nutrition->id]['vitamin_fe'] = $data[$nutrition->id]['vitamin_fe'] + $fe;
                    $data[$nutrition->id]['vitamin_i'] = $data[$nutrition->id]['vitamin_i'] + $i;
                    $data[$nutrition->id]['vitamin_se'] = $data[$nutrition->id]['vitamin_se'] + $se;


                    //raschet v itog za den

                    $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                    $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                    $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                    $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                    $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d;
                    $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                    $super_total_na = $super_total_na + $na;
                    $super_total_k = $super_total_k + $k;
                    $super_total_ca = $super_total_ca + $ca;
                    $super_total_f = $super_total_f + $f;
                    $super_total_mg = $super_total_mg + $mg;
                    $super_total_p = $super_total_p + $p;
                    $super_total_fe = $super_total_fe + $fe;
                    $super_total_i = $super_total_i + $i;
                    $super_total_se = $super_total_se + $se;
                    //КОНЕЦ РАСЧЕТА


                    //итого за завтрак
                    $sheet->setCellValue('C' . $num, $yield);
                    $sheet->setCellValue('D' . $num, $protein);
                    $sheet->setCellValue('E' . $num, $fat);
                    $sheet->setCellValue('F' . $num, $carbohydrates_total);
                    $sheet->setCellValue('G' . $num, $energy_kkal);

                    if ($him == 1)
                    {
                        $sheet->setCellValue('H' . $num, round($vitamin_a, 2));
                        $sheet->setCellValue('I' . $num, round($vitamin_c, 2));
                        $sheet->setCellValue('J' . $num, round($vitamin_b1, 2));
                        $sheet->setCellValue('K' . $num, round($vitamin_b2, 2));
                        $sheet->setCellValue('L' . $num, round($vitamin_d, 2));
                        $sheet->setCellValue('M' . $num, round($vitamin_pp, 2));
                        $sheet->setCellValue('N' . $num, round($na, 2));
                        $sheet->setCellValue('O' . $num, round($k, 2));
                        $sheet->setCellValue('P' . $num, round($ca, 2));
                        $sheet->setCellValue('Q' . $num, round($f, 2));
                        $sheet->setCellValue('R' . $num, round($mg, 2));
                        $sheet->setCellValue('S' . $num, round($p, 2));
                        $sheet->setCellValue('T' . $num, round($fe, 2));
                        $sheet->setCellValue('U' . $num, round($i, 2));
                        $sheet->setCellValue('V' . $num, round($se, 2));
                    }


                    $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->setBold(true);
                    $num = $num + 1;
                }
                $sheet->setCellValue('B' . $num, 'Итого за день');
                $sheet->setCellValue('C' . $num, $super_total_yield);
                $sheet->setCellValue('D' . $num, $super_total_protein);
                $sheet->setCellValue('E' . $num, $super_total_fat);
                $sheet->setCellValue('F' . $num, $super_total_carbohydrates_total);
                $sheet->setCellValue('G' . $num, $super_total_energy_kkal);


                if ($him == 1)
                {
                    $sheet->setCellValue('H' . $num, round($super_total_vitamin_a, 2));
                    $sheet->setCellValue('I' . $num, round($super_total_vitamin_c, 2));
                    $sheet->setCellValue('J' . $num, round($super_total_vitamin_b1, 2));
                    $sheet->setCellValue('K' . $num, round($super_total_vitamin_b2, 2));
                    $sheet->setCellValue('L' . $num, round($super_total_vitamin_d, 2));
                    $sheet->setCellValue('M' . $num, round($super_total_vitamin_pp, 2));
                    $sheet->setCellValue('N' . $num, round($super_total_na, 2));
                    $sheet->setCellValue('O' . $num, round($super_total_k, 2));
                    $sheet->setCellValue('P' . $num, round($super_total_ca, 2));
                    $sheet->setCellValue('Q' . $num, round($super_total_f, 2));
                    $sheet->setCellValue('R' . $num, round($super_total_mg, 2));
                    $sheet->setCellValue('S' . $num, round($super_total_p, 2));
                    $sheet->setCellValue('T' . $num, round($super_total_fe, 2));
                    $sheet->setCellValue('U' . $num, round($super_total_i, 2));
                    $sheet->setCellValue('V' . $num, round($super_total_se, 2));
                }


                $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->setBold(true);
                $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->getColor()->setRGB('cf3042');
                $num = $num + 2;

            }
        }

        $sheet->setCellValue('B' . $num, "Итоги");
        $sheet->setCellValue('C' . $num, "Выход, г");
        $sheet->setCellValue('D' . $num, "Белки, г");
        $sheet->setCellValue('E' . $num, "Жиры, г");
        $sheet->setCellValue('F' . $num, "Углеводы, г.");
        $sheet->setCellValue('G' . $num, "Калорийность, ккал.");

        $sheet->setCellValue('H' . $num, "A, мкг рет.экв");
        $sheet->setCellValue('I' . $num, "C, мг");
        $sheet->setCellValue('J' . $num, "B1, мг");
        $sheet->setCellValue('K' . $num, "B2, мг");
        $sheet->setCellValue('L' . $num, "D, мкг");
        $sheet->setCellValue('M' . $num, "PP, мг");
        $sheet->setCellValue('N' . $num, "Na, мг");
        $sheet->setCellValue('O' . $num, "K, мг");
        $sheet->setCellValue('P' . $num, "Ca, мг");
        $sheet->setCellValue('Q' . $num, "F, мкг");
        $sheet->setCellValue('R' . $num, "Mg, мг");
        $sheet->setCellValue('S' . $num, "P, мг");
        $sheet->setCellValue('T' . $num, "Fe, мг");
        $sheet->setCellValue('U' . $num, "I, мкг");
        $sheet->setCellValue('V' . $num, "Se, мкг");
        $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->setBold(true);
        $data_itog = [];
        $num = $num + 1;
        foreach ($nutritions as $nutrition)
        {
            $data_vit_a = round($data[$nutrition->id]['vitamin_a'] / $count_my_days, 2);
            $procent = \common\models\NutritionProcent::find()->where(['nutrition_id' => $nutrition->id])->one()->procent / 100;
            if ($data_vit_a <= $normativ_vitamin_day_vitamin_a * 1.5 * $procent)
            {
                $data_itog['vitamin_a'] = $data_itog['vitamin_a'] + $data_vit_a;
            }
            else
            {
                $data_vit_a = $normativ_vitamin_day_vitamin_a * 1.5 * $procent;
                $data_itog['vitamin_a'] = $data_itog['vitamin_a'] + $data_vit_a;
            }
            $data_vit_k = round($data[$nutrition->id]['vitamin_k'] / $count_my_days, 2);
            $procent = \common\models\NutritionProcent::find()->where(['nutrition_id' => $nutrition->id])->one()->procent / 100;
            if ($data_vit_k <= $normativ_vitamin_day_k * 1.5 * $procent)
            {
                $data_itog['vitamin_k'] = $data_itog['vitamin_k'] + $data_vit_k;
            }
            else
            {
                $data_vit_k = $normativ_vitamin_day_k * 1.5 * $procent;
                $data_itog['vitamin_k'] = $data_itog['vitamin_k'] + $data_vit_k;
            }
            $sheet->setCellValue('B' . $num, 'Средние показатели за ' . $nutrition->name);
            $sheet->setCellValue('C' . $num, round($data[$nutrition->id]['yield'] / $count_my_days, 2));
            $sheet->setCellValue('D' . $num, round($data[$nutrition->id]['protein'] / $count_my_days, 2));
            $sheet->setCellValue('E' . $num, round($data[$nutrition->id]['fat'] / $count_my_days, 2));
            $sheet->setCellValue('F' . $num, round($data[$nutrition->id]['carbohydrates_total'] / $count_my_days, 2));
            $sheet->setCellValue('G' . $num, round($data[$nutrition->id]['energy_kkal'] / $count_my_days, 2));


            //ВЫВОД ВИТАМИНА
            $sheet->setCellValue('H' . $num, $data_vit_a);
            $sheet->setCellValue('I' . $num, round($data[$nutrition->id]['vitamin_c'] / $count_my_days, 2));
            $sheet->setCellValue('J' . $num, round($data[$nutrition->id]['vitamin_b1'] / $count_my_days, 2));
            $sheet->setCellValue('K' . $num, round($data[$nutrition->id]['vitamin_b2'] / $count_my_days, 2));
            $sheet->setCellValue('L' . $num, round($data[$nutrition->id]['vitamin_d'] / $count_my_days, 2));
            $sheet->setCellValue('M' . $num, round($data[$nutrition->id]['vitamin_pp'] / $count_my_days, 2));
            $sheet->setCellValue('N' . $num, round($data[$nutrition->id]['vitamin_na'] / $count_my_days, 2));
            $sheet->setCellValue('O' . $num, $data_vit_k);
            $sheet->setCellValue('P' . $num, round($data[$nutrition->id]['vitamin_ca'] / $count_my_days, 2));
            $sheet->setCellValue('Q' . $num, round($data[$nutrition->id]['vitamin_f'] / $count_my_days, 2));
            $sheet->setCellValue('R' . $num, round($data[$nutrition->id]['vitamin_mg'] / $count_my_days, 2));
            $sheet->setCellValue('S' . $num, round($data[$nutrition->id]['vitamin_p'] / $count_my_days, 2));
            $sheet->setCellValue('T' . $num, round($data[$nutrition->id]['vitamin_fe'] / $count_my_days, 2));
            $sheet->setCellValue('U' . $num, round($data[$nutrition->id]['vitamin_i'] / $count_my_days, 2));
            $sheet->setCellValue('V' . $num, round($data[$nutrition->id]['vitamin_se'] / $count_my_days, 2));
            //КОНЕЦ

            $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->setBold(true);
            $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->getColor()->setRGB('cf3042');
            $num = $num + 1;

            $data_itog['yield'] = $data_itog['yield'] + round($data[$nutrition->id]['yield'] / $count_my_days, 2);
            $data_itog['protein'] = $data_itog['protein'] + round($data[$nutrition->id]['protein'] / $count_my_days, 2);
            $data_itog['fat'] = $data_itog['fat'] + round($data[$nutrition->id]['fat'] / $count_my_days, 2);
            $data_itog['carbohydrates_total'] = $data_itog['carbohydrates_total'] + round($data[$nutrition->id]['carbohydrates_total'] / $count_my_days, 2);
            $data_itog['energy_kkal'] = $data_itog['energy_kkal'] + round($data[$nutrition->id]['energy_kkal'] / $count_my_days, 2);


            //$data_itog['vitamin_a'] = $data_itog['vitamin_a'] + $data_vit_a;
            $data_itog['vitamin_c'] = $data_itog['vitamin_c'] + round($data[$nutrition->id]['vitamin_c'] / $count_my_days, 2);
            $data_itog['vitamin_b1'] = $data_itog['vitamin_b1'] + round($data[$nutrition->id]['vitamin_b1'] / $count_my_days, 2);
            $data_itog['vitamin_b2'] = $data_itog['vitamin_b2'] + round($data[$nutrition->id]['vitamin_b2'] / $count_my_days, 2);
            $data_itog['vitamin_d'] = $data_itog['vitamin_d'] + round($data[$nutrition->id]['vitamin_d'] / $count_my_days, 2);
            $data_itog['vitamin_pp'] = $data_itog['vitamin_pp'] + round($data[$nutrition->id]['vitamin_pp'] / $count_my_days, 2);
            $data_itog['vitamin_na'] = $data_itog['vitamin_na'] + round($data[$nutrition->id]['vitamin_na'] / $count_my_days, 2);
            //$data_itog['vitamin_k'] = $data_itog['vitamin_k'] + $data_vit_k;
            $data_itog['vitamin_ca'] = $data_itog['vitamin_ca'] + round($data[$nutrition->id]['vitamin_ca'] / $count_my_days, 2);
            $data_itog['vitamin_f'] = $data_itog['vitamin_f'] + round($data[$nutrition->id]['vitamin_f'] / $count_my_days, 2);
            $data_itog['vitamin_mg'] = $data_itog['vitamin_mg'] + round($data[$nutrition->id]['vitamin_mg'] / $count_my_days, 2);
            $data_itog['vitamin_p'] = $data_itog['vitamin_p'] + round($data[$nutrition->id]['vitamin_p'] / $count_my_days, 2);
            $data_itog['vitamin_fe'] = $data_itog['vitamin_fe'] + round($data[$nutrition->id]['vitamin_fe'] / $count_my_days, 2);
            $data_itog['vitamin_i'] = $data_itog['vitamin_i'] + round($data[$nutrition->id]['vitamin_i'] / $count_my_days, 2);
            $data_itog['vitamin_se'] = $data_itog['vitamin_se'] + round($data[$nutrition->id]['vitamin_se'] / $count_my_days, 2);

        }

        $sheet->setCellValue('B' . $num, 'Средние показатели за период');
        $sheet->setCellValue('C' . $num, round($data_itog['yield'], 1));
        $sheet->setCellValue('D' . $num, round($data_itog['protein'], 1));
        $sheet->setCellValue('E' . $num, round($data_itog['fat'], 1));
        $sheet->setCellValue('F' . $num, round($data_itog['carbohydrates_total'], 1));
        $sheet->setCellValue('G' . $num, round($data_itog['energy_kkal'], 1));


        $sheet->setCellValue('H' . $num, round($data_itog['vitamin_a'], 1));
        $sheet->setCellValue('I' . $num, round($data_itog['vitamin_c'], 1));
        $sheet->setCellValue('J' . $num, round($data_itog['vitamin_b1'], 1));
        $sheet->setCellValue('K' . $num, round($data_itog['vitamin_b2'], 1));
        $sheet->setCellValue('L' . $num, round($data_itog['vitamin_d'], 1));
        $sheet->setCellValue('M' . $num, round($data_itog['vitamin_pp'], 1));
        $sheet->setCellValue('N' . $num, round($data_itog['vitamin_na'], 1));
        $sheet->setCellValue('O' . $num, round($data_itog['vitamin_k'], 1));
        $sheet->setCellValue('P' . $num, round($data_itog['vitamin_ca'], 1));
        $sheet->setCellValue('Q' . $num, round($data_itog['vitamin_f'], 1));
        $sheet->setCellValue('R' . $num, round($data_itog['vitamin_mg'], 1));
        $sheet->setCellValue('S' . $num, round($data_itog['vitamin_p'], 1));
        $sheet->setCellValue('T' . $num, round($data_itog['vitamin_fe'], 1));
        $sheet->setCellValue('U' . $num, round($data_itog['vitamin_i'], 1));
        $sheet->setCellValue('V' . $num, round($data_itog['vitamin_se'], 1));

        $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->setBold(true);
        $sheet->getStyle("B" . $num . ":V" . $num)->getFont()->getColor()->setRGB('cf3042');
        $num = $num + 1;


        $filename = 'Отчет_Меню_Период_' . date('Y_m_d_H_i', time()) . '.xlsx';
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function actionExcelProductsList($menu_id, $days_id)
    {
        /*print_r($days_id);
        print_r($menu_id);
        exit();*/
        //require '/../../vendor/autoload.php';


        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->orderby(['dishes_id' => SORT_ASC])->all();
        $dishes_ids = [];
        $categories_ids = [];

        foreach ($menus_dishes as $m_dish)
        {
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->all();
            foreach ($dishes_products as $d_product)
            {
                if (!in_array($d_product->products_id, $dishes_ids))
                {
                    $dishes_ids[] = $d_product->products_id;
                }
            }
        }
        $products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
        foreach ($products as $product)
        {
            if (!in_array($product->products_category_id, $categories_ids))
            {
                $categories_ids[] = $product->products_category_id;
            }
        }
        $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();

        $my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
        $my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
        $first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();
        $menu_cycle_count = $first_menu->cycle;
        $menu_cycle = [];
        $menu_cycle[0] = 'Показать за все недели';
        for ($i = 1; $i <= $menu_cycle_count; $i++)
        {
            $menu_cycle[$i] = $i;//массив из подходящи циклов
        }
//    !!! В $post['days_id'] ХРАНИТСЯ ИНФОРМАЦИЯ БРУТТО/НЕТТО    !!!!
        $chemistry_items = [0 => 'Брутто', 1 => 'Нетто'];
        $params_menu = ['class' => 'form-control', 'options' => [$first_menu->id => ['Selected' => true]]];
        $params_chemistry = ['class' => 'form-control', 'options' => [0 => ['Selected' => true]]];

        $my_menus = Menus::findOne($menu_id);
        $menu_cycle_count = $my_menus->cycle;
        $menu_cycle = [];
        for ($i = 1; $i <= $menu_cycle_count; $i++)
        {
            $menu_cycle[$i] = $i;//массив из подходящи циклов
        }
        $my_days = MenusDays::find()->where(['menu_id' => $menu_id])->all();
        foreach ($my_days as $m_day)
        {
            $ids[] = $m_day->days_id;
        }
        $days = Days::find()->where(['id' => $ids])->all();

        $chemistry_items = [0 => 'Брутто', 1 => 'Нетто'];
        $params_menu = ['class' => 'form-control', 'options' => [$post['menu_id'] => ['Selected' => true]]];
        $params_chemistry = ['class' => 'form-control', 'options' => [$post['days_id'] => ['Selected' => true]]];

        $style_heder = array(
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true,
            )
        );
        require '../../vendor/autoload.php';
        $array_num = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->mergeCells("A2:A3");
        $sheet->setCellValue("A2", "№");

        $sheet->mergeCells("B2:B3");
        $sheet->setCellValue("B2", "Продукт");

        $num = 0;
        foreach ($menu_cycle as $cycle)
        {
            $sheet->setCellValue($array_num[$num] . '2', $cycle . ' неделя ');
            foreach ($days as $day)
            {
                $sheet->setCellValue($array_num[$num] . '3', $day->name);
                $num++;
            }
        }
        $sheet->mergeCells($array_num[$num] . '2' . ':' . $array_num[$num] . '3');
        $sheet->setCellValue($array_num[$num] . '2', 'Итого');
        $num++;
        $sheet->mergeCells($array_num[$num] . '2' . ':' . $array_num[$num] . '3');
        $sheet->setCellValue($array_num[$num] . '2', 'Среднесуточное значение');

        if ($days_id == 0)
        {
            $sheet->mergeCells('A1:' . $array_num[$num] . '1');
            $sheet->setCellValue('A1', 'Перечень продуктов, Брутто, г');
        }
        else
        {
            $sheet->mergeCells('A1:' . $array_num[$num] . '1');
            $sheet->setCellValue('A1', 'Перечень продуктов, Нетто, г');
        }

        $array_num2 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        ];

        $column = 4;

        $number_row = 1;
        $i = 0;
        foreach ($products_categories as $product_cat)
        {

            foreach ($products as $product)
            {

                if ($product_cat->id == $product->products_category_id)
                {
                    $num2 = 0;
                    $totality = 0;
                    $sheet->setCellValue($array_num2[$num2] . $column, $number_row);
                    $num2++;
                    $sheet->setCellValue($array_num2[$num2] . $column, $product->name);
                    //print_r($number_row);
                    $num2++;
                    foreach ($menu_cycle as $cycle)
                    {
                        foreach ($days as $day)
                        {

                            $total = $product->get_total_yield_day($product->id, $menu_id, $cycle, $day->id, $days_id);
                            $sheet->setCellValue($array_num2[$num2] . $column, $total['yield']);
                            //print_r($array_num2[$num2].$column);
                            //print_r('<br><br>');
                            if ($total['yield'] == '-')
                            {
                                $total['yield'] = 0;
                            }
                            $totality = $total['yield'] + $totality;
                            $num2++;
                        }

                    }
                    $sheet->setCellValue($array_num2[$num2] . $column, $totality);
                    $num2++;
                    $sheet->setCellValue($array_num2[$num2] . $column, round($totality / (count($menu_cycle) * count($days)), 2));
                    $column++;
                    $number_row++;
                }
            }

        }
        //print_r($array);
        //exit();
        //$sheet->setCellValueExplicitByColumnAndRow(__COL_INDEX__, __ROW_INDEX__, __SOME_DATA__, DataType::TYPE_STRING);
        /*$sheet->getStyle('A2:G2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);*/


        $filename = 'Перечень продуктов.xlsx'; //save our workbook as this file name
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
        //print_r(123);

        /*//Создаем экземпляр класса электронной таблицы
        $spreadsheet = new Spreadsheet();
        //Получаем текущий активный лист
        $sheet = $spreadsheet->getActiveSheet();
        // Записываем в ячейку A1 данные
        $sheet->setCellValue('A1', 'Hello my Friend!');

        //$writer = new Xlsx($spreadsheet);
        //Сохраняем файл в текущей папке, в которой выполняется скрипт.
        //Чтобы указать другую папку для сохранения.
        //Прописываем полный путь до папки и указываем имя файла
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('hello.xlsx');*/
        /*
         $sOutFile = 'out.xlsx';

         $oSpreadsheet_Out = new Spreadsheet();

         $oSpreadsheet_Out->getProperties()->setCreator('Maarten Balliauw')
             ->setLastModifiedBy('Maarten Balliauw')
             ->setTitle('Office 2007 XLSX Test Document')
             ->setSubject('Office 2007 XLSX Test Document')
             ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
             ->setKeywords('office 2007 openxml php')
             ->setCategory('Test result file');
             // Add some data
         $oSpreadsheet_Out->setActiveSheetIndex(0)
             ->setCellValue('A1', 'Привет 123')
             ->setCellValue('B2', 'world!')
             ->setCellValue('C1', 'Hello')
             ->setCellValue('D2', 'world!');

         $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
         $oWriter->save($sOutFile);*/
    }

    public function actionExcelProductsListNutrition($menu_id, $days_id)
    {


        $nutritions = MenusNutrition::find()->where(['menu_id' => $menu_id])->orderBy(['nutrition_id' => SORT_ASC])->all();
        //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->orderby(['dishes_id' => SORT_ASC])->all();
        //$dishes_ids = [];
        //$categories_ids = [];

        /* foreach ($menus_dishes as $m_dish)
         {
             $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->all();
             foreach ($dishes_products as $d_product)
             {
                 if (!in_array($d_product->products_id, $dishes_ids))
                 {
                     $dishes_ids[] = $d_product->products_id;
                 }
             }
         }
         $products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
         foreach ($products as $product)
         {
             if (!in_array($product->products_category_id, $categories_ids))
             {
                 $categories_ids[] = $product->products_category_id;
             }
         }
         $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();*/

        $my_menus = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->all();
        $my_menus_items = ArrayHelper::map($my_menus, 'id', 'name');
        $first_menu = Menus::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status_archive' => 0])->one();
        $menu_cycle_count = $first_menu->cycle;
        $menu_cycle = [];
        $menu_cycle[0] = 'Показать за все недели';
        for ($i = 1; $i <= $menu_cycle_count; $i++)
        {
            $menu_cycle[$i] = $i;//массив из подходящи циклов
        }

        $my_menus = Menus::findOne($menu_id);
        $menu_cycle_count = $my_menus->cycle;
        $menu_cycle = [];
        for ($i = 1; $i <= $menu_cycle_count; $i++)
        {
            $menu_cycle[$i] = $i;//массив из подходящи циклов
        }
        $my_days = MenusDays::find()->where(['menu_id' => $menu_id])->all();
        foreach ($my_days as $m_day)
        {
            $ids[] = $m_day->days_id;
        }
        $days = Days::find()->where(['id' => $ids])->all();

        $style_heder = array(
            'font' => array(
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true,
            )
        );
        require '../../vendor/autoload.php';
        $array_num = [
            //'A',
            //'B',
            //'C','D',
            'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS',
            'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells("A2:A3");
        $sheet->setCellValue("A2", "Прием пищи");

        $sheet->mergeCells("B2:B3");
        $sheet->setCellValue("B2", "№");

        $sheet->mergeCells("C2:C3");
        $sheet->setCellValue("C2", "Категория продукта");

        $sheet->mergeCells("D2:D3");
        $sheet->setCellValue("D2", "Продукт");

        $num = 0;
        foreach ($menu_cycle as $cycle)
        {
            $sheet->setCellValue($array_num[$num] . '2', $cycle . ' неделя ');
            foreach ($days as $day)
            {
                $sheet->setCellValue($array_num[$num] . '3', $day->name);
                $num++;
            }
        }
        $sheet->mergeCells($array_num[$num] . '2' . ':' . $array_num[$num] . '3');
        $sheet->setCellValue($array_num[$num] . '2', 'Итого');
        $num++;
        $sheet->mergeCells($array_num[$num] . '2' . ':' . $array_num[$num] . '3');
        $sheet->setCellValue($array_num[$num] . '2', 'Среднесуточное значение');

        if ($days_id == 0)
        {
            $sheet->mergeCells('A1:' . $array_num[$num] . '1');
            $sheet->setCellValue('A1', 'Перечень продуктов, Брутто, г');
        }
        else
        {
            $sheet->mergeCells('A1:' . $array_num[$num] . '1');
            $sheet->setCellValue('A1', 'Перечень продуктов, Нетто, г');
        }

        $array_num2 = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        ];

        $column = 4;

        $number_row = 1;
        $i = 0;


        foreach ($nutritions as $nutrition)
        {
            $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'nutrition_id' => $nutrition->nutrition_id])->orderby(['dishes_id' => SORT_ASC])->all();
            $dishes_ids = [];
            $categories_ids = [];

            foreach ($menus_dishes as $m_dish)
            {
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->all();
                foreach ($dishes_products as $d_product)
                {
                    if (!in_array($d_product->products_id, $dishes_ids))
                    {
                        $dishes_ids[] = $d_product->products_id;
                    }
                }
            }
            $products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
            $products_count = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->count();
            foreach ($products as $product)
            {
                if (!in_array($product->products_category_id, $categories_ids))
                {
                    $categories_ids[] = $product->products_category_id;
                }
            }
            $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();

            foreach ($products_categories as $product_cat)
            {

                foreach ($products as $product)
                {

                    if ($product_cat->id == $product->products_category_id)
                    {
                        $num2 = 0;
                        $totality = 0;
                        $sheet->setCellValue($array_num2[$num2] . $column, NutritionInfo::findOne($nutrition->nutrition_id)->name);
                        $num2++;
                        $sheet->setCellValue($array_num2[$num2] . $column, $number_row);
                        $num2++;
                        $sheet->setCellValue($array_num2[$num2] . $column, $product_cat->name);
                        $num2++;
                        $sheet->setCellValue($array_num2[$num2] . $column, $product->name);
                        //print_r($number_row);
                        $num2++;
                        foreach ($menu_cycle as $cycle)
                        {
                            foreach ($days as $day)
                            {

                                $total = $product->get_total_yield_nutrition($product->id, $menu_id, $cycle, $day->id, $nutrition->nutrition_id, $days_id);
                                $sheet->setCellValue($array_num2[$num2] . $column, $total['yield']);
                                if ($total['yield'] == '-')
                                {
                                    $total['yield'] = 0;
                                }
                                $totality = $total['yield'] + $totality;
                                $num2++;
                            }

                        }
                        $sheet->setCellValue($array_num2[$num2] . $column, $totality);
                        $num2++;
                        $sheet->setCellValue($array_num2[$num2] . $column, round($totality / (count($menu_cycle) * count($days)), 2));
                        $column++;
                        $number_row++;
                    }
                }

            }
        }
        $filename = 'Перечень продуктов.xlsx'; //save our workbook as this file name
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
        //print_r(123);

        /*//Создаем экземпляр класса электронной таблицы
        $spreadsheet = new Spreadsheet();
        //Получаем текущий активный лист
        $sheet = $spreadsheet->getActiveSheet();
        // Записываем в ячейку A1 данные
        $sheet->setCellValue('A1', 'Hello my Friend!');

        //$writer = new Xlsx($spreadsheet);
        //Сохраняем файл в текущей папке, в которой выполняется скрипт.
        //Чтобы указать другую папку для сохранения.
        //Прописываем полный путь до папки и указываем имя файла
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('hello.xlsx');*/
        /*
         $sOutFile = 'out.xlsx';

         $oSpreadsheet_Out = new Spreadsheet();

         $oSpreadsheet_Out->getProperties()->setCreator('Maarten Balliauw')
             ->setLastModifiedBy('Maarten Balliauw')
             ->setTitle('Office 2007 XLSX Test Document')
             ->setSubject('Office 2007 XLSX Test Document')
             ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
             ->setKeywords('office 2007 openxml php')
             ->setCategory('Test result file');
             // Add some data
         $oSpreadsheet_Out->setActiveSheetIndex(0)
             ->setCellValue('A1', 'Привет 123')
             ->setCellValue('B2', 'world!')
             ->setCellValue('C1', 'Hello')
             ->setCellValue('D2', 'world!');

         $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
         $oWriter->save($sOutFile);*/
    }

    public function actionExportPrognosStorage($menu_id, $normativ, $brutto_netto)
    {
        require_once Yii::$app->basePath . '\Excel\PHPExcel.php';
        require_once Yii::$app->basePath . '\Excel\PHPExcel\IOFactory.php';

        $document = new \PHPExcel();
        //подгружаем готовый шаблон !!!!
        //$document = \PHPExcel_IOFactory::load('../web/images/generator.xlsx');
        ob_start();

        $model4 = new ProductsCategory();
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->orderby(['nutrition_id' => SORT_ASC])->all();
        $dishes_ids = [];

        foreach ($menus_dishes as $m_dish)
        {
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish->dishes_id])->one();
            if (!in_array($dishes_products->dishes_id, $dishes_ids))
            {
                /*Массив используемых продуктов. Продукты пока что не уникальных в 1м из 2х случаях*/
                $dishes_ids[] = $dishes_products->dishes_id;
            }
        }

        $dishes_dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_ids])->orderby(['id' => SORT_ASC])->all();
        $categories_ids = [];

        foreach ($dishes_dishes_products as $d_d_product)
        {
            $product = Products::find()->where(['id' => $d_d_product->products_id])->one();
            $categories = ProductsCategory::find()->where(['id' => $product->products_category_id])->one();
            if (!in_array($product->products_category_id, $categories_ids))
            {
                $categories_ids[] = $product->products_category_id;
            }
        }

        $categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();

        $menus_nutritions = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();
        $nutrition_ids = [];

        foreach ($menus_nutritions as $m_nutrition)
        {
            $nutrition_ids[] = $m_nutrition->nutrition_id;
        }

        $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();

        $my_menus = Menus::findOne($menu_id);
        $menu_cycle_count = $my_menus->cycle;
        $menu_cycle = [];
        for ($i = 1; $i <= $menu_cycle_count; $i++)
        {
            $menu_cycle[$i] = $i;//массив из подходящи циклов
        }

        $my_days = MenusDays::find()->where(['menu_id' => $menu_id])->all();
        foreach ($my_days as $m_day)
        {
            $ids[] = $m_day->days_id;
        }
        $days = Days::find()->where(['id' => $ids])->all();
        $count_my_days = MenusDays::find()->where(['menu_id' => $menu_id])->count() * $my_menus->cycle;

        if ($normativ == 1)
        {
            $values = $model4->get_total_yield_nutrition_category($menu_id);
            if ($brutto_netto == 0)
            {
                $brutto_netto = 'net_weight';
            }
            if ($brutto_netto == 1)
            {
                $brutto_netto = 'gross_weight';
            }
            $m = [];
            foreach ($values as $value)
            {
                $m[$value['products_category_id'] . '_' . $value['nutrition_id']] = $m[$value['products_category_id'] . '_' . $value['nutrition_id']] + ($value[$brutto_netto] * ($value['menus_yield'] / $value['dishes_yield']));
            }
        }

        $array_num = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

        $num = 2;
        $col = 2;
        $sheet = $document->getActiveSheet();
        $num_st = 2;
        $this->layout = false;

        $sheet->getColumnDimension('A')->setWidth("10");
        $sheet->getColumnDimension('B')->setWidth("60");

        $sheet->getStyle("B1")->getFont()->setBold(true);


        $sheet->setCellValue('B' . 1, $my_menus->name);

        $sheet->setCellValue('A' . $num_st, "№");
        $sheet->setCellValue('B' . $num_st, "Группа продукта");

        if ($normativ == 0)
        {
            foreach ($menu_cycle as $cycle)
            {
                foreach ($days as $day)
                {
                    $sheet->getColumnDimension($array_num[$col] . $num_st)->setWidth("15");
                    $sheet->setCellValue($array_num[$col] . $num_st, $day->name);
                    $col++;
                }
            }
            $sheet->getStyle("A2:" . $array_num[$col] . $num_st)->getFont()->setBold(true);
            $sheet->setCellValue($array_num[$col] . $num_st, "Сумма");
            $col++;
            $sheet->setCellValue($array_num[$col] . $num_st, "Ср.знач.");
            $col++;
        }

        if ($normativ == 1)
        {
            foreach ($nutritions as $nutrition)
            {

                $sheet->getColumnDimension($array_num[$col] . $num_st)->setWidth("15");
                $sheet->setCellValue($array_num[$col] . $num_st, $nutrition->name);
                $col++;

            }
            $sheet->getStyle("A2:" . $array_num[$col] . $num_st)->getFont()->setBold(true);
            $sheet->setCellValue($array_num[$col] . $num_st, "Сутки");
            $col++;
        }


        $col = 2;
        if ($normativ == 0)
        {
            $m = [];
            $values = $model4->get_total_yield_category($menu_id);
            if ($brutto_netto == 0)
            {
                $brutto_netto = 'net_weight';
            }
            if ($brutto_netto == 1)
            {
                $brutto_netto = 'gross_weight';
            }
            foreach ($values as $key => $value)
            {
                $m[$value['products_category_id'] . '_' . $value['cycle'] . '_' . $value['days_id']] = $m[$value['products_category_id'] . '_' . $value['cycle'] . '_' . $value['days_id']] + ($value[$brutto_netto] * ($value['menus_yield'] / $value['dishes_yield']));
            }
        }


        $count = 0;
        foreach ($categories as $category)
        {
            $count++;
            $num++;
            $col = 2;
            $sheet->setCellValue('A' . $num, $count);
            $sheet->setCellValue('B' . $num, $category->name);
            if ($normativ == 0)
            {
                $itog = 0;
                foreach ($menu_cycle as $cycle)
                {
                    foreach ($days as $day)
                    {
                        if (array_key_exists($category->id . '_' . $cycle . '_' . $day->id, $m))
                        {
                            $sheet->setCellValue($array_num[$col] . $num, round($m[$category->id . '_' . $cycle . '_' . $day->id], 1));
                            $itog = $itog + round($m[$category->id . '_' . $cycle . '_' . $day->id], 1);
                            $col++;


                        }
                        else
                        {
                            $sheet->setCellValue($array_num[$col] . $num, '-');
                            $col++;
                        }
                    }
                }
                $sheet->setCellValue($array_num[$col] . $num, $itog);
                //Стили для данного столбца(жирный текст и красный цвет)
                $sheet->getStyle($array_num[$col] . $num)->getFont()->setBold(true);
                $sheet->getStyle($array_num[$col] . $num)->getFont()->getColor()->setRGB('cf3042');
                //конец стилей
                $col++;
                $sheet->setCellValue($array_num[$col] . $num, round(($itog / $count_my_days), 1));
                //Стили для данного столбца(жирный текст и синий цвет)
                $sheet->getStyle($array_num[$col] . $num)->getFont()->setBold(true);
                $sheet->getStyle($array_num[$col] . $num)->getFont()->getColor()->setRGB('3d30cf');
                //конец стилей
                $col++;
            }
            if ($normativ == 1)
            {
                $itog = 0;
                foreach ($nutritions as $nutrition)
                {
                    if (array_key_exists($category->id . '_' . $nutrition->id, $m))
                    {
                        $sheet->setCellValue($array_num[$col] . $num, round($m[$category->id . '_' . $nutrition->id] / $count_my_days, 1));
                        $itog = $itog + round($m[$category->id . '_' . $nutrition->id] / $count_my_days, 1);
                        $col++;


                    }
                    else
                    {
                        $sheet->setCellValue($array_num[$col] . $num, '-');
                        $col++;
                    }
                }
                $sheet->setCellValue($array_num[$col] . $num, $itog);
                $sheet->getStyle($array_num[$col] . $num)->getFont()->setBold(true);
                $sheet->getStyle($array_num[$col] . $num)->getFont()->getColor()->setRGB('cf3042');
            }

        }
        /*$sheet->getStyle("B".$num)->getFont()->setBold(true);
        $sheet->getStyle("B".$num)->getFont()->getColor()->setRGB('3d30cf');*/


        $filename = 'Отчет_Прогнозная_Ведомость' . date('Y_m_d_H_i', time()) . '.xlsx';
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function actionExportPrognosRaskladka($arrau1, $arrau2, $post_brutto_netto, $post_date, $arrau3)
    {

        require_once Yii::$app->basePath . '\Excel\PHPExcel.php';
        require_once Yii::$app->basePath . '\Excel\PHPExcel\IOFactory.php';

        $document = new \PHPExcel();
        //подгружаем готовый шаблон !!!!
        //$document = \PHPExcel_IOFactory::load('../web/images/generator.xlsx');
        ob_start();


        $model_menus_dishes = new MenusDishes();

        /*Собрали массив из строки который пришед с помощью GET*/
        $array1 = unserialize($arrau1);
        $array2 = unserialize($arrau2);
        $array3 = unserialize($arrau3);

        /*меню перед которыми была нажата галочка пришол результат в $arrau1, а в $arrau2 пришол результат количество питающихся*/
        $post_menus = Menus::find()->where(['id' => $array1])->all();
        /*поиск приемов пищи по всем меню перед которыми была нажата галочка*/
        $menus_nutritions = MenusNutrition::find()->where(['menu_id' => $array1])->all();
        $nutrition_ids = [];

        /*foreach ($menus_nutritions as $m_nutrition)
        {
            $nutrition_ids[] = $m_nutrition->nutrition_id;
        }

        $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();*/

        $menus_dishes = [];
        $menus_dishes2 = [];
        $menus_dishes2[] = [];
        $i = 0;
        foreach ($post_menus as $p_menu)
        {
            /*get_cycle_from_date() - функция по определения цикла меню по дате*/
            $cycle = $model_menus_dishes->get_cycle_from_date($p_menu->id, $post_date);
            $day_of_week_start_date = date("w", strtotime($post_date));//День недели даты
            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
            /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
            if ($day_of_week_start_date == 0)
            {
                $day_of_week_start_date = 7;
            }
            /*$cycle - цикл даты данного меню, $day_of_week_start_date - день недели*/
            //$current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date])->asArray()->all();
            $current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => strtotime($post_date), 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date])->asArray()->all();
            //print_r($current_menus_dishes);exit;
            if (empty($current_menus_dishes))
            {
                $current_menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $p_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week_start_date])->asArray()->all();
            }
            //print_r(count($current_menus_dishes));exit;
            /*объединение массив по разным меню*/
            $menus_dishes = ArrayHelper::merge($menus_dishes, $current_menus_dishes);
            $menus_dishes2[$i] = $current_menus_dishes;
            $i++;
        }

        $dishes_ids = [];
        foreach ($menus_dishes as $m_dish)
        {
            $nutrition_ids[] = $m_dish['nutrition_id'];
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $m_dish['dishes_id']])->all();
            foreach ($dishes_products as $d_product)
            {
                if (!in_array($d_product->products_id, $dishes_ids))
                {
                    $dishes_ids[] = $d_product->products_id;
                }
            }
        }
        $nutritions = NutritionInfo::find()->where(['id' => $nutrition_ids])->all();
        //print_r($menus_dishes);exit;
        //$products = Products::find()->where(['id' => $dishes_ids])->orderby(['sort' => SORT_ASC])->all();
        $products = Products::find()->
        select(['products.id as id', 'products_category.id as pc_id', 'products.name as name', 'products.products_category_id as products_category_id', 'products_category.sort as sort'])->
        leftJoin('products_category', 'products.products_category_id = products_category.id')->
        where(['products.id' => $dishes_ids])->
        orderby(['products_category.sort' => SORT_ASC])->
        asArray()->
        all();

        $count_porciya = [];
        $dishes_in_nutrition = [];
        $dishes_count = [];
        $dishes_yield = [];
        $count_dishes_in_nutrition = [];
        $count_menu = count($menus_dishes2); //количество меню, которое вернулось

        for ($i = 0; $i < $count_menu; $i++)
        {
            $count_dishes_in_menu[$i] = count($menus_dishes2[$i]); //количество блюд в каждом меню
        }
        for ($i = 0; $i < $count_menu; $i++)
        { //обходим меню
            for ($j = 0; $j < $count_dishes_in_menu[$i]; $j++)
            { //обходим блюда
                /*echo $menus_dishes2[$i][$j]['nutrition_id'];
                echo ".";
                echo $menus_dishes2[$i][$j]['dishes_id'];
                echo "_";
                echo $menus_dishes2[$i][$j]['menu_id'];
                echo "<br>";*/
                $count_dishes_in_nutrition[$menus_dishes2[$i][$j]['nutrition_id']][$menus_dishes2[$i][$j]['dishes_id']] = $menus_dishes2[$i][$j]['dishes_id'];
                $dishes_in_nutrition[$menus_dishes2[$i][$j]['nutrition_id'] . '_' . $menus_dishes2[$i][$j]['dishes_id']] = $menus_dishes2[$i][$j]['dishes_id'];
                $dishes_count[$menus_dishes2[$i][$j]['nutrition_id'] . '_' . $menus_dishes2[$i][$j]['dishes_id'] . '_' . $menus_dishes2[$i][$j]['menu_id']] = $menus_dishes2[$i][$j]['dishes_id'];
                $dishes_yield[$menus_dishes2[$i][$j]['nutrition_id'] . '_' . $menus_dishes2[$i][$j]['dishes_id'] . '_' . $menus_dishes2[$i][$j]['menu_id']] = $menus_dishes2[$i][$j]['yield'];
            }
        }

        if (Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr'))
        {
            $menus = Menus::find()->where(['organization_id' => Yii::$app->session['organization_id'], 'status_archive' => 0])->all();
        }
        $menus_count_ids = $array3;

        foreach ($post_menus as $p_menu)
        {
            foreach ($nutritions as $nutrition)
            {
                foreach ($menus_dishes as $m_dish)
                {
                    if (array_key_exists($nutrition->id . '_' . $m_dish['dishes_id'] . '_' . $p_menu->id, $dishes_count) && $m_dish['menu_id'] == $p_menu->id && $m_dish['nutrition_id'] == $nutrition->id)
                    {
                        $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']] = $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']] + $menus_count_ids[$p_menu->id];
                        $count_porciya_menu[$nutrition->id . '_' . $m_dish['dishes_id'] . '_' . $p_menu->id] = $menus_count_ids[$p_menu->id];
                    }
                }
            }
        }
        $menus_dishes_id = [];
        if ($post_brutto_netto == 0)
        {
            $brutto_netto = 'net_weight';
        }
        if ($post_brutto_netto == 1)
        {
            $brutto_netto = 'gross_weight';
        }
        foreach ($menus_dishes as $m_dish)
        {
            $menus_dishes_id[] = $m_dish['id'];
        }

        $mas_yield = [];
        $mass = $model_menus_dishes->get_total_raskladka_yield($post_menus, $menus_dishes_id, strtotime($post_date));
        foreach ($mass as $mas)
        {
            $mas_yield[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['products_id']] = $mas_yield[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['products_id']] + (($mas[$brutto_netto] * (($mas['menus_yield'] / $mas['dishes_yield'])) / 1000) * $count_porciya_menu[$mas['nutrition_id'] . '_' . $mas['dishes_id'] . '_' . $mas['menu_id']]);
        }

        $array_num = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

        $num = 2;
        $col = 2;
        $sheet = $document->getActiveSheet();
        $num_st = 2;
        $this->layout = false;

        $sheet->getColumnDimension('A')->setWidth("60");
        $sheet->getColumnDimension('B')->setWidth("10");

        $sheet->getStyle("A1")->getFont()->setBold(true);
        $sheet->getStyle("B1")->getFont()->setBold(true);


        $sheet->setCellValue('A' . 1, "Название меню");
        $sheet->setCellValue('B' . 1, "Количество питающихся");
        $i_num = 0;
        foreach ($post_menus as $p_menu)
        {
            $sheet->setCellValue('A' . $num_st, $p_menu->name);
            $sheet->setCellValue('B' . $num_st, $array2[$i_num]);
            $i_num++;
            $num_st++;
        }
        $num_st++;

        $array_num = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ',
            'EA',
            'EB',
            'EC',
            'ED',
            'EE',
            'EF',
            'EG',
            'EH',
            'EI',
            'EJ',
            'EK',
            'EL',
            'EM',
            'EN',
            'EO',
            'EP',
            'EQ',
            'ER',
            'ES',
            'ET',
            'EU',
            'EV',
            'EW',
            'EX',
            'EY',
            'EZ',
            'FA',
            'FB',
            'FC',
            'FD',
            'FE',
            'FF',
            'FG',
            'FH',
            'FI',
            'FJ',
            'FK',
            'FL',
            'FM',
            'FN',
            'FO',
            'FP',
            'FQ',
            'FR',
            'FS',
            'FT',
            'FU',
            'FV',
            'FW',
            'FX',
            'FY',
            'FZ',
        ];
        $col = 2;
        $sheet->setCellValue('A' . $num_st, "Прием пищи");
        $sheet->setCellValue('B' . $num_st, "Ед. Изм.");
        foreach ($nutritions as $nutrition)
        {
            $sheet->setCellValue($array_num[$col] . $num_st, $nutrition->name);
            for ($i = 0; $i < count($count_dishes_in_nutrition[$nutrition->id]); $i++)
            {
                $col++;
            }
        }

        $sheet->setCellValue($array_num[$col] . $num_st, "Итого");
        $num_st++;

        $sheet->setCellValue('A' . $num_st, "Название блюда");
        $col = 2;
        $count_dish = 0;
        foreach ($nutritions as $nutrition)
        {
            foreach ($menus_dishes as $m_dish)
            {
                if (array_key_exists($nutrition->id . '_' . $m_dish['dishes_id'], $dishes_in_nutrition))
                {
                    // "ЭТОТ МАССИВ НЕ ПУСТОЙ $dishes_in_nutrition
                    $sheet->setCellValue($array_num[$col] . $num_st, $model_menus_dishes->get_dishes($dishes_in_nutrition[$nutrition->id . '_' . $m_dish['dishes_id']]));
                    unset($dishes_in_nutrition[$nutrition->id . '_' . $m_dish['dishes_id']]);
                    $first_row[$nutrition->id . '_' . $m_dish['dishes_id']] = $m_dish['dishes_id'];
                    $count_dish++;
                    $col++;
                }
            }
        }

        $sheet->setCellValue($array_num[$col] . $num_st, $count_dish);
        $num_st++;
        $sheet->setCellValue('A' . $num_st, "Количество порций");
        $sheet->setCellValue('B' . $num_st, "шт");
        $col = 2;
        $itog = 0;
        foreach ($nutritions as $nutrition)
        {
            foreach ($menus_dishes as $m_dish)
            {
                if (array_key_exists($nutrition->id . '_' . $m_dish['dishes_id'], $count_porciya))
                {

                    $sheet->setCellValue($array_num[$col] . $num_st, $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']]);
                    $itog = $itog + $count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']];
                    unset($count_porciya[$nutrition->id . '_' . $m_dish['dishes_id']]);

                    $itog++;
                    $col++;
                }
                /* print_r($nutrition->id );
                 print_r('<br>');
                 print_r($m_dish['dishes_id']);
                 print_r('<br>');
                 print_r($count_porciya);
                 print_r('<br>');*/
            }
        }

        $sheet->setCellValue($array_num[$col] . $num_st, $itog);
        $num_st++;
        //print_r($menus_count_ids);
        //exit();

        foreach ($post_menus as $key => $p_menu)
        {
            $itog = 0;
            $col = 2;
            $sheet->setCellValue('A' . $num_st, $p_menu->name);
            $sheet->setCellValue('B' . $num_st, "г");
            foreach ($first_row as $key => $f_row)
            {
                if (array_key_exists($key . '_' . $p_menu->id, $dishes_yield))
                {
                    $sheet->setCellValue($array_num[$col] . $num_st, $dishes_yield[$key . '_' . $p_menu->id]);
                    $itog = $dishes_yield[$key . '_' . $p_menu->id] + $itog;
                    unset($dishes_yield[$key . '_' . $p_menu->id]);
                }
                else
                {
                    $sheet->setCellValue($array_num[$col] . $num_st, "-");
                }
                $col++;
            }
            /*$itog*/
            $sheet->setCellValue($array_num[$col] . $num_st, $itog);
            $num_st++;
        }

        foreach ($products as $product)
        {
            $itog = 0;
            $sheet->setCellValue('A' . $num_st, $product['name']);
            $sheet->setCellValue('B' . $num_st, "кг");
            $col = 2;
            foreach ($first_row as $key => $f_row)
            {
                if (array_key_exists($key . '_' . $product['id'], $mas_yield))
                {
                    $sheet->setCellValue($array_num[$col] . $num_st, number_format($mas_yield[$key . '_' . $product['id']], 3, '.', ''));
                    $itog = $itog + number_format($mas_yield[$key . '_' . $product['id']], 3, '.', '');
                }
                $col++;
            }
            /*$itog*/
            $sheet->setCellValue($array_num[$col] . $num_st, $itog);
            $num_st++;
        }

        $filename = 'Отчет_Прогнозная_Ведомость' . date('Y_m_d_H_i', time()) . '.xlsx';
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    //ЭТО ПЕЧАТЬ В ПДФ !!!!
    public function actionExportTechmupThisMenu($menu_id)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $indicator = 1;
        $html = '';
        //$all_dishes_from_menus = MenusDishes::find()->where(['menu_id' => $menu_id])->all();
        $all_dishes_from_menus = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->
        orderby(['sort' => SORT_ASC])->
        all();
        $ids = [];
        $recipes_ids = [];
        foreach ($all_dishes_from_menus as $a_menu)
        {
            $d = Dishes::findOne($a_menu->dishes_id);
            if (!in_array($a_menu->dishes_id, $ids) && $d->dishes_category_id != 12)
            {
                $ids[] = $a_menu->dishes_id;
            }
            if (!in_array($d->recipes_collection_id, $recipes_ids) && $d->dishes_category_id != 12)
            {
                $recipes_ids[] = $d->recipes_collection_id;
            }

        }
        $html .= '<p class="mb-0" style="color:#0ea1a8; font-size: 22px;">Сборники рецептур, которые были использованы в меню:</p>';
        foreach ($recipes_ids as $r_id)
        {
            $html .= '
            <p class="mb-0">' . RecipesCollection::findOne($r_id)->name . '</p>';
        }
        //print_r(count($ids));exit;

        $html .= '<p class="mb-0" style="color:#0ea1a8; font-size: 22px;">Список блюд(всего ' . count($ids) . '):</p>';
        foreach ($ids as $id)
        {

            if ($indicator != 1)
            {
                $this->layout = false;
                $menus_dishes = MenusDishes::findOne($id);
                $dishes = Dishes::findOne($menus_dishes->dishes_id);
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $menus_dishes->dishes_id])->all();
                //$indicator = $menus_dishes->yield / 100;
            }
            else
            {
                $this->layout = false;
                $indicator = 1;
                $dishes = Dishes::findOne($id);
                $dishes_products = DishesProducts::find()->
                select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
                leftJoin('products', 'dishes_products.products_id = products.id')->
                leftJoin('products_category', 'products.products_category_id = products_category.id')->
                where(['dishes_id' => $id])->
                orderby(['sort' => SORT_ASC])->
                all();
            }

            $super_total_yield = 0;
            $super_total_protein = 0;
            $super_total_fat = 0;
            $super_total_carbohydrates_total = 0;
            $super_total_energy_kkal = 0;
            $super_total_vitamin_a = 0;
            $super_total_vitamin_c = 0;
            $super_total_vitamin_b1 = 0;
            $super_total_vitamin_b2 = 0;
            $super_total_vitamin_d = 0;
            $super_total_vitamin_pp = 0;
            $super_total_na = 0;
            $super_total_k = 0;
            $super_total_ca = 0;
            $super_total_f = 0;
            $super_total_se = 0;
            $super_total_i = 0;
            $super_total_fe = 0;
            $super_total_p = 0;
            $super_total_mg = 0;
            $number_row = 1;
            $html .= '
            <p class="mb-0 text-center" style="color:red; font-size: 22px;">' . $dishes->name . '</b></p>
            <p class="mb-0"><b>Технологическая карта кулинарного изделия (блюда):</b> ' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование изделия:</b>' . $dishes->name . '</p>
            <p class="mb-0"><b>Номер рецептуры:</b>' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование сборника рецептур, год выпуска, автор:</b>' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->name . ', ' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->year . ' </p>
            <b>Пищевые вещества:</b><br>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Наименование сырья</th>
                    <th class="text-center">Брутто, г.</th>
                    <th class="text-center">Нетто, г.</th>
                    <th class="text-center">Белки, г.</th>
                    <th class="text-center">Жиры, г.</th>
                    <th class="text-center">Углеводы, г.</th>
                    <th class="text-center">Энергетическая ценность, ккал.</th>
                </tr>
        ';

            foreach ($dishes_products as $d_product)
            {

                $html .= '
            <tr>
                <td class="text-center">' . $number_row . '</td>
                <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->gross_weight * $indicator) . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->net_weight * $indicator) . '</td>
                <td class="text-center">' . $protein = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'protein') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $fat = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'fat') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $carbohydrates_total = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'carbohydrates_total') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $energy_kkal = sprintf("%.1f", $d_product->get_kkal($d_product->products_id, $d_product->dishes_id) * (($d_product->net_weight) / 100) * $indicator) . '</td>
            </tr>';
                $super_total_protein = $super_total_protein + $protein;
                $super_total_fat = $super_total_fat + $fat;
                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;
                $number_row++;
            }
            $html .= '
        <tr>
            <td colspan="3"><b>Выход:</b></td>
            <td class="text-center"><b>' . round(($dishes->yield * $indicator), 1) . '</b></td>
            <td class="text-center"><b>' . $super_total_protein . '</b></td>
            <td class="text-center"><b>' . $super_total_fat . '</b></td>
            <td class="text-center"><b>' . $super_total_carbohydrates_total . '</b></td>
            <td class="text-center"><b>' . $super_total_energy_kkal . '</b></td>
        </tr>
        ';
            $html .= '</table>';
            $html .= ' <br><b>Витамины и минеральные вещества</b>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr class="">
                    <th class="text-center">№</th>
                    <th class="text-center">Продукт</th>
                    <th class="text-center">B1, мг</th>
                    <th class="text-center">B2, мг</th>
                    <th class="text-center">А, мкг. рет. экв.</th>
                    <th class="text-center">РР, мг.</th>
                    <th class="text-center">C, мг.</th>
                    <th class="text-center">Na, мг.</th>
                    <th class="text-center">K, мг.</th>
                    <th class="text-center">Ca, мг.</th>
                    <th class="text-center">Mg, мг.</th>
                    <th class="text-center">P, мг.</th>
                    <th class="text-center">FE, мг.</th>
                    <th class="text-center">I, мкг.</th>
                    <th class="text-center">Se, мкг.</th>
            </tr>
        ';

            $number_row = 1;
            foreach ($dishes_products as $d_product)
            {
                $html .= '
           <tr>
               <td class="text-center">' . $number_row . '</td>
               <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
               <td class="text-center">' . $vitamin_b1 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b1') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_b2 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b2') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_a = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_a') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_pp = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_pp') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_c = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_c') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $na = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'na') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $k = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'k') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $ca = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'ca') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $mg = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'mg') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $p = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'p') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $fe = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'fe') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $i = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'i') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $se = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'se') * (($d_product->net_weight) / 100) * $indicator) . '</td>
           </tr>';

                $number_row++;
                $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                $super_total_na = $super_total_na + $na;
                $super_total_k = $super_total_k + $k;
                $super_total_ca = $super_total_ca + $ca;
                $super_total_mg = $super_total_mg + $mg;
                $super_total_p = $super_total_p + $p;
                $super_total_fe = $super_total_fe + $fe;
                $super_total_i = $super_total_i + $i;
                $super_total_se = $super_total_se + $se;
            }
            $html .= ' 
            <tr>
                <td colspan="2"><b>Итого</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b1 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b2 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_a . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_pp . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_c . '</b></td>
                <td class="text-center"><b>' . $super_total_na . '</b></td>
                <td class="text-center"><b>' . $super_total_k . '</b></td>
                <td class="text-center"><b>' . $super_total_ca . '</b></td>
                <td class="text-center"><b>' . $super_total_mg . '</b></td>
                <td class="text-center"><b>' . $super_total_p . '</b></td>
                <td class="text-center"><b>' . $super_total_fe . '</b></td>
                <td class="text-center"><b>' . $super_total_i . '</b></td>
                <td class="text-center"><b>' . $super_total_se . '</b></td>
            </tr>
        ';
            $html .= '</table>';
            $html .= '
            <p class="mb-0"><b>Способ обработки:</b>' . $dishes->get_culinary_processing($dishes->culinary_processing_id) . '</p>
            <p class="mb-0"><b>Технология приготовления:</b> ' . $dishes->description . '</p>
            <b>Характеристика блюда на выходе:</b>
            <p class="mb-0">' . $dishes->dishes_characters . '</p>
        ';
        }
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'); //номер страницы {PAGENO}
        $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
        //$mpdf->Output('MyPDF.pdf', 'I'); //I - откроет в томже окне файл!
        //$mpdf->Output('MyPDF123123.pdf', 'F'); //F - гененирует ссылку на файл и сохранить его на сервере путь сохр backend\web!
    }

    //ЭТО ПЕЧАТЬ В ПДФ !!!! для их выходов
    public function actionExportTechmupThisMenuVihod($menu_id)
    {

        require_once __DIR__ . '/../../vendor/autoload.php';
        ini_set("pcre.backtrack_limit", "5000000");
        $indicator = 1;
        $html = '';
        //$all_dishes_from_menus = MenusDishes::find()->where(['menu_id' => $menu_id])->all();
        $all_dishes_from_menus = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->
        orderby(['sort' => SORT_ASC])->
        all();
        $ids = [];
        $recipes_ids = [];
        foreach ($all_dishes_from_menus as $a_menu)
        {
            $d = Dishes::findOne($a_menu->dishes_id);
            if (!in_array($a_menu->dishes_id, $ids) && $d->dishes_category_id != 12)
            {
                $ids[] = $a_menu->dishes_id;
            }
            if (!in_array($d->recipes_collection_id, $recipes_ids) && $d->dishes_category_id != 12)
            {
                $recipes_ids[] = $d->recipes_collection_id;
            }
        }
        $html .= '<p class="mb-0" style="color:#0ea1a8; font-size: 22px;">Сборники рецептур, которые были использованы в меню:</p>';
        foreach ($recipes_ids as $r_id)
        {
            $html .= '
            <p class="mb-0">' . RecipesCollection::findOne($r_id)->name . '</p>';
        }
        //print_r(count($ids));exit;

        $html .= '<p class="mb-0" style="color:#0ea1a8; font-size: 22px;">Список блюд(всего ' . count($ids) . '):</p>';
        foreach ($ids as $id)
        {


            $this->layout = false;
            $dishes = Dishes::findOne($id);
            $indicator = MenusDishes::find()->where(['dishes_id' => $id, 'menu_id' => $menu_id])->one()->yield / $dishes->yield;
            $dishes_products = DishesProducts::find()->
            select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
            leftJoin('products', 'dishes_products.products_id = products.id')->
            leftJoin('products_category', 'products.products_category_id = products_category.id')->
            where(['dishes_id' => $id])->
            orderby(['sort' => SORT_ASC])->
            all();


            $super_total_yield = 0;
            $super_total_protein = 0;
            $super_total_fat = 0;
            $super_total_carbohydrates_total = 0;
            $super_total_energy_kkal = 0;
            $super_total_vitamin_a = 0;
            $super_total_vitamin_c = 0;
            $super_total_vitamin_b1 = 0;
            $super_total_vitamin_b2 = 0;
            $super_total_vitamin_d = 0;
            $super_total_vitamin_pp = 0;
            $super_total_na = 0;
            $super_total_k = 0;
            $super_total_ca = 0;
            $super_total_f = 0;
            $super_total_se = 0;
            $super_total_i = 0;
            $super_total_fe = 0;
            $super_total_p = 0;
            $super_total_mg = 0;
            $number_row = 1;
            $html .= '
            <p class="mb-0 text-center" style="color:red; font-size: 22px;">' . $dishes->name . '</b></p>
            <p class="mb-0"><b>Технологическая карта кулинарного изделия (блюда):</b> ' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование изделия:</b>' . $dishes->name . '</p>
            <p class="mb-0"><b>Номер рецептуры:</b>' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование сборника рецептур, год выпуска, автор:</b>' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->name . ', ' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->year . ' </p>
            <b>Пищевые вещества:</b><br>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Наименование сырья</th>
                    <th class="text-center">Брутто, г.</th>
                    <th class="text-center">Нетто, г.</th>
                    <th class="text-center">Белки, г.</th>
                    <th class="text-center">Жиры, г.</th>
                    <th class="text-center">Углеводы, г.</th>
                    <th class="text-center">Энергетическая ценность, ккал.</th>
                </tr>
        ';

            foreach ($dishes_products as $d_product)
            {

                $html .= '
            <tr>
                <td class="text-center">' . $number_row . '</td>
                <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->gross_weight * $indicator) . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->net_weight * $indicator) . '</td>
                <td class="text-center">' . $protein = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'protein') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $fat = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'fat') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $carbohydrates_total = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'carbohydrates_total') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $energy_kkal = sprintf("%.1f", $d_product->get_kkal($d_product->products_id, $d_product->dishes_id) * (($d_product->net_weight) / 100) * $indicator) . '</td>
            </tr>';
                $super_total_protein = $super_total_protein + $protein;
                $super_total_fat = $super_total_fat + $fat;
                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;
                $number_row++;
            }
            $html .= '
        <tr>
            <td colspan="3"><b>Выход:</b></td>
            <td class="text-center"><b>' . round(($dishes->yield * $indicator), 1) . '</b></td>
            <td class="text-center"><b>' . $super_total_protein . '</b></td>
            <td class="text-center"><b>' . $super_total_fat . '</b></td>
            <td class="text-center"><b>' . $super_total_carbohydrates_total . '</b></td>
            <td class="text-center"><b>' . $super_total_energy_kkal . '</b></td>
        </tr>
        ';
            $html .= '</table>';
            $html .= ' <br><b>Витамины и минеральные вещества</b>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr class="">
                    <th class="text-center">№</th>
                    <th class="text-center">Продукт</th>
                    <th class="text-center">B1, мг</th>
                    <th class="text-center">B2, мг</th>
                    <th class="text-center">А, мкг. рет. экв.</th>
                    <th class="text-center">РР, мг.</th>
                    <th class="text-center">C, мг.</th>
                    <th class="text-center">Na, мг.</th>
                    <th class="text-center">K, мг.</th>
                    <th class="text-center">Ca, мг.</th>
                    <th class="text-center">Mg, мг.</th>
                    <th class="text-center">P, мг.</th>
                    <th class="text-center">FE, мг.</th>
                    <th class="text-center">I, мкг.</th>
                    <th class="text-center">Se, мкг.</th>
            </tr>
        ';

            $number_row = 1;
            foreach ($dishes_products as $d_product)
            {
                $html .= '
           <tr>
               <td class="text-center">' . $number_row . '</td>
               <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
               <td class="text-center">' . $vitamin_b1 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b1') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_b2 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b2') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_a = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_a') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_pp = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_pp') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_c = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_c') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $na = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'na') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $k = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'k') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $ca = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'ca') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $mg = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'mg') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $p = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'p') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $fe = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'fe') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $i = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'i') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $se = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'se') * (($d_product->net_weight) / 100) * $indicator) . '</td>
           </tr>';

                $number_row++;
                $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                $super_total_na = $super_total_na + $na;
                $super_total_k = $super_total_k + $k;
                $super_total_ca = $super_total_ca + $ca;
                $super_total_mg = $super_total_mg + $mg;
                $super_total_p = $super_total_p + $p;
                $super_total_fe = $super_total_fe + $fe;
                $super_total_i = $super_total_i + $i;
                $super_total_se = $super_total_se + $se;
            }
            $html .= ' 
            <tr>
                <td colspan="2"><b>Итого</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b1 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b2 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_a . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_pp . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_c . '</b></td>
                <td class="text-center"><b>' . $super_total_na . '</b></td>
                <td class="text-center"><b>' . $super_total_k . '</b></td>
                <td class="text-center"><b>' . $super_total_ca . '</b></td>
                <td class="text-center"><b>' . $super_total_mg . '</b></td>
                <td class="text-center"><b>' . $super_total_p . '</b></td>
                <td class="text-center"><b>' . $super_total_fe . '</b></td>
                <td class="text-center"><b>' . $super_total_i . '</b></td>
                <td class="text-center"><b>' . $super_total_se . '</b></td>
            </tr>
        ';
            $html .= '</table>';
            $html .= '
            <p class="mb-0"><b>Способ обработки:</b>' . $dishes->get_culinary_processing($dishes->culinary_processing_id) . '</p>
            <p class="mb-0"><b>Технология приготовления:</b> ' . $dishes->description . '</p>
            <b>Характеристика блюда на выходе:</b>
            <p class="mb-0">' . $dishes->dishes_characters . '</p>
        ';
        }
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'); //номер страницы {PAGENO}
        $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
        //$mpdf->Output('MyPDF.pdf', 'I'); //I - откроет в томже окне файл!
        //$mpdf->Output('MyPDF123123.pdf', 'F'); //F - гененирует ссылку на файл и сохранить его на сервере путь сохр backend\web!
    }

    //ЭТО ПЕЧАТЬ В ПДФ !!!! для их выходов по дням
    public function actionExportTechmupThisMenuDayVihod($menu_id)
    {

        require_once __DIR__ . '/../../vendor/autoload.php';
        $indicator = 1;
        $html = '';
        //$all_dishes_from_menus = MenusDishes::find()->where(['menu_id' => $menu_id])->all();
        /*$all_dishes_from_menus = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin ('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin ('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->
        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort'=>SORT_ASC])->
        all();*/


        $model = new MenusDishes();
        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }
        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ

        $menus_dishes = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->
        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
        all();

        $menus_days_id = MenusDays::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
        $days_ids = [];
        foreach ($menus_days_id as $day_id)
        {
            $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
        }

        $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
        $count_my_days = MenusDays::find()->where(['menu_id' => $menu_id])->count();
        $my_menus = Menus::findOne($menu_id);
        $menu_cycle_count = $my_menus->cycle;
        $cycle = Menus::findOne($menu_id)->cycle;

        if ($cycle == 0)
        {
            $count_my_days = $count_my_days * $menu_cycle_count;
        }

        $cycle_ids = [];

        for ($i = 1; $i <= $menu_cycle_count; $i++)
        {
            $cycle_ids[$i] = $i;//массив из подходящи циклов
        }


        $count_cycle = 0;
        $count = 0;
        $data = [];
        for ($cycle_id = 1; $cycle_id <= $menu_cycle_count; $cycle_id++)
        {
            $count++;

            $html .= '
                <p style="font-size: 12px;">Неделя ' . $cycle_id . '</b></p>';

            foreach ($days as $day)
            {
                $html .= '
                <p style="font-size: 12px;">' . $day->name . '</b></p>';
                foreach ($nutritions as $nutrition)
                {
                    $html .= '
                <p style="font-size: 12px;">' . $nutrition->name . '</b></p>';


                    foreach ($menus_dishes as $key => $m_dish)
                    {

                        $dishes = Dishes::findOne($m_dish->dishes_id);
                        if ($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id && $dishes->dishes_category_id != 12)
                        {

                            $this->layout = false;
                            //$dishes = Dishes::findOne($m_dish->dishes_id);
                            $indicator = MenusDishes::findOne($m_dish->id)->yield / $dishes->yield;
                            $dishes_products = DishesProducts::find()->
                            select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
                            leftJoin('products', 'dishes_products.products_id = products.id')->
                            leftJoin('products_category', 'products.products_category_id = products_category.id')->
                            where(['dishes_id' => $m_dish->dishes_id])->
                            orderby(['sort' => SORT_ASC])->
                            all();


                            $super_total_yield = 0;
                            $super_total_protein = 0;
                            $super_total_fat = 0;
                            $super_total_carbohydrates_total = 0;
                            $super_total_energy_kkal = 0;
                            $super_total_vitamin_a = 0;
                            $super_total_vitamin_c = 0;
                            $super_total_vitamin_b1 = 0;
                            $super_total_vitamin_b2 = 0;
                            $super_total_vitamin_d = 0;
                            $super_total_vitamin_pp = 0;
                            $super_total_na = 0;
                            $super_total_k = 0;
                            $super_total_ca = 0;
                            $super_total_f = 0;
                            $super_total_se = 0;
                            $super_total_i = 0;
                            $super_total_fe = 0;
                            $super_total_p = 0;
                            $super_total_mg = 0;
                            $number_row = 1;
                            $html .= '
                <p class="mb-0 text-center" style="color:red; font-size: 12px;">' . $dishes->name . '</b></p>
                <p class="mb-0" style="font-size: 12px; font-family:\'Times New Roman\';"><b>Технологическая карта кулинарного изделия (блюда):</b> ' . $dishes->techmup_number . '</p>
                <p class="mb-0" style="font-size: 12px; font-family:\'Times New Roman\';"><b>Наименование изделия:</b> ' . $dishes->name . '</p>
                <p class="mb-0" style="font-size: 12px; font-family:\'Times New Roman\';"><b>Номер рецептуры:</b> ' . $dishes->techmup_number . '</p>
                <p class="mb-0" style="font-size: 12px; font-family:\'Times New Roman\';"><b>Наименование сборника рецептур, год выпуска, автор:</b> ' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->name . ', ' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->year . ' </p>
                <b class="mb-0" style="font-size: 12px; font-family:\'Times New Roman\';">Пищевые вещества:</b><br>
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                font-size: 10px;
                ">
                    <tr>
                        <th align="center">№</th>
                        <th align="center">Наименование сырья</th>
                        <th align="center">Брутто, г.</th>
                        <th align="center">Нетто, г.</th>
                        <th align="center">Белки, г.</th>
                        <th align="center">Жиры, г.</th>
                        <th align="center">Углеводы, г.</th>
                        <th align="center">Энергетическая ценность, ккал.</th>
                    </tr>
            ';

                            foreach ($dishes_products as $d_product)
                            {

                                $html .= '
                <tr>
                    <td align="center">' . $number_row . '</td>
                    <td>' . $d_product->get_products($d_product->products_id)->name . '</td>
                    <td align="center">' . sprintf("%.1f", $d_product->gross_weight * $indicator) . '</td>
                    <td align="center">' . sprintf("%.1f", $d_product->net_weight * $indicator) . '</td>
                    <td align="center">' . $protein = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'protein') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                    <td align="center">' . $fat = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'fat') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                    <td align="center">' . $carbohydrates_total = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'carbohydrates_total') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                    <td align="center">' . $energy_kkal = sprintf("%.1f", $d_product->get_kkal($d_product->products_id, $d_product->dishes_id) * (($d_product->net_weight) / 100) * $indicator) . '</td>
                </tr>';
                                $super_total_protein = $super_total_protein + $protein;
                                $super_total_fat = $super_total_fat + $fat;
                                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;
                                $number_row++;
                            }
                            $html .= '
            <tr>
                <td colspan="3"><b>Выход:</b></td>
                <td align="center"><b>' . round(($dishes->yield * $indicator), 1) . '</b></td>
                <td align="center"><b>' . $super_total_protein . '</b></td>
                <td align="center"><b>' . $super_total_fat . '</b></td>
                <td align="center"><b>' . $super_total_carbohydrates_total . '</b></td>
                <td align="center"><b>' . $super_total_energy_kkal . '</b></td>
            </tr>
            ';
                            $html .= '</table>';
                            $html .= ' <br><b class="mb-0" style="font-size: 12px;">Витамины и минеральные вещества</b>
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                font-size: 10px;
                ">
                    <tr class="">
                        <th align="center">№</th>
                        <th align="center">Продукт</th>
                        <th align="center">B1, мг</th>
                        <th align="center">B2, мг</th>
                        <th align="center">А, мкг. рет. экв.</th>
                        <th align="center">РР, мг.</th>
                        <th align="center">C, мг.</th>
                        <th align="center">Na, мг.</th>
                        <th align="center">K, мг.</th>
                        <th align="center">Ca, мг.</th>
                        <th align="center">Mg, мг.</th>
                        <th align="center">P, мг.</th>
                        <th align="center">Fe, мг.</th>
                        <th align="center">I, мкг.</th>
                        <th align="center">Se, мкг.</th>
                </tr>
            ';

                            $number_row = 1;
                            foreach ($dishes_products as $d_product)
                            {
                                $html .= '
               <tr>
                   <td align="center">' . $number_row . '</td>
                   <td>' . $d_product->get_products($d_product->products_id)->name . '</td>
                   <td align="center">' . $vitamin_b1 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b1') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                   <td align="center">' . $vitamin_b2 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b2') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                   <td align="center">' . $vitamin_a = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_a') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                   <td align="center">' . $vitamin_pp = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_pp') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                   <td align="center">' . $vitamin_c = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_c') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $na = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'na') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $k = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'k') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $ca = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'ca') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $mg = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'mg') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $p = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'p') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $fe = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'fe') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
                   <td align="center">' . $i = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'i') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                   <td align="center">' . $se = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'se') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               </tr>';

                                $number_row++;
                                $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                                $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                                $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                                $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                                $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                                $super_total_na = $super_total_na + $na;
                                $super_total_k = $super_total_k + $k;
                                $super_total_ca = $super_total_ca + $ca;
                                $super_total_mg = $super_total_mg + $mg;
                                $super_total_p = $super_total_p + $p;
                                $super_total_fe = $super_total_fe + $fe;
                                $super_total_i = $super_total_i + $i;
                                $super_total_se = $super_total_se + $se;
                            }
                            $html .= ' 
                <tr>
                    <td colspan="2"><b>Итого</b></td>
                    <td align="center"><b>' . $super_total_vitamin_b1 . '</b></td>
                    <td align="center"><b>' . $super_total_vitamin_b2 . '</b></td>
                    <td align="center"><b>' . $super_total_vitamin_a . '</b></td>
                    <td align="center"><b>' . $super_total_vitamin_pp . '</b></td>
                    <td align="center"><b>' . $super_total_vitamin_c . '</b></td>
                    <td align="center"><b>' . $super_total_na . '</b></td>
                    <td align="center"><b>' . $super_total_k . '</b></td>
                    <td align="center"><b>' . $super_total_ca . '</b></td>
                    <td align="center"><b>' . $super_total_mg . '</b></td>
                    <td align="center"><b>' . $super_total_p . '</b></td>
                    <td align="center"><b>' . $super_total_fe . '</b></td>
                    <td align="center"><b>' . $super_total_i . '</b></td>
                    <td align="center"><b>' . $super_total_se . '</b></td>
                </tr>
            ';
                            $html .= '</table>';
                            $html .= '
                <p class="mb-0" style="font-size: 12px;"><b>Способ обработки:</b> ' . $dishes->get_culinary_processing($dishes->culinary_processing_id) . '</p>
                <p class="mb-0" style="font-size: 12px;"><b>Технология приготовления:</b> ' . $dishes->description . '</p>
                <b class="mb-0" style="font-size: 12px;">Характеристика блюда на выходе:</b>
                <p class="mb-0" style="font-size: 12px;">' . $dishes->dishes_characters . '</p>
            ';
                        }


                    }
                }
            }
        }


        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'); //номер страницы {PAGENO}
        $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
        //$mpdf->Output('MyPDF.pdf', 'I'); //I - откроет в томже окне файл!
        //$mpdf->Output('MyPDF123123.pdf', 'F'); //F - гененирует ссылку на файл и сохранить его на сервере путь сохр backend\web!
    }

    public function actionFactDatePdf($menu_id, $date)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $model2 = new FactdateForm();
        $model = new MenusDishes();

        //$nutritions = NutritionInfo::find()->all();
        $post = Yii::$app->request->post()['FactdateForm'];
        $my_menus = Menus::findOne($menu_id);

        if ($my_menus->date_end < strtotime($post['date']) || $my_menus->date_start > strtotime($date))
        {
            Yii::$app->session->setFlash('error', "Указанная дата не входит в диапозон даты начала или даты окончания меню");
            return $this->redirect(['menus-dishes/fact-date']);
        }

        $menus_days_id = MenusDays::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
        $days_ids = [];
        foreach ($menus_days_id as $day_id)
        {
            if ($day_id->days_id != 7)
            {
                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }
            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ФУНКЦИИ DATE() ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
            else
            {
                $days_ids[] = 0;
            }

        }
        if (!in_array(date("w", strtotime($date)), $days_ids))
        {
            Yii::$app->session->setFlash('error', "Этот день недели отсутсвует в меню");
            return $this->redirect(['menus-dishes/fact-date']);
        }


        $start_date = date('d.m.Y', $my_menus->date_start);//Дата старта меню
        $day_of_week = date("w", strtotime($date));//День недели выбранной даты
        $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
        /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
        /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
        if ($day_of_week_start_date == 0)
        {
            $day_of_week_start_date = 7;
        }
        if ($day_of_week == 0)
        {
            $day_of_week = 7;
        }
        /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
        $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

        $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
        $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
        $count_week = ceil((((strtotime($date) - $my_menus->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

        $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
        /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
        if ($day_of_week == 1)
        {
            $cycle = $count_week - 1;
        }
        /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
        if ($count_week == 0)
        {
            $cycle = 1;
        }

        /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
        while ($cycle > $my_menus->cycle)
        {
            $cycle = $cycle - $my_menus->cycle;
        }
        if ($cycle == 0)
        {
            $cycle = $my_menus->cycle;
        }
        /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/

        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }

        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
        //$menus_dishes = MenusDishes::find()->where(['menu_id' => $post['menu_id'], 'cycle' => $cycle, 'days_id' => $day_of_week])->orderby(['nutrition_id' => SORT_ASC])->all();

        $menus_dishes = MenusDishes::find()->
        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
        where(['date_fact_menu' => strtotime($date), 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $day_of_week])->
        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
        all();


        if (empty($menus_dishes))
        {
            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle, 'days_id' => $day_of_week])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();
        }
        $menus_dishes2 = $menus_dishes;


        $super_total_yield = 0;
        $super_total_protein = 0;
        $super_total_fat = 0;
        $super_total_carbohydrates_total = 0;
        $super_total_energy_kkal = 0;
        $super_total_vitamin_a = 0;
        $super_total_vitamin_c = 0;
        $super_total_vitamin_b1 = 0;
        $super_total_vitamin_b2 = 0;
        $super_total_vitamin_d = 0;
        $super_total_vitamin_pp = 0;
        $super_total_na = 0;
        $super_total_k = 0;
        $super_total_ca = 0;
        $super_total_f = 0;
        $super_total_se = 0;

        $html = '
            <div class="block" style="margin-top: 10px;">';
        foreach ($nutritions as $nutrition)
        {
            //print_r($nutrition->name);
            //exit();

            $html .= '
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                 /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                ">
                    <thead>
                    <tr class="text-center"><td colspan="17" align="center"><p  style="font-size: 20px; ">' . $nutrition->name . '</p></td></tr>
                    <tr>
                        <th class="text-center align-middle" rowspan="2">Название блюда</th>
                        <th class="text-center align-middle" rowspan="2">Выход</th>
                        <th class="text-center align-middle" rowspan="2">Белки</th>
                        <th class="text-center align-middle" rowspan="2">Жиры</th>
                        <th class="text-center align-middle" rowspan="2">Углеводы</th>
                        <th class="text-center align-middle" rowspan="2">Эн. ценность</th>
                        <th class="text-center" colspan="6">Витамины</th>
                        <th class="text-center" colspan="6">Минеральные вещества</th>
                    </tr>
                   
                    </thead>
                    <!--<tbody>-->
                ';
            $count = 0;
            $indicator = 0;
            $energy_kkal = 0;
            $protein = 0;
            $fat = 0;
            $carbohydrates_total = 0;

            foreach ($menus_dishes as $key => $m_dish)
            {
                // print_r($m_dish->id);
                // exit();
                if ($nutrition->id == $m_dish->nutrition_id)
                {
                    $count++;

                    $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'), 1);
                    $protein = $protein_dish + $protein;
                    $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'), 1);
                    $fat = $fat_dish + $fat;
                    $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'), 1);
                    $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total;
                    $kkal = round($m_dish->get_kkal_dish($m_dish->id), 1);
                    $energy_kkal = $energy_kkal + $kkal;

                    $html .= '
                        <tr data-id="' . $m_dish->id . '">
                            <td>' . $m_dish->get_dishes($m_dish->dishes_id) . '</td>
                            <td class="text-center">' . $m_dish->yield . '</td>
                            <td class="text-center">' . $protein_dish . '</td>
                            <td class="text-center">' . $fat_dish . '</td>
                            <td class="text-center">' . $carbohydrates_total_dish . '</td>
                            <td class="text-center">' . $kkal . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'), 3) . '</td>
                        </tr>';
                    unset($menus_dishes[$key]);
                }
                else
                {
                    break;
                }
            }
            if ($count > 0)
            {
                $html .= '
                    <tr class="table-primary" style="background: lightskyblue;">
                        <td>Итого за <b>' . $nutrition->name . '</b></td>
                        <td class="text-center">' . $yield = $model->get_total_yield($menu_id, $cycle, $day_of_week, $nutrition->id) . '</td>
                        <td class="text-center">' . $protein . '</td>
                        <td class="text-center">' . $fat . '</td>
                        <td class="text-center">' . $carbohydrates_total . '</td>
                        <td class="text-center">' . $energy_kkal . '</td>
                   
                    </tr>
                    <tr class="table-success" >
                        <td style="background: aquamarine;" colspan="2">Рекомендуемая величина</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'protein_middle_weight') . '</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'fat_middle_weight') . '</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'carbohydrates_middle_weight') . '</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_recommended_normativ($menu_id, $nutrition->id, 'middle_kkal') . '</td>
                    </tr>
                    <tr class="table-warning" >
                        <td colspan="2" style="background: moccasin;">Процент от общей массы пищевых веществ</td>
                        <td class="text-center" style="background: moccasin;">' . $model->get_procent($menu_id, $cycle, $day_of_week, $nutrition->id, 'protein') . '%</td>
                        <td class="text-center" style="background: moccasin;">' . $model->get_procent($menu_id, $cycle, $day_of_week, $nutrition->id, 'fat') . '%</td>
                        <td class="text-center" style="background: moccasin;">' . $model->get_procent($menu_id, $cycle, $day_of_week, $nutrition->id, 'carbohydrates_total') . '%</td>
                    </tr>
                    <tr class="table-info">
                        <td style="background: aquamarine;">Процент от суток</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_yield($menu_id, $cycle, $day_of_week, $nutrition->id) . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $day_of_week, $nutrition->id, 'protein') . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $day_of_week, $nutrition->id, 'fat') . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $day_of_week, $nutrition->id, 'carbohydrates_total') . '%</td>
                        <td class="text-center" style="background: aquamarine;">' . $model->get_super_total_field($menu_id, $cycle, $day_of_week, $nutrition->id, 'energy_kkal') . '%</td>
                    </tr>
                    
                ';


                $super_total_yield = $super_total_yield + $yield;
                $super_total_protein = $super_total_protein + $protein;
                $super_total_fat = $super_total_fat + $fat;
                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;

            }

            //$html .= '</tbody>';
            $html .= '</table>';
            $html .= '<br>';
        }


        $html .= '
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                 /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                ">
                    <thead>
                    <tr>
                        <th class="text-center">A, мкг рет.экв</th>
                        <th class="text-center">C, мг</th>
                        <th class="text-center">B1, мг</th>
                        <th class="text-center">B2, мг</th>
                        <th class="text-center">D, мкг</th>
                        <th class="text-center">PP, мг</th>
                        <th class="text-center">Na, мг</th>
                        <th class="text-center">K, мг</th>
                        <th class="text-center">Ca, мг</th>
                        <th class="text-center">F, мг</th>
                        <th class="text-center">Se, мкг</th>
                    </tr>
                    </thead>';
        foreach ($menus_dishes2 as $key => $m_dish)
        {
            // print_r($m_dish->id);
            // exit();
            if ($nutrition->id == $m_dish->nutrition_id)
            {
                $count++;


                $html .= '
                        <tr data-id="' . $m_dish->id . '">
                            <td>' . $m_dish->get_dishes($m_dish->dishes_id) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'), 2) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'), 3) . '</td>
                            <td class="text-center">' . round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'), 3) . '</td>
                        </tr>';
                unset($menus_dishes[$key]);
            }
            else
            {
                break;
            }
        }
        if ($count > 0)
        {
            $html .= '
                    <tr class="table-primary" style="background: lightskyblue;">
                        <td>Итого за <b>' . $nutrition->name . '</b></td>
                        <td class="text-center">' . $vitamin_a = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'vitamin_a'), 2) . '</td>
                        <td class="text-center">' . $vitamin_c = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'vitamin_c'), 2) . '</td>
                        <td class="text-center">' . $vitamin_b1 = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'vitamin_b1'), 2) . ' </td>
                        <td class="text-center">' . $vitamin_b2 = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'vitamin_b2'), 2) . '</td>
                        <td class="text-center">' . $vitamin_d = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'vitamin_d'), 2) . '</td>
                        <td class="text-center">' . $vitamin_pp = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'vitamin_pp'), 2) . '</td>
                        <td class="text-center">' . $na = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'na'), 3) . '</td>
                        <td class="text-center">' . $k = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'k'), 3) . '</td>
                        <td class="text-center">' . $ca = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'ca'), 3) . '</td>
                        <td class="text-center">' . $f = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'f'), 3) . '</td>
                        <td class="text-center">' . $se = round($model->get_total_vitamin($menu_id, $cycle, $day_of_week, $nutrition->id, 'se'), 3) . '</td>
                    </tr>';


            $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
            $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
            $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
            $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
            $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d;
            $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
            $super_total_na = $super_total_na + $na;
            $super_total_k = $super_total_k + $k;
            $super_total_ca = $super_total_ca + $ca;
            $super_total_f = $super_total_f + $f;
            $super_total_se = $super_total_se + $se;
        }

        $html .= '</table>';
        $html .= '<br>';


        $html .= '
                <table border="1"
                style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
                 /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
                ">';
        $html .= '
            <thead>
                <tr class="text-center"><td colspan="16" align="center"><p  style="font-size: 20px; ">Итого за день</p></td></tr>
                    <tr>
                        <!--<th class="text-center align-middle" rowspan="2">                 </th>-->
                        <th class="text-center align-middle" rowspan="2">Выход</th>
                        <th class="text-center align-middle" rowspan="2">Белки</th>
                        <th class="text-center align-middle" rowspan="2">Жиры</th>
                        <th class="text-center align-middle" rowspan="2">Углеводы</th>
                        <th class="text-center align-middle" rowspan="2">Эн. ценность</th>
                        <th class="text-center" colspan="6">Витамины</th>
                        <th class="text-center" colspan="6">Минеральные вещества</th>
                    </tr>
                    <tr>
                        <th class="text-center">A, мкг рет.экв</th>
                        <th class="text-center">C, мг</th>
                        <th class="text-center">B1, мг</th>
                        <th class="text-center">B2, мг</th>
                        <th class="text-center">D, мкг</th>
                        <th class="text-center">PP, мг</th>
                        <th class="text-center">Na, мг</th>
                        <th class="text-center">K, мг</th>
                        <th class="text-center">Ca, мг</th>
                        <th class="text-center">F, мг</th>
                        <th class="text-center">Se, мкг</th>
                    </tr>
            </thead>
            <tr class="itog_day table-danger">
                <!--<td>Итого за день </td>-->
                <td class="text-center" style="background: lightcoral;">' . $super_total_yield . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_protein . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_fat . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_carbohydrates_total . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_energy_kkal . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_a . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_c . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_b1 . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_b2 . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_d . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_vitamin_pp . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_na . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_k . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_ca . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_f . '</td>
                <td class="text-center" style="background: lightcoral;">' . $super_total_se . '</td>
            </tr>';
        /*$html .= '
        <tr class="procent_day table-danger">
            <td colspan="1" style="background: pink;">Процентное соотношение БЖУ за день</td>
            <td class="text-center" style="background: pink;">' . $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'protein') . '%</td>
            <td class="text-center" style="background: pink;">' . $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'fat') . '%</td>
            <td class="text-center" style="background: pink;">' . $model->get_super_total_bju($menu_id, $cycle, $days_id, 'super_total', 'carbohydrates_total') . '%</td>
        </tr>';*/

        $html .= '</table>';
        $html .= '</div>';
        $mpdf = new Mpdf (['margin_left' => '5', 'margin_right' => '5', 'margin_top' => '10', 'margin_bottom' => '5']);;
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"  {PAGENO}</div>'); //номер страницы {PAGENO}
        $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
    }

    public function actionReportSchool()
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '5092M');
        $model = new MenusDishes();


        if (Yii::$app->request->post())
        {

            $post = Yii::$app->request->post()['MenusDishes'];

            return $this->render('report-school', [
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('report-school', [
            'model' => $model,

        ]);
    }

    public function actionReportSchoolLittle1()
    {
        $model = new MenusDishes();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenusDishes'];
            return $this->render('report-school-little1', [
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('report-school-little1', [
            'model' => $model,

        ]);
    }

    public function actionReportSchoolLittle2()
    {
        $model = new MenusDishes();


        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenusDishes'];
            return $this->render('report-school-little2', [
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('report-school-little2', [
            'model' => $model,

        ]);
    }

    public function actionReportSchoolLittle3()
    {
        $model = new MenusDishes();


        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenusDishes'];
            return $this->render('report-school-little3', [
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('report-school-little3', [
            'model' => $model,

        ]);
    }

    public function actionReportDocument()
    {
        $model = new MenusDishes();


        if (Yii::$app->request->post())
        {
            require_once __DIR__ . '/../../vendor/autoload.php';
            ini_set("pcre.backtrack_limit", "5000000");

            $identificator = Yii::$app->request->post()['identificator'];
            $menu_id = Yii::$app->request->post()['MenusDishes']['menu_id'];
            $display = Yii::$app->request->post()['MenusDishes']['yield'];
            $cycle = Yii::$app->request->post()['MenusDishes']['cycle'];
            $data = Yii::$app->request->post()['MenusDishes']['created_at'];
            $menu = Menus::findOne($menu_id);

            if ($identificator == 'menu')
            {


                $html = '';
                $inputPath = 'images/1.jpg';
                //$html .= '<img src="' . $inputPath . '"/><br>';

                $data_itog = [];
                $ocenka_pish_cen = Yii::$app->request->post()['MenusDishes']['nutrition_id'];
                $analiz = Yii::$app->request->post()['MenusDishes']['dishes_id'];

                if ($display == 1)
                {
                    $data_fac = $data;
                }
                else
                {
                    $data_fac = '';
                }

                // print_r($data);exit;

                $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
                $ids = [];
                $max_nutrition_id = 0;

                $normativ_vitamin_day_vitamin_a = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_a', 'age_info_id' => $menu_id->age_info_id])->one()->value;
                $normativ_vitamin_day_k = \common\models\NormativVitaminDay::find()->where(['name' => 'k', 'age_info_id' => $menu_id->age_info_id])->one()->value;


                foreach ($menus_nutrition_id as $m_id)
                {
                    $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
                }
                $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
                if (!empty($nutritions))
                {
                    $max_nutrition_id = max($ids);
                }


                if ($display == 0)
                {

                    $menus_dishes = MenusDishes::find()->
                    select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                    leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                    leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                    where(['date_fact_menu' => 0, 'menu_id' => $menu_id])->
                    orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
                    all();

                    $menus_days_id = MenusDays::find()->where(['menu_id' => $menu_id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
                    $days_ids = [];
                    foreach ($menus_days_id as $day_id)
                    {
                        $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
                    }

                    $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
                    $count_my_days = MenusDays::find()->where(['menu_id' => $menu_id])->count();
                    $my_menus = Menus::findOne($menu_id);
                    $normativ_vitamin_day_vitamin_a = \common\models\NormativVitaminDay::find()->where(['name' => 'vitamin_a', 'age_info_id' => $my_menus->age_info_id])->one()->value;
                    $normativ_vitamin_day_k = \common\models\NormativVitaminDay::find()->where(['name' => 'k', 'age_info_id' => $my_menus->age_info_id])->one()->value;

                    $menu_cycle_count = $my_menus->cycle;

                    if ($cycle == 0)
                    {
                        $count_my_days = $count_my_days * $menu_cycle_count;
                    }

                    $cycle_ids = [];
                    if ($cycle != 0)
                    {
                        $cycle_ids[$cycle] = $cycle;
                    }
                    else
                    {
                        for ($i = 1; $i <= $menu_cycle_count; $i++)
                        {
                            $cycle_ids[$i] = $i;//массив из подходящи циклов
                        }
                    }
                }
                elseif ($display == 1 && !empty($data))
                {


                    if ($menu->date_end < strtotime($data) || $menu->date_start > strtotime($data))
                    {
                        Yii::$app->session->setFlash('error', "Указанная дата не входит в диапозон даты начала или даты окончания меню");
                        return $this->redirect(['menus-dishes/report-document']);
                    }

                    $menus_days_id = MenusDays::find()->where(['menu_id' => $menu->id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
                    $days_ids = [];
                    foreach ($menus_days_id as $day_id)
                    {
                        if ($day_id->days_id != 7)
                        {
                            $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
                        }
                        /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ФУНКЦИИ DATE() ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                        else
                        {
                            $days_ids[] = 0;
                        }

                    }
                    if (!in_array(date("w", strtotime($data)), $days_ids))
                    {
                        Yii::$app->session->setFlash('error', "Этот день недели отсутсвует в меню");
                        return $this->redirect(['menus-dishes/report-document']);
                    }
                    $count_my_days = 1;
                    $days = Days::find()->where(['id' => date("w", strtotime($data))])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
                    $start_date = date('d.m.Y', $menu->date_start);//Дата старта меню
                    $day_of_week = date("w", strtotime($data));//День недели выбранной даты
                    $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
                    /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                    /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
                    if ($day_of_week_start_date == 0)
                    {
                        $day_of_week_start_date = 7;
                    }
                    if ($day_of_week == 0)
                    {
                        $day_of_week = 7;
                    }
                    /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
                    $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

                    $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
                    $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
                    $count_week = ceil((((strtotime($data) - $menu->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

                    $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
                    /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
                    if ($day_of_week == 1)
                    {
                        $cycle = $count_week - 1;
                    }
                    /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
                    if ($count_week == 0)
                    {
                        $cycle = 1;
                    }

                    /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
                    while ($cycle > $menu->cycle)
                    {
                        $cycle = $cycle - $menu->cycle;
                    }
                    if ($cycle == 0)
                    {
                        $cycle = $menu->cycle;
                    }
                    $cycle_ids[$cycle] = $cycle;
                    /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/

                    $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $menu->id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
                    $ids = [];
                    foreach ($menus_nutrition_id as $m_id)
                    {
                        $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
                    }

                    $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
                    //$menus_dishes = MenusDishes::find()->where(['menu_id' => $post['menu_id'], 'cycle' => $cycle, 'days_id' => $day_of_week])->orderby(['nutrition_id' => SORT_ASC])->all();

                    $menus_dishes = MenusDishes::find()->
                    select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                    leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                    leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                    where(['date_fact_menu' => strtotime($data), 'menu_id' => $menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week])->
                    orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
                    all();


                    if (empty($menus_dishes))
                    {
                        $menus_dishes = MenusDishes::find()->
                        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                        where(['date_fact_menu' => 0, 'menu_id' => $menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week])->
                        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
                        all();
                    }


                }

                $html .= '
                <table
                style="border-collapse: collapse; margin-bottom: 10px;margin-top: 25px; margin: 0 auto;">';
                $html .= '
            
                <tr class="text-center"><td colspan="7"  align="center" style="width: 100%"><p  style="font-size: 18px;"><b>' . $menu->name . '</b></p></td></tr>
                <tr class="text-center"><td colspan="7"  align="center" style="width: 100%"><p  style="font-size: 15px;">Возрастная категория:' . AgeInfo::findOne($menu->age_info_id)->name . '</p></td></tr>
                <tr class="text-center"><td colspan="7"  align="center" style="width: 100%"><p  style="font-size: 15px;">Характеристика питающихся:' . FeedersCharacters::findOne($menu->feeders_characters_id)->name . '</p></td></tr>';
                $html .= '</table>';
                $count_cycle = 0;
                $count = 0;

                $data = [];
                $echo_table_space = 3;

                $html .= '
                        <table border="1" style="border-collapse: collapse; margin-top: 10px;">';
                $html .= '
                    <tr>
                        <td align="center" rowspan="2">№ рецептуры</td>
                        <td align="center" rowspan="2">Название блюда</td>
                        <td>Масса</td>
                        <td>Белки</td>
                        <td>Жиры</td>
                        <td>Углеводы</td>
                        <td>Калорийность</td>
                    </tr>
                    <tr>
                        <td align="center">г.</td>
                        <td align="center">г.</td>
                        <td align="center">г.</td>
                        <td align="center">г.</td>
                        <td align="center">ккал</td>
                    </tr>';

                foreach ($cycle_ids as $cycle_id)
                {
                    $count++;
                    foreach ($days as $day)
                    {
                        $echo_cycle_day = 0;
                        $super_total_yield = 0;
                        $super_total_protein = 0;
                        $super_total_fat = 0;
                        $super_total_carbohydrates_total = 0;
                        $super_total_energy_kkal = 0;
                        $super_total_vitamin_a = 0;
                        $super_total_vitamin_c = 0;
                        $super_total_vitamin_b1 = 0;
                        $super_total_vitamin_b2 = 0;
                        $super_total_vitamin_d = 0;
                        $super_total_vitamin_pp = 0;
                        $super_total_na = 0;
                        $super_total_k = 0;
                        $super_total_ca = 0;
                        $super_total_f = 0;
                        $super_total_se = 0;
                        $super_total_i = 0;
                        $super_total_fe = 0;
                        $super_total_p = 0;
                        $super_total_mg = 0;
                        foreach ($nutritions as $nutrition)
                        {
                            $energy_kkal = 0;
                            $protein = 0;
                            $fat = 0;
                            $carbohydrates_total = 0;
                            $yield = 0;


                            if ($echo_table_space > 0)
                            {
                                $echo_table_space--;
                                $a = 'always';
                            }
                            else
                            {
                                $echo_table_space = 2;
                                $a = 'avoid';
                            }
                            $vitamins = [];
                            unset($vitamins);
                            $vitamin_a = 0;
                            $vitamin_c = 0;
                            $vitamin_b1 = 0;
                            $vitamin_b2 = 0;
                            $vitamin_d = 0;
                            $vitamin_pp = 0;
                            $na = 0;
                            $k = 0;
                            $ca = 0;
                            $f = 0;
                            $p = 0;
                            $se = 0;
                            $i = 0;
                            $mg = 0;
                            $fe = 0;
                            //style="border-collapse: collapse; page-break-after:avoid"


                            if (MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $menu->id, 'cycle' => $cycle_id, 'days_id' => $day->id, 'nutrition_id' => $nutrition->id])->count() > 0)
                            {

                                if ($echo_cycle_day == 0)
                                {
                                    $html .= '
                            <tr>
                                <td></td>
                                <td style="color: #3d30cf"><b>Неделя ' . $cycle_id . ' ' . $day->name . ' ' . $data_fac . '</b></td>
                                <td></td><td></td><td></td><td></td><td></td>
                            </tr>';

                                    $echo_cycle_day++;
                                }

                                $html .= '<tr>
                                <td></td>
                                <td><b>' . $nutrition->name . '</b></td>
                                <td></td><td></td><td></td><td></td><td></td>
                            </tr>';
                                foreach ($menus_dishes as $key => $m_dish)
                                {

                                    if ($nutrition->id == $m_dish->nutrition_id && $m_dish->cycle == $cycle_id && $day->id == $m_dish->days_id)
                                    {

                                        //Расчет итогов за прием пищи
                                        $protein_dish = round($m_dish->get_bju_dish($m_dish->id, 'protein'), 1);
                                        $protein = $protein_dish + $protein;
                                        $fat_dish = round($m_dish->get_bju_dish($m_dish->id, 'fat'), 1);
                                        $fat = $fat_dish + $fat;
                                        $carbohydrates_total_dish = round($m_dish->get_bju_dish($m_dish->id, 'carbohydrates_total'), 1);
                                        $carbohydrates_total = $carbohydrates_total_dish + $carbohydrates_total;
                                        $kkal = round($m_dish->get_kkal_dish($m_dish->id), 1);
                                        $energy_kkal = $energy_kkal + $kkal;
                                        $yield = $yield + $m_dish->yield;


                                        //РАСЧЕТ ВИТАМИНА
                                        $vitamins['vitamin_a'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_a'), 2);
                                        $vitamin_a = $vitamin_a + $vitamins['vitamin_a'];
                                        $vitamins['vitamin_c'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_c'), 2);
                                        $vitamin_c = $vitamin_c + $vitamins['vitamin_c'];
                                        $vitamins['vitamin_b1'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b1'), 2);
                                        $vitamin_b1 = $vitamin_b1 + $vitamins['vitamin_b1'];
                                        $vitamins['vitamin_b2'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_b2'), 2);
                                        $vitamin_b2 = $vitamin_b2 + $vitamins['vitamin_b2'];
                                        $vitamins['vitamin_d'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_d'), 2);
                                        $vitamin_d = $vitamin_d + $vitamins['vitamin_d'];
                                        $vitamins['vitamin_pp'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'vitamin_pp'), 2);
                                        $vitamin_pp = $vitamin_pp + $vitamins['vitamin_pp'];
                                        $vitamins['na'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'na'), 2);
                                        $na = $na + $vitamins['na'];
                                        $vitamins['k'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'k'), 2);
                                        $k = $k + $vitamins['k'];
                                        $vitamins['ca'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'ca'), 2);
                                        $ca = $ca + $vitamins['ca'];
                                        $vitamins['f'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'f'), 2);
                                        $f = $f + $vitamins['f'];
                                        $vitamins['mg'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'mg'), 2);
                                        $mg = $mg + $vitamins['mg'];
                                        $vitamins['p'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'p'), 2);
                                        $p = $p + $vitamins['p'];
                                        $vitamins['fe'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'fe'), 2);
                                        $fe = $fe + $vitamins['fe'];
                                        $vitamins['i'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'i'), 2);
                                        $i = $i + $vitamins['i'];
                                        $vitamins['se'] = round($m_dish->get_vitamin($m_dish->dishes_id, $m_dish->yield, 'se'), 2);
                                        $se = $se + $vitamins['se'];
                                        //КОНЕЦ РАСЧЕТА


                                        //расчет бжу%
                                        if ($protein != 0)
                                        {
                                            $bju_fat = round(($fat / $protein), 2);
                                        }
                                        else
                                        {
                                            $bju_fat = 0;
                                        }
                                        if ($protein != 0)
                                        {
                                            $bju_carbohydrates_total = round(($carbohydrates_total / $protein), 2);
                                        }
                                        else
                                        {
                                            $bju_carbohydrates_total = 0;
                                        }

                                        //Расчет нормативов
                                        $recom_protein = $model->get_recommended_normativ($menu_id, $nutrition->id, 'protein_middle_weight');
                                        $recom_fat_ = $model->get_recommended_normativ($menu_id, $nutrition->id, 'fat_middle_weight');
                                        $recom_carbohydrate = $model->get_recommended_normativ($menu_id, $nutrition->id, 'carbohydrates_middle_weight');
                                        $recom_kkal = $model->get_recommended_normativ($menu_id, $nutrition->id, 'middle_kkal');

                                        $html .= ' <tr class="itog_day table-danger">
                            <td >' . $m_dish->get_techmup($m_dish->dishes_id) . '</td>
                            <td style="width:260px">' . $m_dish->get_dishes($m_dish->dishes_id) . '</td>
                            <td style="width:5px" align="center">' . $m_dish->yield . '</td>
                            <td align="center">' . $protein_dish . '</td>
                            <td align="center">' . $fat_dish . '</td>
                            <td align="center">' . $carbohydrates_total_dish . '</td>
                            <td align="center">' . $kkal . '</td>
                            </tr>';

                                    }

                                }

                                //расчет за день и среднее
                                $data[$nutrition->id]['yield'] = $data[$nutrition->id]['yield'] + $yield;
                                $super_total_yield = $super_total_yield + $yield;
                                $data[$nutrition->id]['protein'] = $data[$nutrition->id]['protein'] + $protein;
                                $super_total_protein = $super_total_protein + $protein;
                                $data[$nutrition->id]['fat'] = $data[$nutrition->id]['fat'] + $fat;
                                $super_total_fat = $super_total_fat + $fat;
                                $data[$nutrition->id]['carbohydrates_total'] = $data[$nutrition->id]['carbohydrates_total'] + $carbohydrates_total;
                                $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                                $data[$nutrition->id]['energy_kkal'] = $data[$nutrition->id]['energy_kkal'] + $energy_kkal;
                                $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;


                                //РАСЧЕТ ВИТАМИНОВ
                                $data[$nutrition->id]['vitamin_a'] = $data[$nutrition->id]['vitamin_a'] + $vitamin_a;
                                $data[$nutrition->id]['vitamin_c'] = $data[$nutrition->id]['vitamin_c'] + $vitamin_c;
                                $data[$nutrition->id]['vitamin_b1'] = $data[$nutrition->id]['vitamin_b1'] + $vitamin_b1;
                                $data[$nutrition->id]['vitamin_b2'] = $data[$nutrition->id]['vitamin_b2'] + $vitamin_b2;
                                $data[$nutrition->id]['vitamin_d'] = $data[$nutrition->id]['vitamin_d'] + $vitamin_d;
                                $data[$nutrition->id]['vitamin_pp'] = $data[$nutrition->id]['vitamin_pp'] + $vitamin_pp;
                                $data[$nutrition->id]['vitamin_na'] = $data[$nutrition->id]['vitamin_na'] + $na;
                                $data[$nutrition->id]['vitamin_k'] = $data[$nutrition->id]['vitamin_k'] + $k;
                                $data[$nutrition->id]['vitamin_ca'] = $data[$nutrition->id]['vitamin_ca'] + $ca;
                                $data[$nutrition->id]['vitamin_f'] = $data[$nutrition->id]['vitamin_f'] + $f;
                                $data[$nutrition->id]['vitamin_mg'] = $data[$nutrition->id]['vitamin_mg'] + $mg;
                                $data[$nutrition->id]['vitamin_p'] = $data[$nutrition->id]['vitamin_p'] + $p;
                                $data[$nutrition->id]['vitamin_fe'] = $data[$nutrition->id]['vitamin_fe'] + $fe;
                                $data[$nutrition->id]['vitamin_i'] = $data[$nutrition->id]['vitamin_i'] + $i;
                                $data[$nutrition->id]['vitamin_se'] = $data[$nutrition->id]['vitamin_se'] + $se;


                                //raschet v itog za den

                                $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                                $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                                $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                                $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                                $super_total_vitamin_d = $super_total_vitamin_d + $vitamin_d;
                                $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                                $super_total_na = $super_total_na + $na;
                                $super_total_k = $super_total_k + $k;
                                $super_total_ca = $super_total_ca + $ca;
                                $super_total_f = $super_total_f + $f;
                                $super_total_mg = $super_total_mg + $mg;
                                $super_total_p = $super_total_p + $p;
                                $super_total_fe = $super_total_fe + $fe;
                                $super_total_i = $super_total_i + $i;
                                $super_total_se = $super_total_se + $se;
                                //КОНЕЦ РАСЧЕТА

                                $html .= ' <tr class="itog_day table-danger">
                            <td></td>
                            <td><b> Итого за ' . $nutrition->name . '</b></td>
                            <td align="center"><b>' . $yield . '</b></td>
                            <td align="center"><b>' . $protein . '</b></td>
                            <td align="center"><b>' . $fat . '</b></td>
                            <td align="center"><b>' . $carbohydrates_total . '</b></td>
                            <td align="center"><b>' . $energy_kkal . '</b></td>
                            </tr>';
                                if ($ocenka_pish_cen == 1)
                                {


                                    $html .= '<tr class="table-success">
                            <td></td>
                            <td colspan="2" style="color: #2bad41"><b>Рекомендуемая величина</b></td>
                            <td align="center" style="color: #2bad41"><b>' . $recom_protein . '</b></td>
                            <td align="center" style="color: #2bad41"><b>' . $recom_fat_ . '</b></td>
                            <td align="center" style="color: #2bad41"><b>' . $recom_carbohydrate . '</b></td>
                            <td align="center" style="color: #2bad41"><b>' . $recom_kkal . '</b></td>
                            </tr>';

                                    $html .= '<tr class="table-success">
                            <td></td>
                            <td colspan="2" style="color: #fcb603"><b>Процентное соотношение БЖУ</b></td>
                            <td align="center" style="color: #fcb603"><b>1</b></td>
                            <td align="center" style="color: #fcb603"><b>' . $bju_fat . '</b></td>
                            <td align="center" style="color: #fcb603"><b>' . $bju_carbohydrates_total . '</b></td>
                            <td align="center"></td><b>
                            </tr>';


                                }
                                if ($nutrition->id == $max_nutrition_id)
                                {
                                    $html .= '<tr class="table-success">
                            <td></td>
                            <td style="color: #cf3042"><b>Итого за день</b></td>
                            <td align="center" style="color: #cf3042"><b>' . $super_total_yield . '</b></td>
                            <td align="center" style="color: #cf3042"><b>' . $super_total_protein . '</b></td>
                            <td align="center" style="color: #cf3042"><b>' . $super_total_fat . '</b></td>
                            <td align="center" style="color: #cf3042"><b>' . $super_total_carbohydrates_total . '</b></td>
                            <td align="center" style="color: #cf3042"><b>' . $super_total_energy_kkal . '</b></td>
                            </tr>';
                                    //print_r($html);exit;
                                }
                                //$html .= '</table>';


                            }
                        }
                    }
                }
                $html .= '</table>';

                if ($ocenka_pish_cen == 1)
                {
                    $html .= '
                            <table border="1" style="border-collapse: collapse; margin-top: 35px;">';
                    $html .= '<tr>
                                    <th colspan="2">Итого за период</th>
                                    <th >Выход</th>
                                    <th >Белки</th>
                                    <th >Жиры</th>
                                    <th >Углеводы</th>
                                    <th >Эн. ценность</th>
                                </tr>';
                    foreach ($nutritions as $nutrition)
                    {
                        $data_vit_a = round($data[$nutrition->id]['vitamin_a'] / $count_my_days, 2);
                        $procent = \common\models\NutritionProcent::find()->where(['nutrition_id' => $nutrition->id])->one()->procent / 100;
                        if ($data_vit_a <= $normativ_vitamin_day_vitamin_a * 1.5 * $procent)
                        {
                            $data_itog['vitamin_a'] = $data_itog['vitamin_a'] + $data_vit_a;
                        }
                        else
                        {
                            $data_vit_a = $normativ_vitamin_day_vitamin_a * 1.5 * $procent;
                            $data_itog['vitamin_a'] = $data_itog['vitamin_a'] + $data_vit_a;
                        }
                        $data_vit_k = round($data[$nutrition->id]['vitamin_k'] / $count_my_days, 2);
                        $procent = \common\models\NutritionProcent::find()->where(['nutrition_id' => $nutrition->id])->one()->procent / 100;
                        if ($data_vit_k <= $normativ_vitamin_day_k * 1.5 * $procent)
                        {
                            $data_itog['vitamin_k'] = $data_itog['vitamin_k'] + $data_vit_k;
                        }
                        else
                        {
                            $data_vit_k = $normativ_vitamin_day_k * 1.5 * $procent;
                            $data_itog['vitamin_k'] = $data_itog['vitamin_k'] + $data_vit_k;
                        }


                        //$data_itog['vitamin_a'] = $data_itog['vitamin_a'] + $data_vit_a;
                        $data_itog['vitamin_c'] = $data_itog['vitamin_c'] + round($data[$nutrition->id]['vitamin_c'] / $count_my_days, 2);
                        $data_itog['vitamin_b1'] = $data_itog['vitamin_b1'] + round($data[$nutrition->id]['vitamin_b1'] / $count_my_days, 2);
                        $data_itog['vitamin_b2'] = $data_itog['vitamin_b2'] + round($data[$nutrition->id]['vitamin_b2'] / $count_my_days, 2);
                        $data_itog['vitamin_d'] = $data_itog['vitamin_d'] + round($data[$nutrition->id]['vitamin_d'] / $count_my_days, 2);
                        $data_itog['vitamin_pp'] = $data_itog['vitamin_pp'] + round($data[$nutrition->id]['vitamin_pp'] / $count_my_days, 2);
                        $data_itog['vitamin_na'] = $data_itog['vitamin_na'] + round($data[$nutrition->id]['vitamin_na'] / $count_my_days, 2);
                        //$data_itog['vitamin_k'] = $data_itog['vitamin_k'] + $data_vit_k;
                        $data_itog['vitamin_ca'] = $data_itog['vitamin_ca'] + round($data[$nutrition->id]['vitamin_ca'] / $count_my_days, 2);
                        $data_itog['vitamin_f'] = $data_itog['vitamin_f'] + round($data[$nutrition->id]['vitamin_f'] / $count_my_days, 2);
                        $data_itog['vitamin_mg'] = $data_itog['vitamin_mg'] + round($data[$nutrition->id]['vitamin_mg'] / $count_my_days, 2);
                        $data_itog['vitamin_p'] = $data_itog['vitamin_p'] + round($data[$nutrition->id]['vitamin_p'] / $count_my_days, 2);
                        $data_itog['vitamin_fe'] = $data_itog['vitamin_fe'] + round($data[$nutrition->id]['vitamin_fe'] / $count_my_days, 2);
                        $data_itog['vitamin_i'] = $data_itog['vitamin_i'] + round($data[$nutrition->id]['vitamin_i'] / $count_my_days, 2);
                        $data_itog['vitamin_se'] = $data_itog['vitamin_se'] + round($data[$nutrition->id]['vitamin_se'] / $count_my_days, 2);
                        $html .= '<tr><td  colspan="2">Средние показатели за ' . $nutrition->name . '</td>
                    <td align="center">' . round($data[$nutrition->id]["yield"] / $count_my_days, 2) . '</td>
                    <td align="center">' . round($data[$nutrition->id]["protein"] / $count_my_days, 2) . '</td>
                    <td align="center">' . round($data[$nutrition->id]["fat"] / $count_my_days, 2) . '</td>
                    <td align="center">' . round($data[$nutrition->id]["carbohydrates_total"] / $count_my_days, 2) . '</td>
                    <td align="center">' . round($data[$nutrition->id]["energy_kkal"] / $count_my_days, 2) . '</td></tr>';

                    }
                    $html .= '</table>';


                    if ($analiz == 1)
                    {
                        $html .= '
                            <table border="1" style="border-collapse: collapse; margin-top: 25px">';
                        $html .= '<tr>
                             <th>Показатели</th>
                             <th>Среднее значение за период</th>
                         </tr>
                         <tr>
                            <td>Витамин С(мг)</td>
                            <td align="center">' . $data_itog['vitamin_c'] . '</td>
                         </tr>
                         <tr>
                            <td>Витамин В1(мг)</td>
                            <td align="center">' . $data_itog['vitamin_b1'] . '</td>
                         </tr>
                         <tr>
                            <td>Витамин В2(мг)</td>
                            <td align="center">' . $data_itog['vitamin_b2'] . '</td>
                         </tr>
                         <tr>
                            <td>Витамин А(мкг рет.экв)</td>
                            <td align="center">' . $data_itog['vitamin_a'] . '</td>
                         </tr>
                         <tr>
                            <td>Кальций(мг)</td>
                            <td align="center">' . $data_itog['vitamin_ca'] . '</td>
                         </tr>
                         <tr>
                            <td>Фосфор(мг)</td>
                            <td align="center">' . $data_itog['vitamin_f'] . '</td>
                         </tr>
                         <tr>
                            <td>Магний(мг)</td>
                            <td align="center">' . $data_itog['vitamin_mg'] . '</td>
                         </tr>
                         <tr>
                            <td>Железо(мг)</td>
                            <td align="center">' . $data_itog['vitamin_fe'] . '</td>
                         </tr>
                         <tr>
                            <td>Калий(мг)</td>
                            <td align="center">' . $data_itog['vitamin_k'] . '</td>
                         </tr>
                         <tr>
                            <td>Йод(мкг)</td>
                            <td align="center">' . $data_itog['vitamin_i'] . '</td>
                         </tr>
                         <tr>
                            <td>Селен(мкг)</td>
                            <td align="center">' . $data_itog['vitamin_se'] . '</td>
                         </tr></table>';

                    }
                }
            }
            $html .= '<div style="margin-top: 25px;"></div>';
            if ($identificator == 'techmup')
            {
                $menu_cycle_count = $menu->cycle;

                if ($cycle != 0)
                {
                    $cycle_ids[$cycle] = $cycle;
                }
                else
                {
                    for ($i = 1; $i <= $menu_cycle_count; $i++)
                    {
                        $cycle_ids[$i] = $i;//массив из подходящи циклов
                    }
                }

                $indicator = 1;
                $html = '';

                $html .= '
                <table 
                style="border-collapse: collapse; margin-bottom: 25px;margin-top: 25px;">';
                $html .= '
            
                <tr class="text-center"><td colspan="7" style="width: 100%"><p  style="font-size: 18px;"><b>Название меню: </b>' . $menu->name . '</p></td></tr>
                <tr class="text-center"><td colspan="7" style="width: 100%"><p  style="font-size: 18px;"><b>Возрастная категория: </b>' . AgeInfo::findOne($menu->age_info_id)->name . '</p></td></tr>
                <tr class="text-center"><td colspan="7" style="width: 100%"><p  style="font-size: 18px;"><b>Характеристика питающихся: </b>' . FeedersCharacters::findOne($menu->feeders_characters_id)->name . '</p></td></tr>';
                $html .= '</table>';

                //$all_dishes_from_menus = MenusDishes::find()->where(['menu_id' => $menu_id])->all();
                if ($display == 0)
                {

                    $all_dishes_from_menus = MenusDishes::find()->
                    select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                    leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                    leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                    where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle_ids])->
                    orderby(['sort' => SORT_ASC])->
                    all();
                    //print_r($menu_cycle_count);exit;
                }
                elseif ($display == 1 && !empty($data))
                {
                    /*$html .= '
                <table 
                style="border-collapse: collapse; margin-bottom: 25px;margin-top: 25px;">';
                    $html .= '
            
                <tr class="text-center"><td colspan="7" style="width: 100%"><p  style="font-size: 18px;"><b>Дата: </b>' . $data . '</p></td></tr>';
                $html .= '</table>';*/


                    $all_dishes_from_menus = MenusDishes::find()->
                    select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                    leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                    leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                    where(['date_fact_menu' => strtotime($data), 'menu_id' => $menu_id])->
                    orderby(['sort' => SORT_ASC])->
                    all();
                    if (empty($all_dishes_from_menus))
                    {


                        if ($menu->date_end < strtotime($data) || $menu->date_start > strtotime($data))
                        {
                            Yii::$app->session->setFlash('error', "Указанная дата не входит в диапозон даты начала или даты окончания меню");
                            return $this->redirect(['menus-dishes/report-document']);
                        }

                        $menus_days_id = MenusDays::find()->where(['menu_id' => $menu->id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
                        $days_ids = [];
                        foreach ($menus_days_id as $day_id)
                        {
                            if ($day_id->days_id != 7)
                            {
                                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
                            }
                            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ФУНКЦИИ DATE() ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                            else
                            {
                                $days_ids[] = 0;
                            }

                        }
                        if (!in_array(date("w", strtotime($data)), $days_ids))
                        {
                            Yii::$app->session->setFlash('error', "Этот день недели отсутсвует в меню");
                            return $this->redirect(['menus-dishes/report-document']);
                        }
                        $count_my_days = 1;
                        $days = Days::find()->where(['id' => date("w", strtotime($data))])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
                        $start_date = date('d.m.Y', $menu->date_start);//Дата старта меню
                        $day_of_week = date("w", strtotime($data));//День недели выбранной даты
                        $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
                        /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
                        /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
                        if ($day_of_week_start_date == 0)
                        {
                            $day_of_week_start_date = 7;
                        }
                        if ($day_of_week == 0)
                        {
                            $day_of_week = 7;
                        }
                        /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
                        $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

                        $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
                        $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
                        $count_week = ceil((((strtotime($data) - $menu->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

                        $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
                        /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
                        if ($day_of_week == 1)
                        {
                            $cycle = $count_week - 1;
                        }
                        /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
                        if ($count_week == 0)
                        {
                            $cycle = 1;
                        }

                        /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
                        while ($cycle > $menu->cycle)
                        {
                            $cycle = $cycle - $menu->cycle;
                        }
                        if ($cycle == 0)
                        {
                            $cycle = $menu->cycle;
                        }
                        $cycle_ids[$cycle] = $cycle;


                        $all_dishes_from_menus = MenusDishes::find()->
                        select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
                        leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
                        leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
                        where(['date_fact_menu' => 0, 'menu_id' => $menu_id, 'cycle' => $cycle_ids, 'days_id' => $day_of_week])->
                        orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
                        all();

                    }
                }
                $ids = [];
                $recipes_ids = [];
                foreach ($all_dishes_from_menus as $a_menu)
                {
                    $d = Dishes::findOne($a_menu->dishes_id);
                    if (!in_array($a_menu->dishes_id, $ids) && $d->dishes_category_id != 12)
                    {
                        $ids[] = $a_menu->dishes_id;
                    }
                    if (!in_array($d->recipes_collection_id, $recipes_ids) && $d->dishes_category_id != 12)
                    {
                        $recipes_ids[] = $d->recipes_collection_id;
                    }

                }
                $html .= '<p class="mb-0" style="color:#0ea1a8; font-size: 22px;">Сборники рецептур, которые были использованы в меню:</p>';
                foreach ($recipes_ids as $r_id)
                {
                    $html .= '
            <p class="mb-0">' . RecipesCollection::findOne($r_id)->name . '</p>';
                }
                //print_r(count($ids));exit;

                $html .= '<p class="mb-0" style="color:#0ea1a8; font-size: 22px;">Список блюд(всего ' . count($ids) . '):</p>';
                foreach ($ids as $id)
                {

                    if ($indicator != 1)
                    {
                        $this->layout = false;
                        $menus_dishes = MenusDishes::findOne($id);
                        $dishes = Dishes::findOne($menus_dishes->dishes_id);
                        $dishes_products = DishesProducts::find()->where(['dishes_id' => $menus_dishes->dishes_id])->all();
                        //$indicator = $menus_dishes->yield / 100;
                    }
                    else
                    {
                        $this->layout = false;
                        $indicator = 1;
                        $dishes = Dishes::findOne($id);
                        $dishes_products = DishesProducts::find()->
                        select(['dishes_products.id as id', 'dishes_products.dishes_id as dishes_id', 'dishes_products.products_id as products_id', 'dishes_products.net_weight as net_weight', 'dishes_products.gross_weight as gross_weight', 'products_category.sort as sort'])->
                        leftJoin('products', 'dishes_products.products_id = products.id')->
                        leftJoin('products_category', 'products.products_category_id = products_category.id')->
                        where(['dishes_id' => $id])->
                        orderby(['sort' => SORT_ASC])->
                        all();
                    }

                    $super_total_yield = 0;
                    $super_total_protein = 0;
                    $super_total_fat = 0;
                    $super_total_carbohydrates_total = 0;
                    $super_total_energy_kkal = 0;
                    $super_total_vitamin_a = 0;
                    $super_total_vitamin_c = 0;
                    $super_total_vitamin_b1 = 0;
                    $super_total_vitamin_b2 = 0;
                    $super_total_vitamin_d = 0;
                    $super_total_vitamin_pp = 0;
                    $super_total_na = 0;
                    $super_total_k = 0;
                    $super_total_ca = 0;
                    $super_total_f = 0;
                    $super_total_se = 0;
                    $super_total_i = 0;
                    $super_total_fe = 0;
                    $super_total_p = 0;
                    $super_total_mg = 0;
                    $number_row = 1;
                    $html .= '
            <p class="mb-0 text-center" style="color:red; font-size: 22px;">' . $dishes->name . '</b></p>
            <p class="mb-0"><b>Технологическая карта кулинарного изделия (блюда):</b> ' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование изделия:</b>' . $dishes->name . '</p>
            <p class="mb-0"><b>Номер рецептуры:</b>' . $dishes->techmup_number . '</p>
            <p class="mb-0"><b>Наименование сборника рецептур, год выпуска, автор:</b>' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->name . ', ' . $dishes->get_recipes_collection($dishes->recipes_collection_id)->year . ' </p>
            <b>Пищевые вещества:</b><br>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Наименование сырья</th>
                    <th class="text-center">Брутто, г.</th>
                    <th class="text-center">Нетто, г.</th>
                    <th class="text-center">Белки, г.</th>
                    <th class="text-center">Жиры, г.</th>
                    <th class="text-center">Углеводы, г.</th>
                    <th class="text-center">Энергетическая ценность, ккал.</th>
                </tr>
        ';

                    foreach ($dishes_products as $d_product)
                    {

                        $html .= '
            <tr>
                <td class="text-center">' . $number_row . '</td>
                <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->gross_weight * $indicator) . '</td>
                <td class="text-center">' . sprintf("%.1f", $d_product->net_weight * $indicator) . '</td>
                <td class="text-center">' . $protein = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'protein') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $fat = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'fat') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $carbohydrates_total = sprintf("%.2f", $d_product->get_products_bju($d_product->products_id, $d_product->dishes_id, 'carbohydrates_total') * (($d_product->net_weight) / 100) * $indicator) . '</td>
                <td class="text-center">' . $energy_kkal = sprintf("%.1f", $d_product->get_kkal($d_product->products_id, $d_product->dishes_id) * (($d_product->net_weight) / 100) * $indicator) . '</td>
            </tr>';
                        $super_total_protein = $super_total_protein + $protein;
                        $super_total_fat = $super_total_fat + $fat;
                        $super_total_carbohydrates_total = $super_total_carbohydrates_total + $carbohydrates_total;
                        $super_total_energy_kkal = $super_total_energy_kkal + $energy_kkal;
                        $number_row++;
                    }
                    $html .= '
        <tr>
            <td colspan="3"><b>Выход:</b></td>
            <td class="text-center"><b>' . round(($dishes->yield * $indicator), 1) . '</b></td>
            <td class="text-center"><b>' . $super_total_protein . '</b></td>
            <td class="text-center"><b>' . $super_total_fat . '</b></td>
            <td class="text-center"><b>' . $super_total_carbohydrates_total . '</b></td>
            <td class="text-center"><b>' . $super_total_energy_kkal . '</b></td>
        </tr>
        ';
                    $html .= '</table>';
                    $html .= ' <br><b>Витамины и минеральные вещества</b>
            <table border="1"
            style="border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
            border: 1px solid #000000; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
            ">
                <tr class="">
                    <th class="text-center">№</th>
                    <th class="text-center">Продукт</th>
                    <th class="text-center">B1, мг</th>
                    <th class="text-center">B2, мг</th>
                    <th class="text-center">А, мкг. рет. экв.</th>
                    <th class="text-center">РР, мг.</th>
                    <th class="text-center">C, мг.</th>
                    <th class="text-center">Na, мг.</th>
                    <th class="text-center">K, мг.</th>
                    <th class="text-center">Ca, мг.</th>
                    <th class="text-center">Mg, мг.</th>
                    <th class="text-center">P, мг.</th>
                    <th class="text-center">FE, мг.</th>
                    <th class="text-center">I, мкг.</th>
                    <th class="text-center">Se, мкг.</th>
            </tr>
        ';

                    $number_row = 1;
                    foreach ($dishes_products as $d_product)
                    {
                        $html .= '
           <tr>
               <td class="text-center">' . $number_row . '</td>
               <td class="text-center">' . $d_product->get_products($d_product->products_id)->name . '</td>
               <td class="text-center">' . $vitamin_b1 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b1') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_b2 = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_b2') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_a = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_a') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_pp = sprintf("%.2f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_pp') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $vitamin_c = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'vitamin_c') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $na = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'na') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $k = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'k') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $ca = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'ca') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $mg = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'mg') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $p = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'p') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $fe = round(($d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'fe') * (($d_product->net_weight) / 100) * $indicator), 0) . '</td>
               <td class="text-center">' . $i = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'i') * (($d_product->net_weight) / 100) * $indicator) . '</td>
               <td class="text-center">' . $se = sprintf("%.1f", $d_product->get_vitamin($d_product->products_id, $d_product->dishes_id, 'se') * (($d_product->net_weight) / 100) * $indicator) . '</td>
           </tr>';

                        $number_row++;
                        $super_total_vitamin_b1 = $super_total_vitamin_b1 + $vitamin_b1;
                        $super_total_vitamin_b2 = $super_total_vitamin_b2 + $vitamin_b2;
                        $super_total_vitamin_a = $super_total_vitamin_a + $vitamin_a;
                        $super_total_vitamin_pp = $super_total_vitamin_pp + $vitamin_pp;
                        $super_total_vitamin_c = $super_total_vitamin_c + $vitamin_c;
                        $super_total_na = $super_total_na + $na;
                        $super_total_k = $super_total_k + $k;
                        $super_total_ca = $super_total_ca + $ca;
                        $super_total_mg = $super_total_mg + $mg;
                        $super_total_p = $super_total_p + $p;
                        $super_total_fe = $super_total_fe + $fe;
                        $super_total_i = $super_total_i + $i;
                        $super_total_se = $super_total_se + $se;
                    }
                    $html .= ' 
            <tr>
                <td colspan="2"><b>Итого</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b1 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_b2 . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_a . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_pp . '</b></td>
                <td class="text-center"><b>' . $super_total_vitamin_c . '</b></td>
                <td class="text-center"><b>' . $super_total_na . '</b></td>
                <td class="text-center"><b>' . $super_total_k . '</b></td>
                <td class="text-center"><b>' . $super_total_ca . '</b></td>
                <td class="text-center"><b>' . $super_total_mg . '</b></td>
                <td class="text-center"><b>' . $super_total_p . '</b></td>
                <td class="text-center"><b>' . $super_total_fe . '</b></td>
                <td class="text-center"><b>' . $super_total_i . '</b></td>
                <td class="text-center"><b>' . $super_total_se . '</b></td>
            </tr>
        ';
                    $html .= '</table>';
                    $html .= '
            <p class="mb-0"><b>Способ обработки:</b>' . $dishes->get_culinary_processing($dishes->culinary_processing_id) . '</p>
            <p class="mb-0"><b>Технология приготовления:</b> ' . $dishes->description . '</p>
            <b>Характеристика блюда на выходе:</b>
            <p class="mb-0">' . $dishes->dishes_characters . '</p>
        ';
                }


            }


            //print_r($max_nutrition_id);exit;

            $mpdf = new Mpdf (['margin_left' => '5', 'margin_right' => '5', 'margin_top' => '10', 'margin_bottom' => '5']);;
            $mpdf->WriteHTML($html);
            $mpdf->defaultfooterline = 1;
            $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"  {PAGENO}</div>'); //номер страницы {PAGENO}
            $mpdf->Output('MyPDF.pdf', 'D'); //D - скачает файл!
        }

        return $this->render('report-document', [
            'model' => $model,

        ]);
    }

    public function actionReportVitamin()
    {
        $model = new MenusDishes();


        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenusDishes'];

            $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
            $ids = [];
            foreach ($menus_nutrition_id as $m_id)
            {
                $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
            }
            $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
            //$menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id']/*, 'cycle' => $post['cycle']*/])->orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC])->all();

            $my_menus = Menus::findOne($post['menu_id']);
            $menu_cycle_count = $my_menus->cycle;
            if ($post['cycle'] != 0)
            {
                $cycle_ids[$post['cycle']] = $post['cycle'];
            }
            else
            {
                for ($i = 1; $i <= $menu_cycle_count; $i++)
                {
                    $cycle_ids[$i] = $i;//массив из подходящи циклов
                }
            }

            $dishes_check = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->count();
            if ($dishes_check == 0)
            {
                Yii::$app->session->setFlash('error', "В меню не внесены блюда. Перейдите в раздел 'Разработка редактирование действующего цикличного меню' и добавьте блюда в меню");
                return $this->redirect(['menus-dishes/index']);
            }

            $menus_dishes = MenusDishes::find()->
            select(['menus_dishes.id as id', 'menus_dishes.date_fact_menu as date_fact_menu', 'menus_dishes.menu_id as menu_id', 'menus_dishes.cycle as cycle', 'menus_dishes.days_id as days_id', 'menus_dishes.nutrition_id as nutrition_id', 'menus_dishes.dishes_id as dishes_id', 'menus_dishes.yield as yield', 'dishes_category.sort as sort'])->
            leftJoin('dishes', 'menus_dishes.dishes_id = dishes.id')->
            leftJoin('dishes_category', 'dishes.dishes_category_id = dishes_category.id')->
            where(['date_fact_menu' => 0, 'menu_id' => $post['menu_id'], 'cycle' => $cycle_ids])->
            orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC, 'sort' => SORT_ASC])->
            all();

            $menus_days_id = MenusDays::find()->where(['menu_id' => $post['menu_id']])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
            $days_ids = [];
            foreach ($menus_days_id as $day_id)
            {
                $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
            }

            $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ


            return $this->render('report-vitamin', [
                'model' => $model,
                'days' => $days,
                'nutritions' => $nutritions,
                'menus_dishes' => $menus_dishes,
                'post' => $post,

            ]);
        }

        return $this->render('report-vitamin', [
            'model' => $model,

        ]);
    }

    public function actionZamena()
    {
        $menus_dishes = MenusDishes::find()->where(['dishes_id' => 348])->all();
        foreach ($menus_dishes as $m_dish)
        {
            $m_dish->dishes_id = 29;
            $m_dish->save();
        }
        exit;
    }

    public function actionFactDateProductsList()
    {

        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '5092M');

        $model = new DateForm();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['DateForm'];
            $my_menu = Menus::findOne($post['field']);
            if (strtotime($post['date_start']) < $my_menu->date_start || strtotime($post['date_start']) > $my_menu->date_end)
            {
                Yii::$app->session->setFlash('error', "Введены некорректные данные. Начальная дата диапозона не входит в период действия меню!");
                return $this->redirect(['fact-date-products-list']);
            }

            if (strtotime($post['date_end']) > $my_menu->date_end || strtotime($post['date_end']) < $my_menu->date_start)
            {
                Yii::$app->session->setFlash('error', "Введены некорректные данные. Конечная дата диапозона не входит в период действия меню!");
                return $this->redirect(['fact-date-products-list']);
            }


            $start_date = date('d.m.Y', $my_menu->date_start);//Дата старта меню
            $day_of_week = date("w", strtotime($post['date_start']));//День недели выбранной даты
            $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
            /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
            /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
            if ($day_of_week_start_date == 0)
            {
                $day_of_week_start_date = 7;
            }
            if ($day_of_week == 0)
            {
                $day_of_week = 7;
            }
            /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
            $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

            $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
            $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
            $count_week = ceil((((strtotime($post['date_start']) - $my_menu->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

            $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
            /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
            if ($day_of_week == 1)
            {
                $cycle = $count_week - 1;
            }
            /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
            if ($count_week == 0)
            {
                $cycle = 1;
            }

            /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
            while ($cycle > $my_menu->cycle)
            {
                $cycle = $cycle - $my_menu->cycle;
            }
            if ($cycle == 0)
            {
                $cycle = $my_menu->cycle;
            }
            /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/
            $dishes_ids = [];
            $current_date = strtotime($post['date_start']);
            $ccc = 0;
            while ($current_date <= strtotime($post['date_end']))
            {
                $fact_dishes = MenusDishes::find()->where(['date_fact_menu' => $current_date, 'menu_id' => $my_menu->id])->all();
                $cycle_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $my_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week])->all();
                if (!empty($fact_dishes) || !empty($cycle_dishes))
                {
                    $datas[$current_date][0] = date('d.m.Y', $current_date);
                    $datas[$current_date][1] = $cycle;

                    foreach ($fact_dishes as $f_dish)
                    {
                        if (!in_array($f_dish->dishes_id, $dishes_ids))
                        {
                            $dishes_ids[] = $f_dish->dishes_id;
                        }
                    }
                    foreach ($cycle_dishes as $c_dish)
                    {
                        if (!in_array($c_dish->dishes_id, $dishes_ids))
                        {
                            $dishes_ids[] = $c_dish->dishes_id;
                        }
                    }
                }

                $day_of_week++;
                if ($day_of_week > 7)
                {
                    $day_of_week = 1;
                    $cycle++;
                }
                if ($cycle > $my_menu->cycle)
                {
                    $cycle = 1;
                }
                $current_date = $current_date + 86400;

            }


            $dishes_dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_ids])->orderby(['id' => SORT_ASC])->all();
            $categories_ids = [];
            $product_ids = [];

            foreach ($dishes_dishes_products as $d_d_product)
            {
                $product = Products::find()->where(['id' => $d_d_product->products_id])->one();
                if (!in_array($product->products_category_id, $categories_ids))
                {
                    $categories_ids[] = $product->products_category_id;
                }

                if (!in_array($product->id, $product_ids))
                {
                    $product_ids[] = $product->id;
                }
            }

            $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();
            $products = Products::find()->
            select(['products.id as id', 'products_category.id as pc_id', 'products.name as name', 'products.products_category_id as products_category_id', 'products_category.sort as sort'])->
            leftJoin('products_category', 'products.products_category_id = products_category.id')->
            where(['products.id' => $product_ids])->
            orderby(['products_category.sort' => SORT_ASC])->
            asArray()->
            all();

            /*foreach($products_categories as $p_cat){
                print_r($p_cat->name.'<br>');
            }
            exit;*/
            //print_r(count($dishes_ids));exit;
            //print_r($datas);exit;

            return $this->render('fact-date-products-list', [
                'products_categories' => $products_categories,
                'products' => $products,
                'datas' => $datas,
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('fact-date-products-list', [
            'model' => $model,
        ]);
    }

    public function actionExcelFactDateProductsList($menu_id, $date_start_diapozon, $date_end_diapozon, $brutto_netto)
    {

        $my_menu = Menus::findOne($menu_id);

        $start_date = date('d.m.Y', $my_menu->date_start);//Дата старта меню
        $day_of_week = date("w", strtotime($date_start_diapozon));//День недели выбранной даты
        $day_of_week_start_date = date("w", strtotime($start_date));//День недели даты старта меню
        /*ПРОБЛЕМА В ТОМ ЧТО ДНИ НЕДЕЛИ В БАЗЕ ХРАНЯТСЯ С ID ОТ 1-7(ВОСКРЕСЕНЬЕ - 7), А PHP ВОСКРЕСЕНЬЕ - 0 ДЕНЬ, ПОЭТОМУ НУЖЕН ELSE ЧТОБЫ 7 ЗАМЕНИТЬ НА 0*/
        /*ПЕРЕОПРЕДЕЛЯЕМ ОБРАТНО ДЕЛАЕМ ВОСКРЕСЕНЬЕ 7М ДНЕМ*/
        if ($day_of_week_start_date == 0)
        {
            $day_of_week_start_date = 7;
        }
        if ($day_of_week == 0)
        {
            $day_of_week = 7;
        }
        /*КОНЕЦ ПЕРЕОПРЕДЕЛЕНИЯ*/
        $day_offset = $day_of_week_start_date - 1;//СКОЛЬКО ДНЕЙ НУЖНО ОТНИМАТЬ ДЛЯ ТОГО ЧТОБЫ ПЕРЕЙТИ К ПОНЕДЕЛЬНИКУ

        $date_monday = date('d.m.Y', strtotime(($start_date) . ' - ' . $day_offset . ' day'));//ДАТА ПОНЕДЕЛЬНИКА САМОГО ПЕРВОГО
        $dif_monday_and_start = ceil(((strtotime($start_date)) - (strtotime($date_monday))) / 86400);//РАЗНИЦА МЕЖДУ ПОНЕДЕЛЬНИКОМ И СТАРТОВОЙ ДАТЫ В ДНЯХ
        $count_week = ceil((((strtotime($date_start_diapozon) - $my_menu->date_start) / 86400) + $dif_monday_and_start) / 7);//РАСЧЕТ КОЛИЧЕСТВА НЕДЕЛЬ МЕЖДУ ВЫБРАННОЙ ДАТОЙ И ПОНЕДЕЛЬНИКОМ КОТОРЫЙ САМЫЙ ПЕРВЫЙ

        $cycle = $count_week;//ПРИРАВНИВАЕМ ЦИКЛ КОЛИЧЕСТВУ НЕДЕЛЬ ДО НАШЕЙ ДАТЫ
        /*ЕСЛИ ВЫБРАННЫЙ ДЕНЬ ЯВЛЯЕТСЯ ПОНЕДЕЛЬНИКОМ, ТО ПРОГРАММА СЧИТАЕТ РАЗНИЦУ МЕЖДУ ДВУМЯ ПОНЕДЕЛЬНИКАМИ, СООТВЕТСТВЕННО ОШИБОЧНО ПРИБАВЛЯЕТСЯ ЛИШНЯЯ НЕДЕЛЯ, ПОЭТОМУ ЕЕ СЛЕДУЮТ УБИРАТЬ. ТАК КАК МЫ ИЩЕМ ПОНЕДЕЛЬНИК( И ОН МОЖЕТ И НЕ ВХОДИТ В ДИАПОЗОН СТАРТА И ОКОНЧАНИЯ, ВОЗНИКАЕТ ОШИБКА ОПРЕДЕЛЕНИЯ ЦИКЛА. СЛЕДУЮЩЕЕ УСЛОВИЕЕ ЕЕ ИСПРАВЛЯЕТ)*/
        if ($day_of_week == 1)
        {
            $cycle = $count_week - 1;
        }
        /*$date_monday дата понедельника с которого идет отсчет. ПРОБЛЕМА В ТОМ ЧТО ЭТОТ ПОНЕДЕЛЬНИК МОЖЕТ ЯВЛЯТЬСЯ ПЕРВЫМ ДНЕМ НАШЕГО МЕНЮ И СООТВЕТСТВЕННО РАЗНИЦА МЕЖДУ ЭТИМИ ДНЯМИ БУДЕТ 0 И ЦИКЛ СООТВЕТСТВЕННО -1. ПОЭТОМУ В ЭТОМ СЛУЧАЕ МЫ НАЗНАЧАЕМ ТАКОЙ ПОНЕДЕЛЬНИК ПЕРВОЙ НЕДЕЛЕЙ*/
        if ($count_week == 0)
        {
            $cycle = 1;
        }

        /*ПРОЦЕСС ИЗМЕНЕНИЯ ЦИКЛА ВЗАВИСИМОСТИ ОТ КОЛИЧЕСТВО НЕДЕЛЬ*/
        while ($cycle > $my_menu->cycle)
        {
            $cycle = $cycle - $my_menu->cycle;
        }
        if ($cycle == 0)
        {
            $cycle = $my_menu->cycle;
        }
        /*КОНЕЦ ПРОЦЕССА ИЗМЕНЕИЯ ЦИКЛАБ ДАЛЕЕ ЦИКЛ ОТПРАВЛЯЕМ ВО VIEW*/
        $dishes_ids = [];
        $current_date = strtotime($date_start_diapozon);
        $ccc = 0;
        while ($current_date <= strtotime($date_end_diapozon))
        {
            $fact_dishes = MenusDishes::find()->where(['date_fact_menu' => $current_date, 'menu_id' => $my_menu->id])->all();
            $cycle_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $my_menu->id, 'cycle' => $cycle, 'days_id' => $day_of_week])->all();
            if (!empty($fact_dishes) || !empty($cycle_dishes))
            {
                $datas[$current_date][0] = date('d.m.Y', $current_date);
                $datas[$current_date][1] = $cycle;

                foreach ($fact_dishes as $f_dish)
                {
                    if (!in_array($f_dish->dishes_id, $dishes_ids))
                    {
                        $dishes_ids[] = $f_dish->dishes_id;
                    }
                }
                foreach ($cycle_dishes as $c_dish)
                {
                    if (!in_array($c_dish->dishes_id, $dishes_ids))
                    {
                        $dishes_ids[] = $c_dish->dishes_id;
                    }
                }
            }

            $day_of_week++;
            if ($day_of_week > 7)
            {
                $day_of_week = 1;
                $cycle++;
            }
            if ($cycle > $my_menu->cycle)
            {
                $cycle = 1;
            }
            $current_date = $current_date + 86400;

        }


        $dishes_dishes_products = DishesProducts::find()->where(['dishes_id' => $dishes_ids])->orderby(['id' => SORT_ASC])->all();
        $categories_ids = [];
        $product_ids = [];

        foreach ($dishes_dishes_products as $d_d_product)
        {
            $product = Products::find()->where(['id' => $d_d_product->products_id])->one();
            if (!in_array($product->products_category_id, $categories_ids))
            {
                $categories_ids[] = $product->products_category_id;
            }

            if (!in_array($product->id, $product_ids))
            {
                $product_ids[] = $product->id;
            }
        }

        $products_categories = ProductsCategory::find()->where(['id' => $categories_ids])->orderby(['sort' => SORT_ASC])->all();
        $products = Products::find()->
        select(['products.id as id', 'products_category.id as pc_id', 'products.name as name', 'products.products_category_id as products_category_id', 'products_category.sort as sort'])->
        leftJoin('products_category', 'products.products_category_id = products_category.id')->
        where(['products.id' => $product_ids])->
        orderby(['products_category.sort' => SORT_ASC])->
        asArray()->
        all();
        $products_model = new Products();


        //print_r($datas);exit;


        require '../../vendor/autoload.php';
        $array_num = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /*Установка шрифта в документа*/
        $styleArray = array(
            'font' => array(
                //'bold'  => true,
                //'color' => array('rgb' => 'FF0000'),
                'size' => 10,
                'name' => 'Times New Roman'
            ));

        $spreadsheet->getActiveSheet()->getStyle('A1:BZ100')->applyFromArray($styleArray);

        /*конец установки*/


        $sheet->mergeCells("A2:A4");
        $sheet->setCellValue("A2", "№");
        //$sheet->getStyle("A2")->getFont()->setBold(true);

        $sheet->mergeCells("B2:B4");
        $sheet->setCellValue("B2", "Продукт");
        $sheet->getColumnDimension('B')->setWidth("30");
        //$sheet->getStyle("B2")->getFont()->setBold(true);

        $num = 0;
        $count_days = 0;
        foreach ($datas as $key => $data)
        {
            $count_days++;
            if (date("w", strtotime($data[0])) == 0)
            {
                $day_name = 7;
            }
            else
            {
                $day_name = date("w", strtotime($data[0]));
            }
            $sheet->getColumnDimension($array_num[$num])->setWidth("11");
            //$sheet->getStyle($array_num[$num] . '2')->getFont()->setBold(true);
            //$sheet->getStyle($array_num[$num] . '3')->getFont()->setBold(true);
            //$sheet->getStyle($array_num[$num] . '4')->getFont()->setBold(true);

            $sheet->setCellValue($array_num[$num] . '2', $data[0]);
            $sheet->setCellValue($array_num[$num] . '3', $data[1] . ' неделя');
            $sheet->setCellValue($array_num[$num] . '4', Days::findOne($day_name)->name);
            $num++;

        }
        $sheet->mergeCells($array_num[$num] . '2' . ':' . $array_num[$num] . '4');
        $sheet->setCellValue($array_num[$num] . '2', 'Итого');
        //$sheet->getStyle($array_num[$num] . '2')->getFont()->setBold(true);
        $num++;
        $sheet->mergeCells($array_num[$num] . '2' . ':' . $array_num[$num] . '4');
        $sheet->setCellValue($array_num[$num] . '2', 'Среднесуточное значение');
        //$sheet->getStyle($array_num[$num] . '2')->getFont()->setBold(true);

        if ($brutto_netto == 0)
        {
            $sheet->mergeCells('A1:' . $array_num[$num] . '1');
            $sheet->setCellValue('A1', 'Перечень продуктов, Брутто, г ' . $my_menu->name);
        }
        else
        {
            $sheet->mergeCells('A1:' . $array_num[$num] . '1');
            $sheet->setCellValue('A1', 'Перечень продуктов, Нетто, г ' . $my_menu->name);
        }
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle("A1")->getFont()->setSize(14);

        $array_num2 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ',
        ];

        $column = 5;

        $number_row = 1;
        $i = 0;
        //print_r($products_categories);exit
        foreach ($products_categories as $product_cat)
        {
            foreach ($products as $product)
            {

                if ($product_cat->id == $product['products_category_id'])
                {
                    $num2 = 0;
                    $totality = 0;
                    $sheet->setCellValue($array_num2[$num2] . $column, $number_row);
                    //$sheet->getStyle($array_num2[$num2] . $column)->getFont()->setBold(true);
                    $num2++;
                    $sheet->setCellValue($array_num2[$num2] . $column, $product['name']);
                    //$sheet->getStyle($array_num2[$num2] . $column)->getFont()->setBold(true);
                    //print_r($number_row);
                    $num2++;


                    foreach ($datas as $data)
                    {
                        if (date("w", strtotime($data[0])) == 0)
                        {
                            $day_name = 7;
                        }
                        else
                        {
                            $day_name = date("w", strtotime($data[0]));
                        }
                        $total = $products_model->get_total_yield_day_period($menu_id, $product['id'], $data, $brutto_netto);
                        if ($total == '-')
                        {
                            $total = '-';
                        }
                        else
                        {
                            $totality = $total + $totality;
                        }

                        $sheet->setCellValue($array_num2[$num2] . $column, $total);
                        $num2++;
                    }
                    $sheet->setCellValue($array_num2[$num2] . $column, $totality);
                    //$sheet->getStyle($array_num2[$num2] . $column)->getFont()->setBold(true);
                    $num2++;
                    $sheet->setCellValue($array_num2[$num2] . $column, round($totality / $count_days, 2));
                    //$sheet->getStyle($array_num2[$num2] . $column)->getFont()->setBold(true);
                    $column++;
                    $number_row++;
                }
            }

        }


        $filename = 'Перечень продуктов с указанной переодичностью.xlsx'; //save our workbook as this file name
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    public function actionChangeMassa()
    {
        $recipes_collections = RecipesCollection::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->all();
        foreach ($recipes_collections as $r_col)
        {
            $dishes = Dishes::find()->where(['recipes_collection_id' => $r_col->id])->all();
            foreach ($dishes as $dish)
            {
                $dishes_products = DishesProducts::find()->where(['dishes_id' => $dish->id])->all();
                foreach ($dishes_products as $d_product)
                {
                    $d_product->net_weight = $d_product->net_weight * 1000;
                    $d_product->gross_weight = $d_product->gross_weight * 1000;
                    $d_product->save();
                }
            }
        }
        exit;
    }
}
