<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use App\Jobs\ProcessDocumentJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test user registration and login.
     */
    public function test_user_can_register_and_login(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'user'
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Test document upload validation constraints.
     */
    public function test_document_upload_validation(): void
    {
        Storage::fake('local');
        Queue::fake();

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        // Test 1: Non-PDF upload should fail
        $invalidFile = UploadedFile::fake()->create('document.txt', 500, 'text/plain');
        $response = $this->post('/dashboard/upload', [
            'title' => 'Invalid File',
            'target_text' => 'test text',
            'document' => $invalidFile,
        ]);
        $response->assertSessionHasErrors(['document']);

        // Test 2: PDF over 10MB should fail
        $largeFile = UploadedFile::fake()->create('large.pdf', 11000, 'application/pdf'); // ~11MB
        $response = $this->post('/dashboard/upload', [
            'title' => 'Too Large File',
            'target_text' => 'test text',
            'document' => $largeFile,
        ]);
        $response->assertSessionHasErrors(['document']);

        // Test 3: Valid PDF under 10MB should succeed
        $validFile = UploadedFile::fake()->create('valid.pdf', 2000, 'application/pdf'); // ~2MB
        $response = $this->post('/dashboard/upload', [
            'title' => 'Valid Meeting Report',
            'target_text' => 'อนุมัติงบประมาณ',
            'document' => $validFile,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('documents', [
            'title' => 'Valid Meeting Report',
            'target_text' => 'อนุมัติงบประมาณ',
            'status' => 'pending'
        ]);

        // Assert job was pushed to queue
        Queue::assertPushed(ProcessDocumentJob::class);
    }

    /**
     * Test security and authorization of private file access.
     */
    public function test_private_file_access_authorization(): void
    {
        Storage::fake('local');

        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a document belonging to user1
        $document = Document::create([
            'user_id' => $user1->id,
            'title' => 'Private Report',
            'file_path' => 'documents/secret.pdf',
            'target_text' => 'something',
            'status' => 'pending',
        ]);
        
        // Write fake file to storage
        Storage::put('documents/secret.pdf', 'dummy pdf content');

        // Test 1: User 1 (owner) can view the file
        $this->actingAs($user1);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertStatus(200);

        // Test 2: User 2 (not owner) cannot view the file (403 Forbidden)
        $this->actingAs($user2);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertStatus(403);

        // Test 3: Admin (master access) can view the file
        $this->actingAs($admin);
        $response = $this->get("/documents/{$document->id}/view");
        $response->assertStatus(200);
    }
}
