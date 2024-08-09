<?php

namespace App\Livewire\Gis;

use App\Models\Lokasi;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use LivewireUI\Modal\ModalComponent;

class LokasiEdit extends ModalComponent
{
    public Lokasi $id;
    public $judulHalaman;
    public $instansi;
    public $lotd;
    public $latd;
    public $alamat;

    public function boot()
    {
        $this->token = config('app.maps.mapbox_token');
    }

    public function mount()
    {
        $this->judulHalaman = $this->id->instansi;
        $this->instansi     = $this->id->instansi;
        $this->lotd         = $this->id->lotd;
        $this->latd         = $this->id->latd;
        $this->alamat       = $this->id->alamat;
    }

    public function update()
    {
        try {
            $validated = $this->validate([
                'instansi' => ['required'],
                'lotd'     => ['required'],
                'latd'     => ['required'],
            ]);
            $data = [
                'instansi' => $this->instansi,
                'lotd'     => $this->lotd,
                'latd'     => $this->latd,
                'alamat'   => $this->alamat
            ];
            $this->id->update($data);
            $this->dispatch('info-update', state: 'success', message: 'Lokasi', text: 'data berhasil diperbarui');
            $this->closeModal();
        } catch (ValidationException $e) {
            $this->dispatch('info-update', state: 'warning', message: 'Lokasi', text: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.gis.lokasi-edit');
    }
}
