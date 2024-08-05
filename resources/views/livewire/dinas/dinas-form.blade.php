<?php

use Livewire\Volt\Component;

new class extends Component {

    public $id;

    function mount()
    {
        $this->id = Auth::id();
    }

}; ?>

<section>

    <div class="bg-white p-4 flex flex-row align-middle">
        {!! DNS2D::getBarcodeHTML('4445645656', 'QRCODE'); !!}
    </div>

</section>
