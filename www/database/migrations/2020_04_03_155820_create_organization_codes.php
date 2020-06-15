<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class CreateOrganizationCodes extends Migration
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
                CREATE TABLE organization_codes
                (
                    id bigserial primary key not null,
                    user_id bigint null,
                    name varchar not null,
                    abbreviation varchar not null,
                    reg_code varchar unique not null,
                    used bool not null,
                    created_at timestamp,
                    updated_at timestamp,
                    deleted_at timestamp
                );
            ');

            DB::statement('
                ALTER TABLE organization_codes ADD CONSTRAINT organization_codes_user_id_fk FOREIGN KEY (user_id) REFERENCES users(id);
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
