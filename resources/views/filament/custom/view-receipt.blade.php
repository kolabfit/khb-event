<div class="p-4">
    @if($receiptUrl)
        <img src="{{ $receiptUrl }}" alt="Receipt" style="max-width:200px;max-height:200px;margin-left:auto;margin-right:auto;display:block;" class="rounded-lg shadow-lg">
    @else
        <div class="text-center text-gray-500">
            No receipt available
        </div>
    @endif
</div> 