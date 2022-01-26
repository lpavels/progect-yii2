<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'key_login' => 'Уникальный номер',
            'training_id' => 'Обучающая программа',
            'type_org' => 'Тип организации',
            'organization_id' => 'Организация',
            'listener_type' => 'Тип слушателя',
            'name_organization_id' => 'Организация',
            'name' => 'ФИО',
            'class' => 'Класс',
            'bukva_klassa' => 'Буква класса',
            'year_birth' => 'Год рождения',
            'created_at' => 'Дата регистрации',


            //'federal_district' => 'Федеральный округ',
            //'subject_feder' => 'Субъект Федерации',
            //'mun_obr' => 'Муниципальное образование',
            //'form_training_id' => 'Форма обучения',
        ];
    }

    public function get_name_organization($id_org)
    {
        $org_name = Organization::findOne($id_org)->short_title;
        return $org_name;
    }

    public function func_rand($f, $r, $m, $t)
    {
        $qq = $f;
        $qq .= 'R';
        $qq .= $r;
        $qq .= 'M';
        $qq .= $m;
        if ($t == 1) {
            $qq .= 'SC';
        } //если выбрана программа школьников то SC, если дошкольников то  KI
        elseif ($t == 'dir') {
            $qq .= 'DIR';
        } else {
            $qq .= 'KI';
        }

        do {
            $rand = mt_rand(10, 10000000000);
            $qq .= $rand;
            $uniqueNumber = User::find()->select('key_login')->where(['key_login' => $qq])->one();
        } while ($uniqueNumber);
        return $qq;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['name' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByLogin($login)
    {
        return static::findOne(['key_login' => $login, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }

    public static function findByVerificationToken($token)
    {
        return static::findOne(
            [
                'verification_token' => $token,
                'status' => self::STATUS_INACTIVE
            ]
        );
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRole()
    {
        return array_values(Yii::$app->authManager->getRolesByUser($this->id))[0];
    }

    public function ball_response_fin($id_u)
    {
        //определяем номер попытки
        $number = QuestionsResponse::find()->where(
            ['user_id' => Yii::$app->user->identity->id, 'status' => 2]
        )->orderBy(['number_trying' => SORT_DESC])->offset(1)->one()->number_trying;
        //$id_u - номер пользователя
        //$questions - количество вопросов всего

        $questions = \common\models\QuestionsResponse::find()->where(
            ['user_id' => $id_u, 'status' => 2, 'number_trying' => $number]
        )->all();
        $caunt = 0;
        foreach ($questions as $question) {
            $variant = \common\models\QuestionsVariant::find()->where(['id' => $question->questions_variant_id])->one();
            if ($variant->correct == '1') {
                $caunt++;
            }
        }
        return $caunt;
    }

    /* LAST REPORT */

    public function reportNew(
        $id,
        $program,
        $lvl
    ) //program: 1-школьная, 2-дошкольная.    lvl: 1 - директор, 2 - управление образования, 3 - роспотребнадзора региона, 4 - по одному ФО, 5 - по всем ФО
    {
        if ($program == 1) {
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
            if ($lvl == 2) {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand(
                    'SELECT organization.id, organization.short_title, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.transfer = 2022 and user.training_id = 1 and organization.municipality_id = ' . $id . '
                    GROUP BY user.type_training,organization.id'
                );
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr) {
                    $beginning[$arr['id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s22 = ReportTbl22::find()->where(['training_id' => 1, 'municipality_id' => $id])->all();
                foreach ($reportTbl_s22 as $reportTbl) {
                    $data[$reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test;
                    $data[$reportTbl->organization_id . '_inputTestCount_calc']++;
                    $data[$reportTbl->organization_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->organization_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->organization_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->organization_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->organization_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->organization_id . '_independentWork'] += $reportTbl->independent_work;
                    $data[$reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test;
                    if (isset($reportTbl->final_test)) {
                        $data[$reportTbl->organization_id . '_finalTestCount_calc']++;
                    }
                    $data[$reportTbl->organization_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->organization_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->organization_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1) {
                        $data[$reportTbl->organization_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2) {
                            $data[$reportTbl->organization_id . '_trainingCompletedParent']++;
                        } elseif ($reportTbl->type_training == 1) {
                            $data[$reportTbl->organization_id . '_trainingCompletedChild']++;
                        }
                    }
                }
                return array($beginning, $data);
            } elseif ($lvl == 3) {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand(
                    'SELECT organization.municipality_id, organization.type_org,
                     user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.transfer = 2022 and user.training_id = 1 AND organization.region_id = ' . $id . '
                    GROUP BY user.type_training,organization.municipality_id'
                );
                $arr_s = $command->queryAll();
                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в муниципальном районе
                {
                    $beginning[$arr['municipality_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s22 = ReportTbl22::find()->where(['training_id' => 1, 'region_id' => $id])->all();
                foreach ($reportTbl_s22 as $reportTbl) {
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
                    if (isset($reportTbl->final_test)) {
                        $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->municipality_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->municipality_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->municipality_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1) {
                        $data[$reportTbl->municipality_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2) {
                            $data[$reportTbl->municipality_id . '_trainingCompletedParent']++;
                        } elseif ($reportTbl->type_training == 1) {
                            $data[$reportTbl->municipality_id . '_trainingCompletedChild']++;
                        }
                    }
                }
                //print_r($data);die();

                if ($municipalyty_ids != '') {
                    foreach ($municipalyty_ids as $municipalyty_id) {
                        //print_r($municipalyty_id.'<br>');
                        foreach ($organizations_ids[$municipalyty_id] as $organizations_id) {
                            $data[$municipalyty_id . '_countOrgStart']++; //кол-во организаций приступивших к обучению в муниципальном = кол-во организаций в которых проходили входной тест
                            $data[$municipalyty_id . '_inputTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTestCount_calc']; //расчет входного теста по организациям для муниципального р-на

                            if ($data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0) {
                                $data[$municipalyty_id . '_finalTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc']; //расчет итогового теста по организациям для муниципального р-на
                                $data[$municipalyty_id . '_countOrgFinal']++;
                            }
                        }
                    }
                }

                return array($beginning, $data);
            } elseif ($lvl == 5) //по федеральному округу
            {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand(
                    'SELECT organization.region_id, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.transfer = 2022 and user.training_id = 1 AND organization.federal_district_id = ' . $id . '
                    GROUP BY user.type_training,organization.region_id'
                );
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в муниципальном районе
                {
                    $beginning[$arr['region_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $data = [];
                $reportTbl_s22 = ReportTbl22::find()->where(['training_id' => 1, 'federal_district_id' => $id])->all();
                foreach ($reportTbl_s22 as $reportTbl) {
                    $region_ids[$reportTbl->region_id] = $reportTbl->region_id; //ids регионов в федеральном
                    $municipalyty_ids[$reportTbl->region_id][$reportTbl->municipality_id] = $reportTbl->municipality_id; //ids муниципальных в регионе
                    $organizations_ids[$reportTbl->municipality_id][$reportTbl->organization_id] = $reportTbl->organization_id; //ids оранизаций в мунипальном образовании

                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test * 10; //входной тест
                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTestCount_calc']++;  //входной тест кол-во организаций

                    $data[$reportTbl->region_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->region_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->region_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->region_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->region_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->region_id . '_independentWork'] += $reportTbl->independent_work;

                    $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test * 10; //итоговый тест
                    if (isset($reportTbl->final_test)) {
                        $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->region_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->region_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->region_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1) {
                        $data[$reportTbl->region_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2) {
                            $data[$reportTbl->region_id . '_trainingCompletedParent']++;
                        } elseif ($reportTbl->type_training == 1) {
                            $data[$reportTbl->region_id . '_trainingCompletedChild']++;
                        }
                    }
                }
                //print_r($data);die();

                if ($region_ids) {
                    foreach ($region_ids as $region_id) {
                        //print_r($region_id.'<br>');

                        foreach ($municipalyty_ids[$region_id] as $municipalyty_id) {
                            $data[$region_id . '_countMunStart']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            $calc[$region_id . '_countMun']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            //print_r($municipalyty_id . '<br>');

                            foreach ($organizations_ids[$municipalyty_id] as $organizations_id) {
                                //print_r($organizations_id . '<br>');
                                $data[$region_id . '_countOrgStart']++; //кол-во организаций в регионе приступивших к обучению в регионе
                                $calc[$municipalyty_id . '_countOrg']++; //кол-во организаций проходивших входной в муниципальном р-не
                                $sr_org[$municipalyty_id] += $calc['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTest_calc'] /
                                    $calc['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTestCount_calc']; //сумма средних по организациям(входной)

                                if ($calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0) {
                                    $sr_org2[$municipalyty_id] += $calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTest_calc'] /
                                        $calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc']; //среднее по организациям  (итоговый)
                                    $calc2[$municipalyty_id . '_countOrg2']++; //кол-во организаций проходивших итоговый в муниципальном р-не
                                }
                            }
                            $sr_mun[$region_id] += $sr_org[$municipalyty_id] / $calc[$municipalyty_id . '_countOrg']; //среднее по муниципальному (входной)
                            if ($calc2[$municipalyty_id . '_countOrg2'] != 0) {
                                $sr_mun2[$region_id] += $sr_org2[$municipalyty_id] / $calc2[$municipalyty_id . '_countOrg2'];
                                $calc2[$region_id . '_countMun2']++;
                            } //среднее по муниципальному (итоговый)
                        }

                        $data[$region_id . '_regionId'] += $sr_mun[$region_id] / $calc[$region_id . '_countMun']; //среднее по региону // (входной)
                        if ($calc2[$region_id . '_countMun2'] != 0) {
                            $data[$region_id . '_regionId2'] += $sr_mun2[$region_id] / $calc2[$region_id . '_countMun2'];
                        } //среднее по региону // (входной)
                    }
                    //die();
                }

                //print_r('<br>');
                //die('exit');
                return array($beginning, $data);
            }
        } elseif ($program == 2) {
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
            if ($lvl == 2) {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand(
                    'SELECT organization.id, organization.short_title, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.transfer = 2022 and user.training_id = 2 and organization.municipality_id = ' . $id . '
                    GROUP BY user.type_training,organization.id'
                );
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr) {
                    $beginning[$arr['id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s22 = ReportTbl22::find()->where(['training_id' => 2, 'municipality_id' => $id])->all();
                foreach ($reportTbl_s22 as $reportTbl) {
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
                    if (isset($reportTbl->final_test)) {
                        $data[$reportTbl->organization_id . '_finalTestCount_calc']++;
                    }
                    $data[$reportTbl->organization_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->organization_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->organization_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1) {
                        $data[$reportTbl->organization_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2) {
                            $data[$reportTbl->organization_id . '_trainingCompletedParent']++;
                        } elseif ($reportTbl->type_training == 1) {
                            $data[$reportTbl->organization_id . '_trainingCompletedChild']++;
                        }
                    }

                    $data[$reportTbl->organization_id . '_numberChildren'] += $reportTbl->number_children;
                }

                return array($beginning, $data);
            } elseif ($lvl == 3) {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand(
                    'SELECT organization.municipality_id, organization.type_org,
                     user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.transfer = 2022 and user.training_id = 2 AND organization.region_id = ' . $id . '
                    GROUP BY user.type_training,organization.municipality_id'
                );
                $arr_s = $command->queryAll();
                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в муниципальном районе
                {
                    $beginning[$arr['municipality_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $reportTbl_s22 = ReportTbl22::find()->where(['training_id' => 2, 'region_id' => $id])->all();
                foreach ($reportTbl_s22 as $reportTbl) {
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
                    if (isset($reportTbl->final_test)) {
                        $data['munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->municipality_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->municipality_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->municipality_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1) {
                        $data[$reportTbl->municipality_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2) {
                            $data[$reportTbl->municipality_id . '_trainingCompletedParent']++;
                        } elseif ($reportTbl->type_training == 1) {
                            $data[$reportTbl->municipality_id . '_trainingCompletedChild']++;
                        }
                    }

                    $data[$reportTbl->municipality_id . '_numberChildren'] += $reportTbl->number_children;
                }
                //print_r($data);die();

                if ($municipalyty_ids != '') {
                    foreach ($municipalyty_ids as $municipalyty_id) {
                        //print_r($municipalyty_id.'<br>');
                        foreach ($organizations_ids[$municipalyty_id] as $organizations_id) {
                            $data[$municipalyty_id . '_countOrgStart']++; //кол-во организаций приступивших к обучению в муниципальном = кол-во организаций в которых проходили входной тест
                            $data[$municipalyty_id . '_inputTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTestCount_calc']; //расчет входного теста по организациям для муниципального р-на

                            if ($data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0) {
                                $data[$municipalyty_id . '_finalTest'] += $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTest_calc'] * 10 / $data['munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc']; //расчет итогового теста по организациям для муниципального р-на
                                $data[$municipalyty_id . '_countOrgFinal']++;
                            }
                        }
                    }
                }
                return array($beginning, $data);
            } elseif ($lvl == 5) //по федеральному округу
            {
                $connection = \Yii::$app->db; // выполняем запрос
                $command = $connection->createCommand(
                    'SELECT organization.region_id, organization.type_org,
                    user.training_id, user.type_training, COUNT(user.type_training)
                    FROM organization
                    LEFT JOIN user ON user.organization_id = organization.id
                    WHERE user.transfer = 2022 and user.training_id = 2 AND organization.federal_district_id = ' . $id . '
                    GROUP BY user.type_training,organization.region_id'
                );
                $arr_s = $command->queryAll();

                foreach ($arr_s as $arr) //кол-во зерегистрированных взрослых и детей в регионе
                {
                    $beginning[$arr['region_id'] . '_training_id_' . $arr['type_training']] = $arr['COUNT(user.type_training)'];
                }

                $data = [];
                $reportTbl_s22 = ReportTbl22::find()->where(['training_id' => 2, 'federal_district_id' => $id])->all();
                foreach ($reportTbl_s22 as $reportTbl) {
                    $region_ids[$reportTbl->region_id] = $reportTbl->region_id; //ids регионов в федеральном
                    $municipalyty_ids[$reportTbl->region_id][$reportTbl->municipality_id] = $reportTbl->municipality_id; //ids муниципальных в регионе
                    $organizations_ids[$reportTbl->municipality_id][$reportTbl->organization_id] = $reportTbl->organization_id; //ids оранизаций в мунипальном образовании

                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTest_calc'] += $reportTbl->input_test * 10; //входной тест
                    $calc['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_inputTestCount_calc']++;  //входной тест кол-во организаций

                    $data[$reportTbl->region_id . '_theme1'] += $reportTbl->theme1;
                    $data[$reportTbl->region_id . '_theme2'] += $reportTbl->theme2;
                    $data[$reportTbl->region_id . '_theme3'] += $reportTbl->theme3;
                    $data[$reportTbl->region_id . '_theme4'] += $reportTbl->theme4;
                    $data[$reportTbl->region_id . '_theme5'] += $reportTbl->theme5;
                    $data[$reportTbl->region_id . '_theme6'] += $reportTbl->theme6;
                    $data[$reportTbl->region_id . '_independentWork'] += $reportTbl->independent_work;

                    $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTest_calc'] += $reportTbl->final_test * 10; //итоговый тест
                    if (isset($reportTbl->final_test)) {
                        $calc2['regId_' . $reportTbl->region_id . '_munId_' . $reportTbl->municipality_id . '_orgId_' . $reportTbl->organization_id . '_finalTestCount_calc']++;
                    }  //итоговый тест кол-во

                    $data[$reportTbl->region_id . '_finalTest_1st'] += $reportTbl->final_test_1st;
                    $data[$reportTbl->region_id . '_finalTest_2st'] += $reportTbl->final_test_2st;
                    $data[$reportTbl->region_id . '_trainingCompleted'] += $reportTbl->training_completed;

                    if ($reportTbl->training_completed == 1) {
                        $data[$reportTbl->region_id . '_trainingCompletedAll']++;
                        if ($reportTbl->type_training == 2) {
                            $data[$reportTbl->region_id . '_trainingCompletedParent']++;
                        } elseif ($reportTbl->type_training == 1) {
                            $data[$reportTbl->region_id . '_trainingCompletedChild']++;
                        }
                    }
                    $data[$reportTbl->region_id . '_numberChildren'] += $reportTbl->number_children;
                }
                //print_r($data);die();

                if ($region_ids) {
                    foreach ($region_ids as $region_id) {
                        //print_r($region_id.'<br>');

                        foreach ($municipalyty_ids[$region_id] as $municipalyty_id) {
                            $data[$region_id . '_countMunStart']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            $calc[$region_id . '_countMun']++; //кол-во муниципальных образований в регионе приступивших к обучению
                            //print_r($municipalyty_id . '<br>');

                            foreach ($organizations_ids[$municipalyty_id] as $organizations_id) {
                                //print_r($organizations_id . '<br>');
                                $data[$region_id . '_countOrgStart']++; //кол-во организаций в регионе приступивших к обучению в регионе
                                $calc[$municipalyty_id . '_countOrg']++; //кол-во организаций проходивших входной в муниципальном р-не
                                $sr_org[$municipalyty_id] += $calc['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTest_calc'] /
                                    $calc['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_inputTestCount_calc']; //сумма средних по организациям(входной)

                                if ($calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc'] != 0) {
                                    $sr_org2[$municipalyty_id] += $calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTest_calc'] /
                                        $calc2['regId_' . $region_id . '_munId_' . $municipalyty_id . '_orgId_' . $organizations_id . '_finalTestCount_calc']; //среднее по организациям  (итоговый)
                                    $calc2[$municipalyty_id . '_countOrg2']++; //кол-во организаций проходивших итоговый в муниципальном р-не
                                }
                            }
                            $sr_mun[$region_id] += $sr_org[$municipalyty_id] / $calc[$municipalyty_id . '_countOrg']; //среднее по муниципальному (входной)
                            if ($calc2[$municipalyty_id . '_countOrg2'] != 0) {
                                $sr_mun2[$region_id] += $sr_org2[$municipalyty_id] / $calc2[$municipalyty_id . '_countOrg2'];
                                $calc2[$region_id . '_countMun2']++;
                            } //среднее по муниципальному (итоговый)
                        }

                        $data[$region_id . '_regionId'] += $sr_mun[$region_id] / $calc[$region_id . '_countMun']; //среднее по региону // (входной)
                        if ($calc2[$region_id . '_countMun2'] != 0) {
                            $data[$region_id . '_regionId2'] += $sr_mun2[$region_id] / $calc2[$region_id . '_countMun2'];
                        } //среднее по региону // (входной)
                    }
                }

                return array($beginning, $data);
            }
        } else {
            echo 'Данных нет (error #852)';
            die();
        }
    }

    /* LAST REPORT (END) */

    public function chartRegistrations() //кол-во зарегистрированных за последние 10 дней
    {
        $today = date('Y-m-d');
        //$today2 = date('Y-m-d', strtotime($today. " - 3 day"));

        for ($i = 0; $i < 10; $i++) {
            $days[] = date('d.m.Y', strtotime($today . " - " . $i . " day"));
            $registration_count[] = User::find()
                ->where(['>=', 'created_at', strtotime(date('Y-m-d', strtotime($today . " - " . $i . " day")))])
                ->andWhere(
                    ['<', 'created_at', strtotime(date('Y-m-d', strtotime($today . " - " . $i . " day") + 86400))]
                )
                ->count();
        }

        //print_r($registration_count);die();

        return array(
            [
                $days,
                $registration_count
            ]
        );
    }
}