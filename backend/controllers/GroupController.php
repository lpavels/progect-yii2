<?php

namespace backend\controllers;

use common\models\KidsQ;
use common\models\QuestionsResponse;
use Yii;
use common\models\Group;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GroupController extends Controller
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
            'query' => Group::find()->where(['user_id' => Yii::$app->user->id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        if (empty(QuestionsResponse::find()->where(['user_id' => Yii::$app->user->id, 'status' => 1])->count()))
        {
            Yii::$app->session->setFlash('error', 'Пройдите входной тест.');
            return $this->redirect(['index']);
        }
        $model = new Group();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', 'Группа успешно создана.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $id])->exists()))
        {
            $model = $this->findModel($id);

            if ($model->load(Yii::$app->request->post()) && $model->save())
            {
                Yii::$app->session->setFlash('success', 'Группа успешно обновлена.');
                return $this->redirect(['index']);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
        else
        {
            Yii::$app->session->setFlash('error', 'Недостаточно прав для редактирования данной группы');
            return $this->redirect(['index']);
        }
    }

    public function actionDelete($id)
    {
        if (!empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $id])->exists()))
        {
            $this->findModel($id)->delete();

            KidsQ::deleteAll(['group_id' => $id]);

            Yii::$app->session->setFlash('success', 'Группа успешно удалена.');
        }
        else
        {
            Yii::$app->session->setFlash('error', 'Вы не можете удалить данную группу');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
