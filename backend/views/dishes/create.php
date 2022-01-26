<?php

use common\models\RecipesCollection;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dishes */

$this->title = 'Добавление блюда';
$this->params['breadcrumbs'][] = ['label' => 'Создание блюда', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dishes-create container">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <?if(RecipesCollection::find()->where(['organization_id' => Yii::$app->user->identity->organization_id])->count() == 0){?>
        <p style="color: red"><b>У вас не создано ни одного сборника. Создайте сборник чтобы добавлять в него блюда.</b></p>
    <?}?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
