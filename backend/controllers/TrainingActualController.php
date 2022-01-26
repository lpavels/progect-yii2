<?php

namespace backend\controllers;

use Yii;
use common\models\TrainingActual;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TrainingActualController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TrainingActual::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($id)
    {
        $model = TrainingActual::findOne(['kids_id' => $id]);
        if (!empty($model))
        {
            return $this->actionUpdate($id);
        }

        $model = new TrainingActual();

        //if(TrainingActual::find()->where(['kids_id' => $id])->count() > 0){
        //    $train = TrainingActual::find()->where(['kids_id' => $id])->one()->kids_id;
        //    Yii::$app->session->setFlash('success', "Данные успешно обновлены");
        //    return $this->redirect(['update', 'id' => $train]);
        //}

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['TrainingActual'];
            $model->kids_id = $id;
            if ($post['field2'] != '')
            {
                $model->field2 = $post['field2'];
            }
            if ($post['field3'] != '')
            {
                $model->field3 = $post['field3'];
            }
            if ($post['field4'] != '')
            {
                $model->field4 = $post['field4'];
            }
            if ($post['field5'] != '')
            {
                $model->field5 = $post['field5'];
            }
            if ($post['field6'] != '')
            {
                $model->field6 = $post['field6'];
            }
            if ($post['field7'] != '')
            {
                $model->field7 = $post['field7'];
            }
            if ($post['field8'] != '')
            {
                $model->field8 = $post['field8'];
            }
            if ($post['field9'] != '')
            {
                $model->field9 = $post['field9'];
            }
            if ($post['field10'] != '')
            {
                $model->field10 = $post['field10'];
            }
            if ($post['field11'] != '')
            {
                $model->field11 = $post['field11'];
            }
            if ($post['field12'] != '')
            {
                $model->field12 = $post['field12'];
            }
            if ($post['field13'] != '')
            {
                $model->field13 = $post['field13'];
            }
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', "Данные успешно сохранены");
                return $this->redirect(['update', 'id' => $model->kids_id]);
            }

            Yii::$app->session->setFlash('error', "Ошибка при добавлении фактической информации");
            return $this->redirect(['create', 'id' => $id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = TrainingActual::find()->where(['kids_id' => $id])->one();

        if ($model->load(Yii::$app->request->post()))
        {
            $post = Yii::$app->request->post()['TrainingActual'];
            if ($post['field2'] != '')
            {
                $model->field2 = $post['field2'];
            }
            if ($post['field3'] != '')
            {
                $model->field3 = $post['field3'];
            }
            if ($post['field4'] != '')
            {
                $model->field4 = $post['field4'];
            }
            if ($post['field5'] != '')
            {
                $model->field5 = $post['field5'];
            }
            if ($post['field6'] != '')
            {
                $model->field6 = $post['field6'];
            }
            if ($post['field7'] != '')
            {
                $model->field7 = $post['field7'];
            }
            if ($post['field8'] != '')
            {
                $model->field8 = $post['field8'];
            }
            if ($post['field9'] != '')
            {
                $model->field9 = $post['field9'];
            }
            if ($post['field10'] != '')
            {
                $model->field10 = $post['field10'];
            }
            if ($post['field11'] != '')
            {
                $model->field11 = $post['field11'];
            }
            if ($post['field12'] != '')
            {
                $model->field12 = $post['field12'];
            }
            if ($post['field13'] != '')
            {
                $model->field13 = $post['field13'];
            }
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', "Данные успешно сохранены");
                return $this->redirect(['update', 'id' => $model->kids_id]);
            }
            Yii::$app->session->setFlash('error', "Ошибка при обновлении фактической информации");
            return $this->goHome();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = TrainingActual::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
