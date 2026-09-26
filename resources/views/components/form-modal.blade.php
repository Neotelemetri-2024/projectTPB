<!-- Modal Background/Backdrop (daisyUI modal pattern) -->
<div id="{{ $id }}" tabindex="-1" role="dialog" aria-modal="true" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box p-0 bg-white rounded-lg shadow-xl max-w-md" data-modal-content>
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $title }}
            </h3>
            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200" data-modal-hide="{{ $id }}">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
        </div>
        <form action="{{ $action }}" method="POST" class="p-4 md:p-5" {!! isset($enctype) ? 'enctype="' . $enctype . '"' : '' !!}>
            @csrf
            @if(isset($method))
                @method($method)
            @endif

            {{ $slot }}

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium px-5 py-2.5 transition-colors duration-200" data-modal-hide="{{ $id }}">
                    Batal
                </button>
                <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 rounded-lg text-sm font-medium px-5 py-2.5 text-center transition-colors duration-200 inline-flex items-center justify-center">
                    <svg data-spinner class="hidden w-4 h-4 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span data-submit-text>{{ $submitText ?? 'Simpan' }}</span>
                </button>
            </div>
        </form>
    </div>
    <button type="button" class="modal-backdrop" data-modal-backdrop data-modal-hide="{{ $id }}" aria-label="Close"></button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('{{ $id }}');

    // Show modal
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-toggle="{{ $id }}"]') || e.target.closest('[data-modal-toggle="{{ $id }}"]')) {
            e.preventDefault();
            modal.classList.add('modal-open');
        }
    });

    // Hide modal
    function hideModal() {
        modal.classList.remove('modal-open');
    }

    // Hide on close buttons (incl. backdrop)
    modal.querySelectorAll('[data-modal-hide="{{ $id }}"]').forEach(btn => {
        btn.addEventListener('click', hideModal);
    });

    // Hide on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('modal-open')) {
            hideModal();
        }
    });

    // Form submission spinner handled globally by setButtonLoading in app.js
});
</script> 