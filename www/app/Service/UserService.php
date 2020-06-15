<?php

namespace App\Service;

use App\Models\OrganizationCode;
use App\Models\User;
use App\Util\Errors\ErrorCode;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class UserService {

    public function create(
        string $name,
        string $surname,
        string $email,
        string $password,
        string $userType = null,
        string $regCode = null
    )
    {
        $userModel = User::where(["email" => $email])->first();
        if($userModel instanceof User)
        {
            return [
                "data" => "Данный эмейл уже зарегистрирован, если вы забыли пароль, нажмите на кнопку забыл пароль",
                "error_code" => ErrorCode::EMAIL_DUPLICATE
            ];
        }

        if($userType === User::USER_TYPE_TEACHER)
        {
            $organizationCodeModel = OrganizationCode::where(["reg_code" => $regCode])->first();
            if(!($organizationCodeModel instanceof OrganizationCode))
            {
                return [
                    "data" => "Код активации не найден",
                    "error_code" => ErrorCode::REG_CODE_NOT_FOUND
                ];
            }

            $organizationCodeModel = OrganizationCode::where(["reg_code" => $regCode, "used" => true])->first();
            if($organizationCodeModel instanceof OrganizationCode)
            {
                return [
                    "data" => "Данный код активации был уже использован",
                    "error_code" => ErrorCode::REG_CODE_USED
                ];
            }
        }

        $userId = 0;
        DB::transaction(function () use ($name, $surname, $email, $password, $userType, $regCode, &$userId) {
            $userModel = new User();
            $userModel->name = $name;
            $userModel->surname = $surname;
            $userModel->email = $email;
            $userModel->password = sha1($password);
            $userModel->avatar_img_path = "/static/images/no_avatar.jpg";
            $userModel->email_confirmed = false;
            $userModel->user_type = $userType;
            $userModel->save();

            $userId = $userModel->id;

            if($userType === User::USER_TYPE_TEACHER)
            {
                $organizationCodeModel = OrganizationCode::where(["reg_code" => $regCode, "used" => false])->first();
                $organizationCodeModel->user_id = $userModel->id;
                $organizationCodeModel->used = true;
                $organizationCodeModel->save();
            }
        });

        return [
            "data" => "Вы успешно зарегистрировались",
            "userId" => $userId,
            "userType" => $userType
        ];
    }

    public function auth(
        string $email,
        string $password
    ) : ?User
    {
        $userModel = User::where(["email" => $email, "password" => sha1($password)])->first();

        return $userModel;
    }

    public function getUserById(int $userId)
    {
        $user = User::where(["id" => $userId])->first();

        return $user;
    }

    public function editUser(
        int $userId,
        ?int $studentNum,
        ?DateTimeImmutable $birthDate,
        ?string $avatarImgPath
    )
    {
        $user = $this->getUserById($userId);

        if($studentNum)
        {
            $user->student_num = $studentNum;
        }

        if($birthDate)
        {
            $user->birth_date = $birthDate;
        }

        if($avatarImgPath)
        {

            if($user->avatar_img_path !== "/static/images/no_avatar.jpg")
            {
                // Удаляем старую фотку профиля
                $publicPath = public_path('images');
                $explode = explode('/', $user->avatar_img_path);
                unlink($publicPath . '/' . $explode[3]);
            }

            $user->avatar_img_path = $avatarImgPath;
        }

        $user->save();
    }

}
