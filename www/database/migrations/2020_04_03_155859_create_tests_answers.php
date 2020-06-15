<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class CreateTestsAnswers extends Migration
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
                CREATE TYPE user_answer_status_type AS ENUM ('registered', 'not_checked', 'checked');
            ");

            DB::statement("
                CREATE TYPE teacher_answer_status_type AS ENUM ('registered', 'finished');
            ");

            DB::statement('
                CREATE TABLE test_answers
                (
                    id bigserial primary key not null,
                    test_id bigint null,
                    user_id bigint null,
                    user_answer_status user_answer_status_type not null,
                    teacher_answer_status teacher_answer_status_type not null,
                    total_score varchar null,
                    created_at timestamp,
                    updated_at timestamp,
                    deleted_at timestamp
                );
            ');

            DB::statement('
                ALTER TABLE test_answers ADD CONSTRAINT test_answers_test_id_fk FOREIGN KEY (test_id) REFERENCES tests(id);
            ');

            DB::statement('
                ALTER TABLE test_answers ADD CONSTRAINT test_answers_user_id_fk FOREIGN KEY (user_id) REFERENCES users(id);
            ');
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
