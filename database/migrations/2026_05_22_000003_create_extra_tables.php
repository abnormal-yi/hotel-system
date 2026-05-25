<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('efd_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->morphs('payable');
            $table->decimal('amount', 12, 2);
            $table->decimal('vat', 12, 2)->default(0);
            $table->string('tin')->nullable();
            $table->string('status')->default('pending');
            $table->text('response')->nullable();
            $table->timestamps();
        });

        Schema::create('smart_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained();
            $table->string('type')->default('pin');
            $table->string('code');
            $table->string('status')->default('inactive');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->foreignId('booking_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::create('cctv_cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('rtsp_url')->nullable();
            $table->string('stream_url')->nullable();
            $table->string('status')->default('offline');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('guest_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_order_id')->constrained('pos_orders');
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->string('unit')->default('pcs');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained();
            $table->string('title');
            $table->text('description');
            $table->string('priority')->default('medium');
            $table->string('status')->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('pos_order_items');
        Schema::dropIfExists('pos_orders');
        Schema::dropIfExists('cctv_cameras');
        Schema::dropIfExists('smart_keys');
        Schema::dropIfExists('efd_transactions');
        Schema::dropIfExists('facilities');
    }
};
