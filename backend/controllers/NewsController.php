<?php

namespace backend\controllers;

use common\models\NewsAccess;
use Yii;
use common\models\News;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class NewsController extends Controller
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

    public function actionIndex()
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => News::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }

        $model = new News();
        $model_access = new NewsAccess();

        $roles = [
            'guest',
            'RPN',
            'RPN_mun',
            'director',
            'kid',
            'school14',
            'school511',
            'school56',
            'school59',
            'school79',
            'school1011',
            'student',
            'parent',
            'teacher',
            'tutor',
            'vospitatel',
            'vospitatel_help',
            'nyanya',
            'medic',
            'nutrition_specialist',
            'otherwise'
        ];

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $post = Yii::$app->request->post()['News'];
            $post_access = Yii::$app->request->post()['NewsAccess'];

            $model->category = $post['category'];
            $model->title = $post['title'];
            $model->news_text = $post['news_text'];
            $model->fix = $post['fix'];
            $model->created_user_id = Yii::$app->user->id;
            if ($model->save())
            {
                $news_id = $model->id;
                foreach ($post_access as $key => $item)
                {
                    if ($item == 1)
                    {
                        $model_access = new NewsAccess();
                        $model_access->news_id = $news_id;
                        $model_access->role = $key;
                        $model_access->save();
                    }
                }
                Yii::$app->session->setFlash('success', "Новость добавлена на сайт");
                return $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'model_access' => $model_access,
            'roles' => $roles,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }

        $model = $this->findModel($id);
        $model_access = new NewsAccess();
        $access_role = NewsAccess::find()->where(['news_id' => $id])->asArray()->all();

        $roles = [
            'guest',
            'RPN',
            'RPN_mun',
            'director',
            'kid',
            'school14',
            'school511',
            'school56',
            'school59',
            'school79',
            'school1011',
            'student',
            'parent',
            'teacher',
            'tutor',
            'vospitatel',
            'vospitatel_help',
            'nyanya',
            'medic',
            'nutrition_specialist',
            'otherwise'
        ];

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            NewsAccess::deleteAll(['news_id' => $id]);

            $post = Yii::$app->request->post()['News'];
            $post_access = Yii::$app->request->post()['NewsAccess'];

            $model->category = $post['category'];
            $model->title = $post['title'];
            $model->news_text = $post['news_text'];
            $model->fix = $post['fix'];
            $model->updated_user_id = Yii::$app->user->id;
            if ($model->save())
            {
                $news_id = $model->id;
                foreach ($post_access as $key => $item)
                {
                    if ($item == 1)
                    {
                        $model_access = new NewsAccess();
                        $model_access->news_id = $news_id;
                        $model_access->role = $key;
                        $model_access->save();
                    }
                }
                Yii::$app->session->setFlash('success', "Новость обновлена");
                return $this->redirect('index');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_access' => $model_access,
            'access_role' => $access_role,
            'roles' => $roles,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
