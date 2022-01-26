<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "training_plan".
 *
 * @property int $id ID
 * @property int $user_id Пользователь
 * @property string $field1
 * @property string $field2
 * @property string $field3
 * @property string $field4
 * @property string $field5
 * @property string $field6
 * @property string $field7
 * @property string $field8
 * @property string $field9
 * @property string $field10
 * @property string $field11
 * @property string $field12
 * @property string $field13
 * @property string $field14
 * @property string $creat_at
 */
class TrainingPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7', 'field8', 'field9', 'field10', 'field11', 'field12', 'field13'], 'required'],
            [['creat_at'], 'safe'],
            [['field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7', 'field8', 'field9', 'field10', 'field11', 'field12', 'field13', 'field14'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field1' => 'Планируемые сроки формирования у детей навыков пищевого и гигиенического поведения',
            'field2' => '1. Мой руки перед едой (как правильно мыть руки; почему надо мыть руки)',
            'field3' => '2. Когда я ем я глух и нем',
            'field4' => '3. Ешь не спеша, во время еды не отвлекайся, старательно пережёвывай пищу',
            'field5' => '4. Не ешь пищу, которая упала на пол',
            'field6' => '5. Ешь только за чистым столом и только из чистой посуды',
            'field7' => '6. После еды убери за собой',
            'field8' => '7. После еды мой руки и полощи рот',
            'field9' => '8. Каждый день ешь фрукты и овощи, пей молоко',
            'field10' => '9. Ешь не реже 4-х раз в день (на завтрак кашу, на обед салат, суп, второе и компот, на полдник булочку или фрукт, на ужин второе блюдо, перед сном кефир)',
            'field11' => '10. Не ешь на ходу',
            'field12' => '11. Умей отличать здоровые продукты (фрукты, овощи, молоко) от пустых продуктов (конфеты, чипсы; колбасы)',
            'field13' => '12. Учись рассказывать родителям, чем кормили в детском саду, что понравилось, а что нет',
            'group_id' => 'Группа',
        ];
    }
}
