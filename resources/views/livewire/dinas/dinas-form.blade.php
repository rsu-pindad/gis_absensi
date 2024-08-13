<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On; 
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\URL;

new class extends Component {

    public $lotd;
    public $latd;
    public $instansi;
    public $alamat;
    public $deviceInformasi;
    public $osInformasi;
    public $resultInformation;

    public $otp;
    public $information;
    public $resultUser;

    #[Locked]
    public string $token;

    #[Locked]
    public $absen;

    function boot()
    {
        $this->token = config('app.maps.mapbox_token');
        $this->id = Auth::id();
    }

    #[Renderless]
    function buatBarcode()
    {
        // $new = [];
        $informasiUser = $this->information;
        
        $this->absen = URL::temporarySignedRoute('signedabsensi',now()->addHours(1),['user' => Auth::id(), 'otp' => $this->otp], absolute: true);
        $informasiUser['absen'] = $this->absen; 
        $informasiUser['otp'] = $this->otp; 
        // dd($informasiUser);
        $jsonData = json_encode($informasiUser);
        $this->resultUser = DNS2D::getBarcodePNG($jsonData,'QRCODE');
        // $this->resultUser = DNS2D::getBarcodeHTML($jsonData,'QRCODE');
        $this->dispatch('generated-barcode');
    }

    #[On('lokasi-didapat')]
    #[Renderless]
    function setLokasi()
    {
        $this->information = [
            'id' => Auth::id(),
            'npp' => Auth::user()->npp,
            'instansi' => $this->instansi,
            'alamat' => $this->alamat,
            'lotd' => $this->lotd,
            'latd' => $this->latd,
            'device' => $this->deviceInformasi,
            'os' => $this->osInformasi,
            'absen' => $this->absen,
        ];
        // $this->jsonData = json_encode($information);
        // $this->resultInformation = DNS2D::getBarcodePNG($jsonData,'QRCODE');
        // $this->skipRender();
        // $this->js("alert('Post saved!')");
        // $this->dispatch('lokasi-update')->self();
        $this->dispatch('open-button');
    }

}; ?>

<section>

    <div id="map" class="w-full h-96 my-6"></div>

    <form wire:submit="buatBarcode" class="mt-6 space-y-6">
        <div>
            <x-input-label for="otp" :value="__('OTP')" />
            <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" placeholder="aktifkan lokasi dulu" required disabled />
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Buat Barcode') }}</x-primary-button>

            <x-action-message class="me-3" on="buat-barcode">
                {{ __('Barcode dibuat.') }}
            </x-action-message>
        </div>
    </form>

    <div id="barcodeUsers" class="flex justify-center bg-white mt-4">
    </div>

</section>

@push('modulejs')
<script type="module">
    const detected = detect(window.navigator.userAgent);
    // console.log(detected);
    const deviceInfo = detected.device;
    const osInfo = detected.os;
    // console.log(deviceInfo);
    // console.log(osInfo);
    
    let getInfo;
    let target = document.querySelector('#barcodeUsers');
    let images = document.createElement('img');
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
        zoom: 1,
    });
    map.scrollZoom.disable();
    map.addControl(
        new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true
        }).on('geolocate', async(e) => {
            barcodeInput.placeholder = 'Masukan Barcode';
            barcodeInput.classList.remove('cursor-not-allowed');
            barcodeInput.classList.remove('hover:cursor-not-allowed');
            @this.lotd = e.coords.latitude ?? null;
            @this.latd = e.coords.longitude ?? null;
            @this.instansi = null;
            @this.alamat = null;
            @this.deviceInformasi = deviceInfo;
            @this.osInformasi = osInfo;
            Livewire.dispatch('lokasi-didapat');

            // getInfo = await this.resultInformation;
            // target.innerHTML = '';
            // images.setAttribute('height', 200);
            // images.setAttribute('width', 200);
            // images.setAttribute('src', `data:image/png;base64,${getInfo}`);
            // target.appendChild(images);
            // target.appendChild(getInfo);
            // console.log(getInfo);
            // return getInfo;
        })
    );

    Livewire.on('open-button', () => {
        // console.log('button-openend');
        document.getElementById('otp').disabled = false;
        // let targetButton = document.querySelector('#otp');
        // targetButton.setAttribute(disabled, false);
    });

    Livewire.on('generated-barcode', async () => {
        getInfo = await @this.resultUser;
        target.innerHTML = '';
        // images.setAttribute('height', 200);
        // images.setAttribute('width', 200);
        images.setAttribute('class', 'object-fill bg-stone-100 my-4 p-4 border-2');
        images.setAttribute('src', `data:image/png;base64,${getInfo}`);
        target.appendChild(images);
        // console.log(getInfo);
        // return getInfo;
        // target.innerHTML = getInfo;
    })

</script>
@endpush
