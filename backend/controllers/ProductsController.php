<?php

namespace backend\controllers;

use common\models\Dishes;
use common\models\DishesProducts;
use common\models\ProductsSubcategory;
use Yii;
use common\models\Products;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ProductsSearch;


/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * Lists all Products models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductsSearch();
        $search = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($search);
        /*$dataProvider = new ActiveDataProvider([
            'query' => Dishes::find(),
        ]);*/

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionProductsBase()
    {
        $searchModel = new ProductsSearch();
        $search = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($search);
        /*$dataProvider = new ActiveDataProvider([
            'query' => Dishes::find(),
        ]);*/

        return $this->render('products-base', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Products model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Products model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Products();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionWhatDishes($id)
    {
        $dishes_products = DishesProducts::find()->where(['products_id' => $id])->all();

        return $this->render('what-dishes', [
            'dishes_products' => $dishes_products,
            'id' => $id,
        ]);
    }

    public function actionDouble($id)
    {
        $model = Products::findOne($id);
        /*$model2 = new Products();
        $model2->attributes = $model->attributes;
        $model2->products_category_id = 40;
        $model2->products_subcategory_id = 0;
        $model2->created_at = date("Y-d-m H:i:s");*/

        $dish = new Dishes();
        $dish->name = $model->name;
        $dish->dishes_category_id = 12;
        $dish->recipes_collection_id = 4;
        $dish->description = 'Продукт пром. производства';
        $dish->culinary_processing_id = 3;
        $dish->yield = 100;
        $dish->techmup_number = 'Пром.';
        $dish->number_of_dish = '-';
        //print_r($dish);//exit;

        if ($dish->save())
        {
            $dishes_products = new DishesProducts();
            $dishes_products->dishes_id = $dish->id;
            $dishes_products->products_id = $id;
            $dishes_products->net_weight = 100;
            $dishes_products->gross_weight = 100;
            //print_r($dishes_products);exit;
            if ($dishes_products->save())
            {
                Yii::$app->session->setFlash('success', "Блюдо успешно создано на основе продукта");
                return $this->redirect(Yii::$app->request->referrer);
            }
            else
            {
                $dish->delete();
                Yii::$app->session->setFlash('error', "Ошибка при добавлении продукта в блюдо. Блюдо не создано");
                return $this->redirect(['index']);

            }
        }
        else
        {
            Yii::$app->session->setFlash('error', "Ошибка при созданиии копии продукта");
            return $this->redirect(['index']);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', "Сохранены");
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Products model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Products::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionSubcategorylist($id)
    {
        $groups = ProductsSubcategory::find()->where(['product_category_id' => $id])->all();
        if ($id == 1)
        {
        }
        $json = '';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $json .= "<option value='{$group->id}'>{$group->name}</option>";
            }
        }
        return $json;
    }
}
