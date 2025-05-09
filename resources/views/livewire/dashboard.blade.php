<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ListeningParty;
use App\Models\Episode;
use App\Jobs\ProcessPodcastUrl;

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

        ProcessPodcastUrl::dispatch($this->mediaUrl, $listeningParty, $episode);

        return redirect()->route('parties.show', $listeningParty);

    }

    public function with(): array
    {
        return [
            'listeningParties' => ListeningParty::where('is_active', true)->with('episode.podcast')->orderBy('start_time', 'asc')->get(),
        ];
    }
    //
}; ?>

<div class="min-h-screen bg-emerald-50 flex flex-col pt-8">
    {{-- Top Half: Create New listening Party Form --}}
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="max-w-lg w-full">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-center mb-6">Create Listening Party</h2>
                <form wire:submit="createListeningParty" class="space-y-6">
                    <x-input wire:model="name" placeholder="Listening Party Name"/>
                    <x-input wire:model="mediaUrl" placeholder="Podcast RSS Feed URL" description="Entering the RSS Feed URL will grab the latest episode"/>
                    <x-datetime-picker wire:model="startTime" placeholder="Listening Party Start Time" :min="now()->subDay()"/>
                    <x-button type="submit" class="w-full">Create Listening Party</x-button>
                </form>
            </div>
        </div>
    </div>

    {{--  Bottom Half: Existing Listening Parties  --}}
    <div class="h-1/2 bg-gray-100 p-4 overflow-hidden">
        <div class="max-w-lg mx-auto">
            <h3 class="text-xl font-semibold mb-4">Ongoing Listening Parties</h3>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-y-auto max-h-[calc(50vh-8rem)]">
                    @foreach ($listeningParties as $listeningParty)
                        <a href="{{ route('parties.show', $listeningParty) }}" class="block">
                            <div
                                class="flex items-center justify-between p-4 border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <x-avatar src="{{ $listeningParty->episode->podcast->artwork_url }}" size="xl" rounded="sm"/>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">
                                            {{ $listeningParty->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate">
                                            {{ $listeningParty->episode->title }}
                                        </div>
                                        <div class="text-xs text-gray-400 truncate">
                                            {{ $listeningParty->episode->podcast->title }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Starts in:
                                        </div>
                                    </div>
                                </div>
                                <x-button primary class="ml-4">Join</x-button>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
