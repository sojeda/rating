<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Ratings extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('rateable_id');
            $table->string('rateable_type');

            $table->integer('qualifier_id')->nullable();
            $table->string('qualifier_type')->nullable();

            $table->float('score', 9, 2);
            $table->text('comments')->nullable();
            $table->string('cause')->nullable();

            $table->date('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
}
