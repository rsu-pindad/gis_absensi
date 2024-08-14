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

    #[Validate('required')] 
    public $selectAbsensi;

    #[Validate('required|digits:4')]  
    public $otp;
    
    #[Validate('required')]  
    public $fingerPrint;

    public $informasiUser;

    function mount()
    {
        $this->absensi = Absensi::with('parentLokasi')->get();
    }

    #[On('select-camera')]
    #[Renderless]
    function selectCamera($cameraId) : void
    {
        sleep(1);
        $this->dispatch('camera-start', cameraId:$cameraId);
    }

    #[On('refresh-otp')]
    #[Renderless]
    function refreshOtp()
    {
        $this->otp = rand(1000, 9999);
    }

    function checkData($case, $userId, $absensi, $finger, $devicesIp, $os)
    {
        switch ($case) {
            case 'checkUserAbsen':
                $dinasAbsen = DinasAbsen::where(['user_id' => $userId, 'absensi_id' => $absensi])->get();
                if(count($dinasAbsen) > 0){
                    return true;
                }
                return false;
            case 'checkFingerPrint':
                $dinasAbsen = DinasAbsen::where(['absensi_id' => $absensi, 'fingerprint' => $finger])->get();
                if(count($dinasAbsen) > 0){
                    return true;
                }
                return false;
            case 'checkIpUser':
                $dinasAbsen = DinasAbsen::where(['absensi_id' => $absensi, 'devices_ip' => $devicesIp])->get();
                if(count($dinasAbsen) > 0){
                    return true;
                }
                return false;
            case 'checkDeviceOs':
                $dinasAbsen = DinasAbsen::where(['absensi_id' => $absensi, 'fingerprint' => $finger])->whereJsonContains('informasi_os',$os)->get();
                if(count($dinasAbsen) > 0){
                    return true;
                }
                return false;
            default:
                return false;
        }
    }

    #[On('info-check')]
    #[Renderless]
    function infoCheck($state,$message,$text)
    {
        $this->alert($state, $message, [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'text' => $text,
        ]);
        $this->dispatch('info-absen');
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
            $checkData = false;
            $checkFingerPrint = false;
            $checkIpUser = false;
            $checkDeviceOs = false;
            // checkData($case, $userId = null, $absensi = null, $finger = null, $devicesIp = null, $os = null)
            $checkData = $this->checkData('checkUserAbsen',$info->id,$this->selectAbsensi,null,null,null);
            if($checkData === true){
                return $this->infoCheck('info','Absen','Anda Sudah Absen di lokasi ini');
            }
            $checkFingerPrint = $this->checkData('checkFingerPrint',null,$this->selectAbsensi,$this->fingerPrint,null,null);
            if($checkFingerPrint === true){
                return $this->infoCheck('warning','Absen','Gagal Absen 401, perangkat terdapat duplikasi data');
            }
            $checkIpUser = $this->checkData('checkIpUser',null,$this->selectAbsensi,null,$clientIp,null);
            if($checkIpUser === true){
                return $this->infoCheck('warning','Absen','Gagal Absen 401 & 501, perangkat memiliki ip yang sama');
            }
            $checkDeviceOs = $this->checkData('checkDeviceOs',null,$this->selectAbsensi,$this->fingerPrint,null,$info->os);
            if($checkDeviceOs === true){
                return $this->infoCheck('warning','Absen','Gagal Absen 401 & 501 & 502, identitass perangkat tedapat duplikasi data');
            }

            $dinasAbsen = new DinasAbsen;
            $data = [
                'user_id' => (int)$info->id,
                'petugas_id' => (int)Auth::user()->id,
                'absensi_id' => (int)$this->selectAbsensi,
                'otp' => $this->otp,
                'fingerprint' => $this->fingerPrint,
                'devices_ip' => $clientIp,
                'informasi_device' => json_encode($info->device),
                'informasi_os' => json_encode($info->os),
                // 'position' => json_encode([
                //     'lotd' => $info->lotd,
                //     'latd' => $info->latd,
                // ]),
                'lotd_user' => $info->lotd,
                'latd_user' => $info->latd,
                'presensi_masuk' => now(),
            ];
            $dinasAbsen->fill($data);
            $dinasAbsen->save();
            
            $this->dispatch('info-absen');
            // $this->dispatch('refresh-otp');
            $this->infoCheck('success','Absen','Absensi Berhasil, '.$info->npp);
        } catch (\Throwable $th) {
            //throw $th;
            $this->infoCheck('warning','Terjadi Kesalahan',$th->getMessage());
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
            <x-action-message class="me-3" on="refresh-otp">
                {{ __('OTP diganti.') }}
            </x-action-message>
            <x-primary-button type="button" wire:click="refreshOtp">{{ __('Buat OTP') }}</x-primary-button>
        </div>
    </form>
    {{-- <div id="reader" class="w-full my-6"></div> --}}
    <div class="mt-6">
        <x-input-label for="selectCamera" :value="__('Pilih Kamera')" />
        <select id="selectCamera" class="w-full bg-neutral-200 border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
            <option hidden>Pilih Kamera</option>
        </select>
    </div>
    <div id="reader"></div>
</section>

@push('modulejs')
<script type="module">

    const fpPromise = FingerprintJS.load()
    .then(fp => fp.get())
    .then(result => {
        const visitorId = result.visitorId;
        // console.log(visitorId);
        return visitorId;
    });
    const visitor = await fpPromise; 
    // console.log(window.navigator);
    function validasiOtp(otpForm,otpQr)
    {
        @this.fingerPrint = visitor;
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
        qrbox: {width: 240, height: 240},
        disableFlip: true,
    }

    const html5QrCode = new Html5Qrcode("reader");
    let target = document.querySelector('#reader');
    var select = document.querySelector('#selectCamera');
    // console.log(html5QrCode.getState());

    select.addEventListener('change', function(value){
        Livewire.dispatch('select-camera', {cameraId:this.value});
    });

    Html5Qrcode.getCameras().then(devices => {
        devices.forEach(async(items)=> {
            var options = document.createElement('option');
            options.text = items.label;
            options.value = items.id;
            await select.appendChild(options)
        });
    }).catch(err => {
        console.warn(err);
    });

    Livewire.on('camera-start', async ({cameraId}) => {
        // let otpForm = otp.otp;
        target.setAttribute('class', 'my-6 size-full md:size-auto rounded border-8');
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            // console.log(`Code matched = ${decodedText}`, decodedResult);
            let otpForm = @this.otp; 
            @this.informasiUser = decodedText;
            let userOtp = JSON.parse(decodedText);
            let state = validasiOtp(otpForm,userOtp.otp);
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
        // if(html5QrCode.getState()){
        if(html5QrCode.getState() != 1){
            html5QrCode.stop().then((ignore) => {
                // alert('camera stopped')
                console.log('camera stopped');
            }).catch((err) => {
                console.warn(err);
            });
            html5QrCode.clear();
        }
        html5QrCode.start({ deviceId: { exact: cameraId} }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
        // }else{ 
        //     html5QrCode.stop().then((ignore) => {
        //         console.log(ignore);
        //     }).catch((err) => {
        //         console.warn(err);
        //     });
        //     html5QrCode.clear();
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
