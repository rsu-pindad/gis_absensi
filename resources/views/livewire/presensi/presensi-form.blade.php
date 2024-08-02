<?php

use Livewire\Volt\Component;
use Livewire\Attribute\Locked;
use App\Models\Absensi;

new class extends Component {
    
    
    #[Locked]
    public $selectName = 'parentLokasi'; 
    #[Locked]
    public $absensi; 
    
    public $selectAbsensi = '';
    public $otp;

    function mount()
    {
        $this->absensi = Absensi::with('parentLokasi')->get();
    }

    function generate()
    {
        $this->otp = rand(1000, 9999);
    }

    function simpanPresensi()
    {

    }

}; ?>

<section>

    <form wire:submit="simpanPresensi" class="mt-6 space-y-6">

        <div>
            <x-input-label for="selectAbsensi" class="text-sm font-medium text-gray-900" :value="__('Instansi')" />
            <x-select-input wire:model="selectAbsensi" id="selectAbsensi" name="selectAbsensi" :items="$this->absensi" :nameValue="$this->selectName" />
            <x-input-error class="mt-2" :messages="$errors->get('selectAbsensi')" />
        </div>

        <div class="flex-auto">
            <x-input-label for="otp" :value="__('OTP')" />
            {{-- <x-text-input wire:model.live="otp" wire:poll.5s="generate" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly /> --}}
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>
        
        <x-action-message class="me-3" on="scanned">
            {{ __('Secan berhasil.') }}
        </x-action-message>

    </form>
    
    <div id="reader" class="w-full h-96 my-6"></div>

</section>

@push('modulejs')
<script type="module">

    function onScanSuccess(decodedText, decodedResult) {
        // handle the scanned code as you like, for example:
        @this.test = decodedResult.result.text
        @this.dispatch('scanned')
        console.log(`Code matched = ${decodedText}`, decodedResult);
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: {width: 200, height: 200} },
        /* verbose= */ false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);

</script>
@endpush
