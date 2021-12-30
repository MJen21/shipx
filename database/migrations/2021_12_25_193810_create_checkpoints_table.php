<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shipment_id')->constrained('shipments', 'id')->cascadeOnDelete();
            $table->foreignUuid('parcel_id')->nullable()->constrained('parcels', 'id');
            $table->string('slug', 35);
            $table->timestamp('checkpoint_time');
            $table->string('code');
            $table->text('message');
            $table->string('location', 35);
            $table->string('country_iso3', 3);
            $table->string('country_name', 35);
            $table->enum('tag', ['InfoReceived', 'InTransit', 'OutForDelivery', 'AttemptFail', 'Delivered', 'AvailableForPickup', 'Exception', 'Expired', 'Pending']);
            $table->enum('subtag', [
                'Delivered_001',
                'Delivered_002',
                'Delivered_003',
                'Delivered_004',
                'AvailableForPickup_001',
                'Exception_001',
                'Exception_002',
                'Exception_003',
                'Exception_004',
                'Exception_005',
                'Exception_006',
                'Exception_007',
                'Exception_008',
                'Exception_009',
                'Exception_010',
                'Exception_011',
                'Exception_012',
                'Exception_013',
                'AttemptFail_001',
                'AttemptFail_002',
                'AttemptFail_003',
                'InTransit_001',
                'InTransit_002',
                'InTransit_003',
                'InTransit_004',
                'InTransit_005',
                'InTransit_006',
                'InTransit_007',
                'InTransit_008',
                'InTransit_009',
                'InfoReceived_001',
                'OutForDelivery_001',
                'OutForDelivery_003',
                'OutForDelivery_004',
                'Pending_001',
                'Expired_001'
            ]);
            $table->string('subtag_message');
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
        Schema::dropIfExists('checkpoints');
    }
}
