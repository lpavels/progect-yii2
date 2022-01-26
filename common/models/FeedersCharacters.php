<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "feeders_characters".
 *
 * В ТАБЛИЦЕ ХРАНЯТСЯ ХАРАКТЕРИСТИКИ ПИТАЮЩИХСЯ
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 */
class FeedersCharacters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feeders_characters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created_at' => 'Дата добавления в базу',
        ];
    }
}
