<?php

namespace App\Service;

use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\TestQuestion;
use App\Models\TestQuestionAnswer;
use App\Util\Errors\ErrorCode;
use App\Util\Helper\Generator;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class TestService {

    public function create(
        int $userId,
        string $name,
        DateTimeImmutable $startDate = null,
        DateTimeImmutable $endDate = null)
    {
        $testPass = Generator::genRandomSixDigitNumber();

        $testModel = new Test();
        $testModel->user_id = $userId;
        $testModel->name = $name;
        $testModel->test_pass = $testPass;
        $testModel->start_date = $startDate;
        $testModel->end_date = $endDate;
        $testModel->save();

        return $testModel;
    }

    public function getTestsByUserId(int $userId)
    {
        $testModel = Test::leftJoin('test_answers as ta', 'tests.id', '=', 'ta.test_id')
            ->select(DB::raw('
                count(ta.id) as answer_count,
                tests.id as id,
                tests.user_id as user_id,
                tests.name as name,
                tests.test_pass as test_pass,
                tests.start_date as start_date,
                tests.end_date as end_date,
                tests.created_at as tests_created_at
            '))
            ->where(["tests.user_id" => $userId])
            ->groupBy('tests.id')
            ->get();

        return $testModel;
    }

    public function createTestQuestion(
        int $testId,
        string $question,
        string $questionImagePath)
    {
        $testAnswerModel = TestAnswer::where(["test_id" => $testId])->first();

        if($testAnswerModel instanceof TestAnswer)
        {
            return [
                "data" => "Нельзя добавить вопрос в тест, который уже пройден студентами",
                "error_code" => ErrorCode::ADDING_QUESTION_TO_ANSWERED_TEST_FORBIDDEN
            ];
        }

        $testQuestionModel = new TestQuestion();
        $testQuestionModel->test_id = $testId;
        $testQuestionModel->question_image_path = $questionImagePath;
        $testQuestionModel->question = $question;
        $testQuestionModel->save();

        return [
            "data" => "Вопрос был успешно добавлен к тесту"
        ];
    }

    public function getQuestionsByTestId(int $testId)
    {
        $testQuestionModel = TestQuestion::where(["test_id" => $testId])->get();

        return $testQuestionModel;
    }

    public function getAnswersByTestId(int $testId)
    {
        $testAnswerModel = TestAnswer::join('users as u', 'u.id', '=', 'test_answers.user_id')
            ->select(
                "test_answers.id as test_answers_id",
                "test_answers.test_id as test_id",
                "test_answers.user_id as user_id",
                "test_answers.teacher_answer_status as teacher_answer_status",
                "test_answers.total_score as total_score",
                "u.name as user_name",
                "u.surname as user_surname",
                "u.student_num as user_student_num",
                "u.email as user_email"
            )
            ->where(["test_answers.test_id" => $testId])->get();

        return $testAnswerModel;
    }

    public function getQuestionAnswersByTestAnswerId(int $testAnswerId)
    {
        $testAnswerModel = TestAnswer::join('test_question_answers as tqa', 'test_answers.id', '=', 'tqa.test_answer_id')
            ->join('test_questions as tq', 'tq.id', '=', 'tqa.test_question_id')
            ->select(
                "test_answers.id as test_answers_id",
                "test_answers.test_id as test_id",
                "test_answers.user_id as user_id",
                "test_answers.teacher_answer_status as teacher_answer_status",
                "test_answers.total_score as total_score",
                "tqa.answer_bool as answer_bool",
                "tqa.answer as answer",
                "tq.question_image_path as question_image_path",
                "tq.question as question"
            )
            ->where(["test_answers.id" => $testAnswerId])->get();

        return $testAnswerModel;
    }

    public function setAnswerScore(int $answerId, int $totalScore)
    {
        $testAnswerModel = TestAnswer::where(["id" => $answerId])->first();
        $testAnswerModel->total_score = $totalScore;
        $testAnswerModel->teacher_answer_status = 'finished';
        $testAnswerModel->user_answer_status = 'checked';
        $testAnswerModel->save();

        return $testAnswerModel->test_id;
    }

    public function addUserToTest (
        int $userId,
        int $testId,
        int $testPass
    )
    {
        $testModel = Test::where(["id" => $testId, "test_pass" => $testPass])->first();

        if (!($testModel instanceof Test))
        {
            return [
                "data" => "Тест не найден, проверьте корректность введенного id и пароля",
                "error_code" => ErrorCode::TEST_NOT_FOUND
            ];
        }

        $checkAlreadyTakedTest = TestAnswer::where(["test_id" => $testId, "user_id" => $userId])->first();
        if ($checkAlreadyTakedTest instanceof TestAnswer)
        {
            return [
                "data" => "Вы уже взяли этот тест",
                "error_code" => ErrorCode::ALREADY_TAKED_TEST
            ];
        }

        $testAnswerModel = new TestAnswer();
        $testAnswerModel->test_id = $testId;
        $testAnswerModel->user_id = $userId;
        $testAnswerModel->user_answer_status = "registered";
        $testAnswerModel->teacher_answer_status = "registered";
        $testAnswerModel->save();

        return [
            "data" => "Вы успешно взяли тест"
        ];
    }

    public function getAnswersByUserId(int $userId)
    {
        $testAnswerModel = TestAnswer::where(["user_id" => $userId])->get();

        return $testAnswerModel;
    }

    public function getQuestionsByAnswerId(int $answerId)
    {
        $testAnswer = TestAnswer::where(["id" => $answerId])->first();

        $testQuestions = $this->getQuestionsByTestId($testAnswer->test_id);

        return [
            "testQuestions" => $testQuestions,
            "testAnswer" => $testAnswer
        ];
    }

    public function setQuestionAnswers(
        int $answerId,
        array $answers
    )
    {
        $testAnswerModel = TestAnswer::where(["id" => $answerId])->first();

        if($testAnswerModel->user_answer_status === TestAnswer::REGISTERED_STATUS)
        {
            DB::transaction(function () use ($answerId, $answers, $testAnswerModel) {
                $testAnswerModel->user_answer_status = 'not_checked';
                $testAnswerModel->save();

                foreach ($answers as $answer)
                {
                    TestQuestionAnswer::create([
                        "test_answer_id" => $answerId,
                        "test_question_id" => $answer["question_id"],
                        "answer_bool" => $answer["answer_bool"],
                        "answer" => $answer["answer"]
                    ]);
                }
            });

            return [
                "data" => "Вы успешно завершили тест"
            ];
        }

        return [
            "data" => "Этот тест уже завершен",
            "error_code" => ErrorCode::TEST_ALREADY_FINISHED
        ];
    }

    public function removeTestQuestion(int $testQuestionId)
    {
        $testQuestionModel = TestQuestion::where(["id" => $testQuestionId])->first();

        if(!($testQuestionModel instanceof TestQuestion))
        {
            return [
                "data" => "Вопрос с таким ID не найден",
                "error_code" => ErrorCode::QUESTION_ID_NOT_FOUND
            ];
        }

        $testQuestionAnswerModel = TestQuestionAnswer::where(["test_question_id" => $testQuestionId])->first();

        if($testQuestionAnswerModel instanceof TestQuestionAnswer)
        {
            return [
                "data" => "Вопрос на который уже ответили студенты, невозможно удалить",
                "error_code" => ErrorCode::ANSWERED_QUESTION_CANT_BE_REMOVED
            ];
        }

        // Удаляем фото вопроса
        $publicPath = public_path('test_images');
        $explode = explode('/', $testQuestionModel->question_image_path);
        unlink($publicPath . '/' . $explode[3]);
        ////

        $testQuestionModel->delete();

        return [
            "data" => "Вопрос успешно удален"
        ];
    }
}
