<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $key_login
 * @property int $training_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $auth_key
 * @property string|null $password_reset_token
 * @property int|null $organization_id
 * @property string|null $type_listener
 * @property int|null $type_training 1-ребенок, 2-взрослый
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string|null $verification_token
 * @property int $year_birth Год рождения
 * @property int|null $class
 * @property string|null $bukva_klassa
 * @property int|null $form_training_id
 * @property int $control_test_1
 * @property int $control_test_2
 * @property int $certificate
 */
class UserEdu20 extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user';
    }

    public static function getDb()
    {
        return Yii::$app->get('db_edu20');
    }

    public function rules()
    {
        return [
            [['key_login', 'training_id', 'year_birth', 'certificate'], 'required'],
            [['training_id', 'organization_id', 'type_training', 'status', 'created_at', 'updated_at', 'year_birth', 'class', 'form_training_id', 'control_test_1', 'control_test_2', 'certificate'], 'integer'],
            [['key_login'], 'string', 'max' => 250],
            [['name', 'phone', 'password_reset_token', 'type_listener', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['bukva_klassa'], 'string', 'max' => 10],
            [['key_login'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key_login' => 'Key Login',
            'training_id' => 'Training ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
            'organization_id' => 'Organization ID',
            'type_listener' => 'Type Listener',
            'type_training' => 'Type Training',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'year_birth' => 'Year Birth',
            'class' => 'Class',
            'bukva_klassa' => 'Bukva Klassa',
            'form_training_id' => 'Form Training ID',
            'control_test_1' => 'Control Test 1',
            'control_test_2' => 'Control Test 2',
            'certificate' => 'Certificate',
        ];
    }

    /* LAST REPORT */
    public function reportNew($id, $program, $lvl) //program: 1-школьная, 2-дошкольная.    lvl: 1 - директор, 2 - управление образования, 3 - роспотребнадзора региона, 4 - по одному ФО, 5 - по всем ФО
    {
        if ($program == 1)
        {
            /*if ($lvl == 1)
            {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand('SELECT user.id,questions_response.user_id,questions_response.questions_id,
                     questions_response.questions_variant_id,questions_variant.correct,questions_response.number_trying,questions_response.`status`
                    FROM questions_response
                    LEFT JOIN questions_variant ON questions_variant.id = questions_response.questions_variant_id
                    LEFT JOIN user ON user.id = questions_response.user_id
                    LEFT JOIN organization ON organization.id = user.organization_id
                    WHERE user.organization_id = "' . $id . '" AND user.training_id = 1');
                $director_arrays = $command->queryAll();

                $scarray_dir = [];
                foreach ($director_arrays as $director_array)
                {
                    $scarray_dir[$director_array['id']] = $director_array['id'];
                    //$scarray_dir[$director_array['id'] . '_item_name'] = $director_array['description'];
                    $scarray_dir[$director_array['id'] . '_test_' . $director_array['status']]['number_trying_' . $director_array['number_trying']][$director_array['correct']]++;
                }

                $command2 = $connection->createCommand('SELECT user.id,trainings.theme_program_id
                    FROM trainings
                    LEFT JOIN user ON user.id = trainings.user_id
                    LEFT JOIN training_themes ON training_themes.theme_program_id = trainings.theme_program_id
                    WHERE user.training_id = 1 AND training_themes.training_program_id = 1 AND user.organization_id = "' . $id . '"');
                $director_arrays2 = $command2->queryAll(); //пройденные темы

                $scarray_dir2 = [];
                foreach ($director_arrays2 as $director_array2)
                {
                    $scarray_dir2[$director_array2['id']][$director_array2['theme_program_id']] = $director_array2['theme_program_id'];
                }

                return array($scarray_dir, $scarray_dir2);
            } расчет в контроллере */
            if ($lvl == 2)
            {
                $connection = \Yii::$app->db_edu20; // выполняем запрос
                $command = $connection->createCommand('SELECT organization.id, organization.short_title, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.training_id = 1 and organization.municipality_id = ' . $id . '
                    GROUP BY user.type_training,organization.id');
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr)
                {
                    $beginning[$arr['id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s = ReportTbl20::find()->where(['training_id' => 1, 'municipality_id' => $id])->all();
                foreach ($reportTbl_s as $reportTbl)
                {
                    $data[$reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test;
                    $data[$reportTbl->organization_id . '_inputTestCount_calc']++;
                    $data[$reportTbl->organization_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->organization_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->organization_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->organization_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->organization_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->organization_id . '_independentWork'] += $reportTbl->independent_work;
                    $data[$reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test;
                    if (isset($reportTbl->final_test))
                    {
                        $data[$reportTbl->organization_id . '_finalTestCount_calc']++;
                    }
                    $data[$reportTbl->organization_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->organization_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->organization_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1)
                    {
                        $data[$reportTbl->organization_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2)
                        {
                            $data[$reportTbl->organization_id . '_trainingCompletedParent']++;
                        }
                        elseif ($reportTbl->type_training == 1)
                        {
                            $data[$reportTbl->organization_id . '_trainingCompletedChild']++;
                        }
                    }
                }

                return array($beginning, $data);
            }
            elseif ($lvl == 3)
            {
                $connection = \Yii::$app->db_edu20; // выполняем запрос
                $command = $connection->createCommand('SELECT organization.municipality_id, organization.type_org,
                     user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.training_id = 1 AND organization.region_id = ' . $id . '
                    GROUP BY user.type_training,organization.municipality_id');
                $arr_s = $command->queryAll();
                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в муниципальном районе
                {
                    $beginning[$arr['municipality_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s = ReportTbl20::find()->where(['training_id' => 1, 'region_id' => $id])->all();
                foreach ($reportTbl_s as $reportTbl)
                {
                    $municipalyty_ids[$reportTbl->municipality_id] = $reportTbl->municipality_id; //ids муниципальных в регионе
                    $organizations_ids[$reportTbl->municipality_id][$reportTbl->organization_id] = $reportTbl->organization_id; //ids оранизаций в мунипальном образовании

                    $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test; //входной тест
                    $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTestCount_calc']++;  //входной тест кол-во

                    $data[$reportTbl->municipality_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->municipality_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->municipality_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->municipality_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->municipality_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->municipality_id . '_independentWork'] += $reportTbl->independent_work;

                    $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test; //итоговый тест
                    if (isset($reportTbl->final_test))
                    {
                        $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->municipality_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->municipality_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->municipality_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1)
                    {
                        $data[$reportTbl->municipality_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2)
                        {
                            $data[$reportTbl->municipality_id . '_trainingCompletedParent']++;
                        }
                        elseif ($reportTbl->type_training == 1)
                        {
                            $data[$reportTbl->municipality_id . '_trainingCompletedChild']++;
                        }
                    }
                }
                //print_r($data);die();

                if ($municipalyty_ids != '')
                {
                    foreach ($municipalyty_ids as $municipalyty_id)
                    {
                        //print_r($municipalyty_id.'<br>');
                        foreach ($organizations_ids[$municipalyty_id] as $organizations_id)
                        {
                            $data[$municipalyty_id . '_countOrgStart']++; //кол-во организаций приступивших к обучению в муниципальном = кол-во организаций в которых проходили входной тест
                            $data[$municipalyty_id . '_inputTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTestCount_calc']; //расчет входного теста по организациям для муниципального р-на

                            if ($data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0)
                            {
                                $data[$municipalyty_id . '_finalTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc']; //расчет итогового теста по организациям для муниципального р-на
                                $data[$municipalyty_id . '_countOrgFinal']++;
                            }
                        }
                    }
                }

                return array($beginning, $data);
            }
            elseif ($lvl == 5) //по федеральному округу
            {
                $connection = \Yii::$app->db_edu20; // выполняем запрос
                $command = $connection->createCommand('SELECT organization.region_id, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.training_id = 1 AND organization.federal_district_id = ' . $id . '
                    GROUP BY user.type_training,organization.region_id');
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в муниципальном районе
                {
                    $beginning[$arr['region_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $data = [];
                $reportTbl_s = ReportTbl20::find()->where(['training_id' => 1, 'federal_district_id' => $id])->all();
                foreach ($reportTbl_s as $reportTbl)
                {
                    $region_ids[$reportTbl->region_id] = $reportTbl->region_id; //ids регионов в федеральном
                    $municipalyty_ids[$reportTbl->region_id][$reportTbl->municipality_id] = $reportTbl->municipality_id; //ids муниципальных в регионе
                    $organizations_ids[$reportTbl->municipality_id][$reportTbl->organization_id] = $reportTbl->organization_id; //ids оранизаций в мунипальном образовании

                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test*10; //входной тест
                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTestCount_calc']++;  //входной тест кол-во организаций

                    $data[$reportTbl->region_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->region_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->region_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->region_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->region_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->region_id . '_independentWork'] += $reportTbl->independent_work;

                    $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test*10; //итоговый тест
                    if (isset($reportTbl->final_test))
                    {
                        $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->region_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->region_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->region_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1)
                    {
                        $data[$reportTbl->region_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2)
                        {
                            $data[$reportTbl->region_id . '_trainingCompletedParent']++;
                        }
                        elseif ($reportTbl->type_training == 1)
                        {
                            $data[$reportTbl->region_id . '_trainingCompletedChild']++;
                        }
                    }
                }
                //print_r($data);die();

                if ($region_ids)
                {
                    foreach ($region_ids as $region_id)
                    {
                        //print_r($region_id.'<br>');

                        foreach ($municipalyty_ids[$region_id] as $municipalyty_id)
                        {
                            $data[$region_id.'_countMunStart']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            $calc[$region_id.'_countMun']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            //print_r($municipalyty_id . '<br>');

                            foreach ($organizations_ids[$municipalyty_id] as $organizations_id)
                            {
                                //print_r($organizations_id . '<br>');
                                $data[$region_id.'_countOrgStart']++; //кол-во организаций в регионе приступивших к обучению в регионе
                                $calc[$municipalyty_id.'_countOrg']++; //кол-во организаций проходивших входной в муниципальном р-не
                                $sr_org[$municipalyty_id] += $calc['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_inputTest_calc']/
                                    $calc['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_inputTestCount_calc']; //сумма средних по организациям(входной)

                                if ($calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0)
                                {
                                    $sr_org2[$municipalyty_id] +=$calc2['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_finalTest_calc']/
                                        $calc2['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_finalTestCount_calc']; //среднее по организациям  (итоговый)
                                    $calc2[$municipalyty_id.'_countOrg2']++; //кол-во организаций проходивших итоговый в муниципальном р-не
                                }
                            }
                            $sr_mun[$region_id] += $sr_org[$municipalyty_id]/$calc[$municipalyty_id.'_countOrg']; //среднее по муниципальному (входной)
                            if ($calc2[$municipalyty_id.'_countOrg2'] != 0) {
                                $sr_mun2[$region_id] += $sr_org2[$municipalyty_id]/$calc2[$municipalyty_id.'_countOrg2'];
                                $calc2[$region_id.'_countMun2']++;
                            } //среднее по муниципальному (итоговый)
                        }

                        $data[$region_id.'_regionId'] += $sr_mun[$region_id]/$calc[$region_id.'_countMun']; //среднее по региону // (входной)
                        if($calc2[$region_id.'_countMun2'] != 0) $data[$region_id.'_regionId2'] += $sr_mun2[$region_id]/$calc2[$region_id.'_countMun2']; //среднее по региону // (входной)
                    }
                    //die();
                }

                //print_r('<br>');
                //die('exit');
                return array($beginning, $data);
            }
        }
        elseif ($program == 2)
        {
            /*if ($lvl == 1)
            {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand('SELECT user.id,questions_response.user_id,questions_response.questions_id,
                     questions_response.questions_variant_id,questions_variant.correct,questions_response.number_trying,questions_response.`status`
                    FROM questions_response
                    LEFT JOIN questions_variant ON questions_variant.id = questions_response.questions_variant_id
                    LEFT JOIN user ON user.id = questions_response.user_id
                    LEFT JOIN organization ON organization.id = user.organization_id
                    WHERE user.organization_id = "' . $id . '" AND user.training_id = 2');
                $director_arrays = $command->queryAll();

                $scarray_dir = [];
                foreach ($director_arrays as $director_array)
                {
                    $scarray_dir[$director_array['id']] = $director_array['id'];
                    //$scarray_dir[$director_array['id'] . '_item_name'] = $director_array['description'];
                    $scarray_dir[$director_array['id'] . '_test_' . $director_array['status']]['number_trying_' . $director_array['number_trying']][$director_array['correct']]++;
                }

                $command2 = $connection->createCommand('SELECT user.id,trainings.theme_program_id
                    FROM trainings
                    LEFT JOIN user ON user.id = trainings.user_id
                    LEFT JOIN training_themes ON training_themes.theme_program_id = trainings.theme_program_id
                    WHERE user.training_id = 2 AND training_themes.training_program_id = 2 AND user.organization_id = "' . $id . '"');
                $director_arrays2 = $command2->queryAll(); //пройденные темы

                $scarray_dir2 = [];
                foreach ($director_arrays2 as $director_array2)
                {
                    $scarray_dir2[$director_array2['id']][$director_array2['theme_program_id']] = $director_array2['theme_program_id'];
                }

                return array($scarray_dir, $scarray_dir2);
            } расчет в контроллере */
            if ($lvl == 2)
            {
                $connection = \Yii::$app->db_edu20; // выполняем запрос
                $command = $connection->createCommand('SELECT organization.id, organization.short_title, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.training_id = 2 and organization.municipality_id = ' . $id . '
                    GROUP BY user.type_training,organization.id');
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr)
                {
                    $beginning[$arr['id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s = ReportTbl20::find()->where(['training_id' => 2, 'municipality_id' => $id])->all();
                foreach ($reportTbl_s as $reportTbl)
                {
                    $data[$reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test;
                    $data[$reportTbl->organization_id . '_inputTestCount_calc']++;
                    $data[$reportTbl->organization_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->organization_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->organization_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->organization_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->organization_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->organization_id . '_theme6'] += $reportTbl->theme6;
                    $data[$reportTbl->organization_id . '_independentWork'] += $reportTbl->independent_work;
                    $data[$reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test;
                    if (isset($reportTbl->final_test))
                    {
                        $data[$reportTbl->organization_id . '_finalTestCount_calc']++;
                    }
                    $data[$reportTbl->organization_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->organization_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->organization_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1)
                    {
                        $data[$reportTbl->organization_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2)
                        {
                            $data[$reportTbl->organization_id . '_trainingCompletedParent']++;
                        }
                        elseif ($reportTbl->type_training == 1)
                        {
                            $data[$reportTbl->organization_id . '_trainingCompletedChild']++;
                        }
                    }

                    $data[$reportTbl->organization_id . '_numberChildren'] += $reportTbl->number_children;
                }

                return array($beginning, $data);
            }
            elseif ($lvl == 3)
            {
                $connection = \Yii::$app->db_edu20; // выполняем запрос
                $command = $connection->createCommand('SELECT organization.municipality_id, organization.type_org,
                     user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.training_id = 2 AND organization.region_id = ' . $id . '
                    GROUP BY user.type_training,organization.municipality_id');
                $arr_s = $command->queryAll();
                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в муниципальном районе
                {
                    $beginning[$arr['municipality_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s = ReportTbl20::find()->where(['training_id' => 2, 'region_id' => $id])->all();
                foreach ($reportTbl_s as $reportTbl)
                {
                    $municipalyty_ids[$reportTbl->municipality_id] = $reportTbl->municipality_id; //ids муниципальных в регионе
                    $organizations_ids[$reportTbl->municipality_id][$reportTbl->organization_id] = $reportTbl->organization_id; //ids оранизаций в мунипальном образовании

                    $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test; //входной тест
                    $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTestCount_calc']++;  //входной тест кол-во

                    $data[$reportTbl->municipality_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->municipality_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->municipality_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->municipality_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->municipality_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->municipality_id . '_theme6'] += $reportTbl->theme6;
                    $data[$reportTbl->municipality_id . '_independentWork'] += $reportTbl->independent_work;

                    $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test; //итоговый тест
                    if (isset($reportTbl->final_test))
                    {
                        $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->municipality_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->municipality_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->municipality_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1)
                    {
                        $data[$reportTbl->municipality_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2)
                        {
                            $data[$reportTbl->municipality_id . '_trainingCompletedParent']++;
                        }
                        elseif ($reportTbl->type_training == 1)
                        {
                            $data[$reportTbl->municipality_id . '_trainingCompletedChild']++;
                        }
                    }

                    $data[$reportTbl->municipality_id . '_numberChildren'] += $reportTbl->number_children;
                }
                //print_r($data);die();

                if ($municipalyty_ids != '')
                {
                    foreach ($municipalyty_ids as $municipalyty_id)
                    {
                        //print_r($municipalyty_id.'<br>');
                        foreach ($organizations_ids[$municipalyty_id] as $organizations_id)
                        {
                            $data[$municipalyty_id . '_countOrgStart']++; //кол-во организаций приступивших к обучению в муниципальном = кол-во организаций в которых проходили входной тест
                            $data[$municipalyty_id . '_inputTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTestCount_calc']; //расчет входного теста по организациям для муниципального р-на

                            if ($data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0)
                            {
                                $data[$municipalyty_id . '_finalTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc']; //расчет итогового теста по организациям для муниципального р-на
                                $data[$municipalyty_id . '_countOrgFinal']++;
                            }
                        }
                    }
                }
                return array($beginning, $data);
            }
            elseif ($lvl == 5) //по федеральному округу
            {
                $connection = \Yii::$app->db_edu20; // выполняем запрос
                $command = $connection->createCommand('SELECT organization.region_id, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.training_id = 2 AND organization.federal_district_id = ' . $id . '
                    GROUP BY user.type_training,organization.region_id');
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в регионе
                {
                    $beginning[$arr['region_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $data = [];
                $reportTbl_s = ReportTbl20::find()->where(['training_id' => 2, 'federal_district_id' => $id])->all();
                foreach ($reportTbl_s as $reportTbl)
                {
                    $region_ids[$reportTbl->region_id] = $reportTbl->region_id; //ids регионов в федеральном
                    $municipalyty_ids[$reportTbl->region_id][$reportTbl->municipality_id] = $reportTbl->municipality_id; //ids муниципальных в регионе
                    $organizations_ids[$reportTbl->municipality_id][$reportTbl->organization_id] = $reportTbl->organization_id; //ids оранизаций в мунипальном образовании

                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test*10; //входной тест
                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTestCount_calc']++;  //входной тест кол-во организаций

                    $data[$reportTbl->region_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->region_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->region_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->region_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->region_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->region_id . '_theme6'] += $reportTbl->theme6;
                    $data[$reportTbl->region_id . '_independentWork'] += $reportTbl->independent_work;

                    $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test*10; //итоговый тест
                    if (isset($reportTbl->final_test))
                    {
                        $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->region_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->region_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->region_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1)
                    {
                        $data[$reportTbl->region_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2)
                        {
                            $data[$reportTbl->region_id . '_trainingCompletedParent']++;
                        }
                        elseif ($reportTbl->type_training == 1)
                        {
                            $data[$reportTbl->region_id . '_trainingCompletedChild']++;
                        }
                    }
                    $data[$reportTbl->region_id . '_numberChildren'] += $reportTbl->number_children;
                }
                //print_r($data);die();

                if ($region_ids)
                {
                    foreach ($region_ids as $region_id)
                    {
                        //print_r($region_id.'<br>');

                        foreach ($municipalyty_ids[$region_id] as $municipalyty_id)
                        {
                            $data[$region_id.'_countMunStart']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            $calc[$region_id.'_countMun']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            //print_r($municipalyty_id . '<br>');

                            foreach ($organizations_ids[$municipalyty_id] as $organizations_id)
                            {
                                //print_r($organizations_id . '<br>');
                                $data[$region_id.'_countOrgStart']++; //кол-во организаций в регионе приступивших к обучению в регионе
                                $calc[$municipalyty_id.'_countOrg']++; //кол-во организаций проходивших входной в муниципальном р-не
                                $sr_org[$municipalyty_id] += $calc['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_inputTest_calc']/
                                    $calc['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_inputTestCount_calc']; //сумма средних по организациям(входной)

                                if ($calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0)
                                {
                                    $sr_org2[$municipalyty_id] +=$calc2['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_finalTest_calc']/
                                        $calc2['regId_'.$region_id.'_munId_'.$municipalyty_id.'_orgId_'.$organizations_id.'_finalTestCount_calc']; //среднее по организациям  (итоговый)
                                    $calc2[$municipalyty_id.'_countOrg2']++; //кол-во организаций проходивших итоговый в муниципальном р-не
                                }
                            }
                            $sr_mun[$region_id] += $sr_org[$municipalyty_id]/$calc[$municipalyty_id.'_countOrg']; //среднее по муниципальному (входной)
                            if ($calc2[$municipalyty_id.'_countOrg2'] != 0) {
                                $sr_mun2[$region_id] += $sr_org2[$municipalyty_id]/$calc2[$municipalyty_id.'_countOrg2'];
                                $calc2[$region_id.'_countMun2']++;
                            } //среднее по муниципальному (итоговый)
                        }

                        $data[$region_id.'_regionId'] += $sr_mun[$region_id]/$calc[$region_id.'_countMun']; //среднее по региону // (входной)
                        if($calc2[$region_id.'_countMun2'] != 0) $data[$region_id.'_regionId2'] += $sr_mun2[$region_id]/$calc2[$region_id.'_countMun2']; //среднее по региону // (входной)
                    }
                    //die();
                }

                //print_r('<br>');
                //die('exit');
                return array($beginning, $data);
            }
        }
        else
        {
            echo 'Данных нет (error #852)';
            die();
        }
    }
    /* LAST REPORT (END) */
}
