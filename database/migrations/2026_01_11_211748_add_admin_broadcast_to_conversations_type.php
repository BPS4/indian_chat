<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Conversation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify enum to include 'admin_broadcast'
        DB::statement("ALTER TABLE conversations MODIFY COLUMN type ENUM('private', 'group', 'admin_broadcast') NOT NULL");
        
        // Get first admin user (not customer role)
        $adminUser = \App\Models\User::where('role_id', '!=', \App\Models\User::CUSTOMER)->first();
        $adminId = $adminUser ? $adminUser->id : 1;
        
        // Create admin broadcast conversation
        Conversation::firstOrCreate(
            ['type' => 'admin_broadcast'],
            [
                'name' => 'Admin Announcements',
                'created_by' => $adminId
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete admin conversation
        Conversation::where('type', 'admin_broadcast')->delete();
        
        // Revert to original enum
        DB::statement("ALTER TABLE conversations MODIFY COLUMN type ENUM('private', 'group') NOT NULL");
    }
};
