<?php

namespace backend\controllers;

use common\models\AuthAssignment;
use common\models\DepartmentEducation;
use Yii;
use common\models\Organization;
use common\models\User;
use common\models\OrganizationSearch;
use yii\rbac\DbManager;
use yii\web\Controller;


class OrganizationsController extends Controller
{
    public function actionCreateOrg()
    {
        $model = new Organization();
        if (Yii::$app->user->can('RPN_mun') || Yii::$app->user->can('RPN'))
        {
            if (Yii::$app->request->post())
            {
                $post = Yii::$app->request->post()['Organization'];
                if ($post['title'] == '' || $post['short_title'] == '' || $post['address'] == '' || $post['type_org'] == '')
                {
                    Yii::$app->session->setFlash('error', "Организация не добавлена, необходимо заполнить все поля");
                    return $this->redirect(['create-org']);
                }
                else
                {
                    if (Yii::$app->user->can('RPN_mun'))
                    {
                        $data = DepartmentEducation::find()->where(['key_login_departament' => Yii::$app->user->identity->key_login])->one();
                        if (empty($data))
                        {
                            $data = DepartmentEducation::find()->where(['key_login_ministry_education' => Yii::$app->user->identity->key_login])->one();
                        }
                    }
                    elseif (Yii::$app->user->can('RPN'))
                    {
                        $data = DepartmentEducation::find()->where(['key_login_rpn' => Yii::$app->user->identity->key_login])->one();
                        if (empty($data))
                        {
                            $data = DepartmentEducation::find()->where(['key_login_ministry_education' => Yii::$app->user->identity->key_login])->one();
                        }
                    }

                    $model->federal_district_id = $data->district_id;
                    $model->region_id = $data->region_id;
                    if (Yii::$app->user->can('RPN_mun'))
                    {
                        $model->municipality_id = $data->municipality_id;
                    }
                    elseif (Yii::$app->user->can('RPN'))
                        $model->municipality_id = $post['municipality_id'];

                    $model->title = $post['title'];
                    $model->short_title = $post['short_title'];
                    $model->address = $post['address'];
                    $model->type_org = $post['type_org'];
                    $model->status = 1;
                    $model->created_user_org_id = Yii::$app->user->id;
                    $model->save(false);

                    Yii::$app->session->setFlash('success', "Данные сохранены");
                    return $this->redirect(['searcho']);
                }
            }
        }
        else return $this->goHome();


        return $this->render('create-org', [
            'model' => $model,
        ]);
    }

    public function actionSearcho()
    {
        $item_name = AuthAssignment::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->item_name;
        $item_arr = ['RPN_mun', 'RPN', 'admin'];
        $key = in_array($item_name, $item_arr);

        if (empty($key))
        {
            return $this->goHome();
        }

        $searchModel = new OrganizationSearch();
        $model = new Organization();
        $search = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->search($search);

        return $this->render('searcho', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    public function actionAddDirector($id)
    {
        $item_name = AuthAssignment::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->item_name;
        $item_arr = ['RPN_mun', 'RPN', 'admin'];
        $key = in_array($item_name, $item_arr);

        if (empty($key))
        {
            return $this->goHome();
        }

        if (!empty($id))
        {
            //$org = Organization::find()->select(['short_title', 'federal_district_id', 'region_id', 'municipality_id'])->where(['id' => $id])->one();
            $organization = Organization::findOne($id);
            if (!is_null($organization->dir_keylogin))
            {
                Yii::$app->session->setFlash('error', "Руководитель организации был зарегистрирован ранее.");
                return $this->redirect(Yii::$app->request->referrer);
            }
            $f = $organization->federal_district_id;
            $r = $organization->region_id;
            $m = $organization->municipality_id;
            $t = 'dir';

            $user = new User();
            $key = $user->func_rand($f, $r, $m, $t);
            $user->key_login = $key;
            $user->training_id = 3;
            $user->organization_id = $id;
            $user->year_birth = 0;
            $user->name = $organization['short_title'] . '. Директор';
            $user->status = 10;
            $user->certificate = 0;

            $user->type_listener = 'Руководитель организации';
            $user->type_training = 10;

            if ($user->save())
            {
                $organization->dir_keylogin = $key;
                $organization->created_dir_user_id = Yii::$app->user->id;

                if ($organization->save(false))
                {
                    $r = new DbManager();
                    $r->init();
                    $assign = $r->createRole('director');
                    $r->assign($assign, $user->id);

                    Yii::$app->session->setFlash('success', "Руководитель зарегистрирован. Идентификационный номер для входа: " . $key);
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }
    }

}