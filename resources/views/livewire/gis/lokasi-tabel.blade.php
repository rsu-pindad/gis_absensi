<?php

use Livewire\Volt\Component;
use App\Models\Lokasi;
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
    public $title = 'Data Tabel GIS';

    public static function destroyOnClose(): bool
    {
        return true;
    }

    #[On('data-update')]
    public function with() : array
    {
        return [
            'columns' => [
                ['label' => 'Longitude', 'column' => 'lotd', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Latitude', 'column' => 'latd', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Instansi', 'column' => 'instansi', 'isData' => true,'hasRelation'=> false],
                ['label' => 'Alamat', 'column' => 'alamat', 'isData' => true,'hasRelation'=> false],
    
                ['label' => 'Aksi', 'column' => 'action', 'isData' => false,'hasRelation'=> false],
            ],
            'lokasi' => Lokasi::search($this->search)->orderBy($this->sortColumn, $this->sortDirection)->paginate($this->perPage, ['*'], 'page'),
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

<section>
    <x-table.table :columns="$columns" :page="$page" :perPage="$perPage" :items="$lokasi" :sortColumn="$sortColumn" :sortDirection="$sortDirection" isModalEdit="true" :title="$this->title" :componentEditName="json_encode($componentEditName)" />
</section>