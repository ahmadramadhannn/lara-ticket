<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Verification Form --}}
        <x-filament::section>
            <x-slot name="heading">
                Verify Ticket
            </x-slot>
            <x-slot name="description">
                Enter a ticket code or booking code to verify
            </x-slot>

            <form wire:submit="verify" class="space-y-4">
                {{ $this->form }}
                
                <div class="flex gap-3">
                    <x-filament::button type="submit" size="lg">
                        <x-heroicon-m-magnifying-glass class="w-5 h-5 mr-2" />
                        Verify Ticket
                    </x-filament::button>
                    
                    <x-filament::button type="button" color="gray" size="lg" wire:click="clearForm">
                        <x-heroicon-m-arrow-path class="w-5 h-5 mr-2" />
                        Reset
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Verification Result --}}
        @if($verificationResult)
            <x-filament::section>
                <x-slot name="heading">
                    Verification Result
                </x-slot>

                <div @class([
                    'p-4 rounded-lg text-center text-lg font-semibold',
                    'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400' => $resultType === 'success',
                    'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400' => $resultType === 'warning',
                    'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-400' => $resultType === 'danger',
                ])>
                    {{ $verificationResult }}
                </div>

                @if($verifiedTicket)
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Passenger Name</div>
                            <div class="text-lg font-semibold">{{ $verifiedTicket->passenger_name }}</div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Ticket Code</div>
                            <div class="text-lg font-mono">{{ $verifiedTicket->ticket_code }}</div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Route</div>
                            <div class="text-lg">{{ $verifiedTicket->schedule->route->route_name }}</div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Departure</div>
                            <div class="text-lg">{{ $verifiedTicket->schedule->departure_time->format('d M Y H:i') }}</div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Seat Number</div>
                            <div class="text-lg font-semibold">{{ $verifiedTicket->seat_number }}</div>
                        </div>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                            <div class="text-lg">
                                <x-filament::badge :color="match($verifiedTicket->status) {
                                    'active' => 'success',
                                    'used' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'gray',
                                }">
                                    {{ ucfirst($verifiedTicket->status) }}
                                </x-filament::badge>
                            </div>
                        </div>
                    </div>

                    @if($verifiedTicket->status === 'active')
                        <div class="mt-6 flex justify-center">
                            <x-filament::button color="success" size="xl" wire:click="markAsUsed">
                                <x-heroicon-m-check-circle class="w-6 h-6 mr-2" />
                                Mark as Used / Board Passenger
                            </x-filament::button>
                        </div>
                    @endif
                @endif
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
