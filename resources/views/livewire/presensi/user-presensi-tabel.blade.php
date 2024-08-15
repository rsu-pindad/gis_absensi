<?php

use Livewire\Volt\Component;
use App\Models\DinasAbsenBarcode;
use App\Models\Absensi;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;
use Livewire\Attribute\Locked;

new class extends Component {
    use WithPagination;
    
    public $isModalOpen = false;
    public $isModalDelete = false;
    public $isUpdatePage = false;
    public $page = 1;
    public $perPage = 10;
    public string $search = '';
    public string $sortDirection = 'DESC';
    public string $sortColumn = 'created_at';
    public $confirmDeleteId;
    public $componentEditName = 'gis.lokasi-edit';
    
    #[Locked]
    public $title = 'Data Tabel User Presensi';

    public static function destroyOnClose(): bool
    {
        return true;
    }

    public function with() : array
    {
        return [
            'columns' => [
                ['label' => 'User', 'column' => 'parentUser', 'isData' => true, 'hasRelation'=> true, 'columnRelation' => 'npp'],
                ['label' => 'Petugas', 'column' => 'parentPetugas', 'isData' => true, 'hasRelation'=> true, 'columnRelation' => 'npp'],
                ['label' => 'Absensi Instansi', 'column' => 'absensi_id', 'isData' => true, 'hasRelation'=> false, 'columnRelation' => 'absensi_id'],
                ['label' => 'Otp QRCode', 'column' => 'otp_qr', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Otp Absen', 'column' => 'otp_input', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Finger', 'column' => 'fingerprint', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'IP User', 'column' => 'devices_ip', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Info Device', 'column' => 'informasi_device', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'Info Os', 'column' => 'informasi_os', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'User Longitude', 'column' => 'lotd_user_barcode', 'isData' => true, 'hasRelation'=> false],
                ['label' => 'User Latitude', 'column' => 'latd_user_barcode', 'isData' => true, 'hasRelation'=> false],
    
                ['label' => 'Aksi', 'column' => 'action', 'isData' => false, 'hasRelation'=> false],
            ],
            'dinas' => DinasAbsenBarcode::search($this->search)->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage, ['*'], 'page'),
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
                $parsedDate = \Carbon\Carbon::parse($data);
                return $parsedDate->diffForHumans();
            case 'absensi_id':
                $lokasi = Absensi::with('parentLokasi')->find($data);
                if(isset($lokasi->parentLokasi->instansi)){
                    return $lokasi->parentLokasi->instansi;
                }
                return null;
            default:
                return $data;
        }
    }

    public function openModalPopover($id = null)
    {
        if ($id) {
            $this->confirmDeleteId = $id;
            $this->isModalDelete = true;
        } else {
            $this->isModalOpen = true;
        }
    }

    public function closeModalPopover()
    {
        $this->isModalDelete = false;
        $this->isModalOpen = false;
        $this->resetCreateForm();
    }

}; ?>

<section wire:poll.visible.30s>
    <h6 class="text-base dark:text-white">tabel data diperbarui setiap 30 detik</h6>
    <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 dark:text-white dark:before:border-neutral-600">terakhir data didapat {{now()->format('d-M-Y H:i')}}</div>
    <x-table.table :columns="$columns" :page="$page" :perPage="$perPage" :items="$dinas" :sortColumn="$sortColumn" :sortDirection="$sortDirection" isModalEdit="true" :title="$this->title" :componentEditName="json_encode($componentEditName)" />
</section>
