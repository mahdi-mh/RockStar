<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Order\Enums\OrderConsumeLocation;
use Modules\Order\Enums\OrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('orders', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete();

            $table->enum('status', collect(OrderStatus::cases())->pluck('value')->toArray())
                ->default(OrderStatus::ORDERING->value);

            $table->enum('consume_location', collect(OrderConsumeLocation::cases())->pluck('value')->toArray())
                ->default(OrderConsumeLocation::IN_SHOP->value);

            $table->string('address')->nullable()->default(null);

            $table->float('total_price')->default(0.0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
