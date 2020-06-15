<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement("
                CREATE TYPE user_type AS ENUM ('teacher', 'student');
            ");

            DB::statement('
                CREATE TABLE users
                (
                    id bigserial primary key not null,
                    name varchar not null,
                    surname varchar not null,
                    student_num varchar null,
                    birth_date date null,
                    email varchar not null,
                    password varchar not null,
                    avatar_img_path varchar not null,
                    email_confirmed boolean not null,
                    user_type user_type not null,
                    created_at timestamp,
                    updated_at timestamp,
                    deleted_at timestamp
                );
            ');

            // password 123 in sha1
            DB::statement("
                INSERT INTO users(name, surname, email, password, avatar_img_path, email_confirmed, user_type, created_at, updated_at)
                    VALUES ('Machine', 'Learning', 'ml@ml.ml', '40bd001563085fc35165329ea1ff5c5ecbdbbeef' ,'static/images/no_avatar.jpg', true, 'student', current_timestamp, current_timestamp)
            ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
