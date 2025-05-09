<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ListeningParty;
use App\Models\Episode;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required')]
    public $startTime;

    #[Validate('required|url')]
    public string $mediaUrl = '';

    public function createListeningParty()
    {
        $this->validate();

        $episode = Episode::create([
            'media_url' => $this->mediaUrl,
        ]);

        $listeningParty = ListeningParty::create([
            'name' => $this->name,
            'start_time' => $this->startTime,
            'episode_id' => $episode->id,
        ]);

        return redirect()->route('parties.show', $listeningParty);

    }

    public function with(): array
    {
        return [
          'listing_parties' => ListeningParty::all(),
        ];
    }
    //
}; ?>

<div class="flex items-center justify-center h-screen bg-slate-50">
    <div class="max-w-lg w-full">
        <form wire:submit="createListeningParty" class="space-y-6">
            <x-input wire:model="name" placeholder="Listening Party Name" />
            <x-input wire:model="mediaUrl" placeholder="Podcast Episode URL" description="Direct Episode Link or Youtube Link, RSS feeds will grab the latest episode"/>
            <x-datetime-picker wire:model="startTime" placeholder="Listening Start Time" />
            <x-button type="submit">Create Listening Party</x-button>
        </form>
    </div>
</div>
