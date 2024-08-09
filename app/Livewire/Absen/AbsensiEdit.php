<?php

namespace App\Livewire\Absen;

// use Livewire\Component;
use App\Models\Absensi;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Gate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use LivewireUI\Modal\ModalComponent;

class AbsensiEdit extends ModalComponent
{
    public Absensi $id;
    public $judulHalaman;
    public $selectName = 'instansi';
    public $selectLokasi;
    public $fetchLokasi;
    public $findLokasi;
    public $mulai;
    public $selesai;
    public $tanggal;

    public function mount()
    {
        // Gate::authorize('update', $this->id);
        $this->fetchLokasi  = Lokasi::select(['id', 'instansi'])->where('id', '!=', $this->id->lokasi_id)->get();
        $this->findLokasi   = Lokasi::select(['id', 'instansi'])->find($this->id->lokasi_id);
        $this->judulHalaman = $this->findLokasi->instansi;
        $this->mulai        = $this->id->mulai;
        $this->selesai      = $this->id->selesai;
        $this->tanggal      = $this->id->tanggal;
    }

    public function update()
    {
        // Gate::authorize('update', $this->id);
        try {
            $validated = $this->validate([
                'selectLokasi' => ['required'],
                'tanggal'      => ['required'],
                'mulai'        => ['required'],
                'selesai'      => ['required']
            ]);
            $data = [
                'lokasi_id' => $this->selectLokasi,
                'tanggal'   => $this->tanggal,
                'mulai'     => $this->mulai,
                'selesai'   => $this->selesai
            ];
            $this->id->update($data);
            $this->dispatch('info-update', state: 'success', message: 'Absensi', text: 'data berhasil diperbarui');
            $this->closeModal();
        } catch (ValidationException $e) {
            // throw $e;
            // dd($e);
            $this->dispatch('info-update', state: 'warning', message: 'Absensi', text: $e->getMessage());
        }

        // $absensi = Absensi::find($this->id);
        // $absensi->lokasi_id = $this->selectLokasi;
        // $absensi->tanggal = $this->tanggal;
        // $absensi->mulai = $this->mulai;
        // $absensi->selesai =$this->selesai;
        // $absensi->save();
    }

    public function render()
    {
        return view('livewire.absen.absensi-edit');
    }
}
