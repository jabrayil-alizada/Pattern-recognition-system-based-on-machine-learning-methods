<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\UserService;
use App\Util\Errors\ErrorCode;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{

    protected $_userService;

    /**
     * UserController constructor.
     * @param $userService
     */
    public function __construct(UserService $userService)
    {
        $this->_userService = $userService;
    }


    public function registerFront()
    {
        return view('user.register');
    }

    public function registerBack(Request $request)
    {
        $name = $request->input("name");
        $surname = $request->input("surname");
        $email = $request->input("email");
        $password = $request->input("password");
        $userType = $request->input("user_type");
        $regCode = $request->input("reg_code");

        if(
            !$name
            ||
            !$surname
            ||
            !$email
            ||
            !$password
            ||
            ($userType === User::USER_TYPE_TEACHER && !$regCode)
        )
        {
            $result = [
                "data" => "Все поля должны быть заполнены",
                "error_code" => ErrorCode::NOT_FILLED_INPUT
            ];

            return view('user.register', ["message" => $result["data"]]);
        }

        $result = $this->_userService->create($name, $surname, $email, $password, $userType, $regCode);

        if(isset($result["error_code"]))
        {
            return view('user.register', ["message" => $result["data"]]);
        }

        session(['userId' => $result['userId']]);
        session(['userType' => $result['userType']]);

        return redirect('/');
    }

    public function authFront()
    {
        $data = session('data');

        $message = $data ? $data['data'] : '';

        return view('user.auth', ["message" => $message]);
    }

    public function authBack(Request $request, Response $response)
    {
        $email = $request->input("email");
        $password = $request->input("password");

        if(
            !$email
            ||
            !$password
        )
        {
            $result = [
                "data" => "Все поля должны быть заполнены",
                "error_code" => ErrorCode::NOT_FILLED_INPUT
            ];

            return view('user.auth', ["message" => $result["data"]]);
        }

        $user = $this->_userService->auth($email, $password);

        if($user instanceof User)
        {
            session(['userId' => $user->id]);
            session(['userType' => $user->user_type]);

            return redirect('/');
        }

        $data = [
            'data' => "Емейл или пароль введен не верно"
        ];

        return \App\Util\Helper\Response::prepareUtf8JsonResponse($data);
    }

    public function logout(Request $request, Response $response)
    {
        $userId = session('userId');

        if($userId)
        {
            session()->forget('userId');
        }

        return redirect('/user/auth');
    }

    public function testFront()
    {
        $data = [
            'userID' => session('userId'),
            'userType' => session('userType')
        ];

        return print_r($data, 1) ;
    }
}
