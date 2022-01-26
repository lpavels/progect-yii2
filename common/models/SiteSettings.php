<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_settings".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $comment
 * @property string $created_at
 * @property string|null $update_at
 */
class SiteSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value', 'comment'], 'required'],
            [['created_at', 'update_at'], 'safe'],
            [['name', 'value', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
        ];
    }
}
