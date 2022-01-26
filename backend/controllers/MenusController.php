<?php

namespace backend\controllers;

use common\models\Days;
use common\models\NutritionApplications;
use common\models\NutritionInfo;
use common\models\Organization;
use Yii;
use common\models\Menus;
use common\models\MenusNutrition;
use common\models\MenusDays;
use common\models\MenusDishes;
use common\models\MenuForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class MenusController extends Controller
{
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
        $dataProvider = new ActiveDataProvider([
            'query' => Menus::find()->where(['status_archive' => 0, 'user_id' => Yii::$app->user->id]),
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUsed()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => Menus::find()->where(['status_archive' => 1]),
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('used', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $menus = Menus::findOne($id);
        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $menus_nutrition = MenusNutrition::find()->where(['menu_id' => $id])->all();

        return $this->render('view', [
            'menus' => $menus,
            'menus_days' => $menus_days,
            'menus_nutrition' => $menus_nutrition,
        ]);
    }

    public function actionViewUsed($id)
    {
        $menus = Menus::findOne($id);
        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $menus_nutrition = MenusNutrition::find()->where(['menu_id' => $id])->all();

        return $this->render('view-used', [
            'menus' => $menus,
            'menus_days' => $menus_days,
            'menus_nutrition' => $menus_nutrition,
        ]);
    }


    public function actionCreate()
    {
        $model = new MenuForm();

        if (Yii::$app->request->post())
        {
            $mas_days = array();
            $mas_nutritions = array();
            $post = Yii::$app->request->post()['MenuForm'];
            $model2 = new Menus();
            //ЕСЛИ РОСПОТРЕБ ИЛИ МИНОБР ПРИСТУПИЛИ К СОЗДАНИЮ СВОИХ МЕНЮ
            if (Yii::$app->user->can('director') || Yii::$app->user->can('minobr'))
            {
                //print_r($post['odno_vnogodnev']);exit;
                //ПРОВЕРЯЕМ: ОНО ОДНОДНЕНВОЕ?В ПЕРЕМЕНУЮ odno_vnogodnev ПОЛОЖИЛИ УКАЗАТЕЛЬ ОДНОДНЕВНОСТИ ИЛИ МНОГОДНЕВНОСТИ
                if ($post['odno_vnogodnev'] == 1)
                {
                    $model2->parent_id = 0;
                    $model2->show_indicator = 0;
                    $model2->organization_id = Yii::$app->user->identity->organization_id;
                    $model2->user_id = Yii::$app->user->id;
                    $model2->feeders_characters_id = Yii::$app->request->post()['MenuForm']['characters'];
                    $model2->age_info_id = Yii::$app->request->post()['MenuForm']['age'];
                    $model2->name = Yii::$app->request->post()['MenuForm']['name'];
                    $model2->cycle = 1;
                    $model2->date_start = strtotime(Yii::$app->request->post()['MenuForm']['date_start']);
                    $model2->date_end = strtotime($post['date_start']);
                    $model2->status_archive = 0;
                    //ГОВОРИМ ЧТО У НАС ТОЛЬКО ОДИН ДЕНЬ И ЭТО ПОНЕДЕЛЬНИК
                    $day_of_week = date("w", strtotime($post['date_start']));//День недели выбранной даты
                    if ($day_of_week == 0)
                    {
                        $day_of_week = 7;
                    }
                    $mas_days[] = $day_of_week;
                }
                else
                {
                    //ИНАЧЕ БУДЕТ МНОГО ДНЕЙ
                    $model2->parent_id = 0;
                    $model2->show_indicator = 0;
                    $model2->organization_id = Yii::$app->user->identity->organization_id;
                    $model2->user_id = Yii::$app->user->id;
                    $model2->feeders_characters_id = Yii::$app->request->post()['MenuForm']['characters'];
                    $model2->age_info_id = Yii::$app->request->post()['MenuForm']['age'];
                    $model2->name = Yii::$app->request->post()['MenuForm']['name'];
                    $model2->cycle = Yii::$app->request->post()['MenuForm']['cycles'];
                    $model2->date_start = strtotime(Yii::$app->request->post()['MenuForm']['date_start']);
                    $model2->date_end = strtotime(Yii::$app->request->post()['MenuForm']['date_end']);
                    $model2->status_archive = 0;
                }
                //ЕСЛИ ПОЛЬЗОВАТЕЛЬ ЯВЛЯЕТСЯ ЛЮБЫМ ДРУГИМ ЮЗЕРОМ
            }
            else
            {
                $model2->parent_id = 0;
                $model2->show_indicator = 0;
                $model2->organization_id = Yii::$app->user->identity->organization_id;
                $model2->user_id = Yii::$app->user->id;
                $model2->feeders_characters_id = Yii::$app->request->post()['MenuForm']['characters'];
                $model2->age_info_id = Yii::$app->request->post()['MenuForm']['age'];
                $model2->name = Yii::$app->request->post()['MenuForm']['name'];
                $model2->cycle = Yii::$app->request->post()['MenuForm']['cycles'];
                $model2->date_start = strtotime(Yii::$app->request->post()['MenuForm']['date_start']);
                $model2->date_end = strtotime(Yii::$app->request->post()['MenuForm']['date_end']);
                $model2->status_archive = 0;
            }
            //БЛОК ОБЩЕЙ ИНФОРМАЦИИ И ДАННЫХ СКОЛЬКО ДНЕЙ В МЕНЮ И ПРИЕМОВ ПИЩИ
            if ($model2->save())
            {


                if ($post['days1'] == 1)
                {
                    $mas_days[] = 1;
                }
                if ($post['days2'] == 1)
                {
                    $mas_days[] = 2;
                }
                if ($post['days3'] == 1)
                {
                    $mas_days[] = 3;
                }
                if ($post['days4'] == 1)
                {
                    $mas_days[] = 4;
                }
                if ($post['days5'] == 1)
                {
                    $mas_days[] = 5;
                }
                if ($post['days6'] == 1)
                {
                    $mas_days[] = 6;
                }
                if ($post['days7'] == 1)
                {
                    $mas_days[] = 7;
                }

                //nutrition
                if ($post['nutrition1'] == 1)
                {
                    $mas_nutritions[] = 1;//ЦИФРА НАПРОТИВ МАССИВА ОБОЗНАЧАЕТ ID ПРИЕМА ПИЩИ В ТАБЛИЦЕ NUTRITION_INFO!!!
                }
                if ($post['nutrition2'] == 1)
                {
                    $mas_nutritions[] = 2;
                }
                if ($post['nutrition3'] == 1)
                {
                    $mas_nutritions[] = 3;
                }
                if ($post['nutrition4'] == 1)
                {
                    $mas_nutritions[] = 4;
                }
                if ($post['nutrition5'] == 1)
                {
                    $mas_nutritions[] = 5;
                }
                if ($post['nutrition6'] == 1)
                {
                    $mas_nutritions[] = 6;
                }

                foreach ($mas_days as $day)
                {
                    $model3 = new MenusDays();
                    $model3->menu_id = $model2->id;
                    $model3->days_id = $day;
                    $model3->save(false);
                }

                foreach ($mas_nutritions as $nutrition)
                {
                    $model4 = new MenusNutrition();
                    $model4->menu_id = $model2->id;
                    $model4->nutrition_id = $nutrition;
                    $model4->save(false);
                }


                Yii::$app->session->setFlash('success', "Меню успешно сохранено");
                return $this->redirect(['menus/index']);

            }
            else
            {
                Yii::$app->session->setFlash('error', "Произошла ошибка при создании меню. Данные не были сохранены");
                return $this->redirect(['menus/create']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionCreateArchive()
    {
        $model = new MenuForm();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['MenuForm'];
            $model2 = new Menus();
            $model2->show_indicator = 1;
            $model2->parent_id = 0;
            $model2->organization_id = Yii::$app->user->identity->organization_id;
            $model2->user_id = Yii::$app->user->id;
            $model2->show_indicator = Yii::$app->request->post()['MenuForm']['show_indicator'];
            $model2->feeders_characters_id = Yii::$app->request->post()['MenuForm']['characters'];
            $model2->age_info_id = Yii::$app->request->post()['MenuForm']['age'];
            $model2->name = Yii::$app->request->post()['MenuForm']['name'];
            $model2->cycle = Yii::$app->request->post()['MenuForm']['cycles'];
            $model2->date_start = strtotime(Yii::$app->request->post()['MenuForm']['date_start']);
            $model2->date_end = strtotime(Yii::$app->request->post()['MenuForm']['date_end']);
            $model2->status_archive = 1;
            if ($model2->save())
            {
                $mas_days = array();
                $mas_nutritions = array();

                if ($post['days1'] == 1)
                {
                    $mas_days[] = 1;
                }
                if ($post['days2'] == 1)
                {
                    $mas_days[] = 2;
                }
                if ($post['days3'] == 1)
                {
                    $mas_days[] = 3;
                }
                if ($post['days4'] == 1)
                {
                    $mas_days[] = 4;
                }
                if ($post['days5'] == 1)
                {
                    $mas_days[] = 5;
                }
                if ($post['days6'] == 1)
                {
                    $mas_days[] = 6;
                }
                if ($post['days7'] == 1)
                {
                    $mas_days[] = 7;
                }

                //nutrition
                if ($post['nutrition1'] == 1)
                {
                    $mas_nutritions[] = 1;//ЦИФРА НАПРОТИВ МАССИВА ОБОЗНАЧАЕТ ID ПРИЕМА ПИЩИ В ТАБЛИЦЕ NUTRITION_INFO!!!
                }
                if ($post['nutrition2'] == 1)
                {
                    $mas_nutritions[] = 2;
                }
                if ($post['nutrition3'] == 1)
                {
                    $mas_nutritions[] = 3;
                }
                if ($post['nutrition4'] == 1)
                {
                    $mas_nutritions[] = 4;
                }
                if ($post['nutrition5'] == 1)
                {
                    $mas_nutritions[] = 5;
                }
                if ($post['nutrition6'] == 1)
                {
                    $mas_nutritions[] = 6;
                }

                foreach ($mas_days as $day)
                {
                    $model3 = new MenusDays();
                    $model3->menu_id = $model2->id;
                    $model3->days_id = $day;
                    $model3->save(false);
                }

                foreach ($mas_nutritions as $nutrition)
                {
                    $model4 = new MenusNutrition();
                    $model4->menu_id = $model2->id;
                    $model4->nutrition_id = $nutrition;
                    $model4->save(false);
                }
                Yii::$app->session->setFlash('success', "Меню успешно добавлено в архив");
                return $this->redirect(['menus/archive']);
            }
            else
            {
                Yii::$app->session->setFlash('error', "Произошла ошибка при добавлении меню в архив. Попробуйте добавить снова");
                return $this->redirect(['menus/archive']);
            }
        }

        return $this->render('create-archive', [
            'model' => $model,
        ]);
    }


    public function actionPushArchive($id)
    {
        $model = Menus::findOne($id);
        $model2 = new Menus();
        /*$menus_dishes = MenusDishes::find()->where(['menu_id' => $id, 'date_fact_menu' => 0])->all();
        print_r($menus_dishes);
        exit;*/
        $model2->organization_id = $model->organization_id;
        $model2->user_id = Yii::$app->user->id;
        $model2->parent_id = 0;
        $model2->show_indicator = 1;
        $model2->feeders_characters_id = $model->feeders_characters_id;
        $model2->age_info_id = $model->age_info_id;
        $model2->name = $model->name;
        $model2->cycle = $model->cycle;
        $model2->date_start = $model->date_start;
        $model2->date_end = $model->date_end;
        $model2->status_archive = 1;
        if ($model2->save())
        {
            $days = MenusDays::find()->where(['menu_id' => $id])->all();
            $nutritions = MenusNutrition::find()->where(['menu_id' => $id])->all();
            $menus_dishes = MenusDishes::find()->where(['menu_id' => $id, 'date_fact_menu' => 0])->all();
            /*print_r($menus_dishes);
            exit;*/

            foreach ($days as $day)
            {
                $model3 = new MenusDays();
                $model3->menu_id = $model2->id;
                $model3->days_id = $day->days_id;
                $model3->save(false);
            }

            foreach ($nutritions as $nutrition)
            {
                $model4 = new MenusNutrition();
                $model4->menu_id = $model2->id;
                $model4->nutrition_id = $nutrition->nutrition_id;
                $model4->save(false);
            }

            foreach ($menus_dishes as $m_dish)
            {
                $model5 = new MenusDishes();
                $model5->date_fact_menu = 0;
                $model5->menu_id = $model2->id;
                $model5->cycle = $m_dish->cycle;
                $model5->days_id = $m_dish->days_id;
                $model5->nutrition_id = $m_dish->nutrition_id;
                $model5->dishes_id = $m_dish->dishes_id;
                $model5->yield = $m_dish->yield;
                $model5->save();
            }
            Yii::$app->session->setFlash('success', "Меню успешно добавлено в архив. " . Html::a("Перейти в архив.", "/menus/archive"));
            return $this->redirect(['menus/index']);
        }
        else
        {
            Yii::$app->session->setFlash('error', "Произошла ошибка при добавлении меню в архив. Попробуйте добавить снова");
            return $this->redirect(['menus/index']);
        }
    }


    public function actionPutArchive($id)
    {
        $model = Menus::findOne($id);
        $model2 = new Menus();
        /*parent_id нужен для того чтобы знать чье меню мы берем, чтобы знать кто его родитель это нужно для отчета, т.е. каким архивным меню сколько раз воспользовались*/
        $model2->parent_id = $model->id;
        $model2->show_indicator = 0;
        $model2->organization_id = Yii::$app->user->identity->organization_id;
        $model2->user_id = Yii::$app->user->id;
        $model2->feeders_characters_id = $model->feeders_characters_id;
        $model2->age_info_id = $model->age_info_id;
        $model2->name = $model->name;
        $model2->cycle = $model->cycle;
        $model2->date_start = $model->date_start;
        $model2->date_end = $model->date_end;
        $model2->status_archive = 0;
        if ($model2->save())
        {
            $days = MenusDays::find()->where(['menu_id' => $id])->all();
            $nutritions = MenusNutrition::find()->where(['menu_id' => $id])->all();
            $menus_dishes = MenusDishes::find()->where(['menu_id' => $id, 'date_fact_menu' => 0])->all();

            foreach ($days as $day)
            {
                $model3 = new MenusDays();
                $model3->menu_id = $model2->id;
                $model3->days_id = $day->days_id;
                $model3->save(false);
            }

            foreach ($nutritions as $nutrition)
            {
                $model4 = new MenusNutrition();
                $model4->menu_id = $model2->id;
                $model4->nutrition_id = $nutrition->nutrition_id;
                $model4->save(false);
            }

            foreach ($menus_dishes as $m_dish)
            {
                $model5 = new MenusDishes();
                $model5->date_fact_menu = 0;
                $model5->menu_id = $model2->id;
                $model5->cycle = $m_dish->cycle;
                $model5->days_id = $m_dish->days_id;
                $model5->nutrition_id = $m_dish->nutrition_id;
                $model5->dishes_id = $m_dish->dishes_id;
                $model5->yield = $m_dish->yield;
                $model5->save(false);
            }
            Yii::$app->session->setFlash('success', "Это архивное меню было успешно подгружено к Вам. " . Html::a("Перейти к своим меню.", "/menus/index"));
            return $this->redirect(['menus/archive']);
        }
        else
        {
            Yii::$app->session->setFlash('error', "Произошла ошибка при подгрузке меню. Попробуйте снова");
            return $this->redirect(['menus/archive']);
        }
    }


    public function actionUpdate($id)
    {
        //$model = $this->findModel($id);

        $model = new MenuForm();
        $menus = Menus::findOne($id);
        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $menus_nutrition = MenusNutrition::find()->where(['menu_id' => $id])->all();


        if (Yii::$app->request->post())
        {
            /*print_r(date("d.m.Y", strtotime(Yii::$app->request->post()['MenuForm']['date_start'])));
            exit;*/
            $post = Yii::$app->request->post()['MenuForm'];
            $model2 = Menus::findOne($id);
            if ($model2->cycle > Yii::$app->request->post()['MenuForm']['cycles'])
            {
                $fordeletes = MenusDishes::find()->where(['menu_id' => $id])->andWhere(['>=', 'cycle', $model2->cycle])->all();
                foreach ($fordeletes as $f)
                {
                    $f->delete();
                }
            }

            $model2->feeders_characters_id = Yii::$app->request->post()['MenuForm']['characters'];
            $model2->age_info_id = Yii::$app->request->post()['MenuForm']['age'];
            $model2->name = Yii::$app->request->post()['MenuForm']['name'];
            $model2->cycle = Yii::$app->request->post()['MenuForm']['cycles'];
            $model2->date_start = strtotime(Yii::$app->request->post()['MenuForm']['date_start']);
            $model2->date_end = strtotime(Yii::$app->request->post()['MenuForm']['date_end']);
            if ($model2->save())
            {

                $old_menus_days = MenusDays::deleteAll('menu_id =:id', [':id' => $id]);
                //$old_menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
                //$old_menus_days->delete();

                $old_menus_nutrition = MenusNutrition::deleteAll('menu_id =:id', [':id' => $id]);
                //$old_menus_nutrition->delete();

                $mas_days = array();
                $mas_nutritions = array();

                if ($post['days1'] == 1)
                {
                    $mas_days[] = 1;
                }
                if ($post['days2'] == 1)
                {
                    $mas_days[] = 2;
                }
                if ($post['days3'] == 1)
                {
                    $mas_days[] = 3;
                }
                if ($post['days4'] == 1)
                {
                    $mas_days[] = 4;
                }
                if ($post['days5'] == 1)
                {
                    $mas_days[] = 5;
                }
                if ($post['days6'] == 1)
                {
                    $mas_days[] = 6;
                }
                if ($post['days7'] == 1)
                {
                    $mas_days[] = 7;
                }

                //nutrition
                if ($post['nutrition1'] == 1)
                {
                    $mas_nutritions[] = 1;//ЦИФРА НАПРОТИВ МАССИВА ОБОЗНАЧАЕТ ID ПРИЕМА ПИЩИ В ТАБЛИЦЕ NUTRITION_INFO!!!
                }
                if ($post['nutrition2'] == 1)
                {
                    $mas_nutritions[] = 2;
                }
                if ($post['nutrition3'] == 1)
                {
                    $mas_nutritions[] = 3;
                }
                if ($post['nutrition4'] == 1)
                {
                    $mas_nutritions[] = 4;
                }
                if ($post['nutrition5'] == 1)
                {
                    $mas_nutritions[] = 5;
                }
                if ($post['nutrition6'] == 1)
                {
                    $mas_nutritions[] = 6;
                }

                foreach ($mas_days as $day)
                {
                    $model3 = new MenusDays();
                    $model3->menu_id = $model2->id;
                    $model3->days_id = $day;
                    $model3->save(false);
                }

                foreach ($mas_nutritions as $nutrition)
                {
                    $model4 = new MenusNutrition();
                    $model4->menu_id = $model2->id;
                    $model4->nutrition_id = $nutrition;
                    $model4->save(false);
                }
                /*При снятии чекбоксов удаление всех блюд из данного приема пищи или меню*/
                $my_menus_dishes = MenusDishes::find()->where(['menu_id' => $model2->id])->distinct()->all();
                $count = 0;
                foreach ($my_menus_dishes as $m_m_dish)
                {
                    if (!in_array($m_m_dish->nutrition_id, $mas_nutritions))
                    {
                        //$count = $count + 1;
                        $delete = MenusDishes::findOne($m_m_dish->id);
                        if (!empty($delete))
                        {
                            $delete->delete();
                        }

                    }
                }
                foreach ($my_menus_dishes as $m_m_dish)
                {
                    if (!in_array($m_m_dish->days_id, $mas_days))
                    {
                        //$count = $count + 1;
                        $delete = MenusDishes::findOne($m_m_dish->id);
                        if (!empty($delete))
                        {
                            $delete->delete();
                        }
                    }
                }
                /*Конец удаления*/
                /*print_r($count);
                exit;*/

                Yii::$app->session->setFlash('success', "Меню успешно сохранено");
                return $this->redirect(['index']);
            }
            else
            {
                Yii::$app->session->setFlash('error', "Произошла ошибка при создании меню. Данные не были сохранены");
                return $this->redirect(['menus/create']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'menus' => $menus,
            'menus_days' => $menus_days,
            'menus_nutrition' => $menus_nutrition,
        ]);
    }


    public function actionSettingArchive($id)
    {
        //$model = $this->findModel($id);

        $model = new Menus();
        $menus = Menus::findOne($id);
        $menus_days = MenusDays::find()->where(['menu_id' => $id])->all();
        $menus_nutrition = MenusNutrition::find()->where(['menu_id' => $id])->all();


        if (Yii::$app->request->post())
        {
            /*print_r(Yii::$app->request->post());
            exit;*/
            $post = Yii::$app->request->post()['Menus'];
            $model2 = Menus::findOne($id);

            $model2->feeders_characters_id = $post['feeders_characters_id'];
            $model2->age_info_id = $post['age_info_id'];
            $model2->show_indicator = $post['show_indicator'];
            $model2->name = $post['name'];
            $model2->cycle = $post['cycle'];
            $model2->date_start = strtotime($post['date_start']);
            $model2->date_end = strtotime($post['date_end']);
            if ($model2->save())
            {
                Yii::$app->session->setFlash('success', "Изменения сохранены!");
                return $this->redirect(['archive']);
            }
            else
            {
                Yii::$app->session->setFlash('error', "Произошла ошибка при редактировании. Данные не были сохранены");
                return $this->redirect(['menus/setting-archive?id=' . $id]);
            }
        }

        return $this->render('setting-archive', [
            'model' => $model,
            'menus' => $menus,
            'menus_days' => $menus_days,
            'menus_nutrition' => $menus_nutrition,
        ]);
    }

    public function actionDelete($id)
    {
        /*Массовое удаление меню из всех таблиц при удалении меню*/
        $this->findModel($id)->delete();

        $old_menus_days = MenusDays::deleteAll('menu_id =:id', [':id' => $id]);

        $old_menus_nutrition = MenusNutrition::deleteAll('menu_id =:id', [':id' => $id]);

        $old_menus_dishes = MenusDishes::deleteAll('menu_id =:id', [':id' => $id]);

        return $this->redirect(['index']);
    }

    public function actionDeleteArchive($id)
    {
        /*Массовое удаление меню из всех таблиц при удалении меню*/
        $this->findModel($id)->delete();

        $old_menus_days = MenusDays::deleteAll('menu_id =:id', [':id' => $id]);

        $old_menus_nutrition = MenusNutrition::deleteAll('menu_id =:id', [':id' => $id]);

        $old_menus_dishes = MenusDishes::deleteAll('menu_id =:id', [':id' => $id]);
        Yii::$app->session->setFlash('success', "Меню успешно удалено!");

        return $this->redirect(['archive']);
    }

    protected function findModel($id)
    {
        if (($model = Menus::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionViewMenus($id)
    {
        $model = new MenusDishes();


        $menus_nutrition_id = MenusNutrition::find()->where(['menu_id' => $id])->all();//ВСЕ ПРИЕМЫ ПИЩИ КОНКРЕТНОГО МЕНЮ
        $ids = [];
        foreach ($menus_nutrition_id as $m_id)
        {
            $ids[] = $m_id->nutrition_id;//МАССИВ ID ПРИЕМОВ ПИЩИ КЛНКРЕЬНОГО МЕНЮ
        }
        $nutritions = NutritionInfo::find()->where(['id' => $ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
        $menus_dishes = MenusDishes::find()->where(['date_fact_menu' => 0, 'menu_id' => $id/*, 'cycle' => $post['cycle']*/])->orderby(['cycle' => SORT_ASC, 'days_id' => SORT_ASC, 'nutrition_id' => SORT_ASC])->all();


        $menus_days_id = MenusDays::find()->where(['menu_id' => $id])->all();//ВСЕ ДНИ КОНКРЕТНОГО МЕНЮ
        $days_ids = [];
        foreach ($menus_days_id as $day_id)
        {
            $days_ids[] = $day_id->days_id;//МАССИВ ID ДНЕЙ КЛНКРЕЬНОГО МЕНЮ
        }

        $days = Days::find()->where(['id' => $days_ids])->all();//ОБЪЕКТ С НАБОРОМ ПРИЕМОВ ПИЩИ ИЗ БАЗЫ С УЧЕТОМ НАШИХ АЙДИ
        $get = $id;
        // print_r($days);
        return $this->render('view-menus', [
            'menus_dishes' => $menus_dishes,
            'nutritions' => $nutritions,
            'days' => $days,
            'get' => $get,
            'model' => $model,
        ]);


    }


    //СДЕЛАТЬ КОПИЮ МЕНЮ
    public function actionCopy($id)
    {
        $model = Menus::findOne($id);
        $model2 = new Menus();

        $model2->organization_id = $model->organization_id;
        $model2->user_id = Yii::$app->user->id;
        $model2->parent_id = 0;
        $model2->show_indicator = 0;
        $model2->feeders_characters_id = $model->feeders_characters_id;
        $model2->age_info_id = $model->age_info_id;
        $model2->name = $model->name . '(КОПИЯ)';
        $model2->cycle = $model->cycle;
        $model2->date_start = $model->date_start;
        $model2->date_end = $model->date_end;
        $model2->status_archive = 0;
        if ($model2->save())
        {
            $days = MenusDays::find()->where(['menu_id' => $id])->all();
            $nutritions = MenusNutrition::find()->where(['menu_id' => $id])->all();
            $menus_dishes = MenusDishes::find()->where(['menu_id' => $id, 'date_fact_menu' => 0])->all();


            foreach ($days as $day)
            {
                $model3 = new MenusDays();
                $model3->menu_id = $model2->id;
                $model3->days_id = $day->days_id;
                $model3->save(false);
            }

            foreach ($nutritions as $nutrition)
            {
                $model4 = new MenusNutrition();
                $model4->menu_id = $model2->id;
                $model4->nutrition_id = $nutrition->nutrition_id;
                $model4->save(false);
            }

            foreach ($menus_dishes as $m_dish)
            {
                $model5 = new MenusDishes();
                $model5->date_fact_menu = 0;
                $model5->menu_id = $model2->id;
                $model5->cycle = $m_dish->cycle;
                $model5->days_id = $m_dish->days_id;
                $model5->nutrition_id = $m_dish->nutrition_id;
                $model5->dishes_id = $m_dish->dishes_id;
                $model5->yield = $m_dish->yield;
                $model5->save();
            }
            Yii::$app->session->setFlash('success', "Копия меню успешно создана.");
            return $this->redirect(['menus/index']);
        }
        else
        {
            Yii::$app->session->setFlash('error', "Произошла ошибка копировании меню");
            return $this->redirect(['menus/index']);
        }
    }


    public function actionReportMinobrRpn()
    {
        $model = new Menus();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Menus'];
            if (Yii::$app->user->can('minobr') || Yii::$app->user->can('rospotrebnadzor_nutrition'))
            {

                if ($post['organization_id'] == 0 && $post['parent_id'] == 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $region_id = Organization::findOne($organization_id)->region_id;
                    $organizations = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id])->all();
                    $org_ids = [];
                    foreach ($organizations as $organization)
                    {
                        $org_ids[] = $organization->id;
                    }
                    //print_r($org_ids);exit;
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $org_ids]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]

                    ]);
                }

                elseif ($post['organization_id'] == 0 && $post['parent_id'] != 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $region_id = Organization::findOne($organization_id)->region_id;
                    $organizations = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id, 'municipality_id' => $post['parent_id']])->all();
                    $org_ids = [];
                    foreach ($organizations as $organization)
                    {
                        $org_ids[] = $organization->id;
                    }
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $org_ids]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]

                    ]);
                }
                else
                {
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $post['organization_id']]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]
                    ]);
                }
            }
            if (Yii::$app->user->can('subject_minobr'))
            {
                //print_r(Yii::$app->request->post());exit;
                if ($post['organization_id'] == 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $org = Organization::findOne($organization_id);
                    $organizations = Organization::find()->where(['type_org' => 3, 'municipality_id' => $org->municipality_id])->all();
                    $org_ids = [];

                    foreach ($organizations as $organization)
                    {
                        $org_ids[] = $organization->id;
                    }
                    //print_r($org_ids);exit;
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $org_ids]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]

                    ]);
                }
                else
                {
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $post['organization_id']]),
                        'pagination' => [
                            'pageSize' => 2000
                        ]
                    ]);
                }
            }
            return $this->render('report-minobr-rpn', [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('report-minobr-rpn', [
            'model' => $model,
        ]);


    }


    public function actionReportMinobrRpnVnesen()
    {
        $model = new Menus();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Menus'];
            if (Yii::$app->user->can('minobr') || Yii::$app->user->can('rospotrebnadzor_nutrition'))
            {

                if ($post['organization_id'] == 0 && $post['parent_id'] == 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $region_id = Organization::findOne($organization_id)->region_id;
                    $organizations = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id])->all();
                    $org_ids = [];
                    foreach ($organizations as $organization)
                    {
                        $org_ids[] = $organization->id;
                    }
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $org_ids]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]

                    ]);
                }

                elseif ($post['organization_id'] == 0 && $post['parent_id'] != 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $region_id = Organization::findOne($organization_id)->region_id;
                    $organizations = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id, 'municipality_id' => $post['parent_id']])->all();
                    $org_ids = [];
                    foreach ($organizations as $organization)
                    {
                        $org_ids[] = $organization->id;
                    }
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $org_ids]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]

                    ]);
                }
                else
                {
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $post['organization_id']]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]
                    ]);
                }
            }
            if (Yii::$app->user->can('subject_minobr'))
            {
                //print_r(Yii::$app->request->post());exit;
                if ($post['organization_id'] == 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $org = Organization::findOne($organization_id);
                    $organizations = Organization::find()->where(['type_org' => 3, 'municipality_id' => $org->municipality_id])->all();
                    $org_ids = [];

                    foreach ($organizations as $organization)
                    {
                        $org_ids[] = $organization->id;
                    }
                    //print_r($org_ids);exit;
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $org_ids]),
                        'pagination' => [
                            //'forcePageParam' => false,
                            //'pageSizeParam' => false,
                            'pageSize' => 2000
                        ]

                    ]);
                }
                else
                {
                    $dataProvider = new ActiveDataProvider([
                        'query' => Organization::find()->where(['id' => $post['organization_id']]),
                        'pagination' => [
                            'pageSize' => 2000
                        ]
                    ]);
                }
            }
            return $this->render('report-minobr-rpn-vnesen', [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('report-minobr-rpn-vnesen', [
            'model' => $model,
        ]);


    }

    public function actionMonitoring()
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '5092M');
        $model = new Menus();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Menus'];
            if (Yii::$app->user->can('minobr') || Yii::$app->user->can('rospotrebnadzor_nutrition'))
            {

                if ($post['parent_id'] == 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $region_id = Organization::findOne($organization_id)->region_id;
                    $organizations = Organization::find()->where(['type_org' => 3, 'region_id' => $region_id])->all();

                }

                elseif ($post['parent_id'] != 0)
                {
                    $organization_id = Yii::$app->user->identity->organization_id;
                    $region_id = Organization::findOne($organization_id)->region_id;
                    $organizations = Organization::find()->where(['type_org' => 3, 'municipality_id' => $post['parent_id']])->all();

                }

            }
            if (Yii::$app->user->can('subject_minobr'))
            {

                $organization_id = Yii::$app->user->identity->organization_id;
                $org = Organization::findOne($organization_id);
                $organizations = Organization::find()->where(['type_org' => 3, 'municipality_id' => $org->municipality_id])->all();
                //print_r($organizations);exit;
                $post['parent_id'] = $org->municipality_id;

            }

            if (Yii::$app->user->can('food_director'))
            {
                /*print_r(123);
                exit;*/
                $ids = [];
                $nutrition_aplications = NutritionApplications::find()->where(['sender_org_id' => Yii::$app->user->identity->organization_id, 'status' => 1])->orWhere(['reciever_org_id' => Yii::$app->user->identity->organization_id, 'status' => 1])->all();
                foreach ($nutrition_aplications as $n_aplication)
                {
                    if ($n_aplication->sender_org_id != Yii::$app->user->identity->organization_id)
                    {
                        $ids[] = $n_aplication->sender_org_id;
                    }
                    if ($n_aplication->reciever_org_id != Yii::$app->user->identity->organization_id)
                    {
                        $ids[] = $n_aplication->reciever_org_id;
                    }
                }


                $organization_id = Yii::$app->user->identity->organization_id;
                $org = Organization::findOne($organization_id);
                $organizations = Organization::find()->where(['id' => $ids])->all();
                //print_r($organizations);exit;
                $post['parent_id'] = $org->municipality_id;

            }
            return $this->render('monitoring', [
                'post_orgs' => $organizations,
                'model' => $model,
                'post' => $post,
            ]);
        }


        return $this->render('monitoring', [
            'model' => $model,
        ]);


    }


    public function actionMyMonitoring()
    {
        $model = new Menus();
        $organizations = Organization::find()->where(['id' => Yii::$app->user->identity->organization_id])->all();

        return $this->render('my-monitoring', [
            'organizations' => $organizations,
            'model' => $model,
        ]);
    }


    /*Подставляет организации в выпадающий список*/
    public function actionOrglist($id)
    {
        $organization_id = Yii::$app->user->identity->organization_id;
        $region_id = Organization::findOne($organization_id)->region_id;

        if ($id == 0)
        {
            $groups = Organization::find()->where(['region_id' => $region_id, 'type_org' => 3])->orderby(['title' => SORT_ASC])->all();
            echo '<option value="0">Все организации...</option>';
        }
        else
        {
            $groups = Organization::find()->where(['region_id' => $region_id, 'municipality_id' => $id, 'type_org' => 3])->orderby(['title' => SORT_ASC])->all();
            echo '<option value="0">Все организации...</option>';
        }

        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                echo '<option value="' . $group->id . '">' . $group->title . '</option>';
            }
        }
    }

    public function actionOrglist2($id)
    {
        $organization_id = Yii::$app->user->identity->organization_id;
        $region_id = Organization::findOne($organization_id)->region_id;

        if ($id == 0)
        {
            $groups = Organization::find()->where(['region_id' => $region_id, 'type_org' => 3])->orderby(['title' => SORT_ASC])->all();
        }
        else
        {
            $groups = Organization::find()->where(['municipality_id' => $id, 'type_org' => 3])->orderby(['title' => SORT_ASC])->all();

        }
        //print_r($groups);exit;

        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                echo '<option value="' . $group->id . '">' . $group->title . '</option>';
            }
        }
    }
}
