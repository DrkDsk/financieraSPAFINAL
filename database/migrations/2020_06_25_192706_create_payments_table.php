<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')->onDelete('cascade');
            $table->integer('numero_pago');
            $table->decimal('cuota',8,2);
            $table->boolean('paid')->default(0);
            $table->date('fecha_pago');
            $table->decimal('monto_recibido',8,2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
