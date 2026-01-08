<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('angsurans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinjaman_id');
            $table->string('nik');
            $table->string('anggota_name')->nullable();
            $table->integer('angsuran_ke');
            $table->decimal('jumlah_angsuran', 15, 2)->nullable();
            $table->string('jenis_bunga')->nullable();
            $table->decimal('bunga_angsuran', 5, 2)->nullable();
            $table->date('tenggat_waktu');
            $table->date('tanggal_bayar')->nullable();
            $table->string('jenis')->nullable();
            $table->string('bukti')->nullable();
            $table->string('status')->default('belum_bayar');
            $table->boolean('is_didenda')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsurans');
    }
};
