<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('name', 35);
            $table->string('company', 35);
            $table->string('street1', 35);
            $table->string('street2', 35);
            $table->string('street3', 35);
            $table->string('postcode', 12);
            $table->string('city', 35);
            $table->string('state', 35);
            $table->string('country', 2);
            $table->string('phone', 35);
            $table->string('extension', 5);
            $table->string('email', 50);
            $table->string('tax_id', 20);
            $table->string('eori_number', 20);
            $table->boolean('is_residential')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->date('date');
            $table->enum('type', ['Doc', 'Non-Doc']);
            $table->string('service', 50);
            $table->string('tracking_number', 10)->nullable();
            $table->foreignUuid('shipper')->nullable()->constrained('addresses', 'id')->cascadeOnDelete();
            $table->foreignUuid('consignee')->nullable()->constrained('addresses', 'id')->cascadeOnDelete();
            $table->enum('purpose', ['Commercial', 'Gift', 'Sample', 'Return', 'Repair', 'Personal Effects', 'Personal Use', 'Documents']);
            $table->string('contents', 90);
            $table->enum('status', ['InfoReceived', 'InTransit', 'OutForDelivery', 'AttemptFail', 'Delivered', 'AvailableForPickup', 'Exception', 'Expired', 'Pending']);
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
        Schema::dropIfExists('shipments');
    }
}
