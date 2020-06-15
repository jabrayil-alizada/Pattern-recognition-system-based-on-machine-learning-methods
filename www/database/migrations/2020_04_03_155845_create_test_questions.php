<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class CreateTestQuestions extends Migration
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
                CREATE TABLE test_questions
                (
                    id bigserial primary key not null,
                    test_id bigint not null,
                    question_image_path varchar not null,
                    question varchar not null,
                    created_at timestamp,
                    updated_at timestamp,
                    deleted_at timestamp
                );
            ');

            DB::statement('
                ALTER TABLE test_questions ADD CONSTRAINT test_questions_id_fk FOREIGN KEY (test_id) REFERENCES tests(id);
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
