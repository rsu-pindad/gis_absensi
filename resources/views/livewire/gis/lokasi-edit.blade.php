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

          <div class="grid grid-cols-2 gap-4 sm:grid-cols-1">
            <div>
                <x-input-label for="lotd" :value="__('Longitude')" />
                <x-text-input wire:model="lotd" id="lotd" name="lotd" type="text" class="mt-1 w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('lotd')" />
            </div>
            <div>
                <x-input-label for="latd" :value="__('Latitude')" />
                <x-text-input wire:model="latd" id="latd" name="latd" type="text" class="mt-1 w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('latd')" />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4">
            <div>
                <x-input-label for="instansi" :value="__('Instansi')" />
                <x-textarea-input wire:model="instansi" id="instansi" name="instansi" class="form-textarea mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('instansi')" />
            </div>
            <div>
                <x-input-label for="alamat" :value="__('Alamat')" />
                <x-textarea-input wire:model="alamat" id="alamat" name="alamat" class="form-textarea mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
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