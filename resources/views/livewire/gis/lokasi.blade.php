<?php

use Livewire\Volt\Component;
use App\Models\Lokasi;

new class extends Component 
{

    public string $lotd = '';
    
    public string $latd = '';
    
    public string $instansi = '';

    public string $alamat = '';

    public function simpanLokasi() : void
    {
        $lokasi = new Lokasi;

        $validated = $this->validate([
            'lotd' => ['required', 'numeric'],
            'latd' => ['required', 'numeric'],
            'instansi' => ['required', 'string', 'max:255', 'min:5'],
        ]);

        $lokasi->fill($validated);
        $lokasi->save();
        // $this->dispatch('lokasi-simpan')->self();
        $this->dispatch('lokasi-simpan');
    }

}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Tambah GIS') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Gunakan fitur pencarian, klik didalam map, klik geolokasi untuk mengisi form") }}
        </p>
    </header>

    <div id="map" class="w-full h-96 my-6" on="lokasi-simpan">
    </div>
    <form wire:submit="simpanLokasi" class="mt-6 space-y-6">
        <div class="flex gap-4">
            
            <div class="flex-auto">
                <x-input-label for="lotd" :value="__('Longitude')" />
                <x-text-input wire:model="lotd" id="lotd" name="lotd" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly />
                <x-input-error class="mt-2" :messages="$errors->get('lotd')" />
            </div>

            <div class="flex-auto">
                <x-input-label for="latd" :value="__('Latitude')" />
                <x-text-input wire:model="latd" id="latd" name="latd" type="text" class="mt-1 block w-full cursor-not-allowed focus:cursor-auto hover:cursor-not-allowed" required readonly />
                <x-input-error class="mt-2" :messages="$errors->get('latd')" />
            </div>
        </div>

        <div class="flex flex-col">
            <div class="flex-auto">
                <x-input-label for="instansi" :value="__('Instansi')" />
                <x-textarea-input wire:model="instansi" id="instansi" name="instansi" class="form-textarea mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('instansi')" />
            </div>
            <div class="flex-auto">
                <x-input-label for="alamat" :value="__('Alamat')" />
                <x-textarea-input wire:model="alamat" id="alamat" name="alamat" class="form-textarea mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            <x-action-message class="me-3" on="lokasi-simpan">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>

@push('modulejs')
<script type="module">
    mapboxgl.accessToken = "pk.eyJ1IjoibWFwYm94LXJzdSIsImEiOiJjbHo4NWd2ODcwM2R3MnBxdmRjcDZ6Z2VsIn0.1ADkuMnClPbKOulkqBOYPw";
    const map = new mapboxgl.Map({
        container: 'map',
        // Choose from Mapbox's core styles, or make your own style with Mapbox Studio
        style: 'mapbox://styles/mapbox/streets-v12'
        , center: [-79.4512, 43.6568]
        , zoom: 14
    });

    // Add the control to the map.
    map.addControl(
        new MapboxGeocoder({
            accessToken: mapboxgl.accessToken
            , mapboxgl: mapboxgl
        }).on('result', function({result}){
            // console.log(result);
            // console.log(result.place_name);
            // console.log(result.geometry.coordinates)
            @this.lotd = ''
            @this.latd = ''
            @this.lotd = result.geometry.coordinates[0] ?? null
            @this.latd = result.geometry.coordinates[1] ?? null
            @this.instansi = result.text ?? null
            @this.alamat = result.properties.address ?? null
        })
    );

    map.addControl(
        new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true,
            trackUserLocation: true,
        }).on('geolocate', (e) => {
            // console.log(e);
            // console.log(e.coords.latitude);
            // console.log(e.coords.longitude);
            
            @this.lotd = e.coords.latitude ?? null;
            @this.latd = e.coords.longitude ?? null;
            @this.instansi = null
            @this.alamat = null
        })
    );

    map.on('click', (e) => {
        // console.log(e);

        @this.lotd = e.lngLat.lng ?? null;
        @this.latd = e.lngLat.lat ?? null;
        @this.instansi = null
        @this.alamat = null

    });

</script>
@endpush
