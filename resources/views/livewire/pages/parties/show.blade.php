<?php

use Livewire\Volt\Component;
use App\Models\ListeningParty;

new class extends Component {
    public ListeningParty $listeningParty;

    public function mount(ListeningParty $listeningParty): void
    {
        $this->listeningParty = $listeningParty;
    }
}; ?>

<div>
    {{ $listeningParty->name  }}
</div>
