<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\DinasAbsen;
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
                ['label' => 'Absen Lokasi', 'column' => 'parentAbsensi', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'lokasi_id'],
                ['label' => 'Petugas', 'column' => 'parentPetugas', 'isData' => true,'hasRelation'=> true, 'columnRelation' => 'npp'],
                ['label' => 'IP Masuk', 'column' => 'devices_ip', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Presensi Masuk', 'column' => 'presensi_masuk', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Presensi Keluar', 'column' => 'presensi_keluar', 'isData' => true,'hasRelation'=> false],
                ['label' => 'dibuat', 'column' => 'created_at', 'isData' => true,'hasRelation'=> false],

            ],
            'absensi' => DinasAbsen::with('parentAbsensi')->where('user_id', Auth::id())->search($this->search)->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage, ['*'], 'page'),
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
            case 'presensi_masuk':
                if($data != null){
                    $parsedDate = Carbon::parse($data);
                    return $parsedDate->format('H:i');
                }
            case 'presensi_keluar':
                if($data != null){
                    $parsedDate = Carbon::parse($data);
                    return $parsedDate->format('H:i:t');
                }
            default:
                return $data;
        }
    }
    
}; ?>

<section>
    <x-table.table :columns="$columns" :page="$page" :perPage="$perPage" :items="$absensi" :sortColumn="$sortColumn" :sortDirection="$sortDirection" isModalEdit="true" :title="$this->title"/>
</section>
