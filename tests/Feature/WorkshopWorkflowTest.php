<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function workshop(array $attributes = []): Workshop
    {
        return Workshop::create(array_merge([
            'code' => 'TEST-'.uniqid(), 'title' => 'Test Workshop', 'instructor' => 'Instructor',
            'starts_at' => now()->addDays(2), 'capacity' => 1, 'status' => 'scheduled',
        ], $attributes));
    }

    private function register(Workshop $workshop, string $email = 'attendee@example.com')
    {
        return $this->post(route('registrations.store'), [
            'workshop_id' => $workshop->id, 'attendee_name' => 'Attendee', 'attendee_email' => $email,
        ]);
    }

    public function test_roles_are_enforced_on_backend(): void
    {
        $workshop = $this->workshop();
        foreach (['admin', 'manager', 'staff'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            $this->get(route('users.create'))->assertStatus($role === 'admin' ? 200 : 403);
            $this->post(route('users.store'), [])->assertStatus($role === 'admin' ? 302 : 403);
            $this->get(route('workshops.index'))->assertStatus($role === 'admin' ? 403 : 200);
            $this->get(route('workshops.show', $workshop))->assertStatus($role === 'admin' ? 403 : 200);
            $this->get(route('workshops.create'))->assertStatus($role === 'manager' ? 200 : 403);
            $this->get(route('workshops.edit', $workshop))->assertStatus($role === 'manager' ? 200 : 403);
            $this->get(route('registrations.create', $workshop))->assertStatus($role === 'admin' ? 403 : 200);
            $this->post(route('workshops.store'), [])->assertStatus($role === 'manager' ? 302 : 403);
            $this->put(route('workshops.update', $workshop), [])->assertStatus($role === 'manager' ? 302 : 403);
            $this->post(route('registrations.store'), [])->assertStatus($role === 'admin' ? 403 : 302);
        }
    }

    public function test_admin_can_create_manager_and_staff_accounts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach (['manager', 'staff'] as $role) {
            $this->post(route('users.store'), [
                'name' => ucfirst($role), 'email' => $role.'@example.com', 'role' => $role,
                'password' => 'password123', 'password_confirmation' => 'password123',
            ])->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', ['email' => $role.'@example.com', 'role' => $role]);
        }
    }

    public function test_profile_updates_cannot_change_roles(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->patch(route('profile.update'), [
            'name' => $staff->name, 'email' => $staff->email, 'role' => 'admin',
        ])->assertSessionHasNoErrors();
        $this->assertSame('staff', $staff->fresh()->role);
    }

    public function test_full_workshop_rejects_registration_and_cancel_frees_seat_with_history(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff);
        $workshop = $this->workshop();
        $this->register($workshop)->assertSessionHasNoErrors();
        $registration = Registration::firstOrFail();
        $this->assertSame($staff->id, $registration->registered_by);
        $this->assertNotNull($registration->registered_at);
        $this->register($workshop, 'second@example.com')->assertSessionHasErrors('workshop_id');
        $this->assertSame(1, $workshop->activeRegistrations()->count());
        $this->patch(route('registrations.cancel', $registration))->assertSessionHasNoErrors();
        $registration->refresh();
        $this->assertSame('cancelled', $registration->status);
        $this->assertSame($staff->id, $registration->cancelled_by);
        $this->assertNotNull($registration->cancelled_at);
        $this->patch(route('registrations.cancel', $registration))->assertSessionHasErrors('registration');
        $this->register($workshop, 'second@example.com')->assertSessionHasNoErrors();
        $this->assertSame(2, $workshop->registrations()->count());
        $this->assertSame(1, $workshop->activeRegistrations()->count());
        $this->get(route('workshops.show', $workshop))->assertSee('attendee@example.com')->assertSee($staff->name);
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->patch(route('registrations.cancel', $registration))->assertForbidden();
    }

    public function test_capacity_cannot_drop_below_active_bookings(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'manager']));
        $workshop = $this->workshop(['capacity' => 2]);
        $this->register($workshop);
        $this->register($workshop, 'second@example.com');
        $data = $workshop->only(['code', 'title', 'instructor', 'starts_at', 'capacity', 'status']);
        $data['starts_at'] = $workshop->starts_at->format('Y-m-d H:i:s');
        $data['capacity'] = 1;
        $this->put(route('workshops.update', $workshop), $data)->assertSessionHasErrors('capacity');
        $this->assertSame(2, $workshop->fresh()->capacity);
        $data['capacity'] = 2;
        $this->put(route('workshops.update', $workshop), $data)->assertSessionHasNoErrors();
    }

    public function test_closed_workshops_reject_registrations(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        foreach ([['status' => 'cancelled'], ['status' => 'completed'], ['starts_at' => now()->subDay()]] as $attributes) {
            $this->register($this->workshop($attributes))->assertSessionHasErrors('workshop_id');
        }
        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_filters_combine_date_status_and_available_seats(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        $date = now()->addDays(2)->format('Y-m-d');
        $available = $this->workshop(['code' => 'AVAILABLE']);
        $full = $this->workshop(['code' => 'FULL']);
        $this->register($full);
        $this->workshop(['code' => 'CANCELLED', 'status' => 'cancelled']);
        $this->workshop(['code' => 'LATER', 'starts_at' => now()->addDays(10)]);
        $this->get(route('workshops.index', [
            'date_from' => $date, 'date_to' => $date, 'status' => 'scheduled', 'available_only' => 1,
        ]))->assertOk()->assertSee($available->code)->assertDontSee('FULL')->assertDontSee('CANCELLED')->assertDontSee('LATER');
        $this->get(route('workshops.index', ['status' => 'invalid']))->assertSessionHasErrors('status');
        $this->get(route('workshops.index', ['date_from' => $date, 'date_to' => now()->format('Y-m-d')]))
            ->assertSessionHasErrors('date_to');
    }

    public function test_seeders_are_repeatable_and_provide_all_roles_and_workshops(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseHas('users', ['email' => 'admin@workshop.com', 'role' => 'admin']);
        foreach (['admin' => 'Admin@12345', 'manager' => 'Manager@12345', 'staff' => 'Staff@12345'] as $role => $password) {
            $user = User::where('email', $role.'@workshop.com')->firstOrFail();
            $this->assertSame($role, $user->role);
            $this->assertTrue(\Illuminate\Support\Facades\Hash::check($password, $user->password));
        }
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('workshops', 3);
    }
}
