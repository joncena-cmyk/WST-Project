<?php

namespace Tests\Feature;

use App\Models\Membership;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MembershipApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_membership_application_with_receipt(): void
    {
        Storage::fake('public');

        $program = Program::create([
            'name'       => 'Bachelor of Science in Computer Science',
            'code'       => 'BSCS',
            'short_name' => 'Computer Science',
            'is_active'  => true,
        ]);

        $student = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_STUDENT,
        ]);

        $file = UploadedFile::fake()->create('receipt.png', 100, 'image/png');

        $response = $this->actingAs($student)->post(route('membership.apply'), [
            'program_id'        => $program->id,
            'payment_method'    => 'gcash',
            'payment_reference' => 'GCASH-12345678',
            'proof_of_payment'  => $file,
            'notes'             => 'Paid via GCash express send',
        ]);

        $response->assertRedirect(route('membership.index'));

        $this->assertDatabaseHas('memberships', [
            'user_id'           => $student->id,
            'program_id'        => $program->id,
            'status'            => Membership::STATUS_PENDING,
            'payment_method'    => 'gcash',
            'payment_reference' => 'GCASH-12345678',
        ]);
    }

    public function test_treasurer_can_approve_and_activate_membership(): void
    {
        $treasurer = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_TREASURER,
        ]);

        $student = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_STUDENT,
        ]);

        $membership = Membership::create([
            'user_id'           => $student->id,
            'academic_year'     => '2026-2027',
            'status'            => Membership::STATUS_PENDING,
            'payment_method'    => 'gcash',
            'payment_reference' => 'REF-999',
            'payment_amount'    => 50.00,
        ]);

        $response = $this->actingAs($treasurer)->post(route('admin.memberships.approve', $membership));

        $response->assertRedirect();
        $this->assertEquals(Membership::STATUS_ACTIVE, $membership->fresh()->status);
        $this->assertEquals(User::ROLE_MEMBER, $student->fresh()->role);
        $this->assertEquals(1, $student->notifications()->count());
    }

    public function test_treasurer_can_reject_membership_with_reason(): void
    {
        $treasurer = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_TREASURER,
        ]);

        $student = User::factory()->create([
            'email_verified_at' => now(),
            'account_status'    => User::STATUS_ACTIVE,
            'role'              => User::ROLE_STUDENT,
        ]);

        $membership = Membership::create([
            'user_id'           => $student->id,
            'academic_year'     => '2026-2027',
            'status'            => Membership::STATUS_PENDING,
            'payment_method'    => 'gcash',
            'payment_reference' => 'INVALID-REF',
            'payment_amount'    => 50.00,
        ]);

        $response = $this->actingAs($treasurer)->post(route('admin.memberships.reject', $membership), [
            'rejection_reason' => 'Invalid reference number provided.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Membership::STATUS_REJECTED, $membership->fresh()->status);
        $this->assertEquals('Invalid reference number provided.', $membership->fresh()->rejection_reason);
        $this->assertEquals(1, $student->notifications()->count());
    }
}
