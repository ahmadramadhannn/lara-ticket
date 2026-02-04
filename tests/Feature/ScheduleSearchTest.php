<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\BusClass;
use App\Models\BusOperator;
use App\Models\City;
use App\Models\Province;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Terminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_loads_and_displays_form()
    {
        $province = Province::create(['name' => 'West Java', 'code' => 'WJ']);
        $city = City::create(['province_id' => $province->id, 'name' => 'City A', 'type' => 'regency']);
        $terminal = Terminal::create(['city_id' => $city->id, 'name' => 'Terminal A', 'code' => 'T1', 'address' => 'Addr 1', 'is_active' => true]);

        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee($terminal->name);
    }

    public function test_search_results_page_loads()
    {
        $province = Province::create(['name' => 'West Java', 'code' => 'WJ']);
        $city = City::create(['province_id' => $province->id, 'name' => 'City A', 'type' => 'regency']);
        $terminalA = Terminal::create(['city_id' => $city->id, 'name' => 'Terminal A', 'code' => 'T1', 'address' => 'Addr 1', 'is_active' => true]);
        $terminalB = Terminal::create(['city_id' => $city->id, 'name' => 'Terminal B', 'code' => 'T2', 'address' => 'Addr 2', 'is_active' => true]);
        
        $response = $this->get('/schedules?' . http_build_query([
            'origin' => $terminalA->id,
            'destination' => $terminalB->id,
            'date' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
    }
}
