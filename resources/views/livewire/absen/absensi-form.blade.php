<?php

use Livewire\Volt\Component;
use App\Models\Lokasi;
use Livewire\Attribute\Locked;
// use Illuminate\Support\Facades\URL;
use App\Models\Absensi;
// use Illuminate\Support\Carbon;

new class extends Component {
    
    #[Locked]
    public $lokasi;

    #[Locked]
    public $selectName = 'instansi';
    
    public $selectLokasi = '';
    public $tanggal = '';
    public $mulai = '';
    public $selesai = '';
    // public $url = '';

    function mount()
    {
        $this->lokasi = Lokasi::select(['id','instansi'])->get();
    }

    // function generate()
    // {
    //     $this->url = URL::temporarySignedRoute(
    //                 'absen', now()->addSeconds(5)
    //             );
    // }

    public function simpanAbsen()
    {
        try {
            $validated = $this->validate([
                'selectLokasi'  => ['required'],
                'tanggal'       => ['required'],
                'mulai'         => ['required'],
                'selesai'       => ['required']
            ]);
        } catch (ValidationException $e) {
            throw $e;
        }

        $absensi = new Absensi;
        $absensi->lokasi_id = $this->selectLokasi;
        $absensi->tanggal = $this->tanggal;
        $absensi->mulai = $this->mulai;
        $absensi->selesai =$this->selesai;
        $absensi->save();
        $this->reset(['tanggal','mulai','selesai']);
        $this->dispatch('absensi-simpan');
    }

}; ?>

<section>

    <form wire:submit="simpanAbsen" class="mt-6 space-y-6">
        <div>
            <x-input-label for="selectLokasi" class="text-sm font-medium text-gray-900" :value="__('Instansi')" />
            <x-select-input wire:model="selectLokasi" id="selectLokasi" name="selectLokasi" :items="$this->lokasi" :nameValue="$this->selectName" />
            <x-input-error class="mt-2" :messages="$errors->get('selectLokasi')" />
        </div>
        <div>
            <x-input-label for="tanggal" class="text-sm font-medium text-gray-900" :value="__('Tanggal')" />
            <x-text-input wire:model="tanggal" id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
        </div>
        <div>
            <x-input-label for="mulai" class="text-sm font-medium text-gray-900" :value="__('Jam Mulai')" />
            <x-text-input wire:model="mulai" id="mulai" name="mulai" type="time" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('mulai')" />
        </div>
        <div>
            <x-input-label for="selesai" class="text-sm font-medium text-gray-900" :value="__('Jam Selesai')" />
            <x-text-input wire:model="selesai" id="selesai" name="selesai" type="time" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('selesai')" />
        </div>
        {{-- <div wire:poll.5s="generate">
            {{$this->url}}
        </div> --}}

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            <x-action-message class="me-3" on="absensi-simpan">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>

</section>
