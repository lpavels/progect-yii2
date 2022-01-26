<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class MenuForm extends Model
{
    public $name;
    public $age;
    public $characters;
    public $cycles;
    public $odno_vnogodnev;//Это для роспотреба и минобра чтобы понять однодневное меню они разрабатывают или многодневное
    public $days1;
    public $days2;
    public $days3;
    public $days4;
    public $days5;
    public $days6;
    public $days7;
    public $nutrition1;
    public $nutrition2;
    public $nutrition3;
    public $nutrition4;
    public $nutrition5;
    public $nutrition6;
    public $show_indicator;
    public $date_start;
    public $date_end;

    public $municipality;
    public $organization_id;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'age', 'characters', 'cycles', 'days', 'nutrition', 'date_start', 'date_end'], 'required'],
            [['characters', 'cycles', 'days1', 'nutrition1', 'show_indicator', 'municipality', 'organization_id'], 'integer'],
            [['days1', 'days2', 'days3', 'days4', 'days5', 'days6', 'days7', 'nutrition1', 'nutrition2', 'nutrition3', 'nutrition4', 'nutrition5', 'nutrition6'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'show_indicator' => 'Настройка видимости',
            'name' => 'Название меню',
            'age' => 'Возрастная категория(лет)',
            'characters' => 'Характеристика питающихся',
            'days1' => 'Понедельник',
            'days2' => 'Вторник',
            'days3' => 'Среда',
            'days4' => 'Четверг',
            'days5' => 'Пятница',
            'days6' => 'Суббота',
            'days7' => 'Воскресенье',
            'nutrition1' => 'Завтрак',
            'nutrition2' => 'Второй завтрак',
            'nutrition3' => 'Обед',
            'nutrition4' => 'Полдник',
            'nutrition5' => 'Ужин',
            'nutrition6' => 'Второй ужин',
            'cycles' => 'Количество недель(цикл)',
            'date_start' => 'Дата начала',
            'date_end' => 'Дата окончания',
            'municipality' => 'Муниципальный район',
            'organization_id' => 'Организация',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
   /*public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByLogin();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный логин или пароль.');
            }
        }
    }*/

    /*public function validateLogin($attribute, $params)
    {
                $this->addError($attribute, 'Неверный логинfsdfsdfsffь.');
    }*/

    /*public function validatePassword($attribute, $params)
    {
                $this->addError($attribute, 'Неверный логин или1 пароль.');
    }*/

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUserByLogin(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    /*protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }*/

    protected function getUserByLogin()
    {
        if ($this->_user === null) {
            $this->_user = User::findByLogin($this->login);
        }

        return $this->_user;
    }
}
