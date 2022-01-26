<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "nutrition_applications".
 *
 * @property int $id
 * @property int $sender_org_id
 * @property int $reciever_org_id
 * @property int $status
 * @property string $created_at
 */
class NutritionApplications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nutrition_applications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_org_id', 'reciever_org_id', 'status', 'type_org_id'], 'required'],
            [['sender_org_id', 'reciever_org_id', 'status', 'type_org_id'], 'integer'],
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
            'sender_org_id' => 'Отправитель заявки',
            'reciever_org_id' => 'Получатель заявки',
            'status' => 'Статус',
            'created_at' => 'Дата поступления',
        ];
    }

    public function get_organization($id){

        $category = Organization::find()->where(['id' => $id])->one();
        if (!empty($category->short_title)){
            return $category->short_title;
        }else{
            return $category->title;
        }
        return $category->title;
    }
    //я как отправитель
    public function get_sender_status(){
        if($this->status == 0){
            return "Отправлена, не рассмотрена";
        }
        if($this->status == 1){
            return "Принята";
        }
        if($this->status == 2){
            return "Отклонена";
        }
        return "Не печатать";
    }

    public function get_receiver_status(){
        if($this->status == 0){
            return "Новая заявка";
        }
        if($this->status == 1){
            return "Принята";
        }
        if($this->status == 2){
            return "Отклонена";
        }
        return "Не печатать";
    }
}
