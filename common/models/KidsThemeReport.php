<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "kids_theme_report".
 *
 * @property int $id
 * @property int|null $municipality_id
 * @property int|null $theme1
 * @property int|null $theme2
 * @property int|null $theme3
 * @property int|null $theme4
 * @property int|null $theme5
 * @property int|null $theme6
 * @property int|null $theme7
 * @property int|null $theme8
 * @property int|null $theme9
 * @property int|null $theme10
 * @property int|null $theme11
 * @property int|null $theme12
 * @property int|null $theme13
 * @property int|null $theme14
 * @property string|null $created_at
 *
 * @property Municipality $municipality
 */
class KidsThemeReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kids_theme_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['municipality_id', 'theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10', 'theme11', 'theme12', 'theme13', 'theme14'], 'integer'],
            [['created_at'], 'safe'],
            [['municipality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Municipality::className(), 'targetAttribute' => ['municipality_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'municipality_id' => 'Municipality ID',
            'theme1' => 'Theme1',
            'theme2' => 'Theme2',
            'theme3' => 'Theme3',
            'theme4' => 'Theme4',
            'theme5' => 'Theme5',
            'theme6' => 'Theme6',
            'theme7' => 'Theme7',
            'theme8' => 'Theme8',
            'theme9' => 'Theme9',
            'theme10' => 'Theme10',
            'theme11' => 'Theme11',
            'theme12' => 'Theme12',
            'theme13' => 'Theme13',
            'theme14' => 'Theme14',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Municipality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipality()
    {
        return $this->hasOne(Municipality::className(), ['id' => 'municipality_id']);
    }
}
