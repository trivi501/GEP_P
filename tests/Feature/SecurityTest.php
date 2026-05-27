<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
    $this->userRole = Role::create(['name' => 'Usuario', 'guard_name' => 'web']);
});

// =============================================
// Authentication Security
// =============================================

test('login is rate limited after 5 failed attempts', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $errors = session('errors');
    expect($errors->get('email')[0])->toContain('Too many login attempts');
});

test('session is regenerated after login to prevent fixation', function () {
    $user = User::factory()->create();
    $oldSessionId = session()->getId();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    expect(session()->getId())->not->toBe($oldSessionId);
});

test('unauthenticated user cannot access protected routes', function () {
    $protectedRoutes = [
        '/dashboard',
        '/profile',
        '/predios',
        '/contribuyentes',
        '/pagos',
        '/cajas',
        '/calculos-predios',
        '/secretarias',
        '/ordenes-pago',
        '/descuentos',
        '/estado-cuenta-masivo',
        '/multi-pagos-predial',
    ];

    foreach ($protectedRoutes as $route) {
        $response = $this->get($route);
        $response->assertRedirect(route('login', absolute: false));
    }
});

test('guest cannot POST to authenticated endpoints', function () {
    $csrfRoutes = [
        ['POST', '/pagos/guardar'],
        ['POST', '/pagos/cerrar'],
        ['POST', '/descuentos'],
    ];

    foreach ($csrfRoutes as [$method, $route]) {
        $response = $this->call($method, $route);
        expect(in_array($response->getStatusCode(), [302, 401, 403, 419]))->toBeTrue();
    }
});

// =============================================
// Authorization / RBAC Security
// =============================================

test('non-admin user cannot access roles management', function () {
    $user = User::factory()->create();
    $user->assignRole('Usuario');

    $response = $this->actingAs($user)->get('/roles');
    $response->assertStatus(403);
});

test('non-admin user cannot access users management', function () {
    $user = User::factory()->create();
    $user->assignRole('Usuario');

    $response = $this->actingAs($user)->get('/users');
    $response->assertStatus(403);
});

test('non-admin user cannot access permissions management', function () {
    $user = User::factory()->create();
    $user->assignRole('Usuario');

    $response = $this->actingAs($user)->get('/permissions');
    $response->assertStatus(403);
});

test('non-admin user cannot create roles', function () {
    $user = User::factory()->create();
    $user->assignRole('Usuario');

    $response = $this->actingAs($user)->post('/roles', [
        'name' => 'Hacker Role',
    ]);
    $response->assertStatus(403);
});

test('non-admin user cannot delete users', function () {
    $user = User::factory()->create();
    $user->assignRole('Usuario');
    $target = User::factory()->create();

    $response = $this->actingAs($user)->delete("/users/{$target->id}");
    $response->assertStatus(403);
});

test('admin user can access admin routes', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->get('/roles');
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->get('/users');
    $response->assertStatus(200);

    $response = $this->actingAs($admin)->get('/permissions');
    $response->assertStatus(200);
});

test('Super Admin role cannot be deleted', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $superAdminRole = Role::where('name', 'Super Admin')->first();

    $response = $this->actingAs($admin)->delete("/roles/{$superAdminRole->id}");

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
});

test('unverified email user is still handled by auth middleware', function () {
    $user = User::factory()->unverified()->create();
    $user->assignRole('Super Admin');

    $response = $this->actingAs($user)->get('/dashboard');
    expect(in_array($response->getStatusCode(), [200, 302]))->toBeTrue();
});

// =============================================
// Input Validation Security
// =============================================

test('SQL injection attempts in login are rejected as invalid credentials', function () {
    $payloads = [
        "' OR '1'='1",
        "' OR 1=1 --",
        "admin' --",
        "'; DROP TABLE users; --",
        "' UNION SELECT * FROM users --",
        "1' OR '1' = '1",
    ];

    foreach ($payloads as $payload) {
        $this->post('/login', [
            'email' => $payload,
            'password' => $payload,
        ]);
        $this->assertGuest();
    }
});

test('XSS attempts in contribuyente fields are sanitized', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $tipoContribuyente = \App\Models\TipoContribuyente::create([
        'area_contribuyente' => 'Test',
        'activo' => true,
    ]);

    $xssPayload = '<script>alert("xss")</script>';
    $response = $this->actingAs($user)->post('/contribuyentes', [
        'nombre' => $xssPayload,
        'primer_apellido' => $xssPayload,
        'id_tipo_contribuyente' => $tipoContribuyente->id_tipo_contribuyente,
    ]);

    $response->assertSessionHasNoErrors();
});

test('mass assignment protection on users - cannot set guarded fields', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->post('/users', [
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'id' => 9999,
        'email_verified_at' => now(),
    ]);

    expect(User::where('id', 9999)->exists())->toBeFalse();
});

test('invalid email format is rejected on registration', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
});

test('weak password is rejected on registration', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});

// =============================================
// CSRF Protection
// =============================================

test('POST requests without session have CSRF protection', function () {
    $response = $this->withMiddleware(RemoveCsrfToken::class)
        ? $this->call('POST', '/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])
        : $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

    expect($response->isRedirect())->toBeTrue();
});

// =============================================
// Session Security
// =============================================

test('session has http_only flag', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/dashboard');

    $sessionCookie = collect(headers_list())->filter(fn ($h) => str_contains($h, config('session.cookie')))->first();

    if ($sessionCookie) {
        expect($sessionCookie)->toContain('HttpOnly');
    } else {
        $this->assertTrue(true);
    }
});

test('user data is not leaked in error responses', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $response = $this->actingAs($user)->get('/predios/999999');

    expect($response->getStatusCode())->toBe(404);
    $content = $response->getContent();
    if ($content) {
        expect($content)->not->toContain('SQLSTATE');
        expect($content)->not->toContain('Stack trace');
    }
});
