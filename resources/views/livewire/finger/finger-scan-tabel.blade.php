<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Absensi;
use App\Models\DinasAbsenBarcode;
use Illuminate\Support\Carbon;

new class extends Component {
    use WithPagination;

    public $isModalOpen = false;
    public $isModalDelete = false;
    public $isUpdatePage = false;
    public $page = 1;
    public $perPage = 10;
    public $search = '';
    public $sortDirection = 'DESC';
    public $sortColumn = 'created_at';
    public $confirmDeleteId;

    #[Locked]
    public $title = 'Data Dinas Absen';

    public function with() : array
    {
        return [
            'columns' => [
                ['label' => 'Npp', 'column' => 'parentUser', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'npp'],
                ['label' => 'Petugas pembuat QR', 'column' => 'parentPetugas', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'npp'],
                ['label' => 'Lokasi Dinas', 'column' => 'parentAbsensi', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'id'],
                ['label' => 'Otp Absen', 'column' => 'otp_input', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Finger', 'column' => 'fingerprint', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Absen Masuk', 'column' => 'user_masuk', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Absen Keluar', 'column' => 'user_keluar', 'isData' => true, 'hasRelation'=> false],
            ],
            'absensi' => DinasAbsenbarcode::with(['parentUser','parentPetugas','parentAbsensi'])->where('user_id', Auth::id())->where('fingerprint' ,'!=', null)->search($this->search)->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage, ['*'], 'page'),
        ];
    }

    public function doSort($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = ($this->sortDirection === 'ASC') ? 'DESC' : 'ASC';
            return;
        }
        $this->sortColumn = $column;
        $this->sortDirection = 'ASC';
    }

    public function updatingPage($page)
    {
        $this->page = $page ?: 1;
    }

    public function updatedPage()
    {
        session(['page' => $this->page]);
    }

    public function mount(){
        if (session()->has('page')) {
            $this->page = session('page');
        }
    }

    public function customFormat($column, $data)
    {
        switch ($column) {
            case 'created_at':
                if($data != null){
                    $parsedDate = Carbon::parse($data);
                    return $parsedDate->diffForHumans();
                }
            case 'parentAbsensi':
                if($data != null){
                    $absen = Absensi::with('parentLokasi')->find($data);
                    return $absen->parentLokasi->instansi;
                }
            default:
                return $data;
        }
    }
}; ?>

<section wire:poll.visible.30s>
    <h6 class="text-base dark:text-white">tabel data diperbarui setiap 30 detik</h6>
    <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 dark:text-white dark:before:border-neutral-600">terakhir data didapat {{now()->format('d-M-Y H:i')}}</div>
    <x-table.table :columns="$columns" :page="$page" :perPage="$perPage" :items="$absensi" :sortColumn="$sortColumn" :sortDirection="$sortDirection" isModalEdit="true" :title="$this->title" />
</section>
