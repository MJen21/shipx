<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomsDeclarationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customs_declarations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignUuid('shipment_id')->constrained('shipments', 'id')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->string('invoice_number', 35);
            $table->enum('incoterm', ['CFR','CIF','CIP','CPT','DAF','DAP','DAT','DDP','DDU','DEQ','DES','DVU','EXW','FAS','FCA','FOB','DPU']);
            $table->string('currency', 3);
            $table->timestamps();
        });

        Schema::create('customs_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignUuid('shipment_id')->constrained('shipments', 'id')->cascadeOnDelete();
            $table->unsignedTinyInteger('line_number');
            $table->string('description', 75);
            $table->unsignedSmallInteger('quantity');
            $table->enum('quantity_unit', ['BOX','2GM','2M','2M3','3M3','M3','DPR','DOZ','2NO','PCS','GM','GRS','KG','L','M','3GM','3L','X','NO','2KG','PRS','2L','3KG','CM2','2M2','3M2','M2','4M2','3M','CM','CONE','CT','EA','LBS','RILL','ROLL','SET','TU','YDS']);
            $table->unsignedDecimal('net_weight', $precision = 10, $scale = 3);
            $table->unsignedDecimal('gross_weight', $precision = 10, $scale = 3);
            $table->enum('weight_unit', ['kg', 'lb']);
            $table->unsignedDecimal('unit_value', $precision = 12, $scale = 3);
            $table->string('tariff_number', 12);
            $table->string('origin_country', 2);
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
        Schema::dropIfExists('customs_declarations');
    }
}
