<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "themes_content".
 *
 * @property int $id
 * @property int $theme_program_id
 * @property string $content
 * @property string $created_at
 */
class ThemesContent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'themes_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['theme_program_id', 'content'], 'required'],
            [['theme_program_id'], 'integer'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme_program_id' => 'Theme Program ID',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }
}
