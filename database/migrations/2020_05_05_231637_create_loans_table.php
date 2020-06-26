<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')->onDelete('cascade');
            $table->decimal('amount',8,2);
            $table->decimal('total_pay',8,2);
            $table->integer('payments_number');
            $table->decimal('fee',8,2);
            $table->date('ministry_date');
            $table->date('due_date');
            $table->tinyInteger('finished')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
