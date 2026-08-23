<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;
    private User $student;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

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

        $this->event = Event::create([
            'title'                 => 'Tech Innovation Summit 2026',
            'description'           => 'A summit for CCIS students.',
            'event_date'            => '2026-08-26',
            'start_time'            => '09:00',
            'end_time'              => '17:00',
            'venue'                 => 'Auditorium',
            'registration_deadline' => '2026-08-25 23:59:00',
            'created_by'            => $this->officer->id,
            'status'                => Event::STATUS_DRAFT,
        ]);
    }

    public function test_officer_can_view_documents_hub(): void
    {
        $response = $this->actingAs($this->officer)->get(route('documents.index'));
        $response->assertStatus(200);
        $response->assertSee('Organization Documents');
    }

    public function test_student_cannot_view_documents_hub(): void
    {
        $response = $this->actingAs($this->student)->get(route('documents.index'));
        $response->assertStatus(403);
    }

    public function test_officer_can_upload_document_to_private_storage(): void
    {
        $file = UploadedFile::fake()->create('reservation_letter.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->officer)->post(route('documents.store'), [
            'title'    => 'Venue Reservation Letter',
            'category' => Document::CATEGORY_RESERVATION,
            'event_id' => $this->event->id,
            'file'     => $file,
            'notes'    => 'Need approval by Friday',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'title'       => 'Venue Reservation Letter',
            'category'    => Document::CATEGORY_RESERVATION,
            'event_id'    => $this->event->id,
            'uploaded_by' => $this->officer->id,
            'status'      => Document::STATUS_DRAFT,
            'file_name'   => 'reservation_letter.pdf',
            'version'     => 1,
        ]);

        $document = Document::first();
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_officer_can_submit_document_for_review(): void
    {
        $file = UploadedFile::fake()->create('proposal.docx', 300);

        $document = Document::create([
            'title'       => 'Activity Proposal',
            'category'    => Document::CATEGORY_PROPOSAL,
            'uploaded_by' => $this->officer->id,
            'file_path'   => $file->store('documents', 'local'),
            'file_name'   => 'proposal.docx',
            'file_size'   => 300000,
            'status'      => Document::STATUS_DRAFT,
            'version'     => 1,
        ]);

        $response = $this->actingAs($this->officer)->post(route('documents.submit', $document));
        $response->assertRedirect();

        $this->assertEquals(Document::STATUS_SUBMITTED, $document->fresh()->status);
        $this->assertNotNull($document->fresh()->submitted_at);
    }

    public function test_authorized_user_can_download_private_document(): void
    {
        $file = UploadedFile::fake()->create('budget.xlsx', 100);
        $path = $file->store('documents', 'local');

        $document = Document::create([
            'title'       => 'Budget Request',
            'category'    => Document::CATEGORY_BUDGET,
            'uploaded_by' => $this->officer->id,
            'file_path'   => $path,
            'file_name'   => 'budget.xlsx',
            'file_size'   => 100000,
            'file_mime'   => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'status'      => Document::STATUS_APPROVED,
            'version'     => 1,
        ]);

        $response = $this->actingAs($this->officer)->get(route('documents.download', $document));
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=budget.xlsx');
    }
}
