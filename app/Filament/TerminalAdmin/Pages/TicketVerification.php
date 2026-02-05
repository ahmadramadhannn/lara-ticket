<?php

namespace App\Filament\TerminalAdmin\Pages;

use App\Models\Ticket;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class TicketVerification extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.terminal-admin.pages.ticket-verification';

    protected static ?string $navigationGroup = 'Terminal Operations';

    protected static ?int $navigationSort = 3;

    public ?string $ticketCode = '';
    public ?Ticket $verifiedTicket = null;
    public ?string $verificationResult = null;
    public ?string $resultType = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ticketCode')
                    ->label('Ticket Code / Booking Code')
                    ->placeholder('Enter ticket code or scan QR')
                    ->required()
                    ->autofocus()
                    ->extraInputAttributes(['class' => 'text-center text-2xl font-mono']),
            ]);
    }

    public function verify(): void
    {
        $user = Auth::user();
        $terminalIds = $user->assignedTerminals()
            ->wherePivot('can_verify_tickets', true)
            ->pluck('terminals.id')
            ->toArray();

        if (empty($terminalIds)) {
            Notification::make()
                ->title('Not Authorized')
                ->body('You do not have permission to verify tickets at any terminal.')
                ->danger()
                ->send();
            return;
        }

        // Find ticket by code
        $ticket = Ticket::where('ticket_code', $this->ticketCode)
            ->orWhereHas('booking', fn($q) => $q->where('booking_code', $this->ticketCode))
            ->first();

        if (!$ticket) {
            $this->verificationResult = 'Ticket not found.';
            $this->resultType = 'danger';
            $this->verifiedTicket = null;
            
            Notification::make()
                ->title('Ticket Not Found')
                ->body("No ticket found with code: {$this->ticketCode}")
                ->danger()
                ->send();
            return;
        }

        // Check if ticket is for a route at this terminal
        $schedule = $ticket->schedule;
        $route = $schedule->route;
        
        $isValidTerminal = in_array($route->origin_terminal_id, $terminalIds) || 
                           in_array($route->destination_terminal_id, $terminalIds);

        if (!$isValidTerminal) {
            $this->verificationResult = 'This ticket is not for your terminal.';
            $this->resultType = 'warning';
            $this->verifiedTicket = $ticket;
            
            Notification::make()
                ->title('Wrong Terminal')
                ->body('This ticket is for a different terminal.')
                ->warning()
                ->send();
            return;
        }

        // If user is linked to specific operator, check if ticket belongs to that operator
        if ($user->bus_operator_id && $schedule->bus_operator_id !== $user->bus_operator_id) {
            $this->verificationResult = 'This ticket is not for your airline/operator.';
            $this->resultType = 'warning';
            $this->verifiedTicket = $ticket;
            
            Notification::make()
                ->title('Wrong Operator')
                ->body('You are not authorized to verify tickets for this operator.')
                ->warning()
                ->send();
            return;
        }

        // Check ticket status
        if ($ticket->status === 'used') {
            $this->verificationResult = 'Ticket already used.';
            $this->resultType = 'warning';
            $this->verifiedTicket = $ticket;
            
            Notification::make()
                ->title('Already Used')
                ->body('This ticket has already been used.')
                ->warning()
                ->send();
            return;
        }

        if ($ticket->status === 'cancelled') {
            $this->verificationResult = 'Ticket is cancelled.';
            $this->resultType = 'danger';
            $this->verifiedTicket = $ticket;
            
            Notification::make()
                ->title('Cancelled Ticket')
                ->body('This ticket has been cancelled.')
                ->danger()
                ->send();
            return;
        }

        // Valid ticket
        $this->verificationResult = 'Valid ticket! Ready for boarding.';
        $this->resultType = 'success';
        $this->verifiedTicket = $ticket;

        Notification::make()
            ->title('Valid Ticket')
            ->body("Passenger: {$ticket->passenger_name}")
            ->success()
            ->send();
    }

    public function markAsUsed(): void
    {
        if (!$this->verifiedTicket) {
            return;
        }

        $this->verifiedTicket->update([
            'status' => 'used',
            'used_at' => now(),
        ]);

        Notification::make()
            ->title('Ticket Marked as Used')
            ->body("Ticket {$this->verifiedTicket->ticket_code} has been marked as used.")
            ->success()
            ->send();

        $this->verificationResult = 'Ticket marked as used.';
        $this->resultType = 'success';
    }

    public function clearForm(): void
    {
        $this->ticketCode = '';
        $this->verifiedTicket = null;
        $this->verificationResult = null;
        $this->resultType = null;
    }
}
