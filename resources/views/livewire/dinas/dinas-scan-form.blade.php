<?php

use Livewire\Volt\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\DinasAbsenBarcode;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
// use Illuminate\Http\Client\Response;
// use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use KMLaravel\GeographicalCalculator\Facade\GeoFacade;

new class extends Component {

    use LivewireAlert;

    #[Locked]
    public string $token;

    public $showInputOtp = false;
    public $tipeAbsen = true;
    public $radioTipeAbsen = 'masuk';
    public $area;
    public $jarak = 0;

    // Qrcode Database
    public $urlCode;
    public $statusQr;
    public $statusQrJenis = 'masuk';
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
        $lotdQr = $this->dataQrUser->parentAbsensi->parentLokasi->lotd;
        $latdQr = $this->dataQrUser->parentAbsensi->parentLokasi->latd;
        $latdUserRound = round((double)$this->latdUser,6);
        $lotdUserRound = round((double)$this->lotdUser,6);
        $latdQrRound = round((double)$latdQr,6);
        $lotdQrRound = round((double)$lotdQr,6);
        $this->area = GeoFacade::clearResult()->setPoints([
            [$latdUserRound, $lotdUserRound],
            [$latdQrRound, $lotdQrRound]
            ])
            ->setOptions(['units' => ['m']])
            ->getDistance();
        $this->jarak = round($this->area['1-2']['m'],2);
        // dd($this->jarak);
        if($this->jarak > 100){
            return $this->dispatch('info-status','warning','Absen','Absensi Gagal, Anda lokasi absen anda berada '.$this->jarak.' Meter dengan lokasi dinas')->self();
        }
        return $this->dispatch('valid-area')->self();
    }

    #[On('check-signed')]
    public function checkSigned()
    {
        // return Http::dd()->get($this->urlCode)->status();
        // $response = Http::get($this->urlCode);
        // $data = json_decode($response->body(), true);
        $status = $this->statusQr;
        $this->radioTipeAbsen = $this->statusQrJenis;
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
        if($this->radioTipeAbsen == 'keluar'){
            $this->tipeAbsen = false;
        }
        $this->dataQrUser = DinasAbsenBarcode::with('parentAbsensi')->where('user_id', Auth::id())->where('user_barcode_url', $this->urlCode)->first();
        $this->showInputOtp = true;
    }

    #[On('select-camera')]
    #[Renderless]
    public function selectCamera($cameraId) : void
    {
        sleep(1);
        $this->dispatch('camera-start', cameraId:$cameraId);
    }

    #[On('info-status')]
    #[Renderless]
    public function infoStatus($state, $info, $text) : void
    {
        $this->alert($state, $info, [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'text' => $text,
        ]);
    }

    #[On('valid-area')]
    #[Renderless]
    public function validArea()
    {
        try {
            $dinas = DinasAbsenBarcode::findOrFail($this->dataQrUser->id);
            $tipe = false;
            if($this->radioTipeAbsen == 'masuk'){
                if($this->otp !== $dinas->otp_input)
                {
                    return $this->dispatch('info-status','warning','Absen','Invalid OTP')->self();
                }
                if($dinas->fingerprint !== null){
                    return $this->dispatch('info-status','info','Absen','Anda sudah absen masuk')->self();
                }
                $dinas->fingerprint = $this->finger;
                $dinas->devices_ip = \Request::ip();
                $dinas->informasi_device = json_encode($this->deviceInformasi);
                $dinas->informasi_os = json_encode($this->osInformasi);
                $dinas->lotd_user_barcode_masuk = $this->lotdUser;
                $dinas->latd_user_barcode_masuk = $this->latdUser;
                $dinas->user_masuk = now();
                $dinas->updated_at = now();
                $dinas->save();
                $this->dispatch('qrcode-keluar');
            }else{
                $dinas->user_keluar = now();
                $dinas->updated_at = now();
                $dinas->save();
                $this->kirimStatusWa($tipe,null);
            }
            $this->alert('success', 'Absen', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'timerProgressBar' => true,
                'text' => 'Absen berhasil.. halaman otomatis akan dimuat ulang',
                ]);
            $this->dispatch('finish-absen');
            return false;
            // sleep(5);
            // return $this->js('location.reload()');
        } catch (\Throwable $th) {
            return $this->dispatch('info-status','error','Valid Area',$th->getMessage())->self();
        }
    }
    
    #[On('qrcode-keluar')]
    #[Renderless]
    public function buatQrKeluar()
    {
        $random_string = md5(microtime());
        $tipe = true;
        try {
            $findDinas = DinasAbsenBarcode::findOrFail($this->dataQrUser->id);
            Storage::disk('public')->delete('qr/QR'.$findDinas->user_barcode_img.'.png');
            $urlAbsen = URL::signedRoute('signed-absensi-keluar', ['user' => Auth::id(), 'absensi' => $findDinas->id], absolute:true);
            Storage::disk('public')->put('qr/QR'.$random_string.'.png',base64_decode(DNS2D::getBarcodePNG($urlAbsen,'QRCODE')));
            $findDinas->user_barcode_url = $urlAbsen;
            $findDinas->user_barcode_img = $random_string;
            $findDinas->save();
            $url = Storage::disk('public')->url('qr/QR'.$random_string.'.png');
            try {
                $this->kirimStatusWa($tipe,$url);
            } catch (\Throwable $th) {
                return $this->dispatch('info-status','error','QR Wa Out',$th->getMessage())->self();
            }
        } catch (\Throwable $th) {
            return $this->dispatch('info-status','error','QR Out',$th->getMessage())->self();
        }
    }

    #[Renderless]
    public function kirimStatusWa($tipe = true,$urlAbsen = null)
    {
        $msg = '';
        try {
            if($tipe){
                $msg = 'Halo '.Auth::user()->npp.' Terimakasih Absensi berhasil di catat, gunakan tautan berikut untuk absen keluar '.$urlAbsen;
            }else{
                $msg = 'Halo '.Auth::user()->npp.' Terimakasih Absensi keluar berhasil di catat';
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                'target' => '0818831140',
                // 'target' => $user->no_hp,
                'message' => $msg,
                // 'url' => $url,
                // 'filename' => 'Qr Absensi',
                'schedule' => 0,
                'typing' => false,
                'delay' => '5',
                'countryCode' => '62',
                // 'file' => $url,
                // 'file' => new CURLFile('qr/QR'.$qrData),
            ),
            CURLOPT_HTTPHEADER => array(
                    'Authorization: '.config('app.fonnte.fonnte_token'),
                ),
            ));

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
            }
            curl_close($curl);

            if (isset($error_msg)) {
                return $error_msg;
            }
            return $response;
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }   
    }


}; ?>

<section class="mb-10">

    <div id="map" class="w-full h-96 my-6 "></div>
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
                <div class="mt-3 grid sm:grid-cols-2 gap-2">
                    <x-input-label for="hs-radio-in-form" :value="__('Jenis Absen')" />
                    @if($tipeAbsen)
                    <label for="hs-radio-in-form" class="flex p-3 w-full bg-white border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                      <input wire:model="radioTipeAbsen" checked value="masuk" type="radio" name="hs-radio-in-form" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                      <span class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Masuk</span>
                    </label>
                    @else
                    <label for="hs-radio-in-form" class="flex p-3 w-full bg-white border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                      <input wire:model="radioTipeAbsen" checked value="keluar" type="radio" name="hs-radio-in-form" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                      <span class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Keluar</span>
                    </label>
                    @endif
                  </div>
                <div class="mt-3 space-y-3">
                    <x-input-label for="otp" :value="__('OTP Barcode')" />
                    <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-auto focus:cursor-auto hover:cursor-auto" required />
                    <x-input-error class="mt-2" :messages="$errors->get('otp')" />
                </div>
                <div class="mt-3 flex justify-center gap-x-2">
                    <x-primary-button>{{ __('Absen') }}</x-primary-button>
                    <button wire:click="$dispatch('batal-absen')" type="button" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-yellow-500 text-white hover:bg-yellow-600 focus:outline-none focus:bg-yellow-600 disabled:opacity-50 disabled:pointer-events-none">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @if(!$showInputOtp)
    <div>
        <x-input-label for="selectCamera" :value="__('Scan barcode dengan kamera')" />
        <select disabled id="selectCamera" class="w-full bg-neutral-200 border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
            <option hidden>Pilih Kamera</option>
        </select>
    </div>
    <div class="my-3 py-3 flex items-center uppercase text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 dark:text-white dark:before:border-neutral-600 dark:after:border-neutral-600">
        Atau
    </div>
    <div>
        <x-input-label for="qr-input-file" :value="__('Unggah Barcode (.png)')" />
        <input disabled type="file" id="qr-input-file" class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none dark:text-neutral-500 dark:file:bg-blue-500 dark:hover:file:bg-blue-400" accept="image/png" placeholder="scan file">
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

    const config = { 
        fps: 6,
        qrbox: {width: 240, height: 240},
        disableFlip: true,
    }
    const html5QrCode = new Html5Qrcode("reader");
    const fileinput = document.getElementById('qr-input-file');
    
    var target = document.querySelector('#reader');
    var select = document.querySelector('#selectCamera');
    
    @this.deviceInformasi = deviceInfo;
    @this.osInformasi = osInfo;
    @this.finger = visitor;
    // console.log(window.navigator);

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
            // Livewire.dispatch('lokasi-didapat');
            select.disabled = false;
            fileinput.disabled = false;
        })
    );

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
            @this.urlCode = decodedText;
            html5QrCode.pause(true);
            getData(decodedText).then(msg =>{
                @this.statusQr = msg.status;
                setTimeout(() => {
                    html5QrCode.stop();
                    Livewire.dispatch('check-signed');
                }, 1000);
            });
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
                @this.statusQrJenis = msg.tipe;
                setTimeout(() => {
                Livewire.dispatch('check-signed');
                }, 1000);
            });
        })
        .catch(err => {
            // failure, handle it.
            console.log(`Error scanning file. Reason: ${err}`)
            html5QrCode.clear();
        });
    });

    Livewire.on('finish-absen', () => {
        html5QrCode.clear();
        setTimeout(() => {
            location.reload();
        }, 5500);
    });
    
    Livewire.on('batal-absen', () => {
        html5QrCode.clear();
        location.reload();
    });

</script>
@endpush
