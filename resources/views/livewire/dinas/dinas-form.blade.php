<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On; 

new class extends Component {

    public $qr;
    public $resultInformation;

    public $lotd;
    public $latd;
    public $instansi;
    public $alamat;

    public $otp;

    public string $token;

    function boot()
    {
        $this->token = config('app.maps.mapbox_token');
    }

    function mount()
    {
        $this->id = Auth::id();
    }

    #[On('lokasi-didapat')]
    function setLokasi()
    {
        $information = [
            'id' => Auth::id(),
            'npp' => Auth::user()->npp,
            'instansi' => $this->instansi,
            'alamat' => $this->alamat,
            'lotd' => $this->lotd,
            'latd' => $this->latd,
        ];
        $jsonData = json_encode($information);
        $this->resultInformation = DNS2D::getBarcodePNG($jsonData,'QRCODE');
        $this->skipRender();
        // $this->js("alert('Post saved!')");
        // $this->dispatch('lokasi-update')->self();
    }

}; ?>

<section>

    <div id="map" class="w-full h-96 my-6"></div>

    <div class="bg-stone-100 my-4 p-4 border-2" id="barcodeUsers"></div>

    <form>
        <div class="flex-auto">
            <x-input-label for="otp" :value="__('OTP')" />
            <x-text-input wire:model.live="otp"  id="otp" name="otp" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly />
            <x-input-error class="mt-2" :messages="$errors->get('otp')" />
        </div>
    </form>

</section>

@push('modulejs')
<script type="module">
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
    let getInfo;
    let target = document.querySelector('#barcodeUsers');
    let images = document.createElement('img');
    map.addControl(
        new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true
        }).on('geolocate', async(e) => {
            @this.lotd = e.coords.latitude ?? null;
            @this.latd = e.coords.longitude ?? null;
            @this.instansi = null;
            @this.alamat = null;
            Livewire.dispatch('lokasi-didapat');

            getInfo = await @this.resultInformation;
            target.innerHTML = '';
            images.setAttribute('height', 200);
            images.setAttribute('width', 200);
            images.setAttribute('src', `data:image/png;base64,${getInfo}`);
            target.appendChild(images);
            // console.log(getInfo);
            // return getInfo;
        })
    );

</script>
@endpush
