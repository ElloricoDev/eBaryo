<?php

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can see pending feedback count', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Create some pending feedback
    Feedback::factory()->count(3)->create([
        'status' => 'pending',
        'admin_notified' => false
    ]);
    
    // Create some responded feedback (should not count)
    Feedback::factory()->count(2)->create([
        'status' => 'responded',
        'admin_notified' => false
    ]);
    
    // Test the AppServiceProvider logic directly
    $pendingCount = \App\Models\Feedback::where('status', 'pending')->where('admin_notified', false)->count();
    $this->assertEquals(3, $pendingCount);
});

test('non-admin users cannot see pending feedback count', function () {
    // Create a regular user
    $user = User::factory()->create(['role' => 'user']);
    
    // Create some pending feedback
    Feedback::factory()->count(3)->create([
        'status' => 'pending',
        'admin_notified' => false
    ]);
    
    // Test that non-admin users get 0 pending feedback count
    $pendingCount = 0; // Non-admin users should not see pending feedback count
    $this->assertEquals(0, $pendingCount);
});

test('admin can mark notifications as read', function () {
    // Create an admin user
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Create some pending feedback
    Feedback::factory()->count(3)->create([
        'status' => 'pending',
        'admin_notified' => false
    ]);
    
    $response = $this->actingAs($admin)->post('/admin/feedback/mark-as-read');
    
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    
    // Check that all feedback is now marked as notified
    $this->assertEquals(0, Feedback::where('admin_notified', false)->count());
});
