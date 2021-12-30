<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignUuid('shipment_id')->constrained('shipments', 'id')->cascadeOnDelete();
            $table->unsignedDecimal('weight', $precision = 10, $scale = 3);
            $table->enum('weight_unit', ['kg', 'lb']);
            $table->unsignedDecimal('length', $precision = 10, $scale = 3);
            $table->unsignedDecimal('width', $precision = 10, $scale = 3);
            $table->unsignedDecimal('height', $precision = 10, $scale = 3);
            $table->enum('dimension_unit', ['cm', 'in']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcels');
    }
}
