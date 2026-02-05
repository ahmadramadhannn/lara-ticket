<?php

namespace Tests\Feature;

use App\Filament\TerminalAdmin\Resources\ScheduleResource\Pages\ListSchedules;
use App\Models\BusOperator;
use App\Models\City;
use App\Models\Province;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TerminalAdminScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Filament\Facades\Filament::setCurrentPanel(
            \Filament\Facades\Filament::getPanel('terminal-admin') 
        );
    }

    private function createTerminalAdminUser()
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

        // Assign to terminal with create/manage permission
        $user->assignedTerminals()->attach($terminal->id, [
            'assignment_type' => 'primary',
            'can_manage_schedules' => true,
            'can_verify_tickets' => true,
            'can_confirm_arrivals' => true,
        ]);

        return compact('operator', 'user', 'terminal');
    }

    public function test_terminal_admin_can_render_list_schedules_page()
    {
        $data = $this->createTerminalAdminUser();
        
        $this->actingAs($data['user']);
        
        Livewire::test(ListSchedules::class)
            ->assertSuccessful();
    }

    public function test_terminal_admin_can_see_create_action()
    {
        $data = $this->createTerminalAdminUser();
        
        $this->actingAs($data['user']);
        
        Livewire::test(ListSchedules::class)
            ->assertActionExists('create');
    }
}
