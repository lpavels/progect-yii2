<?php

namespace backend\controllers;

use common\models\AuthAssignment;
use common\models\DepartamentSearch;
use common\models\DepartmentEducation;
use common\models\Municipality;
use common\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DepartamentController extends Controller
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
            Yii::$app->session->setFlash('error', "Доступ запрещен");
            return $this->goHome();
        }

        $searchModel = new DepartamentSearch();
        $search = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($search);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate($id)
    {
        if (!Yii::$app->user->can('admin'))
        {
            Yii::$app->session->setFlash('error', "Доступ запрещен");
            return $this->goHome();
        }

        $model = DepartmentEducation::findOne($id);
        $key_login = $model->func_rand($model->district_id, $model->region_id, $model->municipality_id);
        $model->key_login_departament = $key_login;
        if ($model->save())
        {
            $model_user = new User();
            $model_user->key_login = $key_login;
            $model_user->training_id = 4;
            $model_user->organization_id = NULL;
            $model_user->name = Municipality::findOne($model->municipality_id)->name . '. Управление образования.';
            $model_user->year_birth = 0;
            $model_user->type_listener = 'Роспотребнадзор (муниципальный)';
            $model_user->type_training = 10;
            $model_user->transfer = 2021;
            $model_user->version = 1;
            $model_user->status = 10;
            $model_user->save();

            $model_auth = new AuthAssignment();
            $model_auth->user_id = $model_user->id;
            $model_auth->item_name = 'RPN_mun';
            $model_auth->save(false);

            Yii::$app->session->setFlash('success', "Идентификационный номер создан: " . $key_login);
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}
