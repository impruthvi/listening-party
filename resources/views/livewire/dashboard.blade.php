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
            'listeningParties' => ListeningParty::where('is_active', true)
                ->with('episode.podcast')
                ->whereNotNull('end_time')
                ->orderBy('start_time', 'asc')
                ->get(),
        ];
    }
    //
}; ?>

<div class="min-h-screen bg-emerald-50 flex flex-col">
    {{-- Top Half: Create New listening Party Form --}}
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="max-w-lg w-full">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-center mb-6">Create Listening Party</h2>
                <form wire:submit="createListeningParty" class="space-y-6">
                    <x-input wire:model="name" placeholder="Listening Party Name"/>
                    <x-input wire:model="mediaUrl" placeholder="Podcast RSS Feed URL"
                             description="Entering the RSS Feed URL will grab the latest episode"/>
                    <x-datetime-picker wire:model="startTime" placeholder="Listening Party Start Time"
                                       :min="now()->subDay()"/>
                    <x-button type="submit" class="w-full">Create Listening Party</x-button>
                </form>
            </div>
        </div>
    </div>

    {{--  Bottom Half: Existing Listening Parties  --}}
    <div class="h-1/2 bg-gray-100 p-4 overflow-hidden">
        <div class="max-w-lg mx-auto">
            <h3 class="text-xl font-serif font-bold mb-4">Ongoing Listening Parties</h3>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-y-auto max-h-[calc(50vh-8rem)]">
                    @foreach ($listeningParties as $listeningParty)
                        <a href="{{ route('parties.show', $listeningParty) }}" class="block">
                            <div
                                class="flex items-center justify-between p-4 border-b border-gray-200 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <x-avatar src="{{ $listeningParty->episode->podcast->artwork_url }}" size="xl"
                                                  rounded="sm"/>
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
                                        <div class="text-xs text-gray-500 mt-1" x-data="{
                                            startTime: new Date('{{ $listeningParty->start_time->toIso8601String() }}'),
                                            countdownText: '',
                                            isLive: {{ $listeningParty->start_time->isPast() ? 'true' : 'false' }},
                                            updateCountdown() {
                                                const now = new Date().getTime();
                                                const startTime = new Date(this.startTime).getTime();
                                                const timeDiff = startTime - now;

                                                if (timeDiff < 0) {
                                                    this.isLive = true;
                                                    this.countdownText = 'Live Now';
                                                } else {
                                                    const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                                                    const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                                                    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
                                                    this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                                                }

                                            }

                                        }"
                                             x-init="updateCountdown();
                                             setInterval(() => updateCountdown(), 1000)"

                                            >
                                            <div x-show="isLive">
                                                <x-badge flat rose label="Live">
                                                    <x-slot name="prepend" class="relative flex items-center w-2 h-2">
                                                        <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-rose-500 animate-ping"></span>
                                                        <span class="relative inline-flex w-2 h-2 rounded-full bg-rose-500"></span>
                                                    </x-slot>
                                                </x-badge>
                                            </div>
                                            <div x-show="!isLive">
                                                Starts in: <span x-text="countdownText"></span>
                                            </div>
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
