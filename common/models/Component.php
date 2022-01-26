<?php

namespace common\models;

use Yii;
use yii\console\Exception;

class Component extends \yii\db\ActiveRecord
{
    const YAER_2021 = 2021;
    const YAER_2022 = 2022;


    public static function ip()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    public static function checkDataReportTable() #проверка на существование записи в таблице отчёта прошлого года
    {
        $user = \Yii::$app->user->identity;

        if (date('Y')== self::YAER_2022 &&$user->transfer == 2021) {
            $report_tbl22 = ReportTbl22::find()->where(['user_id' => $user->id])->exists();

            if (!$report_tbl22) {
                $report_tbl21 = ReportTbl21::findOne(['user_id' => $user->id]);
                unset($report_tbl21['id']);
                $report_tbl22 = new ReportTbl22();
                $report_tbl22->attributes = $report_tbl21->attributes;
                $report_tbl22->save();
            }
        }
    }
}
