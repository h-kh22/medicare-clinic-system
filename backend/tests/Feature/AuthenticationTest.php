<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed default dataset (Admin, 5 Doctors, 10 Patients, Specialties)
        $this->seed();
    }

    /**
     * 1. Test public registration creates a Patient account and profile.
     */
    public function test_patient_can_register_successfully(): void
    {
        $payload = [
            'name' => 'Sarah Connor',
            'email' => 'sarah.connor@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'phone' => '+1-555-9988',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Patient registered successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role', 'phone', 'patient'],
                    'token',
                ],
            ]);

        // Verify database user
        $this->assertDatabaseHas('users', [
            'email' => 'sarah.connor@example.com',
            'role' => 'patient',
        ]);

        $user = User::where('email', 'sarah.connor@example.com')->first();
        $this->assertNotNull($user->patient);
        $this->assertDatabaseHas('patients', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * 2. Test registration ignores/overrides submitted role='admin' to prevent privilege escalation.
     */
    public function test_registration_never_allows_admin_role_escalation(): void
    {
        $payload = [
            'name' => 'Hacker Trying Admin',
            'email' => 'wannabe.admin@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'phone' => '+1-555-0000',
            'role' => 'admin', // Attempted role escalation
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);

        $user = User::where('email', 'wannabe.admin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('patient', $user->role);
        $this->assertFalse($user->isAdmin());
    }

    /**
     * 3. Test registration input validation.
     */
    public function test_registration_validation_fails_for_invalid_input(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
            'phone' => '',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'Validation error',
            ]);
    }

    /**
     * 4. Test login with valid credentials for Admin, Doctor, and Patient.
     */
    public function test_demo_users_can_login_successfully(): void
    {
        // Admin
        $adminRes = $this->postJson('/api/login', [
            'email' => 'admin@medicare.test',
            'password' => 'password123',
        ]);
        $adminRes->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => ['email' => 'admin@medicare.test', 'role' => 'admin'],
                ],
            ]);
        $this->assertNotEmpty($adminRes->json('data.token'));

        // Doctor
        $docRes = $this->postJson('/api/login', [
            'email' => 'doctor@medicare.test',
            'password' => 'password123',
        ]);
        $docRes->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => ['email' => 'doctor@medicare.test', 'role' => 'doctor'],
                ],
            ]);

        // Patient
        $patRes = $this->postJson('/api/login', [
            'email' => 'patient@medicare.test',
            'password' => 'password123',
        ]);
        $patRes->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => ['email' => 'patient@medicare.test', 'role' => 'patient'],
                ],
            ]);
    }

    /**
     * 5. Test login with invalid credentials.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@medicare.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid email or password',
            ]);
    }

    /**
     * 6. Test GET /api/me endpoint with Bearer token.
     */
    public function test_authenticated_user_can_retrieve_profile(): void
    {
        $user = User::where('email', 'doctor@medicare.test')->first();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'user' => [
                        'email' => 'doctor@medicare.test',
                        'role' => 'doctor',
                    ],
                ],
            ]);
    }

    /**
     * 7. Test unauthenticated request to /api/me is rejected.
     */
    public function test_unauthenticated_request_to_me_is_rejected(): void
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(401);
    }

    /**
     * 8. Test role-based middleware authorization.
     */
    public function test_role_based_authorization_restrictions(): void
    {
        $admin = User::where('email', 'admin@medicare.test')->first();
        $doctor = User::where('email', 'doctor@medicare.test')->first();
        $patient = User::where('email', 'patient@medicare.test')->first();

        $adminToken = $admin->createToken('admin_token')->plainTextToken;
        $docToken = $doctor->createToken('doc_token')->plainTextToken;
        $patToken = $patient->createToken('pat_token')->plainTextToken;

        // Admin route
        $this->withHeader('Authorization', "Bearer $adminToken")
            ->getJson('/api/admin/dashboard-test')
            ->assertStatus(200);

        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', "Bearer $docToken")
            ->getJson('/api/admin/dashboard-test')
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', "Bearer $patToken")
            ->getJson('/api/admin/dashboard-test')
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        // Doctor route
        $this->withHeader('Authorization', "Bearer $docToken")
            ->getJson('/api/doctor/dashboard-test')
            ->assertStatus(200);

        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', "Bearer $patToken")
            ->getJson('/api/doctor/dashboard-test')
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        // Patient route
        $this->withHeader('Authorization', "Bearer $patToken")
            ->getJson('/api/patient/dashboard-test')
            ->assertStatus(200);

        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', "Bearer $docToken")
            ->getJson('/api/patient/dashboard-test')
            ->assertStatus(403);
    }

    /**
     * 9. Test logout revokes token and prevents subsequent access.
     */
    public function test_logout_revokes_token_successfully(): void
    {
        $user = User::where('email', 'patient@medicare.test')->first();
        $token = $user->createToken('logout_test_token')->plainTextToken;

        // First verify token works
        $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me')
            ->assertStatus(200);

        // Logout
        $logoutRes = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $logoutRes->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Logged out successfully',
            ]);

        // Reset memory guard to force re-authenticating from the revoked token
        $this->app['auth']->forgetGuards();

        // Verify token is no longer usable
        $subsequentRes = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me');

        $subsequentRes->assertStatus(401);
    }
}
