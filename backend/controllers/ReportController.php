<?php

namespace backend\controllers;

use common\models\DepartmentEducation;
use common\models\FederalDistrict;
use common\models\Municipality;
use common\models\Organization;
use common\models\Region;
use common\models\Report;
use common\models\ReportTbl22;
use common\models\ReportTbl21;
use common\models\ReportTbl20;
use common\models\SportsSectionName;
use common\models\ThemeProgram;
use common\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;

class ReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /* отчеты (2021-2022) */
    public function actionRepDirector() //1 - 2020г, 0 - 2021г
    {
        if (!Yii::$app->user->can('director'))
        {
            return $this->goHome();
        }

        $model = new Organization();
        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Organization']['title'];
            if ($post == 2022)
            {
                $themes = ThemeProgram::find()
                    ->select(
                        [
                            'theme_program.id as id',
                            'theme_program.name as name',
                            'theme_program.short_name as short_name',
                            'training_themes.training_program_id as training_program_id',
                            'training_themes.theme_program_id as theme_program_id',
                            'training_themes.sort as sort'
                        ]
                    )
                    ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                    ->where(['training_program_id' => 1])
                    ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

                $organization_id = Organization::find()->select('id')->where(
                    ['id' => Yii::$app->user->identity->organization_id]
                )->one()->id;
                $data = ReportTbl22::find()->where(
                    ['organization_id' => $organization_id, 'training_id' => 1]
                )->orderBy(
                    ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                )->all();

                return $this->render(
                    'rep-director',
                    [
                        'model' => $model,
                        'themes' => $themes,
                        'post' => $post,
                        'data' => $data,
                        'show' => '1',
                    ]
                );
            }
            elseif ($post == 2021)
            {
                $themes = ThemeProgram::find()
                    ->select(
                        [
                            'theme_program.id as id',
                            'theme_program.name as name',
                            'theme_program.short_name as short_name',
                            'training_themes.training_program_id as training_program_id',
                            'training_themes.theme_program_id as theme_program_id',
                            'training_themes.sort as sort'
                        ]
                    )
                    ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                    ->where(['training_program_id' => 1])
                    ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

                $organization_id = Organization::find()->select('id')->where(
                    ['id' => Yii::$app->user->identity->organization_id]
                )->one()->id;
                $data = ReportTbl21::find()->where(
                    ['organization_id' => $organization_id, 'training_id' => 1]
                )->orderBy(
                    ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                )->all();

                return $this->render(
                    'rep-director',
                    [
                        'model' => $model,
                        'themes' => $themes,
                        'post' => $post,
                        'data' => $data,
                        'show' => '1',
                    ]
                );
            }
            elseif ($post == 2020)
            {
                $themes = ThemeProgram::find()
                    ->select(
                        [
                            'theme_program.id as id',
                            'theme_program.name as name',
                            'theme_program.short_name as short_name',
                            'training_themes.training_program_id as training_program_id',
                            'training_themes.theme_program_id as theme_program_id',
                            'training_themes.sort as sort'
                        ]
                    )
                    ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                    ->where(['training_program_id' => 1])
                    ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

                $organization_id = Organization::find()->select('id')->where(
                    ['id' => Yii::$app->user->identity->organization_id]
                )->one()->id;
                $data = ReportTbl20::find()->where(
                    ['organization_id' => $organization_id, 'training_id' => 1]
                )->orderBy(
                    ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                )->all();

                return $this->render(
                    'rep-director',
                    [
                        'model' => $model,
                        'themes' => $themes,
                        'post' => $post,
                        'data' => $data,
                        'show' => '1',
                    ]
                );
            }
        }
        return $this->render(
            'rep-director',
            [
                'model' => $model,
            ]
        );
    }

    public function actionRepDirectorP()
    {
        if (!Yii::$app->user->can('director'))
        {
            return $this->goHome();
        }

        $model = new Organization();
        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Organization']['title'];
            if ($post == 2022)
            {
                $themes = ThemeProgram::find()
                    ->select(
                        [
                            'theme_program.id as id',
                            'theme_program.name as name',
                            'theme_program.short_name as short_name',
                            'training_themes.training_program_id as training_program_id',
                            'training_themes.theme_program_id as theme_program_id',
                            'training_themes.sort as sort'
                        ]
                    )
                    ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                    ->where(['training_program_id' => 2])
                    ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

                $organization_id = Organization::find()->select('id')->where(
                    ['id' => Yii::$app->user->identity->organization_id]
                )->one()->id;
                $data = ReportTbl22::find()->where(
                    ['organization_id' => $organization_id, 'training_id' => 2]
                )->orderBy(
                    ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                )->all();

                return $this->render(
                    'rep-director-p',
                    [
                        'model' => $model,
                        'themes' => $themes,
                        'post' => $post,
                        'data' => $data,
                        'show' => '1',
                    ]
                );
            }
            elseif ($post == 2021)
            {
                $themes = ThemeProgram::find()
                    ->select(
                        [
                            'theme_program.id as id',
                            'theme_program.name as name',
                            'theme_program.short_name as short_name',
                            'training_themes.training_program_id as training_program_id',
                            'training_themes.theme_program_id as theme_program_id',
                            'training_themes.sort as sort'
                        ]
                    )
                    ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                    ->where(['training_program_id' => 2])
                    ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

                $organization_id = Organization::find()->select('id')->where(
                    ['id' => Yii::$app->user->identity->organization_id]
                )->one()->id;
                $data = ReportTbl21::find()->where(
                    ['organization_id' => $organization_id, 'training_id' => 2]
                )->orderBy(
                    ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                )->all();

                return $this->render(
                    'rep-director-p',
                    [
                        'model' => $model,
                        'themes' => $themes,
                        'post' => $post,
                        'data' => $data,
                        'show' => '1',
                    ]
                );
            }
            elseif ($post == 2020)
            {
                $themes = ThemeProgram::find()
                    ->select(
                        [
                            'theme_program.id as id',
                            'theme_program.name as name',
                            'theme_program.short_name as short_name',
                            'training_themes.training_program_id as training_program_id',
                            'training_themes.theme_program_id as theme_program_id',
                            'training_themes.sort as sort'
                        ]
                    )
                    ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                    ->where(['training_program_id' => 2])
                    ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

                $organization_id = Organization::find()->select('id')->where(
                    ['id' => Yii::$app->user->identity->organization_id]
                )->one()->id;
                $data = ReportTbl20::find()->where(
                    ['organization_id' => $organization_id, 'training_id' => 2]
                )->orderBy(
                    ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                )->all();

                return $this->render(
                    'rep-director-p',
                    [
                        'model' => $model,
                        'themes' => $themes,
                        'post' => $post,
                        'data' => $data,
                        'show' => '1',
                    ]
                );
            }
        }
        return $this->render(
            'rep-director-p',
            [
                'model' => $model,
            ]
        );
    }

    public function actionRepEducationDep() //школьная программа
    {
        if (!Yii::$app->user->can('RPN_mun'))
        {
            throw new HttpException(123, 'Доступ запрещён');
        }

        $model = new Organization();
        $depEdu = DepartmentEducation::findOne(['key_login_departament' => Yii::$app->user->identity->key_login]);
        if (empty($depEdu))
        {
            $depEdu = DepartmentEducation::findOne(
                ['key_login_ministry_education' => Yii::$app->user->identity->key_login]
            );
        }

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Organization'];
            $themes = ThemeProgram::find()
                ->select(
                    [
                        'theme_program.id as id',
                        'theme_program.name as name',
                        'theme_program.short_name as short_name',
                        'training_themes.training_program_id as training_program_id',
                        'training_themes.theme_program_id as theme_program_id',
                        'training_themes.sort as sort'
                    ]
                )
                ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                ->where(['training_program_id' => 1])
                ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

            if ($post['title'] == 2022)
            {
                if ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl22::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-education-dep',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0)
                { //для всех организаций в муниципальном
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $depEdu->municipality_id])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-education-dep',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2021)
            {
                if ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl21::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-education-dep',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0)
                { //для всех организаций в муниципальном
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $depEdu->municipality_id])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-education-dep',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2020)
            {
                if ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl20::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-education-dep',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $depEdu->municipality_id])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-education-dep',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
        }

        return $this->render(
            'rep-education-dep',
            [
                'depEdu' => $depEdu,
                'model' => $model,
            ]
        );
    }

    public function actionRepEducationDepP() //дошкольная программа
    {
        if (!Yii::$app->user->can('RPN_mun'))
        {
            throw new HttpException(123, 'Доступ запрещён');
        }

        $model = new Organization();
        $depEdu = DepartmentEducation::findOne(['key_login_departament' => Yii::$app->user->identity->key_login]);
        if (empty($depEdu))
        {
            $depEdu = DepartmentEducation::findOne(
                ['key_login_ministry_education' => Yii::$app->user->identity->key_login]
            );
        }

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Organization'];
            $themes = ThemeProgram::find()
                ->select(
                    [
                        'theme_program.id as id',
                        'theme_program.name as name',
                        'theme_program.short_name as short_name',
                        'training_themes.training_program_id as training_program_id',
                        'training_themes.theme_program_id as theme_program_id',
                        'training_themes.sort as sort'
                    ]
                )
                ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                ->where(['training_program_id' => 2])
                ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

            if ($post['title'] == 2022)
            {
                if ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl22::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-education-dep-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $depEdu->municipality_id])
                        ->orderBy(['type_org' => SORT_DESC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-education-dep-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2021)
            {
                if ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl21::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-education-dep-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $depEdu->municipality_id])
                        ->orderBy(['type_org' => SORT_DESC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-education-dep-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2020)
            {
                if ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl20::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-education-dep-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $depEdu->municipality_id])
                        ->orderBy(['type_org' => SORT_DESC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-education-dep-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
        }

        return $this->render(
            'rep-education-dep-p',
            [
                'depEdu' => $depEdu,
                'model' => $model,
            ]
        );
    }

    public function actionRepRpn() //школьная программа
    {
        if (!Yii::$app->user->can('RPN'))
        {
            return $this->goHome();
        }

        $model = new Organization();
        $depEdu = DepartmentEducation::findOne(['key_login_rpn' => Yii::$app->user->identity->key_login]);
        if (!isset($depEdu))
        {
            $depEdu = DepartmentEducation::findOne(
                ['key_login_ministry_education' => Yii::$app->user->identity->key_login]
            );
        }

        if (Yii::$app->request->post())
        {
            $themes = ThemeProgram::find()
                ->select(
                    [
                        'theme_program.id as id',
                        'theme_program.name as name',
                        'theme_program.short_name as short_name',
                        'training_themes.training_program_id as training_program_id',
                        'training_themes.theme_program_id as theme_program_id',
                        'training_themes.sort as sort'
                    ]
                )
                ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                ->where(['training_program_id' => 1])
                ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

            $post = Yii::$app->request->post()['Organization'];

            /*-------------------------------------------------------------*/

            //$data1 = ReportTbl21::find()->where(['region_id' => 48, 'training_id' => 1])->andWhere(['!=','type_listener','Воспитатель'])->andWhere(['!=','type_listener','Иное'])->andWhere(['!=','type_listener','Медицинский работник'])->andWhere(['!=','type_listener','Няня (неработающая в дошкольной организации)'])->andWhere(['!=','type_listener','Обучающийся 1-4 классов'])->andWhere(['!=','type_listener','Педагог'])->andWhere(['!=','type_listener','Помощник воспитателя'])->andWhere(['!=','type_listener','Родитель (законный представитель)'])->andWhere(['!=','type_listener','Специалист по питанию (повар, зав. производством и.т.д.)'])->andWhere(['!=','type_listener','Тьютор'])->andWhere(['!=','type_listener','Ребенок'])->andWhere(['!=','type_listener','Студент'])->orderBy(['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,])->all();
            //$data2 = ReportTbl20::find()->where(['region_id' => 48, 'training_id' => 1])->andWhere(['!=','type_listener','Воспитатель'])->andWhere(['!=','type_listener','Иное'])->andWhere(['!=','type_listener','Медицинский работник'])->andWhere(['!=','type_listener','Няня (неработающая в дошкольной организации)'])->andWhere(['!=','type_listener','Обучающийся 1-4 классов'])->andWhere(['!=','type_listener','Педагог'])->andWhere(['!=','type_listener','Помощник воспитателя'])->andWhere(['!=','type_listener','Родитель (законный представитель)'])->andWhere(['!=','type_listener','Специалист по питанию (повар, зав. производством и.т.д.)'])->andWhere(['!=','type_listener','Тьютор'])->andWhere(['!=','type_listener','Ребенок'])->andWhere(['!=','type_listener','Студент'])->orderBy(['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,])->all();
            //$data = array_merge($data1,$data2);

            //return $this->render('rep-rpn', [
            //    'depEdu' => $depEdu,
            //    'model' => $model,
            //    'themes' => $themes,
            //    'post' => $post,
            //    'data' => $data,
            //    'org' => '1',
            //]);

            /*-------------------------------------------------------------*/

            if ($post['title'] == 2022)
            {
                if ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $depEdu->region_id])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl22::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2021)
            {
                if ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $depEdu->region_id])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl21::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2020)
            {
                if ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $depEdu->region_id])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl20::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-rpn',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
        }

        return $this->render(
            'rep-rpn',
            [
                'depEdu' => $depEdu,
                'model' => $model,
            ]
        );
    }

    public function actionRepRpnP() //дошкольная программа
    {
        ini_set('max_execution_time', 3600);
        if (!Yii::$app->user->can('RPN'))
        {
            throw new HttpException(123, 'Доступ запрещён');
        }

        $model = new Organization();
        $depEdu = DepartmentEducation::findOne(['key_login_rpn' => Yii::$app->user->identity->key_login]);
        if (!isset($depEdu))
        {
            $depEdu = DepartmentEducation::findOne(
                ['key_login_ministry_education' => Yii::$app->user->identity->key_login]
            );
        }

        if (Yii::$app->request->post())
        {
            $themes = ThemeProgram::find()
                ->select(
                    [
                        'theme_program.id as id',
                        'theme_program.name as name',
                        'theme_program.short_name as short_name',
                        'training_themes.training_program_id as training_program_id',
                        'training_themes.theme_program_id as theme_program_id',
                        'training_themes.sort as sort'
                    ]
                )
                ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                ->where(['training_program_id' => 2])
                ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

            $post = Yii::$app->request->post()['Organization'];
            if ($post['title'] == 2022)
            {
                if ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $depEdu->region_id])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl22::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2021)
            {
                if ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $depEdu->region_id])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl21::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2020)
            {
                if ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $depEdu->region_id])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl20::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-rpn-p',
                        [
                            'depEdu' => $depEdu,
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
            }
        }

        return $this->render(
            'rep-rpn-p',
            [
                'depEdu' => $depEdu,
                'model' => $model,
            ]
        );
    }

    public function actionRepAdm() //школьная программа
    {
        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);

        if (!Yii::$app->user->can('admin'))
        {
            throw new HttpException(123, 'Доступ запрещён');
        }

        $model = new Organization();
        if (Yii::$app->request->post())
        {
            $themes = ThemeProgram::find()
                ->select(
                    [
                        'theme_program.id as id',
                        'theme_program.name as name',
                        'theme_program.short_name as short_name',
                        'training_themes.training_program_id as training_program_id',
                        'training_themes.theme_program_id as theme_program_id',
                        'training_themes.sort as sort'
                    ]
                )
                ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                ->where(['training_program_id' => 1])
                ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

            $post = Yii::$app->request->post()['Organization'];
            //title = 0 - все года/ 2021-2021 / 2020-2020
            if ($post['title'] == 2022)
            {
                if (1 == 2)
                {
                    echo 'пусто';
                    die();
                }
                elseif ($post['region_id'] == 0) //для всех регионов в муниципальном
                {
                    $regions = Region::find()
                        ->select(['id', 'district_id', 'name'])
                        ->where(['district_id' => $post['federal_district_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'regions' => $regions,
                            'fed_org' => '1',
                        ]
                    );
                }
                elseif ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $post['region_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl22::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2021)
            {
                if (1 == 2)
                {
                    echo 'пусто';
                    die();
                }
                elseif ($post['region_id'] == 0) //для всех регионов в муниципальном
                {
                    $regions = Region::find()
                        ->select(['id', 'district_id', 'name'])
                        ->where(['district_id' => $post['federal_district_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'regions' => $regions,
                            'fed_org' => '1',
                        ]
                    );
                }
                elseif ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $post['region_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl21::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2020)
            {
                if (1 == 2)
                {
                    echo 'хз';
                    die();
                }
                elseif ($post['region_id'] == 0) //для всех регионов в округе
                {
                    $regions = Region::find()
                        ->select(['id', 'district_id', 'name'])
                        ->where(['district_id' => $post['federal_district_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'regions' => $regions,
                            'fed_org' => '1',
                        ]
                    );
                }
                elseif ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $post['region_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl20::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 1]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-adm',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
            }
        }

        return $this->render(
            'rep-adm',
            [
                'model' => $model,
            ]
        );
    }

    public function actionRepAdmP() //дошкольная программа
    {
        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);

        if (!Yii::$app->user->can('admin'))
        {
            throw new HttpException(123, 'Доступ запрещён');
        }

        $model = new Organization();
        if (Yii::$app->request->post())
        {
            $themes = ThemeProgram::find()
                ->select(
                    [
                        'theme_program.id as id',
                        'theme_program.name as name',
                        'theme_program.short_name as short_name',
                        'training_themes.training_program_id as training_program_id',
                        'training_themes.theme_program_id as theme_program_id',
                        'training_themes.sort as sort'
                    ]
                )
                ->leftJoin('training_themes', 'theme_program.id = training_themes.theme_program_id')
                ->where(['training_program_id' => 2])
                ->orderby(['training_program_id' => SORT_ASC, 'sort' => SORT_ASC])->all(); //название тем

            $post = Yii::$app->request->post()['Organization'];

            if ($post['title'] == 2022)
            {
                if (1 == 2)
                {
                    echo '123123';
                    die();
                }
                elseif ($post['region_id'] == 0) //для всех регионов в федеральном
                {
                    $regions = Region::find()
                        ->select(['id', 'district_id', 'name'])
                        ->where(['district_id' => $post['federal_district_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'regions' => $regions,
                            'fed_org' => '1',
                        ]
                    );
                }
                elseif ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $post['region_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl22::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
            }
            if ($post['title'] == 2021)
            {
                if (1 == 2)
                {
                    echo '123123';
                    die();
                }
                elseif ($post['region_id'] == 0) //для всех регионов в федеральном
                {
                    $regions = Region::find()
                        ->select(['id', 'district_id', 'name'])
                        ->where(['district_id' => $post['federal_district_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'regions' => $regions,
                            'fed_org' => '1',
                        ]
                    );
                }
                elseif ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $post['region_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl21::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
            }
            elseif ($post['title'] == 2020)
            {
                if (1 == 2)
                {
                    echo '123123';
                    die();
                }
                elseif ($post['region_id'] == 0) //для всех регионов в муниципальном
                {
                    $regions = Region::find()
                        ->select(['id', 'district_id', 'name'])
                        ->where(['district_id' => $post['federal_district_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'regions' => $regions,
                            'fed_org' => '1',
                        ]
                    );
                }
                elseif ($post['municipality_id'] == 0) //для всех муниципальных в регионе
                {
                    $municipalitys = Municipality::find()
                        ->select(['id', 'region_id', 'name'])
                        ->where(['region_id' => $post['region_id']])
                        ->orderBy(['name' => SORT_ASC])
                        ->all(); //список всех организаций в регионе

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'municipalitys' => $municipalitys,
                            'reg_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] == 0) //для всех организаций в муниципальном
                {
                    $organizations = Organization::find()
                        ->select(['id', 'short_title', 'type_org', 'municipality_id'])
                        ->where(['municipality_id' => $post['municipality_id']])
                        ->orderBy(['type_org' => SORT_ASC, 'short_title' => SORT_ASC])
                        ->all(); //список всех организаций в муниципальном

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'organizations' => $organizations,
                            'mun_org' => '1',
                        ]
                    );
                }
                elseif ($post['short_title'] != 0) //для одной организации
                {
                    $organization_id = Organization::find()->select('id')->where(['id' => $post['short_title']])->one()->id;
                    $data = ReportTbl20::find()->where(
                        ['organization_id' => $organization_id, 'training_id' => 2]
                    )->orderBy(
                        ['type_listener' => SORT_ASC, 'class_number' => SORT_ASC, 'letter_number' => SORT_ASC,]
                    )->all();

                    return $this->render(
                        'rep-adm-p',
                        [
                            'model' => $model,
                            'themes' => $themes,
                            'post' => $post,
                            'data' => $data,
                            'org' => '1',
                        ]
                    );
                }
            }
        }

        return $this->render(
            'rep-adm-p',
            [
                'model' => $model,
            ]
        );
    }

    public function actionRepAdmPre() //кол-во обученных детей по темам
    {
        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);

        if (!Yii::$app->user->can('admin'))
        {
            throw new HttpException(123, 'Доступ запрещён');
        }

        $regions = Region::find()->all();
        $data = User::find()
            ->select(
                [
                    //'user.id',
                    //'user.organization_id',
                    //'user.type_listener',
                    'federal_district.id as federal_district_id',
                    'federal_district.name as federal_district_name',
                    'region.id as region_id',
                    'region.name as region_name',
                    'municipality.id as municipality_id',
                    'municipality.name as municipality_name',
                    //'group.id as group_id',
                    //'kids_q.id as kidsq_id',
                    'training_actual.field2',
                    'training_actual.field3',
                    'training_actual.field4',
                    'training_actual.field5',
                    'training_actual.field6',
                    'training_actual.field7',
                    'training_actual.field8',
                    'training_actual.field9',
                    'training_actual.field10',
                    'training_actual.field11',
                    'training_actual.field12',
                    'training_actual.field13'
                ]
            )
            ->leftJoin('organization', 'organization.id=user.organization_id')
            ->leftJoin('federal_district', 'federal_district.id=organization.federal_district_id')
            ->leftJoin('region', 'region.id=organization.region_id')
            ->leftJoin('municipality', 'municipality.id=organization.municipality_id')
            ->leftJoin('group', 'group.user_id=user.id')
            ->leftJoin('kids_q', 'kids_q.group_id=group.id')
            ->leftJoin('training_actual', 'training_actual.kids_id=kids_q.id')
            ->where(
                [
                    'OR',
                    ['user.type_listener' => 'Воспитатель'],
                    ['user.type_listener' => 'Помощник воспитателя'],
                ]
            )
            ->andWhere(['organization.region_id' => 1])
            //->groupBy([])
            ->asArray()
            ->all();

        $program2 = 0;
        $program3 = 0;
        $program4 = 0;
        $program5 = 0;
        $program6 = 0;
        $program7 = 0;
        $program8 = 0;
        $program9 = 0;
        $program10 = 0;
        $program11 = 0;
        $program12 = 0;
        $program13 = 0;
        $data_fin = [];
        foreach ($data as $program)
        {
            if ($program['field2'])
            {
                $data_fin[$program['region_id'] . '_program2'] = $program2++;
            }
            if ($program['field3'])
            {
                $data_fin[$program['region_id'] . '_program3'] = $program3++;
            }
            if ($program['field4'])
            {
                $data_fin[$program['region_id'] . '_program4'] = $program4++;
            }
            if ($program['field5'])
            {
                $data_fin[$program['region_id'] . '_program5'] = $program5++;
            }
            if ($program['field6'])
            {
                $data_fin[$program['region_id'] . '_program6'] = $program6++;
            }
            if ($program['field7'])
            {
                $data_fin[$program['region_id'] . '_program7'] = $program7++;
            }
            if ($program['field8'])
            {
                $data_fin[$program['region_id'] . '_program8'] = $program8++;
            }
            if ($program['field9'])
            {
                $data_fin[$program['region_id'] . '_program9'] = $program9++;
            }
            if ($program['field10'])
            {
                $data_fin[$program['region_id'] . '_program10'] = $program10++;
            }
            if ($program['field11'])
            {
                $data_fin[$program['region_id'] . '_program11'] = $program11++;
            }
            if ($program['field12'])
            {
                $data_fin[$program['region_id'] . '_program12'] = $program12++;
            }
            if ($program['field13'])
            {
                $data_fin[$program['region_id'] . '_program13'] = $program13++;
            }
        }

        //print_r($data_fin);die();
        return $this->render(
            'rep-adm-pre',
            [
                'data_fin' => $data_fin,
                'regions' => $regions,
            ]
        );
    }

    /* отчеты (2021) END */

    public function actionRepChild() //Отчет по детям
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }

        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);

        $model_report = new Report();
        $district = FederalDistrict::find()->all();
        $district_item = ArrayHelper::map($district, 'id', 'name');

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Report'];
            $data = User::find()
                ->select(
                    [
                        //'federal_district.name AS federal_district_name', 'region.name AS region_name', 'municipality.name AS municipality_name',
                        //'organization.short_title AS organization_name', 'user.key_login', 'user.year_birth','user.type_listener as type_listener',


                        'federal_district.name AS federal_district_name',
                        'region.name AS region_name',
                        'municipality.name AS municipality_name',
                        'user.type_listener as type_listener',
                        'organization.short_title AS organization_name',
                        'user.key_login',
                        'kids.sex',
                        'user.year_birth',
                        'kids.age',
                        'kids.height',
                        'kids.mass',
                        'kids.additional_education',
                        'kids.sports_section',
                        'kids.use_telephone',
                        'kids.food_intake',
                        'daily_routine.field18',
                        'daily_routine.field19',
                        'kids.sleep_day',
                        'daily_routine.field17',
                        'kids.charging',
                        'daily_routine.field2',
                        'kids.walk',
                        'daily_routine.field15',
                        'kids.additional_education',
                        'daily_routine.field4',
                        'kids.sports_section',
                        'kids.sports_section1',
                        'daily_routine.field6',
                        'kids.sports_section2',
                        'daily_routine.field7',
                        'daily_routine.field8',
                        'daily_routine.field9',
                        'daily_routine.field20',
                        'daily_routine.field21',
                        'menus.id AS menu_id'
                    ]
                )
                ->rightJoin('organization', 'organization.id = user.organization_id')
                ->rightJoin('federal_district', 'federal_district.id = organization.federal_district_id')
                ->rightJoin('region', 'region.id = organization.region_id')
                ->rightJoin('municipality', 'municipality.id = organization.municipality_id')
                ->leftJoin(
                    'kids',
                    'kids.user_id = user.id'
                )#если не надо выводить пустых детей, то изменить на RIGHT JOIN
                ->rightJoin('daily_routine', 'daily_routine.user_id = user.id')
                ->rightJoin('menus', 'menus.user_id = user.id')
                ->rightJoin('menus_dishes', 'menus_dishes.menu_id = menus.id')
                //->where(['user.version' => 2, 'federal_district.id' => $post['report_federal_district']])
                ->where(
                    ['user.training_id' => 1/*, 'organization.region_id' => 48*/, 'organization.municipality_id' => 253]
                )
                ->andWhere(['like', 'user.type_listener', 'Обучающийся 1-4'])
                ->groupBy(['menus_dishes.menu_id'])
                ->orderBy(
                    [
                        'federal_district_name' => SORT_ASC,
                        'region_name' => SORT_ASC,
                        'municipality_name' => SORT_ASC,
                        'organization_name' => SORT_ASC,
                        /*'age' => SORT_ASC*/
                    ]
                )
                ->asArray()
                ->all();

            $sex_arr = ['ж', 'м'];
            $yesOrNo = ['нет', 'да'];

            return $this->render(
                'rep-child',
                [
                    'data' => $data,
                    'sex_arr' => $sex_arr,
                    'yesOrNo' => $yesOrNo,
                    'model_report' => $model_report,
                    'district_item' => $district_item,
                    'show' => 1,
                    'post' => $post,
                ]
            );
        }

        return $this->render(
            'rep-child',
            [
                'model_report' => $model_report,
                'district_item' => $district_item,
            ]
        );
    }

    public function actionRepChildDoshk() //Отчет по детям дошк
    {
        if (!Yii::$app->user->can('admin'))
        {
            return $this->goHome();
        }

        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);

        $model_report = new Report();
        $district = FederalDistrict::find()->all();
        $district_item = ArrayHelper::map($district, 'id', 'name');

        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['Report'];
            $data = User::find()
                ->select(
                    [
                        'federal_district.name AS federal_district_name',
                        'region.name AS region_name',
                        'municipality.name AS municipality_name',
                        'organization.short_title AS organization_name',
                        'user.key_login',
                        'kids.sex',
                        'user.year_birth',
                        'kids.age',
                        'kids.height',
                        'kids.mass',
                        'kids.additional_education',
                        'kids.sports_section',
                        'kids.use_telephone',
                        'kids.food_intake',
                        'daily_routine.field18',
                        'daily_routine.field19',
                        'kids.sleep_day',
                        'daily_routine.field17',
                        'kids.charging',
                        'daily_routine.field2',
                        'kids.walk',
                        'daily_routine.field15',
                        'kids.additional_education',
                        'daily_routine.field4',
                        'kids.sports_section',
                        'kids.sports_section1',
                        'daily_routine.field6',
                        'kids.sports_section2',
                        'daily_routine.field7',
                        'daily_routine.field8',
                        'daily_routine.field9',
                        'daily_routine.field20',
                        'daily_routine.field21',
                        'menus.id AS menu_id'
                    ]
                )
                ->rightJoin('organization', 'organization.id = user.organization_id')
                ->rightJoin('federal_district', 'federal_district.id = organization.federal_district_id')
                ->rightJoin('region', 'region.id = organization.region_id')
                ->rightJoin('municipality', 'municipality.id = organization.municipality_id')
                ->rightJoin(
                    'kids',
                    'kids.user_id = user.id'
                )#если не надо выводить пустых детей, то изменить на RIGHT JOIN
                ->rightJoin('daily_routine', 'daily_routine.user_id = user.id')
                ->rightJoin('menus', 'menus.user_id = user.id')
                ->rightJoin('menus_dishes', 'menus_dishes.menu_id = menus.id')
                ->where(['user.version' => 2, 'federal_district.id' => $post['report_federal_district']])
                ->andWhere(
                    [
                        'OR',
                        ['user.type_listener' => 'Обучающийся 5-11 классов'],
                        ['user.type_listener' => 'Обучающийся 1-4 классов']
                    ]
                )
                ->groupBy(['menus_dishes.menu_id'])
                ->orderBy(
                    [
                        'federal_district_name' => SORT_ASC,
                        'region_name' => SORT_ASC,
                        'municipality_name' => SORT_ASC,
                        'organization_name' => SORT_ASC,
                        'age' => SORT_ASC
                    ]
                )
                ->asArray()
                ->all();

            $sex_arr = ['ж', 'м'];
            $yesOrNo = ['нет', 'да'];

            return $this->render(
                'rep-child-doshk',
                [
                    'data' => $data,
                    'sex_arr' => $sex_arr,
                    'yesOrNo' => $yesOrNo,
                    'model_report' => $model_report,
                    'district_item' => $district_item,
                    'show' => 1,
                    'post' => $post,
                ]
            );
        }


        return $this->render(
            'rep-child-doshk',
            [
                'model_report' => $model_report,
                'district_item' => $district_item,
            ]
        );
    }


    /* Подставляет регионы в выпадающий список */
    public function actionSubjectslist($id_f)
    {
        $groups = Region::find()->where(['district_id' => $id_f])->orderby(['name' => SORT_ASC])->all();
        $list = '<option value="0">Все субъекты федерации</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $list .= '<option value="' . $group->id . '">' . $group->name . '</option>';
            }
        }
        return $list;
    }

    /* Подставляет муниципальные образования в выпадающий список */
    public function actionMunicipalitylist($id_r)
    {
        $groups = Municipality::find()->where(['region_id' => $id_r])->orderby(['name' => SORT_ASC])->all();
        $municipality = '<option value="0">Все муниципальные образования</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $municipality .= '<option value="' . $group->id . '">' . $group->name . '</option>';
            }
        }
        return $municipality;
    }

    /* Подставляет организации в выдающий список */
    public function actionOrganizationNameSchool($id_m)
    {
        $groups = Organization::find()->where(['municipality_id' => $id_m])->orderby(
            ['type_org' => SORT_ASC, 'short_title' => SORT_ASC]
        )->all();
        $organization = '<option value="0">Все организации</option>';
        if (!empty($groups))
        {
            foreach ($groups as $key => $group)
            {
                $organization .= '<option value="' . $group->id . '">' . $group->short_title . '</option>';
            }
        }
        return $organization;
    }


    public function actionRepPeople() //Отчет по детям для Казани
    {
        if (Yii::$app->user->id != 1)
        {
            return $this->goHome();
        }

        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);

        $users = User::find()
            ->select(
                [
                    'user.id',
                    'user.organization_id',
                    'user.key_login',
                    'user.name',
                    'user.year_birth',
                    'user.class',
                    'user.bukva_klassa',
                    'kids.height',
                    'kids.mass',
                    'kids.age',
                    'kids.sex',
                    'kids.charging',
                    'kids.additional_education',
                    'kids.sports_section',
                    'kids.sports_section1',
                    'kids.sports_section2',
                    'kids.walk',
                    'kids.sleep_day',
                    'kids.use_telephone',
                    'kids.food_intake',
                    'daily_routine.field2',
                    'daily_routine.field4',
                    'daily_routine.field6',
                    'daily_routine.field7',
                    'daily_routine.field8',
                    'daily_routine.field9',
                    'daily_routine.field15',
                    'daily_routine.field17',
                    'daily_routine.field18',
                    'daily_routine.field19',
                    'daily_routine.field20',
                    'daily_routine.field21',
                    'menus.id as menu_id'
                ]
            )
            ->leftJoin('kids', 'kids.user_id = user.id')
            ->leftJoin('daily_routine', 'daily_routine.user_id = user.id')
            ->leftJoin('menus', 'menus.user_id = user.id')
            //->where(['>', 'user.id', 201])
            ->where(['>', 'user.id', 1199])
            ->andWhere(['<', 'kids.id', 1400])
            //->andWhere(['<', 'kids.id', 301])
            ->asArray()
            ->all();
        $organizations = Organization::find()->asArray()->all();
        $sportsSection_arr = SportsSectionName::find()->asArray()->all();

        return $this->render(
            'rep-people',
            [
                'users' => $users,
                'organizations' => $organizations,
                'sportsSection_arr' => $sportsSection_arr
            ]
        );
    }

    public function actionMapSubjects() //Отчет по детям для Казани
    {
        if (Yii::$app->user->id != 1)
        {
            return $this->goHome();
        }

        ini_set('memory_limit', '7168M');
        ini_set('max_execution_time', 3600);


        return $this->render(
            'map-subjects',
            [
                //'users' => $users,
                //'organizations' => $organizations,
                //'sportsSection_arr' => $sportsSection_arr
            ]
        );
    }
}
