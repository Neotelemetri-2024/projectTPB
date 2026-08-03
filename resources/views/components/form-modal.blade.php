<!-- Modal Background/Backdrop -->
<div id="{{ $id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center min-h-screen w-full transition-opacity duration-300 ease-out" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-md max-h-full transform transition-all duration-300 ease-out scale-95 opacity-0" data-modal-content>
        <div class="relative bg-white rounded-lg shadow-xl animate-in fade-in slide-in-from-top-4 duration-300">
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
                    <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 transition-colors duration-200" data-modal-hide="{{ $id }}">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200 inline-flex items-center justify-center">
                        <svg data-spinner class="hidden w-4 h-4 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span data-submit-text>{{ $submitText ?? 'Simpan' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('{{ $id }}');
    const modalContent = modal.querySelector('[data-modal-content]');
    
    // Show modal with animation
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-toggle="{{ $id }}"]') || e.target.closest('[data-modal-toggle="{{ $id }}"]')) {
            e.preventDefault();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger animation
            setTimeout(() => {
                modal.classList.remove('bg-opacity-0');
                modal.classList.add('bg-opacity-10');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    });
    
    // Hide modal with animation
    function hideModal() {
        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modal.classList.remove('bg-opacity-10');
        modal.classList.add('bg-opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    // Hide on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
    
    // Hide on close button click
    modal.querySelectorAll('[data-modal-hide="{{ $id }}"]').forEach(btn => {
        btn.addEventListener('click', hideModal);
    });
    
    // Hide on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });

    // Form submission spinner
    const form = modal.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                    const spinner = submitBtn.querySelector('[data-spinner]');
                    const text = submitBtn.querySelector('[data-submit-text]');
                    if (spinner) spinner.classList.remove('hidden');
                }, 10);
            }
        });
    }
});
</script> 