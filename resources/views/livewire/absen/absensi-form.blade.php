<?php

use Livewire\Volt\Component;
use App\Models\Lokasi;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
// use Illuminate\Support\Facades\URL;
use App\Models\Absensi;
// use Illuminate\Support\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

new class extends Component {

    use LivewireAlert;
    
    #[Locked]
    public $lokasi;

    #[Locked]
    public $id;

    #[Locked]
    public string $selectName = 'instansi';
    
    public $selectLokasi = '';
    public $tanggal = '';
    public $mulai = '';
    public $selesai = '';
    // public $url = '';

    protected $listeners = [
        'confirmedDelete',
        'dismissedDelete'
    ];

    function mount()
    {
        $this->lokasi = Lokasi::select(['id','instansi','alamat'])->get();
    }

    #[Renderless]
    public function simpanAbsen()
    {
        try {
            $validated = $this->validate([
                'selectLokasi'  => ['required'],
                'tanggal'       => ['required'],
                'mulai'         => ['required'],
                'selesai'       => ['required']
            ]);
            $absensi = new Absensi;
            $absensi->lokasi_id = $this->selectLokasi;
            $absensi->tanggal = $this->tanggal;
            $absensi->mulai = $this->mulai;
            $absensi->selesai =$this->selesai;
            $absensi->save();
            $this->reset(['tanggal','mulai','selesai']);
            $this->dispatch('absensi-simpan')->self();
        } catch (ValidationException $e) {
            // throw $e;
            $this->alert('warning', 'terjadi kesalahan', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => $e->getMessage(),
            ]);
        }

    }

    #[On('info-update')]
    #[Renderless]
    public function infoUpdate($state, $message, $text)
    {
        $this->alert($state, $message, [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'text' => $text,
        ]);   
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
            $deleteAbsensi = Absensi::find($this->id);
            $deleteAbsensi->delete();
            $this->alert('success', 'Hapus Data', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => 'berhasil menghapus data',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('error', 'terjadi kesalahan', [
                'position' => 'center',
                'timer' => '5000',
                'toast' => true,
                'text' => $th->getMessage(),
            ]);
        }
    }

}; ?>

<section>
    <form wire:submit="simpanAbsen" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-input-label for="selectLokasi" class="text-sm font-medium text-gray-900" :value="__('Lokasi Instansi')" />
                <select wire:model="selectLokasi" name="selectLokasi" id="selectLokasi" class="w-full bg-neutral-100 border-gray-300 dark:border-gray-700 dark:bg-neutral-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm sm:text-sm">
                    <option hidden>Pilih Lokasi</option>
                    @foreach($this->lokasi as $key)
                    <option value="{{$key->id}}">{{$key->instansi}} - {{$key->alamat}}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('selectLokasi')" />
            </div>
            <div>
                <x-input-label for="tanggal" class="text-sm font-medium text-gray-900" :value="__('Tanggal')" />
                <x-text-input wire:model="tanggal" id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="mulai" class="text-sm font-medium text-gray-900" :value="__('Jam Mulai')" />
                <x-text-input wire:model="mulai" id="mulai" name="mulai" type="time" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('mulai')" />
            </div>
            <div>
                <x-input-label for="selesai" class="text-sm font-medium text-gray-900" :value="__('Jam Selesai')" />
                <x-text-input wire:model="selesai" id="selesai" name="selesai" type="time" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('selesai')" />
            </div>
        </div>
        {{-- <div wire:poll.5s="generate">
            {{$this->url}}
        </div> --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            <x-action-message class="me-3" on="absensi-simpan">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>
