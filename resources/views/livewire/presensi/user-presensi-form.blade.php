<?php

use Livewire\Volt\Component;
use App\Models\Absensi;
use App\Models\User;
use App\Models\DinasAbsenBarcode;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On; 
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

new class extends Component {

    use LivewireAlert;

    public $selectName = 'parentLokasi';
    
    #[Validate('required')]
    public $selectAbsensi;
    
    #[Validate('required')]
    public $selectUser;
    
    public $absensi;

    public $users;

    #[Locked]
    public $selectData;

    public function mount()
    {
        // $this->users = User::where('id', '!=', Auth::id())->get();
        $this->users = User::select(['id','npp','email'])->get();
        $this->absensi = Absensi::with('parentLokasi')->get();
        $this->selectData = json_encode($this->users);
        // dd($this->selectData);
    }

    #[Renderless]
    public function sendQr($user,$qrData)
    {
        try {
            $user = User::select('no_hp')->findOrFail($user);
            $url = Storage::disk('public')->url('qr/QR'.$qrData.'.png');
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                'target' => '0818831140',
                // 'target' => $user->no_hp,
                'message' => 'Halo '.$user->npp.' Qr telah di buat, silahkan gunakan Qr berikut untuk absen '.$url,
                // 'url' => $url,
                // 'filename' => 'Qr Absensi',
                'schedule' => 0,
                'typing' => false,
                'delay' => '5',
                'countryCode' => '62',
                // 'file' => $url,
                // 'file' => new CURLFile('qr/QR'.$qrData),
            ),
            CURLOPT_HTTPHEADER => array(
                    'Authorization: '.config('app.fonnte.fonnte_token'),
                ),
            ));

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
            }
            curl_close($curl);

            if (isset($error_msg)) {
                return $error_msg;
            }
            return $response;
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }   
    }

    #[Renderless]
    public function buatBarcode($id)
    {
        $idUser = $id;
        $random_string = md5(microtime());
        $otp = rand(1000,9999);
        try {
            $urlAbsen = URL::temporarySignedRoute('signedabsensi', now()->addHours(1), ['user' => $idUser, 'otp' => $otp], absolute:true);
            Storage::disk('public')->put('qr/QR'.$random_string.'.png',base64_decode(DNS2D::getBarcodePNG($urlAbsen,'QRCODE')));
            return [
                'user_barcode_url' => $urlAbsen,
                'user_barcode_img' => $random_string,
                'otp_qr' => $otp,
            ];
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    #[Renderless]
    public function simpanPresensi()
    {
        // dd($this->selectUser);
        $data = $this->selectUser;
        $newData = [];
        $buatBarcode = [];
        $fonnte = [];
        foreach ($data as $key => $value) {
            try {
                $buatBarcode = $this->buatBarcode($value);
                $otp_input = rand(1000, 9999);
                $newData[] = [
                    'user_id' => (int)$value,
                    'petugas_id' => Auth::id(),
                    'absensi_id' => (int)$this->selectAbsensi,
                    'user_barcode_url' => $buatBarcode['user_barcode_url'],
                    'user_barcode_img' => $buatBarcode['user_barcode_img'],
                    'otp_qr' => $buatBarcode['otp_qr'],
                    'otp_input' => $otp_input,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (\Throwable $th) {
                //throw $th;
                $this->dispatch('infoUpdate', state:'warning',message:'terjadi kesalahan', text:$th->getMessage());
                continue;
            }
            try {
                $fonnte = $this->sendQr($value, $buatBarcode['user_barcode_img']);
            } catch (\Throwable $th) {
                //throw $th;
                 throw $this->dispatch('infoUpdate', state:'warning',message:'terjadi kesalahan', text:$th->getMessage());
            }
        }
        try {
            $dinasAbsenBarcode = new DinasAbsenBarcode;
            $dinasAbsenBarcode->insert($newData);
            // $dinasAbsenBarcode->save();
            $this->infoUpdate('success','Presensi', 'Presensi berhasil disimpan');
        } catch (\Throwable $th) {
            $this->infoUpdate('warning','terjadi kesalahan', $th->getMessage());
        }
    }

    #[On('info-update')]
    #[Renderless]
    public function infoUpdate($state, $message, $text) : void
    {
        $this->alert($state, $message, [
            'position' => 'center',
            'timer' => '5000',
            'toast' => true,
            'text' => $text,
        ]);   
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
            <div class="relative">
                <select wire:model="selectUser" id="selectUser" name="selectUser[]" class="block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" multiple placeholder="pilih user..." autocomplete="off">
                </select>
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
    .ts-wrapper .option .npp {
        display: block;
    }

    .ts-wrapper .option .email {
        font-size: 12px;
        display: block;
        color: #a0a0a0;
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
    // var selectUsers = document.querySelector('#selectUser');
    // selectUsers.classList.add(
    //     'py-3','px-4','pe-9','block',
    //     'w-full', 'bg-gray-100' ,'border-transparent' ,'rounded-lg',
    //     'text-sm','focus:border-blue-500','focus:ring-blue-500','disabled:opacity-50',
    //     'disabled:pointer-events-none', 'dark:bg-neutral-700', 'dark:border-transparent',
    //     'dark:text-neutral-400','dark:focus:ring-neutral-600');
    // var tsControll = document.getElementsByClassName('.ts-control');
    // var tsControll = document.querySelectorAll('.ts-control');
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
                return '<div title="' + escape(data.npp) + '">' + escape(data.email) + '</div>';
            }
        }
    });
</script>
@endpush
