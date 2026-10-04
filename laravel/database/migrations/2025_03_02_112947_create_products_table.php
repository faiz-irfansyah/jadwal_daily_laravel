<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('seri');
            $table->enum( 'merk',allowed: ['samsung','vivo','iphone','oppo']);
            $table->enum(column:'sistem',allowed:['android','ios'] );
            $table->string(column:'ukuran');
            $table->string(column:'kamera_depan');
            $table->string(column:'kamera_belakang');
            $table->bigInteger('price');
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
