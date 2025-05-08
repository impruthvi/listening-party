<?php

use Livewire\Volt\Component;
use App\Models\ListeningParty;

new class extends Component {
    public string $name = '';
    public $startTime;

    public function createListeningParty()
    {

    }

    public function with()
    {
        return [
          'listing_parties' => ListeningParty::all(),
        ];
    }
    //
}; ?>

<div>
    Hello, this is the dashboard component!
</div>
