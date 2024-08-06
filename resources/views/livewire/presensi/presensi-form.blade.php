<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
// use Livewire\Attributes\Validate;
use App\Models\Absensi;
use App\Models\DinasAbsen;
use Illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;

new class extends Component {
    use LivewireAlert;

    #[Locked]
    public $selectName = 'parentLokasi'; 

    #[Locked]
    public $absensi; 
    
    public $selectAbsensi;
    public $otp;
    public $informasiUser;
    public string $token;

    function mount()
    {
        $this->absensi = Absensi::with('parentLokasi')->get();
    }

    #[Renderless]
    function generate()
    {
        $this->otp = rand(1000, 9999);
    }

    #[On('simpan-absensi')]
    #[Renderless]
    function simpanPresensi()
    {
        // $this->redirectRoute('signedabsen', [$info->absen]);
        $info = json_decode($this->informasiUser);
        $userOtp = (int)$info->otp;
        $formOtp = (int)$this->otp;
        $clientIp = \Request::ip();
        // dd((int)$this->selectAbsensi);
        // sleep(2);
        // if($formOtp == $userOtp){
            // Belum Work
            // redirect($info->absen);
            try {
                $dinasAbsen = new DinasAbsen;
                $data = [
                    'user_id' => (int)$info->id,
                    'petugas_id' => (int)Auth::user()->id,
                    'absensi_id' => (int)$this->selectAbsensi,
                    'otp' => $this->otp,
                    'devices_ip' => $clientIp,
                    'informasi_device' => json_encode($info->device),
                    'informasi_os' => json_encode($info->os),
                    'position' => json_encode([
                        'lotd' => $info->lotd,
                        'latd' => $info->latd,
                    ]),
                    'presensi_masuk' => now(),
                ];
                $dinasAbsen->fill($data);
                $dinasAbsen->save();
                
                $this->alert('success', 'Absen', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'Berhasil Absen',
                ]);
                $this->dispatch('info-absen');
            } catch (\Throwable $th) {
                //throw $th;
                // dd($th);
                $this->alert('warning', 'DB', [
                    'position' => 'center',
                    'timer' => '50000',
                    'toast' => true,
                    'text' => $th->getMessage(),
                ]);
                $this->dispatch('info-absen');
            }

        // }
        // $this->alert('warning', 'Absen', [
        //     'position' => 'center',
        //     'timer' => '5000',
        //     'toast' => true,
        //     'text' => 'OTP Tidak Sama',
        // ]);
        // $this->dispatch('info-absen');
    }

}; ?>

<section>
    <form class="flex flex-col mt-6 space-y-6">
        <div class="flex-auto">
            <x-input-label for="selectAbsensi" class="text-sm font-medium text-gray-900" :value="__('Instansi')" />
            <x-select-input wire:model="selectAbsensi" id="selectAbsensi" name="selectAbsensi" :items="$this->absensi" :nameValue="$this->selectName" />
            <x-input-error class="mt-2" :messages="$errors->get('selectAbsensi')" />
        </div>
        <div class="flex-auto">
            <x-input-label for="otp" :value="__('OTP')" />
            <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly />
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button type="button" wire:click="generate">{{ __('Buat OTP') }}</x-primary-button>
        </div>
    </form>
    <div id="reader" class="w-full my-6"></div>
</section>

@push('modulejs')
<script type="module">
    const config = { 
        fps: 6,
        qrbox: {width: 200, height: 200},
        disableFlip: true,
    }

    const html5QrCode = new Html5Qrcode("reader");

    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        // console.log(`Code matched = ${decodedText}`, decodedResult);
        // console.log(`Code matched = ${decodedText}`, decodedResult);
        @this.informasiUser = decodedText;
        html5QrCode.pause(true);
        Livewire.dispatch('simpan-absensi');
    };
    const qrCodeErrorCallback = (error) => {
        console.warn(`Code scan error = ${error}`);
    };
    html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
    
    Livewire.on('info-absen',() => {
    //     console.log('resumed');
        html5QrCode.resume(true)
    });

    // document.addEventListener('otp-start', async function (event) {  
        // await html5QrCode.stop();
        // await html5QrCode.clear();
        // html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
    // });
</script>
@endpush
