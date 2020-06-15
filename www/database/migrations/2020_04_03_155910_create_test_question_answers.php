<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class CreateTestQuestionAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement('
                CREATE TABLE test_question_answers
                (
                    id bigserial primary key not null,
                    test_answer_id bigint not null,
                    test_question_id bigint not null,
                    answer_bool bool not null,
                    answer varchar null,
                    created_at timestamp,
                    updated_at timestamp,
                    deleted_at timestamp
                );
            ');

            DB::statement('
                ALTER TABLE test_question_answers ADD CONSTRAINT test_question_answers_test_answer_id_fk FOREIGN KEY (test_answer_id) REFERENCES test_answers(id);
            ');

            DB::statement('
                ALTER TABLE test_question_answers ADD CONSTRAINT test_question_answers_test_question_id_fk FOREIGN KEY (test_question_id) REFERENCES test_questions(id);
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
