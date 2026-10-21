<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('event_key', 191);
            $table->string('kind', 40);
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'event_key']);
            $table->index(['user_id', 'read_at', 'id']);
        });
        Schema::create('lead_digest_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('digest_date');
            $table->string('status', 30)->default('pending');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('retry_at')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('last_error')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'digest_date']);
        });
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->restrictOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained('contact_inquiries')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('direction', 10);
            $table->string('phone', 30);
            $table->text('body');
            $table->string('provider_message_id', 191)->nullable()->unique();
            $table->uuid('client_reference')->nullable()->unique();
            $table->string('status', 30);
            $table->timestamp('provider_timestamp')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('error_code', 30)->nullable();
            $table->string('error_message')->nullable();
            $table->timestamps();
            $table->index(['lead_id', 'id']);
        });
        Schema::create('whatsapp_delivery_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('provider_message_id', 191);
            $table->string('status', 20);
            $table->string('recipient', 30)->nullable();
            $table->timestamp('occurred_at');
            $table->string('error_code', 30)->nullable();
            $table->timestamps();
            $table->unique(['provider_message_id', 'status', 'occurred_at'], 'whatsapp_receipt_event_unique');
        });
        DB::table('contact_inquiries')->where('source', 'whatsapp')->whereNotNull('lead_id')
            ->whereNotNull('external_message_id')->orderBy('id')->chunkById(100, function ($inquiries) {
                foreach ($inquiries as $inquiry) {
                    DB::table('whatsapp_messages')->insertOrIgnore([
                        'lead_id' => $inquiry->lead_id, 'inquiry_id' => $inquiry->id,
                        'direction' => 'inbound', 'phone' => $inquiry->phone, 'body' => $inquiry->message,
                        'provider_message_id' => $inquiry->external_message_id, 'status' => 'received',
                        'provider_timestamp' => $inquiry->created_at, 'created_at' => $inquiry->created_at, 'updated_at' => $inquiry->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        if (DB::table('whatsapp_messages')->exists() || DB::table('lead_digest_deliveries')->exists() || DB::table('lead_notifications')->exists()) {
            throw new RuntimeException('Back up notification and message history before restoring the previous database.');
        }
        Schema::dropIfExists('whatsapp_delivery_receipts');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('lead_digest_deliveries');
        Schema::dropIfExists('lead_notifications');
    }
};
