<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomFieldsTables extends Migration
{
    public function up()
    {
        Schema::create(config('custom-fields.tables.fields', 'custom_fields'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('model_id');
            $table->string('model_type');
            $table->string('type');
            $table->boolean('required')->default(false);
            $table->json('answers')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('default_value')->nullable();
            $table->string('order');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create(config('custom-fields.tables.field_responses', 'custom_field_responses'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_id');
            $table->foreign('field_id')->references('id')->on(config('custom-fields.tables.fields', 'custom_fields'));
            $table->unsignedInteger('model_id');
            $table->string('model_type');
            $table->string('value_str')->nullable();
            $table->text('value_text')->nullable();
            $table->integer('value_int')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('custom-fields.tables.fields', 'custom_fields'));
        Schema::dropIfExists(config('custom-fields.tables.field_responses', 'custom_field_responses'));
    }
}
