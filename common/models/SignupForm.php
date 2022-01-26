<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\User;
use yii\rbac\DbManager;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $training_id;
    public $name;
    public $federal_district_id;
    public $region_id;
    public $municipality;
    public $type_org;
    public $name_organization;
    public $class;
    public $bukva_klassa;
    public $year_birth;
    public $check;
    public $check2;


    public function rules()
    {
        return [
            ['federal_district_id', 'required'],

            ['region_id', 'required'],

            ['municipality', 'required'],

            ['name_organization', 'required'],

            ['listener_type', 'required'],

            ['training_id', 'required'],

            ['name', 'required'],
            ['name', 'trim'],
            ['name', 'string', 'min' => 8, 'max' => 255],

            //['class', 'required'],
            ['class', 'trim'],
            ['class', 'integer', 'min' => 1, 'max' => 11],

            //['bukva_klassa', 'required'],
            ['bukva_klassa', 'trim'],
            ['bukva_klassa', 'string', 'max' => 1],

            ['year_birth', 'required'],
            ['year_birth', 'integer', 'min' => 1940, 'max' => 2020],

            ['check', 'required'],
            ['check', 'compare', 'compareValue' => 1, 'message' => 'Необходимо Ваше согласие на обработку персональных данных'],

            ['check2', 'compare', 'compareValue' => 1, 'message' => 'Необходимо проверить заполненные данные'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'federal_district_id' => 'Федеральный округ',
            'region_id' => 'Субъект Федерации',
            'municipality' => 'Муниципальное образование',
            'name_organization' => 'Наименование организации',
            'listener_type' => 'Тип слушателя',
            'training_id' => 'Выбор обучающей программы',
            'name' => 'ФИО (на кого будет сертификат)',
            'class' => 'Класс',
            'bukva_klassa' => 'Буква класса',
            'year_birth' => 'Год рождения',
            'check' => 'Согласие',

            //'type_org' => 'Тип Организации',
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        if ($user->save()) {
            $r = new DbManager();
            $r->init();
            $assign = $r->createRole('user');
            $r->assign($assign, $user->id);
            return 'ok';
        }
    }

    /*protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }*/
}
