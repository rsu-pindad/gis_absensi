<section class="bg-gray-100 dark:bg-gray-900">
  <div class="mx-auto max-w-screen-xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-x-16 gap-y-8 px-6">
      <div class="lg:col-span-2 lg:py-12">
        <p class="max-w-xl text-lg text-center dark:text-white">
          Edit Data : {{$this->judulHalaman}}
        </p>
      </div>

      <div class="rounded-lg dark:bg-gray-600 p-8 shadow-lg lg:col-span-3 lg:p-12">
        <form wire:submit="update" class="space-y-4">
          <div>
              <x-input-label for="selectLokasi" :value="__('Instansi')" />
              <x-select-input wire:model="selectLokasi" id="selectLokasi" name="selectLokasi" :items="$this->fetchLokasi" :nameValue="$this->selectName" :custom="true">
                <x-slot:customOption>
                    <option value="{{$this->findLokasi->id}}" selected>{{$this->findLokasi->instansi}}</option>
                </x-slot:customOption>
              </x-select-input>
              <x-input-error class="mt-2" :messages="$errors->get('selectLokasi')" />
          </div>
          <div>
              <x-input-label for="tanggal" :value="__('Tanggal')" />
              <x-text-input wire:model="tanggal" id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" required />
              <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
          </div>

          <div class="grid grid-cols-2 gap-4 sm:grid-cols-1">
              <div>
                  <x-input-label for="mulai" :value="__('Mulai')" />
                  <x-text-input wire:model="mulai" id="mulai" name="mulai" type="time" class="mt-1 w-full" required />
                  <x-input-error class="mt-2" :messages="$errors->get('mulai')" />
              </div>
              <div>
                  <x-input-label for="selesai" :value="__('Selesai')" />
                  <x-text-input wire:model="selesai" id="selesai" name="selesai" type="time" class="mt-1 w-full" required />
                  <x-input-error class="mt-2" :messages="$errors->get('selesai')" />
              </div>
          </div>

          <div class="mt-4">
            <button
              type="submit"
              class="inline-block w-full rounded-lg bg-black px-5 py-3 font-medium text-white sm:w-auto"
            >
              Edit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>