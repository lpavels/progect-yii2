<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AnketParentControl */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Anket Parent Controls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="anket-parent-control-view">

    <h1>Подробный просмотр</h1>



    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'organization_id',
            //'date',
            'name',
            'question1',
            'question2',
            'question3',
            'question4',
            'question5',
            'question6',
            'question7',
            'question8',
            'question9',
            'question10',
            'question11',
            'question12',
            'question13',
            'question14',
            'count',
            'masa_porcii',
            'masa_othodov',
            'created_at',
        ],
    ]) ?>

</div>
