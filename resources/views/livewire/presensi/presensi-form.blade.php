<?php

use Livewire\Volt\Component;
use Livewire\Attribute\Locked;
// use Livewire\Attribute\Validate;
// use Livewire\Attribute\On;
// use Livewire\Attribute\Renderless;
use App\Models\Absensi;

new class extends Component {
    
    #[Locked]
    public $selectName = 'parentLokasi'; 

    #[Locked]
    public $absensi; 
    
    public $selectAbsensi;
    public $otp = 'otp sedang dihitung...';

    function boot()
    {
        // $this->dispatch('otp-start');
        $this->otp = rand(1000, 9999);
    }

    function mount()
    {
        $this->absensi = Absensi::with('parentLokasi')->get();
    }

    function generate()
    {
        $this->skipRender();
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
            <x-text-input wire:model="otp" wire:poll.5s="generate" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly />
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>

        <x-action-message class="me-3" on="scanned">
            {{ __('Berhasil Absen') }}
        </x-action-message>
    </form>

    @persist('scannerAbsen')
    <div id="reader" class="w-full my-6"></div>
    @endpersist

</section>

@push('modulejs')
<script type="module">
    const detected = detect(window.navigator.userAgent);
    // console.log(detected);
    const deviceInfo = detected.device;
    const osInfo = detected.os;
    // console.log(deviceInfo);
    // console.log(osInfo);
    const config = { 
        fps: 12,
        qrbox: {width: 200, height: 200},
        disableFlip: true,
    }

    const html5QrCode = new Html5Qrcode("reader");

    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        console.log(`Code matched = ${decodedText}`, decodedResult);
    };
    const qrCodeErrorCallback = (error) => {
        console.warn(`Code scan error = ${error}`);
    };
    html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
    
    // document.addEventListener('otp-start', async function (event) {  
        // await html5QrCode.stop();
        // await html5QrCode.clear();
        // html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
    // });
</script>
@endpush
