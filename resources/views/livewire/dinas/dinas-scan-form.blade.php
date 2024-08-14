<?php

use Livewire\Volt\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\DinasAbsenBarcode;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use KMLaravel\GeographicalCalculator\Facade\GeoFacade;

new class extends Component {

    use LivewireAlert;

    #[Locked]
    public string $token;

    public $showInputOtp = false;
    public $area;
    public $jarak = 0;

    // Qrcode Database
    public $urlCode;
    public $statusQr;
    public $dataQrUser;
    
    // Ambil dari device user
    public $lotdUser;
    public $latdUser;
    public $deviceInformasi;
    public $osInformasi;

    #[Locked]
    public string $finger;

    #[Validate('required|digits:4')]
    public $otp;

    public function boot()
    {
        $this->token = config('app.maps.mapbox_token');
    }

    #[Renderless]
    public function prosesAbsensi()
    {
        $idBarcode = $this->dataQrUser->id;
        $lotdQr = $this->dataQrUser->parentAbsensi->parentLokasi->lotd;
        $latdQr = $this->dataQrUser->parentAbsensi->parentLokasi->latd;
        // $area = GeoFacade::setMainPoint([-6.914004, 107.634633])
        // dd($this->latdUser,$this->lotdUser);
        $latdUserRound = round((double)$this->latdUser,6);
        $lotdUserRound = round((double)$this->lotdUser,6);
        $latdQrRound = round((double)$latdQr,6);
        $lotdQrRound = round((double)$lotdQr,6);
        $this->area = GeoFacade::clearResult()->setPoints([
            [$latdUserRound, $lotdUserRound],
            [$latdQrRound, $lotdQrRound]
            ])
            // ->setPoint([$latdQrRound, $lotdQrRound])
            ->setOptions(['units' => ['m']])
            ->getDistance();
        // dd($this->area);
        $this->jarak = round($this->area['1-2']['m'],2);
        // dd($this->jarak);
        if($this->jarak > 120){
            return $this->alert('warning', 'Absen', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'Absensi Gagal, Anda lokasi absen anda berada '.$this->jarak.' Meter dengan lokasi dinas',
            ]);
        }
        return $this->alert('success', 'Absen', [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'text' => 'Absensi Berhasil, Anda lokasi absen anda berada '.$this->jarak.' Meter dengan lokasi dinas',
        ]);
    }

    #[On('check-signed')]
    public function checkSigned()
    {
        // return Http::dd()->get($this->urlCode)->status();
        // $response = Http::get($this->urlCode);
        // $data = json_decode($response->body(), true);
        $status = $this->statusQr;
        sleep(1);
        if($status !== 200)
        {
            return $this->alert('warning', 'Absen', [
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

<section class="mb-10">

    @if($showInputOtp)
    <div class="mt-6 text-center">
        <h3 class="text-xl dark:text-white">
            QrCode Valid, Silahkan Masukan OTP
        </h3>
        <h4 class="text-xl dark:text-white">
            {{$this->dataQrUser->parentAbsensi->parentLokasi->instansi}}
        </h4>
    </div>
    <div class="max-w-2xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-neutral-900">
            <form wire:submit="prosesAbsensi">
                <div class="mt-2 space-y-3">
                    <x-input-label for="otp" :value="__('OTP Barcode')" />
                    <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-auto focus:cursor-auto hover:cursor-auto" required />
                    <x-input-error class="mt-2" :messages="$errors->get('otp')" />
                </div>
                <div class="mt-5 flex justify-center gap-x-2">
                    <x-primary-button>{{ __('Absen') }}</x-primary-button>
        
                    <x-action-message class="me-3" on="lokasi-simpan">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </div>
    @endif
    @if(!$showInputOtp)
    <div id="map" class="w-full h-96 my-6 "></div>
    <div class="mt-6">
        <x-input-label for="selectCamera" :value="__('Scan barcode dengan kamera')" />
        <select id="selectCamera" class="w-full bg-neutral-200 border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
            <option hidden>Pilih Kamera</option>
        </select>
    </div>
    <div class="py-3 flex items-center uppercase text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 dark:text-white dark:before:border-neutral-600 dark:after:border-neutral-600">
        Atau
    </div>
    <div>
        <x-input-label for="selectCamera" :value="__('Unggah Barcode (.png)')" />
        <input type="file" id="qr-input-file" class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none dark:text-neutral-500 dark:file:bg-blue-500 dark:hover:file:bg-blue-400" accept="image/png" capture placeholder="scan file">
    </div>
    <div id="reader"></div>
    @endif
</section>

@push('modulejs')
<script type="module">

    const detected = detect(window.navigator.userAgent);
    const deviceInfo = detected.device;
    const osInfo = detected.os;

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
        e.preventDefault();
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

    let getInfo;
    let barcodeInput = document.querySelector('#otp');

    mapboxgl.accessToken = `{{$this->token}}`;
    
    if(!mapboxgl.supported()){
        alert('browser (peramban) tidak mendukung maps');
    }
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [
                107.60998,
                -6.919709
            ],
        zoom: 15,
    });
    map.scrollZoom.disable();
    map.addControl(
        new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: false,
            showUserHeading: true
        }).on('geolocate', async(e) => {
            @this.latdUser = e.coords.latitude ?? null;
            @this.lotdUser = e.coords.longitude ?? null;
            @this.deviceInformasi = deviceInfo;
            @this.osInformasi = osInfo;
            @this.finger = visitor;
            // Livewire.dispatch('lokasi-didapat');
        })
    );

</script>
@endpush
