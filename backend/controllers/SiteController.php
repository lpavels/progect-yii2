<?php

namespace backend\controllers;

use common\models\AnketParentControl;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\DepartmentLogAuth;
use common\models\FederalDistrict;
use common\models\Kids;
use common\models\KidsQ;
use common\models\Menus;
use common\models\Municipality;
use common\models\News;
use common\models\NewsAccess;
use common\models\OrganizationEdu20;
use common\models\ProductsChangeOrganization;
use common\models\RecipesCollection;
use common\models\ReportTbl21;
use common\models\ReportTbl20;
use common\models\ReportTbl20Edu20;
use common\models\ReportTbl22;
use common\models\TrainingProgram;
use common\models\UserEdu20;
use Mpdf\Mpdf;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\SignupForm;
use common\models\User;
use common\models\Organization;
use common\models\Region;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'login', 'logout', 'error', 'signup', 'subjectslist', 'municipalitylist', 'organization-name', 'listen-type', 'training-program', 'listener', 'welcome', 'export', 'faq'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'login', 'logout', 'index', 'select-organization', 'error', 'faq', 'transferring-users-and-del-organization'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->can('admin'))
        {
            $news = News::find()->orderBy(['fix' => SORT_DESC, 'updated_at' => SORT_DESC])->all();
        }
        else
        {
            $u_role = Yii::$app->user->identity->role->name;
            if (!$u_role)
            {
                $u_role = 'guest';
            }
            $newsAccesses = NewsAccess::find()->where(['role' => $u_role])->all();

            $news_ids = [];
            foreach ($newsAccesses as $newsAccess)
            {
                $news_ids[] = $newsAccess->news_id;
            }

            $news = News::find()->where(['id' => $news_ids])->orderBy(['fix' => SORT_DESC, 'updated_at' => SORT_DESC])->all();
        }

        return $this->render('index', [
            'news' => $news
        ]);
    }

    public function actionFaq()
    {
        return $this->render('faq');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest)
        {
            return $this->redirect(['/site/index']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            $user_training_id = Yii::$app->user->identity->training_id;
            $training_id = [3, 4, 5, 6];
            if (in_array($user_training_id, $training_id))
            {
                $DepartmentLogAuth_model = new DepartmentLogAuth();
                $DepartmentLogAuth_model->user_id = Yii::$app->user->id;
                $DepartmentLogAuth_model->auth_ip = Yii::$app->userHelp->ip();
                $DepartmentLogAuth_model->save();
            }

            return $this->redirect(['/site/index']);
        }
        else
        {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->redirect(['/site/index']);
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        //$this->layout = false;
        $model = new SignupForm();

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post();

            if ($post['SignupForm']['check'] != 1 || $post['SignupForm']['check2'] != 1)
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Не получено согласие на обработку персональных данных или не подтверждено правильность введенных данных");
                return $this->goHome();
            }

            $f = $post['federal_district_id'];
            if (empty(FederalDistrict::findOne($f)))
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Федеральный округ не определен");
                return $this->goHome();
            }

            $r = $post['region_id'];
            if (empty(Region::findOne($r)))
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Субъект федерации не определен");
                return $this->goHome();
            }
            if (empty(Region::find()->where(['district_id' => $f])->one()))
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Субъект федерации не сопоставлен с федеральным округом");
                return $this->goHome();
            }

            $m = $post['municipality'];
            if (empty(Municipality::findOne($m)))
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Муниципальное образование не определено");
                return $this->goHome();
            }
            if (empty(Municipality::find()->where(['region_id' => $r])->one()))
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Муниципальное образование не сопоставлено с субъектом федерации");
                return $this->goHome();
            }

            $org = Organization::find()->where(['federal_district_id' => $f, 'region_id' => $r, 'municipality_id' => $m, 'status' => 1])->one();
            if (empty($org))
            {
                Yii::$app->session->setFlash('error', "Ошибка регистрации. Организация не определена или более не активна");
                return $this->goHome();
            }

            $t = $org->type_org;
            //$AuthItem = AuthItem::find()->where(['name'=>$post['listener_type']])->andWhere(['name'=>['kid','medic','nutrition_specialist','nyanya','otherwise','parent','school14','school511','student','teacher','tutor','vospitatel','vospitatel_help']])->one();
            $AuthItem = AuthItem::find()->where(['name' => $post['listener_type']])->one();
            if (empty($AuthItem))
            {
                Yii::$app->session->setFlash('error', "Произошла ошибка с определением роли.");
                return $this->goHome();
            }

            $class = $post['SignupForm']['class'];
            $bukva_klassa = $post['SignupForm']['bukva_klassa'];
            if (($AuthItem->name == 'school14' || $AuthItem->name == 'school511') && ($class == '' || $bukva_klassa == ''))
            {
                Yii::$app->session->setFlash('error', "Произошла ошибка. Не заполнен класс или буква класса.");
                return $this->goHome();
            }

            $user = new User();
            $key = $user->func_rand($f, $r, $m, $t);
            $user->key_login = $key;
            $user->training_id = $post['training_id'];
            $user->organization_id = $post['name_organization'];
            $user->name = $post['SignupForm']['name'];
            $user->year_birth = $post['SignupForm']['year_birth'];
            $user->class = $class;
            $user->bukva_klassa = $bukva_klassa;
            $user->type_listener = $AuthItem->description;
            if ($AuthItem->name == 'kid' || $AuthItem->name == 'school14' || $AuthItem->name == 'school511' || $AuthItem->name == 'student')
            {
                $user->type_training = 1; //1-ребенок
            }
            elseif ($AuthItem->name == 'medic' || $AuthItem->name == 'nutrition_specialist' || $AuthItem->name == 'nyanya' || $AuthItem->name == 'otherwise' || $AuthItem->name == 'parent' || $AuthItem->name == 'teacher' || $AuthItem->name == 'tutor' || $AuthItem->name == 'vospitatel' || $AuthItem->name == 'vospitatel_help')
            {
                $user->type_training = 2; //2-взрослый
            }
            else
            {
                print_r('Не удалось определить тип слутеля. Напишите на электронную почту edu@niig.su указав выбранный тип слушателя. ' . $post['listener_type']);
                die();
            }
            $user->transfer = date("Y");
            $user->version = 2;
            $user->status = 10;

            if ($user->save())
            {
                $AuthItemDb = new DbManager();
                $AuthItemDb->init();
                $assign = $AuthItemDb->createRole($AuthItem->name);
                $AuthItemDb->assign($assign, $user->id);

                Yii::$app->session->setFlash('success', "Регистрация прошла успешна. Ваш ключ для входа в программу: " . $key);
                $this->redirect(['/site/welcome?id=' . $user->key_login]);
            }
            else
            {
                $user->delete();
                Yii::$app->session->setFlash('error', "Ошибка сохранения пользователя. Пользователь не был зарегистрирован");
                return $this->goHome();
            }
        }

        $districs = FederalDistrict::find()->all();
        $district_items = ArrayHelper::map($districs, 'id', 'name');

        return $this->render('signup', [
            'model' => $model,
            'district_items' => $district_items,
        ]);
    }

    public function actionWelcome($id)
    {
        return $this->render('welcome', [
            'id' => $id,
        ]);
    }

    public function actionExport($id)
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $queru = User::find()->where(['key_login' => $id])->one();
        $training = TrainingProgram::find()->where(['id' => $queru->training_id])->one()->name;
        $organization = Organization::find()->where(['id' => $queru->organization_id])->one();
        $District = FederalDistrict::find()->where(['id' => $organization->federal_district_id])->one();
        $Region = Region::find()->where(['id' => $organization->region_id])->one();
        $Municipality = Municipality::find()->where(['id' => $organization->municipality_id])->one();

        $html = '
            <h3 align="center">Личные данные:</h3>
            <p><b>Уникальный номер:</b> ' . $queru->key_login . '</p>
            <p><b>Программа обучения:</b> ' . $training . '</p>
            <p><b>ФИО:</b> ' . $queru->name . ',  ' . $queru->year_birth . ' года рождения</p>
            <p><b>Организация:</b> ' . $organization->title . '</p>
            <p><b>Федеральный округ:</b> ' . $District->name . '</p>
            <p><b>Регион:</b> ' . $Region->name . '</p>
            <p><b>Муниципальное образование:</b> ' . $Municipality->name . '</p>
            <p><b>Дата регистрации:</b> ' . date('d.m.Y', $queru->created_at) . '</p>';

        $mpdf = new Mpdf (['margin_left' => '15', 'margin_right' => '5', 'margin_top' => '10', 'margin_bottom' => '5']);;
        $mpdf->WriteHTML($html);
        $mpdf->defaultfooterline = 1;
        $mpdf->setFooter('<div>Разработчик: "ФБУН Новосибирский НИИ гигиены Роспотребнадзора"</div>'); //номер страницы {PAGENO}
        $mpdf->Output($queru->key_login . '.pdf', 'D'); //D - скачает файл!
    }

    /*Подставляет регионы в выпадающий список*/
    public function actionSubjectslist($id)
    {
        $groups = Region::find()->where(['district_id' => $id])->orderby(['name' => SORT_ASC])->all();

        $data = [];
        $data[] = '<option value="">Выберите регион...</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $data[] = '<option value="' . $group->id . '">' . $group->name . '</option>';
            }
        }

        print_r($data);
        //return $data;
    }

    /*Подставляет муниципальные образования в выпадающий список*/
    public function actionMunicipalitylist($id)
    {
        $groups = Municipality::find()->where(['region_id' => $id])->orderby(['name' => SORT_ASC])->all();

        $data = [];
        $data[] = '<option value="">Выберите муниципальное образование...</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $data[] = '<option value="' . $group->id . '">' . $group->name . '</option>';
            }
        }
        print_r($data);
    }

    /*Подставляет организации в выпадающий список*/
    public function actionOrganizationName($id)
    {
        $groups = Organization::find()->where(['municipality_id' => $id, 'status' => 1])->orderby(['type_org' => SORT_ASC, 'title' => SORT_ASC])->all();

        $data = [];
        $data[] = '<option value="">Выберите Вашу организацию...</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $data[] = '<option value="' . $group->id . '">' . $group->short_title . '</option>';
            }
        }
        else
        {
            $data[] = '<option value="">Организации не добавлены.</option>';
        }
        print_r($data);
    }

    /*Подставляет тип слушателя в выпадающий список*/
    public function actionListenType($id)
    {
        $type_org = Organization::findOne($id)->type_org;

        if ($type_org == 1)
        {
            //$groups = AuthItem::find()->where(['name' => ['school14', 'school56', 'school79', 'school1011', 'parent', 'teacher', 'tutor', 'medic', 'nyanya', 'nutrition_specialist', 'vospitatel', 'vospitatel_help', 'otherwise']])->orderBy(['name' => SORT_ASC])->all();
            $groups = array(
                'school14' => 'Обучающийся 1-4 классов',
                'school511' => 'Обучающийся 5-11 классов',
                'teacher' => 'Педагог',
                'tutor' => 'Тьютор',
                'parent' => 'Родитель (законный представитель)',
                'nutrition_specialist' => 'Специалист по питанию (повар, зав. производством и.т.д.)',
                'medic' => 'Медицинский работник',
                'vospitatel' => 'Воспитатель',
                'vospitatel_help' => 'Помощник воспитателя',
                'nyanya' => 'Няня (неработающая в дошкольной организации)',
                'otherwise' => 'Иное',
            );
        }
        elseif ($type_org == 2)
        {
            //$groups = AuthItem::find()->where(['name' => ['medic', 'vospitatel', 'vospitatel_help', 'nyanya', 'parent', 'tutor', 'vospitatel_help', 'otherwise']])->orderBy(['name' => SORT_ASC])->all();
            $groups = array(
                'tutor' => 'Тьютор',
                'parent' => 'Родитель (законный представитель)',
                'nutrition_specialist' => 'Специалист по питанию (повар, зав. производством и.т.д.)',
                'medic' => 'Медицинский работник',
                'vospitatel' => 'Воспитатель',
                'vospitatel_help' => 'Помощник воспитателя',
                'nyanya' => 'Няня (неработающая в дошкольной организации)',
                'otherwise' => 'Иное',
            );
        }
        elseif ($type_org == 3)
        {
            //$groups = AuthItem::find()->where(['name' => ['medic', 'kid', 'school14', 'school56', 'school79', 'school1011', 'student', 'teacher', 'tutor', 'parent', 'vospitatel', 'vospitatel_help', 'nutrition_specialist', 'otherwise']])->orderBy(['name' => SORT_ASC])->all();
            $groups = array(
                'school14' => 'Обучающийся 1-4 классов',
                'school511' => 'Обучающийся 5-11 классов',
                'student' => 'Студент',
                'teacher' => 'Педагог',
                'tutor' => 'Тьютор',
                'parent' => 'Родитель (законный представитель)',
                'nutrition_specialist' => 'Специалист по питанию (повар, зав. производством и.т.д.)',
                'medic' => 'Медицинский работник',
                'vospitatel' => 'Воспитатель',
                'vospitatel_help' => 'Помощник воспитателя',
                'nyanya' => 'Няня (неработающая в дошкольной организации)',
                'otherwise' => 'Иное',
            );
        }
        elseif ($type_org == 4)
        {
            //$groups = AuthItem::find()->where(['name' => ['medic', 'kid', 'teacher', 'tutor', 'parent', 'nutrition_specialist', 'otherwise']])->orderBy(['name' => SORT_ASC])->all();
            $groups = array(
                'kid' => 'Ребенок',
                'teacher' => 'Педагог',
                'tutor' => 'Тьютор',
                'nutrition_specialist' => 'Специалист по питанию (повар, зав. производством и.т.д.)',
                'medic' => 'Медицинский работник',
                'otherwise' => 'Иное',
            );
        }
        elseif ($type_org == 5)
        {
            //$groups = AuthItem::find()->where(['name' => ['medic', 'tutor', 'nutrition_specialist', 'otherwise']])->orderBy(['name' => SORT_ASC])->all();
            $groups = array(
                'kid' => 'Ребенок',
                'tutor' => 'Тьютор',
                'nutrition_specialist' => 'Специалист по питанию (повар, зав. производством и.т.д.)',
                'medic' => 'Медицинский работник',
                'otherwise' => 'Иное',
            );
        }
        elseif ($type_org == 6 || $type_org == 7 || $type_org == 8 || $type_org == 9)
        {
            //$groups = AuthItem::find()->where(['name' => ['medic', 'kid', 'teacher', 'tutor', 'parent', 'nutrition_specialist', 'otherwise']])->orderBy(['name' => SORT_ASC])->all();
            $groups = array(
                'kid' => 'Ребенок',
                'teacher' => 'Педагог',
                'tutor' => 'Тьютор',
                'nutrition_specialist' => 'Специалист по питанию (повар, зав. производством и.т.д.)',
                'medic' => 'Медицинский работник',
                'otherwise' => 'Иное',
            );
        }

        $data = [];
        $data[] = '<option value="">Выберите тип слушателя...</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $data[] = '<option value="' . $key . '">' . $group . '</option>';
            }
        }
        print_r($data);
    }

    /*Подставляет обучающую программы в выпадающий список*/
    public function actionTrainingProgram($name)
    {
        if ($name == 'school14' || $name == 'school511')
        {
            $groups = TrainingProgram::find()->where(['id' => 1])->all();
        }
        else
        {
            $groups = TrainingProgram::find()->all();
        }

        $data = [];
        if ($name != '0')
        {
            $data[] = '<option value="">Выберите обучающую прогрумму...</option>';
            if (!empty($groups))
            {
                foreach ($groups as $key => $group)
                {
                    $data[] = '<option value="' . $group->id . '">' . $group->name . '</option>';
                }
            }
        }
        else
        {
            $data[] = '<option value="">Выберите обучающую прогрумму...</option>';
        }

        print_r($data);
    }

}
