<?php

use Livewire\Volt\Component;
use App\Models\Absensi;
use App\Models\User;

new class extends Component {

    public $selectAbsensi;
    public $selectName = 'parentLokasi';
    public $selectUser;
    public $users;
    public $selectData;

    public function mount()
    {
        // $this->users = User::where('id', '!=', Auth::id())->get();
        $this->users = User::select(['id','npp','email'])->get();
        $this->absensi = Absensi::with('parentLokasi')->get();
        $this->selectData = json_encode($this->users);
        // dd($this->selectData);
    }

    public function simpanPresensi()
    {
        dd($this->selectUser);
    }

}; ?>

<section>
    <form wire:submit="simpanPresensi" class="flex flex-col mt-6 space-y-6">
        <div class="flex-auto">
            <x-input-label for="selectAbsensi" class="text-sm font-medium text-gray-900" :value="__('Instansi')" />
            <x-select-input wire:model="selectAbsensi" id="selectAbsensi" name="selectAbsensi" :items="$this->absensi" :nameValue="$this->selectName" required />
            <x-input-error class="mt-2" :messages="$errors->get('selectAbsensi')" />
        </div>
        <div class="flex-auto">
            <x-input-label for="selectUser" :value="__('User')" />
            <div class="relative flex w-full">
                <select wire:model="selectUser" id="selectUser" name="selectUser[]"
                multiple placeholder="pilih user..." autocomplete="off"></select>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('selectUser')" />
        </div>
        <div class="flex items-center gap-4">
            <x-action-message class="me-3" on="simpanPresensi">
                {{ __('Presensi disimpan') }}
            </x-action-message>
            <x-primary-button type="submit">{{ __('Simpan Presensi') }}</x-primary-button>
        </div>
    </form>
</section>

@push('modulecss')
<style>
/* .ts-wrapper .option .npp {
    display: block;
}
.ts-wrapper .option .email {
    font-size: 12px;
    display: block;
    color: #a0a0a0;
} */
.ts-wrapper .option .email {
    font-size: 12px;
    display: block;
    color: #a0a0a0;
    /* class: 'grid grid-flow-row-dense grid-cols-3 grid-rows-3'; */
}
</style>
@endpush

@push('modulejs')

<script type="module">

let dataOption = {!!$this->selectData!!};
// let dataOptionParse = JSON.stringify(dataOption);
// console.log(dataOption);
// console.log(dataOptionParse);
// console.log(JSON.parse(dataOptionParse));
new TomSelect('#selectUser',{
    plugins: ['input_autogrow'],
	valueField: 'id',
	searchField: 'npp',
	options: 
        dataOption
	,
	render: {
		option: function(data, escape) {
			return '<div>' +
					'<span class="npp">' + escape(data.npp) + '</span>' +
					'<span class="email">' + escape(data.email) + '</span>' +
				'</div>';
		},
		item: function(data, escape) {
			return '<div title="' + escape(data.npp) + '" class="mx-4 border-2">' + escape(data.email) + '</div>';
		}
	}
});
</script> 
@endpush
