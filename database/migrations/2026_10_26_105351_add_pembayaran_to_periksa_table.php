    public function up(): void
    {
        Schema::table('periksa', function (Blueprint $table) {
            $table->enum('status_bayar', ['belum_bayar', 'menunggu_verifikasi', 'lunas'])
                ->default('belum_bayar')->after('biaya_periksa');
            $table->string('bukti_bayar')->nullable()->after('status_bayar');
            $table->timestamp('tgl_upload_bukti')->nullable()->after('bukti_bayar');
            $table->timestamp('tgl_lunas')->nullable()->after('tgl_upload_bukti');
        });
    }

    public function down(): void
    {
        Schema::table('periksa', function (Blueprint $table) {
            $table->dropColumn(['status_bayar', 'bukti_bayar', 'tgl_upload_bukti', 'tgl_lunas']);
        });
    }