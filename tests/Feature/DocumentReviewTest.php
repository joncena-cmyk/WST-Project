<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $adviser;
    private User $officer;
    private User $student;
    private Document $document;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->adviser = User::factory()->create([
            'role'           => User::ROLE_ADVISER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'adviser@umak.edu.ph',
        ]);

        $this->officer = User::factory()->create([
            'role'           => User::ROLE_OFFICER,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'officer@umak.edu.ph',
        ]);

        $this->student = User::factory()->create([
            'role'           => User::ROLE_STUDENT,
            'account_status' => User::STATUS_ACTIVE,
            'email'          => 'student@umak.edu.ph',
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 200);

        $this->document = Document::create([
            'title'        => 'Freshmen Orientation Proposal',
            'category'     => Document::CATEGORY_PROPOSAL,
            'uploaded_by'  => $this->officer->id,
            'file_path'    => $file->store('documents', 'local'),
            'file_name'    => 'proposal.pdf',
            'file_size'    => 200000,
            'status'       => Document::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'version'      => 1,
        ]);
    }

    public function test_adviser_can_view_document_review_queue(): void
    {
        $response = $this->actingAs($this->adviser)->get(route('documents.review.index'));
        $response->assertStatus(200);
        $response->assertSee('Freshmen Orientation Proposal');
    }

    public function test_adviser_can_approve_document_with_remarks(): void
    {
        $response = $this->actingAs($this->adviser)->post(route('documents.review.process', $this->document), [
            'action'  => DocumentReview::ACTION_APPROVED,
            'remarks' => 'Proposal is sound and aligned with academic calendar.',
        ]);

        $response->assertRedirect(route('documents.review.index'));

        $fresh = $this->document->fresh();
        $this->assertEquals(Document::STATUS_APPROVED, $fresh->status);
        $this->assertNotNull($fresh->approved_at);

        $this->assertDatabaseHas('document_reviews', [
            'document_id' => $this->document->id,
            'reviewed_by' => $this->adviser->id,
            'action'      => DocumentReview::ACTION_APPROVED,
            'remarks'     => 'Proposal is sound and aligned with academic calendar.',
        ]);
    }

    public function test_adviser_can_return_document_for_revisions(): void
    {
        $response = $this->actingAs($this->adviser)->post(route('documents.review.process', $this->document), [
            'action'  => DocumentReview::ACTION_RETURNED,
            'remarks' => 'Please attach the room booking confirmation.',
        ]);

        $response->assertRedirect(route('documents.review.index'));

        $this->assertEquals(Document::STATUS_RETURNED, $this->document->fresh()->status);

        $this->assertDatabaseHas('document_reviews', [
            'document_id' => $this->document->id,
            'reviewed_by' => $this->adviser->id,
            'action'      => DocumentReview::ACTION_RETURNED,
            'remarks'     => 'Please attach the room booking confirmation.',
        ]);
    }

    public function test_student_cannot_access_review_console(): void
    {
        $response = $this->actingAs($this->student)->get(route('documents.review.index'));
        $response->assertStatus(403);

        $postResponse = $this->actingAs($this->student)->post(route('documents.review.process', $this->document), [
            'action' => 'approved',
        ]);
        $postResponse->assertStatus(403);
    }
}
