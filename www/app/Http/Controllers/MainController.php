<?php

namespace App\Http\Controllers;

use App\Service\TestService;
use App\Util\Errors\ErrorCode;
use App\Util\Helper\CheckUser;
use Illuminate\Http\Request;

class MainController extends Controller
{

    protected $_testService;

    public function __construct(TestService $testService)
    {
        $this->_testService = $testService;
    }

    public function mainFront(Request $request)
    {
        $userId = session('userId');
        $message = $request->input('message');

        if(CheckUser::isTeacher())
        {
            $tests = $this->_testService->getTestsByUserId($userId);
            $userTests = '';
        }
        else
        {
            $userTests = $this->_testService->getAnswersByUserId($userId);
            $tests = '';
        }

        return view('main', [
            "tests" => $tests,
            "userId" => $userId,
            "userTests" => $userTests,
            "message" => $message
        ]);
    }
}
