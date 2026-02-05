<?php

namespace Tests\Feature;

use App\Models\BusOperator;
use App\Models\City;
use App\Models\Province;
use App\Models\Route;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createCompanyAdminUser()
    {
        $operator = BusOperator::create([
            'name' => 'Operator X',
            'code' => 'OPX',
            'approval_status' => 'approved',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'role' => 'company_admin',
            'bus_operator_id' => $operator->id,
            'user_status' => 'active',
        ]);

        return compact('operator', 'user');
    }

    private function createRoute()
    {
        $province = Province::create(['name' => 'West Java', 'code' => 'WJ']);
        $city = City::create(['province_id' => $province->id, 'name' => 'City A', 'type' => 'regency']);
        $terminalA = Terminal::create(['city_id' => $city->id, 'name' => 'Terminal A', 'code' => 'T1', 'address' => 'Addr 1', 'is_active' => true]);
        $terminalB = Terminal::create(['city_id' => $city->id, 'name' => 'Terminal B', 'code' => 'T2', 'address' => 'Addr 2', 'is_active' => true]);
        
        return Route::create([
            'origin_terminal_id' => $terminalA->id,
            'destination_terminal_id' => $terminalB->id,
            'distance_km' => 100,
            'estimated_duration_minutes' => 120,
            'is_active' => true,
        ]);
    }

    public function test_company_admin_can_access_filament_panel()
    {
        $data = $this->createCompanyAdminUser();

        $response = $this->actingAs($data['user'])
            ->get('/company-admin');

        // Filament panel should be accessible (200 OK or redirect to dashboard)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    public function test_company_admin_role_check_works()
    {
        $data = $this->createCompanyAdminUser();

        $this->assertTrue($data['user']->isCompanyAdmin());
        $this->assertTrue($data['user']->isOperator()); // Backward compat
        $this->assertTrue($data['user']->hasApprovedOperator());
        $this->assertFalse($data['user']->isTerminalAdmin());
        $this->assertFalse($data['user']->isBuyer());
    }

    public function test_unapproved_company_admin_cannot_access_panel()
    {
        $operator = BusOperator::create([
            'name' => 'Pending Operator',
            'code' => 'POP',
            'approval_status' => 'pending',
            'is_active' => false
        ]);

        $user = User::factory()->create([
            'role' => 'company_admin',
            'bus_operator_id' => $operator->id,
            'user_status' => 'pending',
        ]);

        $this->assertFalse($user->hasApprovedOperator());
        
        // HTTP test: unapproved company admin should be redirected
        $response = $this->actingAs($user)->get('/company-admin');
        
        // Should redirect to login (Filament denies access)
        $response->assertStatus(403);
    }

    public function test_terminal_admin_role_check_works()
    {
        $province = Province::create(['name' => 'West Java', 'code' => 'WJ']);
        $city = City::create(['province_id' => $province->id, 'name' => 'City A', 'type' => 'regency']);
        $terminal = Terminal::create(['city_id' => $city->id, 'name' => 'Terminal A', 'code' => 'T1', 'address' => 'Addr 1', 'is_active' => true]);
        
        $operator = BusOperator::create([
            'name' => 'Operator X',
            'code' => 'OPX',
            'approval_status' => 'approved',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'role' => 'terminal_admin',
            'bus_operator_id' => $operator->id,
            'terminal_id' => $terminal->id,
            'user_status' => 'active',
        ]);

        // Assign to terminal
        $user->assignedTerminals()->attach($terminal->id, [
            'assignment_type' => 'primary',
            'can_manage_schedules' => true,
            'can_verify_tickets' => true,
            'can_confirm_arrivals' => true,
        ]);

        $this->assertTrue($user->isTerminalAdmin());
        $this->assertTrue($user->isOperator()); // Backward compat
        $this->assertTrue($user->hasApprovedOperator());
        $this->assertTrue($user->hasTerminalAssignment());
        $this->assertTrue($user->canManageTerminal($terminal));
        $this->assertTrue($user->canVerifyAtTerminal($terminal));
        $this->assertFalse($user->isCompanyAdmin());
        $this->assertFalse($user->isBuyer());
    }
}
