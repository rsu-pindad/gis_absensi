<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Absensi;
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
    public $componentEditName = 'absen.absensi-edit';

    #[Locked]
    public $title = 'Data Absensi';

    public static function destroyOnClose(): bool
    {
        return true;
    }

    #[On('absensi-simpan')]
    public function with() : array
    {
        return [
            'columns' => [
                ['label' => 'Lokasi', 'column' => 'parentLokasi', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'instansi'],
                ['label' => 'Tanggal', 'column' => 'tanggal', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Jam Mulai', 'column' => 'mulai', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Jam Selesai', 'column' => 'selesai', 'isData' => true,'hasRelation'=> false],
                ['label' => 'dibuat', 'column' => 'created_at', 'isData' => true,'hasRelation'=> false],
    
                ['label' => 'Aksi', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
            ],
            'absensi' => Absensi::with('parentLokasi')->search($this->search)->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage, ['*'], 'page'),
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
                $parsedDate = Carbon::parse($data);
                return $parsedDate->diffForHumans();
            default:
                return $data;
        }
    }

}; ?>

<section wire:poll.visible.30s>
    <h6 class="text-base dark:text-white">tabel data diperbarui setiap 30 detik</h6>
    <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 dark:text-white dark:before:border-neutral-600">terakhir data didapat {{now()->format('d-M-Y H:i')}}</div>
    <x-table.table :columns="$columns" :page="$page" :perPage="$perPage" :items="$absensi" :sortColumn="$sortColumn" :sortDirection="$sortDirection" isModalEdit="true" :title="$this->title" :componentEditName="json_encode($componentEditName)" />
</section>
