<?php

use common\models\AuthAssignment;
use common\models\Questions;
use common\models\QuestionsVariant;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\QuestionsResponse */

$this->title = 'Входной тест:';
$this->params['breadcrumbs'][] = ['label' => 'Questions Responses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="questions-response-create container">
    <h1><?= Html::encode($this->title) ?></h1>
    <h5 class="text-center">Выберите один из вариантов ответов, который считаете правильным</h5>

    <div class="questions-response-form">

        <?php
        $two_column = ['options' => ['class' => 'row mt-3'], 'labelOptions' => ['class' => 'col-sm-12 col-lg-7 col-form-label font-weight-bold']];
        $training_id = Yii::$app->user->identity->training_id;

        if ($training_id == '1')
        {
            if (AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name == 'school14')
            {
                $type_question = 14;
                $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '1', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '2', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '3', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
                $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '4', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
            }
            elseif (AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name == 'school511')
            {
                $type_question = 511;
                $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '1', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '2', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '3', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
                $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '4', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
            }
            elseif (AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name == 'school56')
            {
                $type_question = 56;
                $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '1', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '2', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '3', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
                $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '4', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
            }
            elseif (AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name == 'school79')
            {
                $type_question = 79;
                $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '1', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '2', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '3', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
                $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '4', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
            }
            elseif (AuthAssignment::find()->where(['user_id' => Yii::$app->user->id])->one()->item_name == 'school1011')
            {
                $type_question = 1011;
                $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '1', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '2', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '3', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
                $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '4', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
            }
            else
            {
                $type_question = 1;
                $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '1', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '2', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('3')->all();
                $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '3', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
                $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '4', 'type_questions' => $type_question])->orderBy(new Expression('rand()'))->limit('2')->all();
            }
        }
        else
        {
            $questions_tupe1 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '5'])->orderBy(new Expression('rand()'))->limit('3')->all();
            $questions_tupe2 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '6'])->orderBy(new Expression('rand()'))->limit('3')->all();
            $questions_tupe3 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '7'])->orderBy(new Expression('rand()'))->limit('2')->all();
            $questions_tupe4 = Questions::find()->where(['training_program_id' => $training_id, 'theme_questions_id' => '8'])->orderBy(new Expression('rand()'))->limit('2')->all();

        }
        //print_r($type_question);

        $form = ActiveForm::begin();
        $count = 0;
        $count_num = 1;
        foreach ($questions_tupe1 as $question)
        {
            $count++;
            //print_r($question->id);
            $question_num = 'question' . $count;

            /*if ($question->type_questions == 0){
                echo $form->field($model, $question_num, $two_column)->textInput(['name' => $question->id, 'class' => 'form-control col-8'])->label($question->name);
            }*/
            //elseif($question->type_questions == 1){
            $question_variants_null = array('' => '');
            $question_variants = QuestionsVariant::find()->where(['questions_id' => $question->id])->all();
            $question_item = ArrayHelper::map($question_variants, 'id', 'name');
            $question_item = ArrayHelper::merge($question_variants_null, $question_item);

            echo $form->field($model, $question_num, $two_column)->dropDownList($question_item,
                [
                    'name' => $question->id,
                    'class' => 'form-control  col-sm-12 col-lg-5'
                ])->label($count_num . '. ' . $question->name);
            //}
            /* elseif($question->type_questions == 3){
                 $question_variants = QuestionsVariant::find()->where(['questions_id'=>$question->id])->all();
                 echo '<h6 class="text-center mb-2">'.$question->name.'</h6>';
                 foreach ($question_variants as $question_variant)
                 {
                     echo $form->field($model, $question_num)->checkbox(['name' => 'question'.$question->id])->label($question_variant->name);
                 }
             }*/
            $count_num++;
        }
        foreach ($questions_tupe2 as $question)
        {
            $count++;
            //print_r($question->id);
            $question_num = 'question' . $count;

            /*if ($question->type_questions == 0){
                echo $form->field($model, $question_num, $two_column)->textInput(['name' => $question->id, 'class' => 'form-control col-8'])->label($question->name);
            }*/
            //elseif($question->type_questions == 1){
            $question_variants_null = array('' => '');
            $question_variants = QuestionsVariant::find()->where(['questions_id' => $question->id])->all();
            $question_item = ArrayHelper::map($question_variants, 'id', 'name');
            $question_item = ArrayHelper::merge($question_variants_null, $question_item);
            echo $form->field($model, $question_num, $two_column)->dropDownList($question_item,
                [
                    'name' => $question->id,
                    'class' => 'form-control  col-sm-12 col-lg-5'
                ])->label($count_num . '. ' . $question->name);
            //}
            /* elseif($question->type_questions == 3){
                 $question_variants = QuestionsVariant::find()->where(['questions_id'=>$question->id])->all();
                 echo '<h6 class="text-center mb-2">'.$question->name.'</h6>';
                 foreach ($question_variants as $question_variant)
                 {
                     echo $form->field($model, $question_num)->checkbox(['name' => 'question'.$question->id])->label($question_variant->name);
                 }
             }*/
            $count_num++;
        }
        foreach ($questions_tupe3 as $question)
        {
            $count++;
            //print_r($question->id);
            $question_num = 'question' . $count;

            /*if ($question->type_questions == 0){
                echo $form->field($model, $question_num, $two_column)->textInput(['name' => $question->id, 'class' => 'form-control col-8'])->label($question->name);
            }*/
            //elseif($question->type_questions == 1){
            $question_variants_null = array('' => '');
            $question_variants = QuestionsVariant::find()->where(['questions_id' => $question->id])->all();
            $question_item = ArrayHelper::map($question_variants, 'id', 'name');
            $question_item = ArrayHelper::merge($question_variants_null, $question_item);
            echo $form->field($model, $question_num, $two_column)->dropDownList($question_item,
                [
                    'name' => $question->id,
                    'class' => 'form-control  col-sm-12 col-lg-5'
                ])->label($count_num . '. ' . $question->name);
            //}
            /* elseif($question->type_questions == 3){
                 $question_variants = QuestionsVariant::find()->where(['questions_id'=>$question->id])->all();
                 echo '<h6 class="text-center mb-2">'.$question->name.'</h6>';
                 foreach ($question_variants as $question_variant)
                 {
                     echo $form->field($model, $question_num)->checkbox(['name' => 'question'.$question->id])->label($question_variant->name);
                 }
             }*/
            $count_num++;
        }
        foreach ($questions_tupe4 as $question)
        {
            $count++;
            //print_r($question->id);
            $question_num = 'question' . $count;

            /* if ($question->type_questions == 0){
                 echo $form->field($model, $question_num, $two_column)->textInput(['name' => $question->id, 'class' => 'form-control col-8'])->label($question->name);
             }*/
            //elseif($question->type_questions == 1){
            $question_variants_null = array('' => '');
            $question_variants = QuestionsVariant::find()->where(['questions_id' => $question->id])->all();
            $question_item = ArrayHelper::map($question_variants, 'id', 'name');
            $question_item = ArrayHelper::merge($question_variants_null, $question_item);
            echo $form->field($model, $question_num, $two_column)->dropDownList($question_item,
                [
                    'name' => $question->id,
                    'class' => 'form-control  col-sm-12 col-lg-5'
                ])->label($count_num . '. ' . $question->name);
            //}
            /* elseif($question->type_questions == 3){
                 $question_variants = QuestionsVariant::find()->where(['questions_id'=>$question->id])->all();
                 echo '<h6 class="text-center mb-2">'.$question->name.'</h6>';
                 foreach ($question_variants as $question_variant)
                 {
                     echo $form->field($model, $question_num)->checkbox(['name' => 'question'.$question->id])->label($question_variant->name);
                 }
             }*/
            $count_num++;
        }
        ?>

        <div class="form-group text-center">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success form-control mt-3 col-sm-12 col-lg-5']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
