<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTests extends Migration
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
                CREATE TABLE tests
                (
                    id bigserial primary key not null,
                    user_id bigint not null,
                    name varchar not null,
                    test_pass varchar not null,
                    start_date timestamp null,
                    end_date timestamp null,
                    created_at timestamp,
                    updated_at timestamp,
                    deleted_at timestamp
                );
            ');

            DB::statement('
                ALTER TABLE tests ADD CONSTRAINT tests_user_id_fk FOREIGN KEY (user_id) REFERENCES users(id);
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
