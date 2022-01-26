<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class AllergenForm extends Model
{
    public $menu_id;
    public $cycle;
    public $allergen1;
    public $allergen2;
    public $allergen3;
    public $allergen4;
    public $allergen5;
    public $allergen6;
    public $allergen7;
    public $allergen8;



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['menu_id', 'cycle'], 'required'],
            [[
                'menu_id', 'cycle',
                'allergen1',
                'allergen2',
                'allergen3',
                'allergen4',
                'allergen5',
                'allergen6',
                'allergen7',
                'allergen8',

            ], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'menu_id' => 'Меню',
            'cycle' => 'Цикл(Неделя)',

        ];
    }
}
