<?php

use Livewire\Volt\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\DinasAbsenBarcode;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

new class extends Component {
    use LivewireAlert;

    public $showInputOtp = false;
    public $area;
    public $selectLokasi;
    public $urlCode;
    public $statusQr;
    public $otp;
    public $dataQrUser;

    public function mount()
    {
        $this->area = DinasAbsenBarcode::with('parentAbsensi')->where('user_id', Auth::id())->where('fingerprint', null)->get();
        // $action = Route::currentRouteAction(); 
    }

    public function prosesAbsensi()
    {

    }

    #[On('check-signed')]
    public function checkSigned() : void
    {
        // return Http::dd()->get($this->urlCode)->status();
        // $response = Http::get($this->urlCode);
        // $data = json_decode($response->body(), true);
        $status = $this->statusQr;
        sleep(1);
        if($status !== 200)
        {
            $this->alert('warning', 'Absen', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'Gagal Absen, invalid signature qrcode',
            ]);
        }
        $this->dataQrUser = DinasAbsenBarcode::with('parentAbsensi')->where('user_id', Auth::id())->where('user_barcode_url', $this->urlCode)->first();
        $this->showInputOtp = true;
    }

    #[On('select-camera')]
    #[Renderless]
    function selectCamera($cameraId) : void
    {
        sleep(1);
        $this->dispatch('camera-start', cameraId:$cameraId);
    }

}; ?>

<section>

    @if($showInputOtp)
    <div class="mt-6">
        <p>QrCode Valid, Silahkan Masukan OTP</p>
        <p>
            {{$this->dataQrUser->parentAbsensi->parentLokasi->instansi}}
        </p>
    </div>
    @endif
    @if($showInputOtp)
    <form wire:submit="prosesAbsensi">
        <div class="flex items-center gap-4">
            <x-input-label for="otp" :value="__('OTP')" />
            <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-auto focus:cursor-auto hover:cursor-auto" required/>
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Absen') }}</x-primary-button>

            <x-action-message class="me-3" on="lokasi-simpan">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
    @endif
    @if(!$showInputOtp)
    <div class="mt-6">
        <x-input-label for="selectCamera" :value="__('Pilih Kamera')" />
        <select id="selectCamera" class="w-full bg-neutral-200 border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
            <option hidden>Pilih Kamera</option>
        </select>
    </div>
    <div id="reader"></div>
    <p>atau</p>
    <input type="file" id="qr-input-file" accept="image/png" capture placeholder="scan file">
    @endif
</section>

@push('modulejs')
<script type="module">

    const fpPromise = FingerprintJS.load()
    .then(fp => fp.get())
    .then(result => {
        const visitorId = result.visitorId;
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
    const fileinput = document.getElementById('qr-input-file');

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
        target.setAttribute('class', 'my-6 size-full md:size-auto rounded border-8');
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            let otpForm = @this.otp; 
            // @this.informasiUser = decodedText;
            // let userOtp = JSON.parse(decodedText);
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
        };
        const qrCodeErrorCallback = (error) => {
            console.warn(`Code scan error = ${error}`);
        };
        if(html5QrCode.getState() != 1){
            html5QrCode.stop().then((ignore) => {
                console.log('camera stopped');
            }).catch((err) => {
                console.warn(err);
            });
            html5QrCode.clear();
        }
        html5QrCode.start({ deviceId: { exact: cameraId} }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
        
    });

    Livewire.on('info-absen',() => {
        html5QrCode.resume(true)
    });

    async function getData(urls) {
        const url = urls;
        try {
            const response = await fetch(url,
                {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'url': url,
                        "X-CSRF-Token": "+{{csrf_token()}}+",
                    }
                }
            );
            if (!response.ok) {
            // throw new Error(`Response status: ${response.status}`);
                const json = await response.json();
                return json;
            }
            const json = await response.json();
            // console.log(json);
            return json;
        } catch (error) {
            console.error(error.message);
        }
    }
    
    fileinput.addEventListener('change', e => {
        if (e.target.files.length == 0) {
            // No file selected, ignore 
            return;
        }
        // Use the first item in the list
        const imageFile = e.target.files[0];
        html5QrCode.scanFile(imageFile, true)
        .then(qrCodeMessage => {
            // success, use qrCodeMessage
            @this.urlCode = qrCodeMessage;
            getData(qrCodeMessage).then(msg =>{
                @this.statusQr = msg.status;
                Livewire.dispatch('check-signed');
                // html5QrCode.clear();
            });
        })
        .catch(err => {
            // failure, handle it.
            console.log(`Error scanning file. Reason: ${err}`)
            html5QrCode.clear();
        });
    });

</script>
@endpush
