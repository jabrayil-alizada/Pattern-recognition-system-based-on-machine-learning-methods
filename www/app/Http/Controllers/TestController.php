<?php

namespace App\Http\Controllers;

use App\Service\TestService;
use App\Util\Errors\ErrorCode;
use Illuminate\Http\Request;

class TestController extends Controller
{

    protected $_testService;

    public function __construct(TestService $testService)
    {
        $this->_testService = $testService;
    }

    public function createTestBack(Request $request)
    {
        $name = $request->input("name");
        $startDate = $request->input("start_date");
        $endDate = $request->input("end_date");

        if(!$name)
        {
            $result = [
                "data" => "Название должно быть заполнено",
                "error_code" => ErrorCode::NOT_FILLED_INPUT
            ];

            return redirect('/')->with(["message" => $result["data"]]);
        }

        $userId = session('userId');

        $result = $this->_testService->create($userId, $name, $startDate, $endDate);

        return redirect('/')->with(["message" => "Тест успешно создан"]);
    }

    public function addQuestionToTestBack(Request $request)
    {
        $testId = $request->input('test_id');
        $question = $request->input('question');

        if($request->file('question_image_path') && $question && $testId) {
            request()->validate([
                'question_image_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = time() . '.' . request()->question_image_path->getClientOriginalExtension();

            request()->question_image_path->move(public_path('test_images'), $imageName);

            $questionImagePath = '/static/test_images/' . $imageName;
        }
        else
        {
            $result =  [
                "data" => "Вопрос обязательно должен содержать снимок УЗИ/Рентген и текст вопроса"
            ];

            return redirect()
                ->route("testQuestions", ["testId" => $testId])
                ->with("message", $result["data"]);
        }

        $result = $this->_testService->createTestQuestion($testId, $question, $questionImagePath);

        return redirect()
            ->route("testQuestions", ["testId" => $testId])
            ->with("message", $result["data"]);
    }

    public function removeQuestionFromTestBack(Request $request)
    {
        $testId = $request->input('test_id');
        $testQuestionId = $request->input('test_question_id');

        $result = $this->_testService->removeTestQuestion($testQuestionId);

        return redirect()
            ->route("testQuestions", ["testId" => $testId])
            ->with("message", $result["data"]);
    }

    public function testQuestionsListFront(int $testId)
    {
        $testQuestions = $this->_testService->getQuestionsByTestId($testId);

        return view('test.questionsList', [
            "testQuestions" => $testQuestions,
            "testId" => $testId
        ]);
    }

    public function testAnswersListFront(int $testId)
    {
        $testAnswers = $this->_testService->getAnswersByTestId($testId);

        return view('test.answersList', ["testAnswers" => $testAnswers]);
    }

    public function testQuestionAnswersListFront(int $answerId)
    {
        $questionAnswers = $this->_testService->getQuestionAnswersByTestAnswerId($answerId);

        return view('test.questionAnswersList', [
            "questionAnswers" => $questionAnswers,
            "testAnswerId" => $answerId
        ]);
    }

    public function testAnswersTotalScoreBack(Request $request)
    {
        $answerId = $request->input('answer_id');
        $totalScore = $request->input('total_score');

        $testId = $this->_testService->setAnswerScore($answerId, $totalScore);

        return redirect()->route('testAnswers', [
            "testId" => $testId
        ])->with(["message" => "Ответ успешно оценен"]);
    }

    public function studentAddTestBack(Request $request)
    {
        $userId = $request->input('user_id');
        $testId = $request->input('test_id');
        $testPass = $request->input('test_pass');

        if(!$testId || !$testPass || !is_numeric($testId) || !is_numeric($testPass))
        {
            return redirect()->route('main')
                ->with(["message" => "Все поля должны быть заполнены, и должны быть цифрами"]);
        }

        $result = $this->_testService->addUserToTest($userId, $testId, $testPass);

        return redirect()->route('main')
            ->with(["message" => $result["data"]]);
    }

    public function studentAnswerTestFront(int $answerId)
    {
        $result = $this->_testService->getQuestionsByAnswerId($answerId);

        return view('student.test.answer', [
            "questions" => $result["testQuestions"],
            "testAnswer" => $result["testAnswer"]
        ]);
    }

    public function studentAnswerTestBack(Request $request)
    {
        $answerId = $request->input('answer_id');
        $answers = $request->input("answers");

        $result = $this->_testService->setQuestionAnswers($answerId, $answers);

        return redirect()->route('main')
            ->with(["message" => $result["data"]]);
    }
}
