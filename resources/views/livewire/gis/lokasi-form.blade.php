<?php

use Livewire\Volt\Component;
use App\Models\Lokasi;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

new class extends Component 
{

    use LivewireAlert;

    public $lotd;
    
    public $latd;
    
    public string $instansi = '';

    public string $alamat = '';

    public string $token = '';

    #[Locked]
    public $id;

    protected $listeners = [
        'confirmedDelete',
        'dismissedDelete'
    ];

    public function boot()
    {
        $this->token = config('app.maps.mapbox_token');
    }

    #[Renderless]
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

    #[On('delete-confirmation')]
    #[Renderless]
    public function deleteConfirmation($id)
    {
        $this->id = $id;
        $this->alert('warning', 'Hapus Data', [
            'position' => 'center',
            'toast' => true,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmedDelete',
            'showCancelButton' => true,
            'onDismissed' => 'dismissedDelete',
            'cancelButtonText' => 'batal',
            'text' => 'Anda yakin akan menghapus data ?',
            'confirmButtonText' => 'hapus',
        ]);   
    }

    #[Renderless]
    public function confirmedDelete()
    {
        try {
            $deleteLokasi = Lokasi::find($this->id);
            $deleteLokasi->delete();
            $this->alert('success', 'Hapus Data', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'berhasil menghapus data',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('warning', 'terjadi kesalahan', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => $th->getMessage(),
            ]);
        }
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

    <div class="flex flex-col">
        <div id="map" class="w-full h-80 my-6" on="lokasi-simpan">
        </div>
        <form wire:submit="simpanLokasi" class="mt-4 space-y-6">
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

            <div class="flex flex-col gap-4">
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
    </div>
    
</section>

@push('modulejs')
<script type="module">
    mapboxgl.accessToken = `{{$this->token}}`;
    const map = new mapboxgl.Map({
        container: 'map',
        // Choose from Mapbox's core styles, or make your own style with Mapbox Studio
        style: 'mapbox://styles/mapbox/streets-v12'
        , center: [107.646407, -6.924785]
        , zoom: 15
    });

    // Add the control to the map.
    map.addControl(
        new MapboxGeocoder({
            accessToken: mapboxgl.accessToken, 
            mapboxgl: mapboxgl,
            // marker: false,
            placeholder: 'Cari tempat',
            countries: 'id',
            bbox: [
                94.915567305,
                -11.092337999,
                141.022151,
                6.160876721
            ],
            // proximity: {
            //     longitude: 107.60998,
            //     latitude: -6.919709
            // },
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
        }).on('geolocate', (e) => {
            // console.log(e);
            // console.log(e.coords.latitude);
            // console.log(e.coords.longitude);
            
            @this.latd = e.coords.latitude ?? null;
            @this.lotd = e.coords.longitude ?? null;
            @this.instansi = null;
            @this.alamat = null;
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
