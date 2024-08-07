<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Validate;
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

    #[Validate('required',message: 'Mohon pilih lokasi absen')] 
    public $selectAbsensi;

    #[Validate('required|digits:4')]  
    public $otp;

    public $stateCamera;

    public $informasiUser;
    public string $token;

    function mount()
    {
        $this->absensi = Absensi::with('parentLokasi')->get();
        $this->stateCamera = false;
    }

    #[Renderless]
    function generate() : void
    {
        $this->otp = rand(1000, 9999);
        // $this->dispatch('camera-start', otp:$this->otp);
        if($this->stateCamera == false){
            $this->dispatch('camera-start');
        }
    }

    #[On('refresh-otp')]
    #[Renderless]
    function refreshOtp()
    {
        $this->otp = rand(1000, 9999);
    }

    function checkData($userId,$absensi)
    {
        $dinasAbsen = DinasAbsen::where(['user_id' => $userId, 'absensi_id' => $absensi])->get();
        if($dinasAbsen){
            return false;
        }
        return true;
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
        try {
            $this->validate();

            $check = $this->checkData($info->id,$this->selectAbsensi);

            if($check !== true){
                $this->dispatch('info-absen');
                $this->dispatch('refresh-otp');
                return $this->alert('info', 'Info', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'Terimakasih Anda Sudah Absen di lokasi ini',
            ]);
            }

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
            $this->dispatch('refresh-otp');
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('warning', 'Terjadi Kesalahan', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => $th->getMessage(),
            ]);
            $this->dispatch('info-absen');
        }
    }

    #[On('invalid-otp')]
    #[Renderless]
    function invalidOtp() : void
    {
        $this->alert('warning', 'Absen', [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'text' => 'OTP Tidak Sama',
        ]);
        $this->dispatch('info-absen');
    }

}; ?>

<section>
    <form class="flex flex-col mt-6 space-y-6">
        <div class="flex-auto">
            <x-input-label for="selectAbsensi" class="text-sm font-medium text-gray-900" :value="__('Instansi')" />
            <x-select-input wire:model="selectAbsensi" id="selectAbsensi" name="selectAbsensi" :items="$this->absensi" :nameValue="$this->selectName" required />
            <x-input-error class="mt-2" :messages="$errors->get('selectAbsensi')" />
        </div>
        <div class="flex-auto">
            <x-input-label for="otp" :value="__('OTP')" />
            <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly on="refresh-otp" />
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button type="button" wire:click="generate">{{ __('Buat OTP') }}</x-primary-button>
        </div>
    </form>
    {{-- <div id="reader" class="w-full my-6"></div> --}}
    <div id="reader"></div>
</section>

@push('modulejs')
<script type="module">

    function validasiOtp(otpForm,otpQr)
    {
        if(parseInt(otpForm) === parseInt(otpQr)){
            return true;
        }
        return false;
    }

    async function simpanAbsensi(){
        Livewire.dispatch('simpan-absensi');
    }
    
    const config = { 
        fps: 6,
        qrbox: {width: 200, height: 200},
        disableFlip: true,
    }

    const html5QrCode = new Html5Qrcode("reader");
    let target = document.querySelector('#reader');
    // console.log(html5QrCode.getState());
    Livewire.on('camera-start', async () => {
        // let otpForm = otp.otp;
        @this.stateCamera = true;
        
        target.setAttribute('class', 'my-6 size-full md:size-auto rounded border-8');

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            // console.log(`Code matched = ${decodedText}`, decodedResult);
            // console.log(decodedResult);    
            let otpForm = @this.otp; 
            console.log('Form OTP ' + otpForm);
            @this.informasiUser = decodedText;
            let userOtp = JSON.parse(decodedText);
            let state = validasiOtp(otpForm,userOtp.otp);
            console.log('QR OTP ' + userOtp.otp);
            html5QrCode.pause(true);
            if(state === false){
                setTimeout(() => {
                    Livewire.dispatch('invalid-otp');
                }, 1500);
            }else{
                setTimeout(() => {
                    simpanAbsensi();
                }, 1500);
            }
            // Livewire.dispatch('simpan-absensi');
        };

        const qrCodeErrorCallback = (error) => {
            console.warn(`Code scan error = ${error}`);
        };

        // if(html5QrCode.getState() == 1){
            // html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
            // console.log('status kamera setelah 1 ' + html5QrCode.getState());
        // }else{
        //     html5QrCode.pause(true);
        //     await html5QrCode.stop();
        //     await html5QrCode.clear();
        //     console.log('status kamera selain 1 ' + html5QrCode.getState());
        // }
        
    });

    Livewire.on('info-absen',() => {
        html5QrCode.resume(true)
    });

    // document.addEventListener('otp-start', async function (event) {  
        // await html5QrCode.stop();
        // await html5QrCode.clear();
        // html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
    // });
</script>
@endpush
