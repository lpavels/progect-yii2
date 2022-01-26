<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int $sender_user_id
 * @property int $receiver_user_id
 * @property string $message
 * @property string $created_at
 */
class Chat extends \yii\db\ActiveRecord
{
    public $fio;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_user_id', 'receiver_user_id', 'message'], 'required'],
            [['sender_user_id', 'receiver_user_id'], 'integer'],
            [['message'], 'string'],
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
            'sender_user_id' => 'Sender User ID',
            'receiver_user_id' => 'Receiver User ID',
            'message' => 'Message',
            'created_at' => 'Created At',
        ];
    }
}
