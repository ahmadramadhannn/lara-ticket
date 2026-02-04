<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusClass;
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

    private function createOperatorUser()
    {
        $operator = BusOperator::create([
            'name' => 'Operator X',
            'code' => 'OPX',
            'approval_status' => 'approved',
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'role' => 'operator',
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

    public function test_operator_can_access_dashboard()
    {
        $data = $this->createOperatorUser();

        $response = $this->actingAs($data['user'])
            ->get(route('operator.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_operator_can_create_schedule()
    {
        $data = $this->createOperatorUser();
        $route = $this->createRoute();
        
        $busClass = BusClass::create(['name' => 'Executive', 'amenities' => []]);

        $bus = Bus::create([
            'bus_operator_id' => $data['operator']->id,
            'bus_class_id' => $busClass->id,
            'registration_number' => 'D 1234 AB',
            'total_seats' => 40,
            'seat_layout' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($data['user'])
            ->post(route('operator.schedules.store'), [
                'route_id' => $route->id,
                'bus_id' => $bus->id,
                'departure_date' => now()->addDay()->format('Y-m-d'),
                'departure_time' => '08:00',
                'base_price' => 150000,
            ]);

        $response->assertRedirect(route('operator.schedules.index'));
        
        $this->assertDatabaseHas('schedules', [
            'route_id' => $route->id,
            'bus_id' => $bus->id,
            'base_price' => 150000,
            'status' => 'scheduled',
        ]);
    }
}
