<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamificationTables extends Migration
{
    public function up()
    {
        // 1. Member Points Summary
        Schema::create('member_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('available_points')->default(0);
            $table->integer('redeemed_points')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
            $table->index('total_points');
        });

        // 2. Badges Master
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_my');
            $table->text('description');
            $table->text('description_my');
            $table->text('icon_svg')->nullable(); // SVG path or heroicons name
            $table->string('tier');
            $table->integer('points_awarded')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Badge Earnings (Pivot)
        Schema::create('badge_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->unsignedBigInteger('source_event_id')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'badge_id']);
        });

        // 4. Reward Catalog
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_my');
            $table->text('description');
            $table->string('category');
            $table->integer('points_cost');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Point Transactions (Audit Trail)
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->integer('points');
            $table->integer('balance_after');
            $table->string('reason');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });

        // 6. Reward Redemptions with Fulfillment
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->integer('points_spent');
            $table->string('status')->default('pending');
            $table->timestamp('redeemed_at');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('claim_code')->nullable();
            $table->text('fulfillment_notes')->nullable();
            $table->foreignId('fulfilled_by')->nullable()->constrained('users');
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });

        // 7. Tier Milestones
        Schema::create('tier_milestones', function (Blueprint $table) {
            $table->id();
            $table->string('tier')->unique();
            $table->integer('min_points');
            $table->string('name');
            $table->string('name_my');
            $table->text('benefits');
            $table->text('benefits_my');
            $table->text('icon_svg')->nullable();
            $table->timestamps();
        });

        // 8. Leaderboard Preferences (merged with user update)
    }

    public function down()
    {
        Schema::dropIfExists('reward_redemptions');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('badge_earnings');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('member_points');
        Schema::dropIfExists('tier_milestones');
    }
}
