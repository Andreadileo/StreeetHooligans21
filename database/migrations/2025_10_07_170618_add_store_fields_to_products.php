<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $t) {
            $t->decimal('price_compare', 8,2)->nullable()->after('price');   // prezzo barrato
            $t->string('brand')->nullable()->after('name');
            $t->string('color')->nullable()->after('brand');
            $t->json('sizes')->nullable()->after('color');                   // ["S","M","L","XL"]
            $t->json('images')->nullable()->after('image_url');              // galleria
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $t) {
            $t->dropColumn(['price_compare','brand','color','sizes','images']);
        });
    }
};
