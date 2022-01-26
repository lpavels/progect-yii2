<?php

namespace backend\controllers;

use common\models\DateForm;
use Yii;
use common\models\AnketParentControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class AnketParentControlController extends Controller
{

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        $dataProvider = new ActiveDataProvider([
            'query' => AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionView($id)
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionResult($id)
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        return $this->render('result', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreate()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        $model = new AnketParentControl();
        //print_r(Yii::$app->request->pathInfo);exit;

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['AnketParentControl'];

            if (($post['masa_othodov'] * 1000) > ($post['count'] * $post['masa_porcii']))
            {
                //print_r( $post['masa_othodov']*1000 . ' >' . $post['count'] * $post['masa_porcii']);exit;
                Yii::$app->session->setFlash('error', "Введены некорректные данные. Масса несъеденной пищи не может превышать массу всех блюд. Обратите внимание, что массу несъеденной пищи нужно указывать в килограммах!");
                return $this->redirect(['create']);
            }

        }
        if ($model->load(Yii::$app->request->post()) && $model->save(false))
        {
            return $this->redirect(['result', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionInside()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        $model = new AnketParentControl();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['AnketParentControl'];

            if (($post['masa_othodov'] * 1000) > ($post['count'] * $post['masa_porcii']))
            {
                //print_r( $post['masa_othodov']*1000 . ' >' . $post['count'] * $post['masa_porcii']);exit;
                Yii::$app->session->setFlash('error', "Введены некорректные данные. Масса несъеденной пищи не может превышать массу всех блюд. Обратите внимание, что массу несъеденной пищи нужно указывать в килограммах!");
                return $this->redirect(['inside']);
            }

        }

        if ($model->load(Yii::$app->request->post()) && $model->save(false))
        {
            return $this->redirect(['result', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionSocial()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model = new AnketParentControl();

        if ($model->load(Yii::$app->request->post()) && $model->save(false))
        {
            return $this->redirect(['result', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = AnketParentControl::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionReport()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        $model = new DateForm();
        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['DateForm'];
            if (empty($post['date_start']) && !empty($post['date_end']))
            {
                $models = AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => $post['field']])->andWhere(['<=', 'date', strtotime($post['date_end'])])->orderBy(['date' => SORT_ASC])->all();
            }
            elseif (!empty($post['date_start']) && empty($post['date_end']))
            {
                $models = AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => $post['field']])->andWhere(['>=', 'date', strtotime($post['date_start'])])->orderBy(['date' => SORT_ASC])->all();
            }
            elseif (empty($post['date_start']) && empty($post['date_end']))
            {
                $models = AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => $post['field']])->orderBy(['date' => SORT_ASC])->all();
            }
            else
            {
                $models = AnketParentControl::find()->where(['organization_id' => Yii::$app->user->identity->organization_id, 'status' => $post['field']])->andWhere(['>=', 'date', strtotime($post['date_start'])])->andWhere(['<=', 'date', strtotime($post['date_end'])])->orderBy(['date' => SORT_ASC])->all();
            }
            return $this->render('report', [
                'model' => $model,
                'models' => $models,
                'post' => $post,
            ]);
        }

        return $this->render('report', [
            'model' => $model,
        ]);
    }
}
