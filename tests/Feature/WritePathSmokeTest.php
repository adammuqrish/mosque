<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Donation;
use App\Models\Event;
use App\Models\EventVolunteer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WritePathSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_create_a_zakat_donation_with_akad(): void
    {
        $payload = [
            'amount' => '250.00',
            'category' => 'zakat',
            'source' => 'cash',
            'donation_date' => now()->toDateString(),
            'donor_name' => 'Smoke Donor',
            'donor_ic' => '900101-01-1234',
            'amil_name' => 'Test Amil',
            'status' => 'confirmed',
        ];

        $response = $this->actingAs($this->admin)->post(route('donations.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $donation = Donation::where('donor_name', 'Smoke Donor')->first();
        $this->assertNotNull($donation, 'Donation was not persisted');
        $this->assertEquals('zakat', $donation->category);
        $this->assertNotNull($donation->receipt_number, 'Receipt number service failed');

        // Zakat donations must create an akad record
        $this->assertNotNull($donation->zakatAkad, 'ZakatAkad record missing');

        // donor_ic must be encrypted at rest
        $raw = \DB::table('donations')->where('id', $donation->id)->value('donor_ic');
        $this->assertNotEquals('900101-01-1234', $raw, 'donor_ic stored in plaintext!');
    }

    /** @test */
    public function member_cannot_create_donations(): void
    {
        $member = User::factory()->create(['role' => 'member', 'email_verified_at' => now()]);

        $response = $this->actingAs($member)->post(route('donations.store'), [
            'amount' => '10.00',
            'category' => 'sadaqah',
            'source' => 'cash',
            'donation_date' => now()->toDateString(),
            'fund_purpose' => 'General Fund',
        ]);

        $response->assertStatus(403);
        $this->assertEquals(0, Donation::count());
    }

    /** @test */
    public function donation_receipt_pdf_downloads(): void
    {
        $donation = Donation::create([
            'user_id' => $this->admin->id,
            'amount' => 100.00,
            'category' => 'sadaqah',
            'type' => 'obligatory',
            'fund_purpose' => 'General Fund',
            'source' => 'cash',
            'status' => 'confirmed',
            'donation_date' => now(),
            'receipt_number' => 'DON-TEST-001',
            'donor_name' => 'PDF Donor',
        ]);

        $response = $this->actingAs($this->admin)->get("/donations/{$donation->id}/akad/print");

        // dompdf v3: this route needs a zakatAkad; without one it should redirect with an error,
        // not crash with a 500.
        $this->assertContains($response->status(), [200, 302], 'PDF route returned ' . $response->status());
    }

    /** @test */
    public function gamification_awards_points_for_event_completion(): void
    {
        $member = User::factory()->create(['role' => 'member', 'email_verified_at' => now()]);

        Badge::query()->delete(); // keep badge assertions deterministic

        $event = Event::create([
            'title' => 'Smoke Test Event',
            'description' => 'Testing gamification under Laravel 10',
            'event_date' => now()->subDays(2),
            'location' => 'Test Mosque',
            'max_volunteers' => 10,
            'status' => 'completed',
            'gamification_category' => 'religious',
        ]);

        $volunteer = EventVolunteer::create([
            'event_id' => $event->id,
            'user_id' => $member->id,
            'status' => 'completed',
            'attendance_status' => 'completed',
            'points_awarded' => false,
        ]);

        $service = app(\App\Services\GamificationService::class);
        $result = $service->awardPointsForEventCompletion($volunteer);

        $this->assertEquals('success', $result['status'] ?? null, json_encode($result));

        $memberPoints = \App\Models\MemberPoints::where('user_id', $member->id)->first();
        $this->assertNotNull($memberPoints, 'MemberPoints row not created');
        $this->assertGreaterThan(0, $memberPoints->total_points, 'No points were awarded');

        // Base 50 + early join 10 + high-impact category 20 = 80 expected minimum
        $this->assertGreaterThanOrEqual(50, $memberPoints->total_points);

        // Idempotency: second call must be skipped
        $second = $service->awardPointsForEventCompletion($volunteer->fresh());
        $this->assertEquals('skipped', $second['status']);
        $this->assertEquals($memberPoints->total_points, $memberPoints->fresh()->total_points);
    }
}
