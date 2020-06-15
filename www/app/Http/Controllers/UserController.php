<?php

namespace App\Http\Controllers;

use App\Service\UserService;
use DateTimeImmutable;
use Illuminate\Http\Request;

class UserController extends Controller
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

    public function profileFront()
    {
        $userId = session('userId');

        $user = $this->_userService->getUserById($userId);

        return view('user.profile', [
            "user" => $user
        ]);
    }

    public function profileEditFront()
    {
        $userId = session('userId');

        $user = $this->_userService->getUserById($userId);

        return view('user.editProfile', [
            "user" => $user
        ]);
    }

    public function profileEditBack(Request $request)
    {
        $userId = session('userId');

        $studentNum = $request->input('student_num');
        $birthDate = $request->input('birth_date');

        $birthDateImmutable = $birthDate ? (new DateTimeImmutable($birthDate)) : null;

        if($request->file('avatar_img_path'))
        {
            request()->validate([
                'avatar_img_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = time().'.'.request()->avatar_img_path->getClientOriginalExtension();

            request()->avatar_img_path->move(public_path('images'), $imageName);

            $avatarImgPath = '/static/images/' . $imageName;
        }
        else
        {
            $avatarImgPath = '';
        }

        $this->_userService->editUser($userId, $studentNum, $birthDateImmutable, $avatarImgPath);

        return redirect('/user/profile')->with('Профиль успешно изменен');
    }

}
