<?php

namespace backend\controllers;

use common\models\MenusDishes;
use common\models\SettingsAdmin;
use Yii;
use common\models\Dishes;
use common\models\DishesSearch;
use common\models\DishesProducts;
use common\models\BruttoNettoKoef;
use common\models\Products;
use common\models\ProductsCategory;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\db\Expression;

use yii\helpers\Html;
use yii\filters\VerbFilter;

/**
 * DishesController implements the CRUD actions for Dishes model.
 */
class DishesController extends Controller
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
        $searchModel = new DishesSearch();
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

    public function actionIndexAdmin()
    {
        $searchModel = new DishesSearch();
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

    public function actionDishesBase()
    {
        $searchModel = new DishesSearch();
        $search = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($search);
        /*$dataProvider = new ActiveDataProvider([
            'query' => Dishes::find(),
        ]);*/

        return $this->render('dishes-base', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionCreate()
    {
        $model = new Dishes();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', "Блюдо успешно создано");
            if (Yii::$app->user->can('admin'))
            {
                return $this->redirect(['index-admin']);
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', "Блюдо успешно обновлено");
            if (Yii::$app->user->can('admin'))
            {
                return $this->redirect(['index-admin']);
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function actionDelete($id)
    {
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $id])->all();
        foreach ($dishes_products as $d_product)
        {
            $d_product->delete();
        }
        $menus_dishes = MenusDishes::find()->where(['dishes_id' => $id])->all();
        foreach ($menus_dishes as $m_dish)
        {
            $m_dish->delete();
        }
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "Блюдо успешно удалено из базы данных и из всех меню");

        return $this->redirect(Yii::$app->request->referrer);
    }


    protected function findModel($id)
    {
        if (($model = Dishes::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddproduct($id)
    {
        $model = new DishesProducts();
        $dataProvider = new ActiveDataProvider([
            'query' => DishesProducts::find()->where(['dishes_id' => $id])->orderBy('id'),
        ]);

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['DishesProducts'];
            $model->dishes_id = $id;
            $model->products_id = $post['products_id'];
            $model->net_weight = $post['net_weight'];
            $model->gross_weight = $post['gross_weight'];
            $model->save();

            return $this->redirect(['addproduct?id=' . $id]);
        }

        return $this->render('addproduct', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCopyDish($id)
    {
        $dish = Dishes::findOne($id);

        $s_ad = SettingsAdmin::find()->one();
        $recipe_collection = $s_ad->recipe_id;
        $yield = $s_ad->yield;

        $new_dish = new Dishes();
        $new_dish->attributes = $dish->attributes;
        $new_dish->recipes_collection_id = $recipe_collection;
        $new_dish->yield = $yield;
        $new_dish->created_at = new Expression('NOW()');
        if ($new_dish->save())
        {
            $dishes_products = DishesProducts::find()->where(['dishes_id' => $id])->all();;
            foreach ($dishes_products as $d_product)
            {
                $model = new DishesProducts();
                //$model->attributes = $d_product->attributes;
                $model->products_id = $d_product->products_id;
                $model->dishes_id = $new_dish->id;
                $model->net_weight = ($yield * $d_product->net_weight) / $dish->yield;
                $model->gross_weight = ($yield * $d_product->gross_weight) / $dish->yield;
                $model->created_at = new Expression('NOW()');
                $model->save();
                /*print_r($model);
                exit;*/
            }
        }
        else
        {
            Yii::$app->session->setFlash('error', "Ошибка сохранения. Блюдо не дублировано в новый сборник");
            return $this->redirect(Yii::$app->request->referrer);
        }

        /*print_r($dishes_products);
        exit;*/

        Yii::$app->session->setFlash('success', "Блюдо успешно дублировано в новый сборник");
        return $this->redirect(Yii::$app->request->referrer);

    }

    public function actionDeleteProduct($id)
    {
        $model = DishesProducts::findOne($id);
        $model->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSearchfulltext()
    {
        $json = array();
        $json[] = array();
        $e = Yii::$app->request->post()['e'];
        $products = Products::find()->where(['like', 'name', $e])->all();

        $field = array();
        foreach ($products as $product)
        {
            $field[] = array('id' => $product->id, 'name' => $product->name);
        }
        $result = array("field" => $field);
        return json_encode($result);
    }

    public function actionBruttoNettoCount($id)
    {
        $dishes_products = DishesProducts::find()->where(['dishes_id' => $id])->all();

        foreach ($dishes_products as $d_product)
        {
            $products_category = Products::find()->where(['id' => $d_product->products_id])->one();
            $products_category = $products_category->products_category_id;
            /*КЛЮЧЕВОЕ СЛОВО ALL() ПОТОМУ ЧТО В 1ОМ БЛЮДЕ МОГУТ БЫТЬ 2 ИЛИ БОЛЕЕ ОДИНАКОВЫХ ПРОДУКТОВ, ПОЭТОМУ ALL()*/
            $model = DishesProducts::find()->where(['dishes_id' => $id, 'products_id' => $d_product->products_id])->all();
            foreach ($model as $mod)
            {
                $koef = BruttoNettoKoef::find()->where(['products_category_id' => $products_category, 'products_id' => $d_product->products_id])->one()->koeff_netto;
                //Если есть исключение по брутто/нетто по конкретному продукту, то расчитываем по исключению, иначе без исключения в елсе
                if (!empty($koef))
                {
                    $mod->gross_weight = $mod->net_weight * $koef;
                    $mod->save();
                }
                else
                {
                    $koef = BruttoNettoKoef::find()->where(['products_category_id' => $products_category, 'products_id' => 0])->one()->koeff_netto;
                    $mod->gross_weight = $mod->net_weight * $koef;
                    $mod->save();
                }

            }
        }
        Yii::$app->session->setFlash('success', "Выход в брутто пересчитан.");
        return $this->redirect(['dishes/addproduct?id=' . $id]);

    }
}
