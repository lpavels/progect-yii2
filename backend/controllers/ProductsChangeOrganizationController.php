<?php

namespace backend\controllers;

use common\models\Products;
use common\models\ProductsChange;
use Yii;
use common\models\ProductsChangeOrganization;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductsChangeOrganizationController implements the CRUD actions for ProductsChangeOrganization model.
 */
class ProductsChangeOrganizationController extends Controller
{
    /**
     * {@inheritdoc}
     */

    public function actionIndex()
    {
        $model = new ProductsChangeOrganization();
        $dataProvider = new ActiveDataProvider([
            'query' => ProductsChangeOrganization::find()->where(['organization_id' => Yii::$app->user->identity->organization_id]),
        ]);

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['ProductsChangeOrganization'];
            //print_r($post);exit;
            $model->organization_id = Yii::$app->user->identity->organization_id;
            $model->products_id = $post['products_id'];
            $model->change_products_id = $post['change_products_id'];
            $model->save();
            Yii::$app->session->setFlash('success', "Пункт добавлен");
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'post' => $post,
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "Удалено");
        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductsChangeOrganization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductsChangeOrganization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductsChangeOrganization::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionProductslist($id)
    {
        $products_changes = ProductsChange::find()->where(['products_id' => $id])->all();
        $products_change_ids = [];
        foreach ($products_changes as $p_changes)
        {
            $products_change_ids[] = $p_changes->change_products_id;
        }
        $products = Products::find()->where(['id' => $products_change_ids])->all();
        print_r($products);
        foreach ($products as $product)
        {
            echo '<option value="' . $product->id . '">' . $product->name . '</option>';
        }
    }
}
