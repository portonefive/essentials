<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesAndPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'roles',
            function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('title');
            }
        );

        Schema::create(
            'role_user',
            function (Blueprint $table) {
                $table->string('role_id');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['role_id', 'user_id']);
            }
        );

        Schema::create(
            'permission_groups',
            function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('title');
            }
        );

        Schema::create(
            'permissions',
            function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('title');
                $table->string('permission_group_id');
                $table->foreign('permission_group_id')
                    ->references('id')
                    ->on('permission_groups')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }
        );

        Schema::create(
            'permission_role',
            function (Blueprint $table) {
                $table->string('role_id');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
                $table->string('permission_id');
                $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade')->onDelete('cascade');
                $table->unique(['role_id', 'permission_id']);
            }
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permission_role');
        Schema::drop('permissions');
        Schema::drop('permission_groups');
        Schema::drop('role_user');
        Schema::drop('roles');
    }
}