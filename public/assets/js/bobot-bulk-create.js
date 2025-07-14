document.addEventListener('DOMContentLoaded', function() {
    // Get data from window object (set by Blade)
    const cpmkListData = window.cpmkListData || [];
    const komponenListData = window.komponenListData || [];
    const existingCombinationsData = window.existingCombinationsData || {};
    const bobotWithNilaiData = window.bobotWithNilaiData || {};
    const usedKomponenIdsData = window.usedKomponenIdsData || [];
    // DOM elements
    const availableKomponenDiv = document.getElementById('available-komponen');
    const bobotTable = document.getElementById('bobot-table');
    const noKomponenMessage = document.getElementById('no-komponen-message');
    const tableHeaderRow = document.getElementById('table-header-row');
    const tableBody = document.getElementById('table-body');
    const totalDisplay = document.getElementById('total-display');
    const statusDisplay = document.getElementById('status-display');
    const progressBar = document.getElementById('progress-bar');
    const submitBtn = document.getElementById('submit-btn');
    const clearAllBtn = document.getElementById('clear-all');
    const clearKomponenBtn = document.getElementById('clear-komponen');

    // Check if essential elements exist
    if (!availableKomponenDiv || !bobotTable || !noKomponenMessage || !tableHeaderRow || !tableBody ||
        !totalDisplay || !statusDisplay || !progressBar || !submitBtn || !clearAllBtn || !clearKomponenBtn) {
        console.error('Some required DOM elements are missing!');
        return;
    }

    // Track selected komponen
    let selectedKomponen = [];    // Handle komponen toggle with direct click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('toggle-komponen-btn') || e.target.closest('.toggle-komponen-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const btn = e.target.classList.contains('toggle-komponen-btn') ? e.target : e.target.closest('.toggle-komponen-btn');
            const card = btn.closest('.komponen-card');
            const komponenId = card.getAttribute('data-id').toString(); // Ensure string
            const komponenNama = card.getAttribute('data-nama');

            // Check if currently selected
            const isSelected = selectedKomponen.find(k => k.id === komponenId);

            if (isSelected) {
                // Remove from selected komponen
                selectedKomponen = selectedKomponen.filter(k => k.id !== komponenId);
            } else {
                // Add to selected komponen
                selectedKomponen.push({
                    id: komponenId,
                    nama: komponenNama
                });
            }

            updateKomponenCardsState();
            renderTable();
        }
    });

    // Update komponen cards state based on selections
    function updateKomponenCardsState() {

        document.querySelectorAll('.komponen-card').forEach(card => {
            const komponenId = card.getAttribute('data-id').toString();
            const komponenNama = card.getAttribute('data-nama');
            const toggleBtn = card.querySelector('.toggle-komponen-btn');
            const addIcon = card.querySelector('.add-icon');
            const removeIcon = card.querySelector('.remove-icon');

            const isSelected = selectedKomponen.find(k => k.id === komponenId);

            if (isSelected) {
                card.classList.remove('border-gray-300', 'hover:border-green-500', 'hover:bg-green-50');
                card.classList.add('border-green-500', 'bg-green-50');
                toggleBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                toggleBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                addIcon.classList.add('hidden');
                removeIcon.classList.remove('hidden');
            } else {
                // Not selected - show as available with + icon
                card.classList.remove('border-green-500', 'bg-green-50');
                card.classList.add('border-gray-300', 'hover:border-green-500', 'hover:bg-green-50');
                toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                toggleBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                addIcon.classList.remove('hidden');
                removeIcon.classList.add('hidden');
            }
        });
    }

    // Clear all komponen
    clearKomponenBtn.addEventListener('click', function() {
        selectedKomponen = [];
        updateKomponenCardsState();
        renderTable();
    });

    // Render selected komponen tags (removed since we no longer have separate display area)
    // Components are now shown with visual state changes directly on the cards    // Render table based on selected komponen
    function renderTable() {
        // Show/hide no komponen message
        if (selectedKomponen.length === 0) {
            noKomponenMessage.style.display = 'block';
        } else {
            noKomponenMessage.style.display = 'none';
        }

        // Clear existing komponen headers (keep CPMK header)
        const cpmkHeader = tableHeaderRow.querySelector('th:first-child');
        tableHeaderRow.innerHTML = '';
        tableHeaderRow.appendChild(cpmkHeader);

        // Add komponen headers
        selectedKomponen.forEach(function(komponen) {
            const th = document.createElement('th');
            th.className = 'px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-r border-gray-200';
            th.textContent = komponen.nama;
            tableHeaderRow.appendChild(th);
        });

        // Update existing table rows by adding/removing komponen columns
        const tableRows = tableBody.querySelectorAll('tr');

        tableRows.forEach(function(tr, index) {
            // Remove all existing komponen columns (keep only CPMK column)
            const cpmkColumn = tr.querySelector('td:first-child');
            tr.innerHTML = '';
            tr.appendChild(cpmkColumn);

            // Add komponen columns for this CPMK
            if (selectedKomponen.length > 0) {
                const cpmkMatKul = cpmkListData[index];

                selectedKomponen.forEach(function(komponen) {
                    const combination = cpmkMatKul.cpmk.id + '_' + komponen.id;
                    const existingValue = existingCombinationsData[combination] || 0;
                    const hasNilai = bobotWithNilaiData.hasOwnProperty(combination);

                    const td = document.createElement('td');
                    td.className = 'px-2 py-3 text-center border-r border-gray-200';

                    if (hasNilai) {
                        // Locked input
                        td.innerHTML = '<div class="relative inline-block"><input type="number" step="0.1" min="0" max="100" value="' + existingValue + '" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center bg-gray-100" disabled><div class="absolute -top-1 -right-1"><svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"></path></svg></div></div><div class="text-xs text-red-600 mt-1">Terkunci</div>';
                    } else {
                        // Editable input
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.step = '0.1';
                        input.min = '0';
                        input.max = '100';
                        input.name = 'bobot[' + combination + ']';
                        input.value = existingValue > 0 ? existingValue : '';
                        input.setAttribute('data-original', existingValue);
                        input.placeholder = '0.0';
                        input.className = 'w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center bobot-input focus:ring-2 focus:ring-blue-500 focus:border-blue-500';

                        // Add event listeners
                        input.addEventListener('input', updateTotal);
                        input.addEventListener('change', updateTotal);

                        td.appendChild(input);
                    }

                    tr.appendChild(td);
                });
            }
        });

        updateTotal();
    }

    // Quick actions
    clearAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.bobot-input').forEach(input => {
            if (!input.disabled) {
                input.value = '';
            }
        });
        updateTotal();
    });

    // Update total calculation
    function updateTotal() {
        let total = 0;

        // Calculate total from all inputs (including disabled ones)
        document.querySelectorAll('input[type="number"]').forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });

        totalDisplay.textContent = total.toFixed(1) + '%';

        // Update progress bar
        const percentage = Math.min(total, 100);
        progressBar.style.width = percentage + '%';

        // Update status and colors
        if (total === 100) {
            statusDisplay.textContent = 'Sesuai (100%)';
            statusDisplay.className = 'text-sm text-green-600';
            progressBar.className = 'h-3 rounded-full bg-green-500 transition-all duration-300';
            submitBtn.disabled = false;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        } else if (total > 100) {
            statusDisplay.textContent = 'Melebihi 100%';
            statusDisplay.className = 'text-sm text-red-600';
            progressBar.className = 'h-3 rounded-full bg-red-500 transition-all duration-300';
            submitBtn.disabled = true;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed';
        } else {
            statusDisplay.textContent = 'Dapat disimpan';
            statusDisplay.className = 'text-sm text-blue-600';
            progressBar.className = 'h-3 rounded-full bg-blue-500 transition-all duration-300';
            submitBtn.disabled = false;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        }
    }

    // Form validation before submit (removed 100% requirement)
    const formElement = document.querySelector('form');
    if (formElement) {
        formElement.addEventListener('submit', function(e) {
            if (selectedKomponen.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu komponen penilaian');
                return false;
            }

            let total = 0;
            document.querySelectorAll('input[type="number"]').forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });

            // Only check if total exceeds 100%, allow saving even if less than 100%
            if (total > 100) {
                e.preventDefault();
                alert('Total bobot tidak boleh melebihi 100%. Saat ini: ' + total.toFixed(1) + '%');
                return false;
            }
        });
    }

    // Initialize with existing used components
    function initializeUsedKomponen() {

        if (usedKomponenIdsData.length > 0) {
            usedKomponenIdsData.forEach(komponenId => {
                const komponenIdStr = komponenId.toString();
                const komponenData = komponenListData.find(k => k.id.toString() === komponenIdStr);

                if (komponenData && !selectedKomponen.find(k => k.id === komponenIdStr)) {
                    selectedKomponen.push({
                        id: komponenIdStr,
                        nama: komponenData.nama
                    });
                }
            });
        } else {
            console.log('No used komponen IDs to initialize');
        }
    }

    // Initial setup - with delay to ensure DOM is fully ready
    setTimeout(function() {
        initializeUsedKomponen();
        updateKomponenCardsState();
        renderTable();
    }, 100);
});
