<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    const REGISTERED_STATUS = "registered";

    const NOT_CHECKED_STATUS = "not_checked";

    const CHEKED_STATUS_STATUS = "checked";

    const REGISTERED_STATUS_RUSSIAN = "еще не прошел тест";

    const NOT_CHECKED_STATUS_RUSSIAN = "учитель ещё не проверил";

    const CHEKED_STATUS_RUSSIAN = "оценен";

    protected $table = 'test_answers';

    protected $guarded = [];

    public function getUserAnswerStatus()
    {
        $userAnswerStatus = $this->attributes['user_answer_status'];

        switch ($userAnswerStatus) {
            case (self::REGISTERED_STATUS):
                $userAnswerStatus = self::REGISTERED_STATUS_RUSSIAN;
                break;
            case (self::NOT_CHECKED_STATUS):
                $userAnswerStatus = self::NOT_CHECKED_STATUS_RUSSIAN;
                break;
            case (self::CHEKED_STATUS_STATUS):
                $userAnswerStatus = self::CHEKED_STATUS_RUSSIAN;
                break;
            default:
                break;
        }

        return $userAnswerStatus;
    }
}
