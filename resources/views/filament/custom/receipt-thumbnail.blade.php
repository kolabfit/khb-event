<div>
    <img src="{{ $receiptUrl }}" alt="Receipt" class="w-16 h-16 object-cover rounded-lg cursor-pointer">

    <x-filament::modal
        id="view-receipt-modal-{{ md5($receiptUrl) }}"
        width="4xl"
        :heading="'Receipt'"
        :description="null"
        :submit-action="null"
        :cancel-action="[
            'label' => 'Close',
            'color' => 'gray',
        ]"
    >
        <div class="p-4">
            <img src="{{ $receiptUrl }}" alt="Receipt" class="w-full h-auto rounded-lg shadow-lg">
        </div>
    </x-filament::modal>
</div> 