<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AnketParentControl */

if(Yii::$app->request->pathInfo == 'anket-parent-control/create')
{
    $this->title = 'Родительский контроль';
}

if(Yii::$app->request->pathInfo == 'anket-parent-control/inside')
{
    $this->title = 'Внутренний контроль';
}

if(Yii::$app->request->pathInfo == 'anket-parent-control/social')
{
    $this->title = 'Общественный контроль';
}
$this->params['breadcrumbs'][] = ['label' => 'Anket Parent Controls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="anket-parent-control-create">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?if(Yii::$app->request->pathInfo == 'anket-parent-control/create'){?>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    <?}?>

    <?if(Yii::$app->request->pathInfo == 'anket-parent-control/inside'){?>
        <?= $this->render('_form2', [
            'model' => $model,
        ]) ?>
    <?}?>

    <?if(Yii::$app->request->pathInfo == 'anket-parent-control/social'){?>
        <?= $this->render('_form3', [
            'model' => $model,
        ]) ?>
    <?}?>

</div>
