<?php

namespace App\Util\Errors;

class ErrorCode
{

    const ALL_FIELDS_MUST_BE_FILLED = 1;

    /**
     * REG_CODE with given ID not found
     */
    const REG_CODE_NOT_FOUND = 3;

    /**
     * User with given ID not found
     */
    const USER_NOT_FOUND = 7;

    /**
     * Only users with confirmed email, phone, passport can take orders
     */
    const NOT_CONFIRMED_USER = 8;

    /**
     * Reg cod already used
     */
    const REG_CODE_USED = 9;

    /**
     * Test with not found
     */
    const TEST_NOT_FOUND = 10;

    /**
     * User with this email already exists
     */
    const EMAIL_DUPLICATE = 11;

    /**
     * User with this phone already exists
     */
    const PHONE_DUPLICATE = 12;

    /**
     * Not filled input
     */
    const NOT_FILLED_INPUT = 13;

    /**
     * Email already have been confirmed
     */
    const EMAIL_ALREADY_COMFIRMED = 14;

    const EMAIL_ACTIVATION_CODE_NOT_FOUND = 15;

    const ALREADY_TAKED_TEST = 16;

    /**
     * If user tries to finish test which has been already finished
     */
    const TEST_ALREADY_FINISHED = 17;

    const QUESTION_ID_NOT_FOUND = 18;

    const ANSWERED_QUESTION_CANT_BE_REMOVED = 19;

    const ADDING_QUESTION_TO_ANSWERED_TEST_FORBIDDEN = 20;
}
