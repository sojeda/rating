<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Ratings extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('rateable_id');
            $table->string('rateable_type');

            $table->integer('rater_id')->nullable();
            $table->string('rater_type')->nullable();

            $table->float('rating', 9, 2);
            $table->text('comment')->nullable();
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
