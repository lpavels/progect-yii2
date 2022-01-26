<?php

namespace backend\controllers;

use common\models\Group;
use common\models\ReportTbl21;
use common\models\ReportTbl22;
use common\models\TrainingActual;
use Yii;
use common\models\KidsQ;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class KidsQController extends Controller
{
    const YAER_2021 = 2021;
    const YAER_2022 = 2022;

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
        $groups = Group::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
        foreach ($groups as $group)
        {
            $group_array[] = $group->id;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => KidsQ::find()->where(['group_id' => $group_array]),
            'pagination' => [
                'pageSize' => 50]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new KidsQ();

        if (Yii::$app->request->post() && empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => Yii::$app->request->post()['KidsQ']['group_id']])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для добавления ребёнка в данную группу.");
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $model_groups = Group::find()->where(['user_id' => Yii::$app->user->id])->all();
            $array_group = [];
            foreach ($model_groups as $model_group)
            {
                $array_group[] += $model_group->id;
            }
            $kidsq_count = KidsQ::find()->where(['group_id' => $array_group])->count();
            //print_r($kidsq_count);die();


            if (date('Y') == self::YAER_2021)
            {
                $modelReport = ReportTbl21::findOne(['user_id' => Yii::$app->user->id]);
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
            {
                $modelReport = ReportTbl22::findOne(['user_id' => Yii::$app->user->id]);

                if (empty($modelReport))
                {
                    Yii::$app->userHelp->checkDataReportTable();
                    $modelReport = ReportTbl22::findOne(['user_id' => Yii::$app->user->id]);
                }
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
            {
                $modelReport = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
            else
            {
                print_r('Ошибка. Обратиесь к администратору');
                die();
            }

            $modelReport->number_children = $kidsq_count;
            $modelReport->updated_ip = Yii::$app->userHelp->ip();

            if ($modelReport->save())
            {
                Yii::$app->session->setFlash('success', "Ребёнок успешно добавлен.");
            }
            else
            {
                Yii::$app->session->setFlash('error', "Ошибка при добавлении ребёнка в отчёт. Напишите о данной ошибке на электронный адрес edu@niig.su указав в теме сообщения: \"Ошибка при добавлении ребёнка в отчёт (#kids3)\".");
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $KidsQ = KidsQ::findOne($id);
        if (empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $KidsQ->group_id])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для редактирования этого ребёнка.");
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', "Данные обновлены");
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model_KidsQ = KidsQ::findOne($id);
        if (empty(Group::find()->where(['user_id' => Yii::$app->user->id, 'id' => $model_KidsQ->group_id])->exists()))
        {
            Yii::$app->session->setFlash('error', "Недостаточно прав для удаления этого ребёнка.");
            return $this->redirect(['index']);
        }

        if ($model_KidsQ->delete())
        {
            if (TrainingActual::find()->where(['kids_id' => $id])->count() != 0)
            {
                $trainingActual_id = TrainingActual::find()->where(['kids_id' => $id])->one();
                $model_trainingActual = TrainingActual::findOne($trainingActual_id);
                $model_trainingActual->delete();
            }

            $model_groups = Group::find()->where(['user_id' => Yii::$app->user->id])->all();
            $array_group = [];
            foreach ($model_groups as $model_group)
            {
                $array_group[] += $model_group->id;
            }
            $kidsq_count = KidsQ::find()->where(['group_id' => $array_group])->count();


            if (date('Y') == self::YAER_2021)
            {
                $modelReport = ReportTbl21::findOne(['user_id' => Yii::$app->user->id]);
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2021)
            {
                $modelReport = ReportTbl22::findOne(['user_id' => Yii::$app->user->id]);

                if (empty($modelReport))
                {
                    Yii::$app->userHelp->checkDataReportTable();
                    $modelReport = ReportTbl22::findOne(['user_id' => Yii::$app->user->id]);
                }
            }
            elseif (date('Y') == self::YAER_2022 && Yii::$app->user->identity->transfer == 2022)
            {
                $modelReport = ReportTbl22::find()->where(['user_id' => Yii::$app->user->id])->one();
            }
            else
            {
                print_r('Ошибка. Обратиесь к администратору');
                die();
            }

            $modelReport->number_children = $kidsq_count;
            $modelReport->updated_ip = Yii::$app->userHelp->ip();
            $modelReport->save();

            Yii::$app->session->setFlash('success', "Данные удалены");
            return $this->redirect(['index']);
        }
        Yii::$app->session->setFlash('error', "Ошибка. Данные удалены не полностью. Повторите удаление или обратитесь в техническую поддержку.");
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = KidsQ::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
