<?php

namespace backend\controllers;

use common\models\FeedersCharacters;
use common\models\RecipesCollection;
use common\models\NutritionInfo;
use common\models\CulinaryProcessing;
use Yii;
use common\models\AgeInfo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AgeInfoController implements the CRUD actions for AgeInfo model.
 */
class SettingsController extends Controller
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

    public function actionRecipesIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => RecipesCollection::find()->where(['organization_id' => Yii::$app->user->identity->organization_id]),
        ]);

        if (Yii::$app->user->can('rospotrebnadzor_camp') || Yii::$app->user->can('rospotrebnadzor_nutrition') || Yii::$app->user->can('subject_minobr'))
        {
            $dataProvider = new ActiveDataProvider([
                'query' => RecipesCollection::find()->where(['organization_id' => Yii::$app->session['organization_id']]),
            ]);
        }

        return $this->render('recipes-index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionRecipesView($id)
    {
        return $this->render('recipes-view', [
            'model' => $this->findModelRecipes($id),
        ]);
    }


    public function actionRecipesCreate()
    {
        $model = new RecipesCollection();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', "Сборник успешно создан");
            if (Yii::$app->user->can('admin'))
            {
                return $this->redirect(['recipes-index-admin']);
            }
            return $this->redirect(['recipes-index']);
        }

        return $this->render('recipes-create', [
            'model' => $model,
        ]);
    }


    public function actionRecipesUpdate($id)
    {
        $model = $this->findModelRecipes($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', "Сборник успешно обновлен");
            if (Yii::$app->user->can('admin'))
            {
                return $this->redirect(['recipes-index-admin']);
            }
            return $this->redirect(['recipes-index']);
        }

        return $this->render('recipes-update', [
            'model' => $model,
        ]);
    }


    public function actionRecipesDelete($id)
    {
        $this->findModelRecipes($id)->delete();
        if (Yii::$app->user->can('admin'))
        {
            return $this->redirect(['recipes-index-admin']);
        }

        return $this->redirect(['recipes-index']);
    }


    protected function findModelRecipes($id)
    {
        if (($model = RecipesCollection::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModelCulinaryProcessing($id)
    {
        if (($model = CulinaryProcessing::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
