<?php

namespace backend\controllers;

use common\models\Group;
use Yii;
use common\models\TrainingPlan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TrainingPlanController extends Controller
{
    public function actionIndex()
    {
        $groups = Group::find()->where(['user_id' => Yii::$app->user->id])->all();
        $ids = [];
        foreach ($groups as $group)
        {
            $ids[] = $group->id;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => TrainingPlan::find()->where(['group_id' => $ids]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $TrainingPlan = TrainingPlan::findOne($id);
        if (empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $TrainingPlan->group_id])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для просмотра информации по данной группе.");
            return $this->redirect(['index']);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new TrainingPlan();
        if (TrainingPlan::find()->where(['group_id' => Yii::$app->request->post()['TrainingPlan']['group_id']])->count() > 0)
        {
            Yii::$app->session->setFlash('error', "Информация по этой группе уже добавлена, прейдите в редактирование, чтобы изменить информацию");
            return $this->redirect(['index']);
        }
        if (!empty(Yii::$app->request->post()) && empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => Yii::$app->request->post()['TrainingPlan']['group_id']])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для внесения информации по данной группе.");
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {

            Yii::$app->session->setFlash('success', "Информация сохранена.");
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $TrainingPlan = TrainingPlan::findOne($id);
        if (empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $TrainingPlan->group_id])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для редактирования информации по данной группе.");
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $TrainingPlan = TrainingPlan::findOne($id);
        if (empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $TrainingPlan->group_id])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для удаления информации по данной группе.");
            return $this->redirect(['index']);
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = TrainingPlan::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
