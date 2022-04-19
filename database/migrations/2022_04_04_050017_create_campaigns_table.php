<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            // gm stand for group message
            // bm stand for bot message
            $table->id();
            $table->text('gm_text');
            $table->json('gm_geo')->nullable();
            $table->json('gm_interest')->nullable();
            $table->integer('gm_claim_now_btn_num_click');
            $table->string('bm_image');
            $table->text('bm_text');
            $table->timestamp('bm_apply_btn_active_duration');
            $table->string('bm_apply_btn_url');
            $table->json('payment_methods');
            $table->json('message_ids')->nullable();
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
        Schema::dropIfExists('campaigns');
    }
};
