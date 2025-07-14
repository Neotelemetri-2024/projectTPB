document.addEventListener('DOMContentLoaded', function() {
    // Get data from window object (set by Blade)
    const cpmkListData = window.cpmkListData || [];
    const komponenListData = window.komponenListData || [];
    const existingCombinationsData = window.existingCombinationsData || {};
    const bobotWithNilaiData = window.bobotWithNilaiData || {};

    // DOM elements
    const availableKomponenDiv = document.getElementById('available-komponen');
    const addSelectedKomponenBtn = document.getElementById('add-selected-komponen');
    const selectedKomponenDiv = document.getElementById('selected-komponen');
    const emptyMessage = document.getElementById('empty-message');
    const bobotTable = document.getElementById('bobot-table');
    const noKomponenMessage = document.getElementById('no-komponen-message');
    const komponenHeaders = document.getElementById('komponen-headers');
    const tableBody = document.getElementById('table-body');
    const totalDisplay = document.getElementById('total-display');
    const statusDisplay = document.getElementById('status-display');
    const progressBar = document.getElementById('progress-bar');
    const submitBtn = document.getElementById('submit-btn');
    const addBtnText = document.getElementById('add-btn-text');
    const selectedCount = document.getElementById('selected-count');

    // Track selected komponen
    let selectedKomponen = [];
    let tempSelectedKomponen = []; // For tracking which cards are selected before adding

    // Handle komponen card selection
    document.querySelectorAll('.komponen-card').forEach(card => {
        card.addEventListener('click', function() {
            const komponenId = this.getAttribute('data-id');
            const komponenNama = this.getAttribute('data-nama');
            const indicator = this.querySelector('.selection-indicator');
            const checkmark = this.querySelector('.checkmark');

            // Check if already in final selected list
            if (selectedKomponen.find(k => k.id == komponenId)) {
                return; // Already selected, ignore click
            }

            // Toggle selection in temp list
            const tempIndex = tempSelectedKomponen.findIndex(k => k.id == komponenId);

            if (tempIndex > -1) {
                // Remove from temp selection
                tempSelectedKomponen.splice(tempIndex, 1);
                this.classList.remove('border-green-500', 'bg-green-50');
                this.classList.add('border-gray-300');
                indicator.classList.remove('border-green-500', 'bg-green-500');
                indicator.classList.add('border-gray-300');
                checkmark.classList.add('hidden');
            } else {
                // Add to temp selection
                tempSelectedKomponen.push({
                    id: komponenId,
                    nama: komponenNama
                });
                this.classList.remove('border-gray-300');
                this.classList.add('border-green-500', 'bg-green-50');
                indicator.classList.remove('border-gray-300');
                indicator.classList.add('border-green-500', 'bg-green-500');
                checkmark.classList.remove('hidden');
            }

            updateAddButton();
        });
    });

    // Update add button state
    function updateAddButton() {
        if (tempSelectedKomponen.length > 0) {
            addSelectedKomponenBtn.disabled = false;
            addBtnText.textContent = 'Tambahkan Komponen Terpilih';
            selectedCount.textContent = '(' + tempSelectedKomponen.length + ')';
            selectedCount.classList.remove('hidden');
        } else {
            addSelectedKomponenBtn.disabled = true;
            addBtnText.textContent = 'Tambahkan Komponen Terpilih';
            selectedCount.classList.add('hidden');
        }
    }

    // Add selected komponen event
    addSelectedKomponenBtn.addEventListener('click', function() {
        if (tempSelectedKomponen.length === 0) {
            alert('Pilih komponen terlebih dahulu');
            return;
        }

        // Move temp selected to final selected
        tempSelectedKomponen.forEach(komponen => {
            if (!selectedKomponen.find(k => k.id == komponen.id)) {
                selectedKomponen.push(komponen);
            }
        });

        // Clear temp selection and update UI
        tempSelectedKomponen = [];
        updateKomponenCardsState();
        updateAddButton();
        renderSelectedKomponen();
        renderTable();
    });

    // Update komponen cards state based on selections
    function updateKomponenCardsState() {
        document.querySelectorAll('.komponen-card').forEach(card => {
            const komponenId = card.getAttribute('data-id');
            const indicator = card.querySelector('.selection-indicator');
            const checkmark = card.querySelector('.checkmark');

            // Reset all cards first
            card.classList.remove('border-green-500', 'bg-green-50', 'border-gray-400', 'bg-gray-100');
            indicator.classList.remove('border-green-500', 'bg-green-500', 'border-gray-400', 'bg-gray-300');
            checkmark.classList.add('hidden');
            card.style.cursor = 'pointer';

            if (selectedKomponen.find(k => k.id == komponenId)) {
                // Already in final selection - make it disabled/grayed out
                card.classList.add('border-gray-400', 'bg-gray-100');
                indicator.classList.add('border-gray-400', 'bg-gray-300');
                checkmark.classList.remove('hidden');
                card.style.cursor = 'not-allowed';
            } else if (tempSelectedKomponen.find(k => k.id == komponenId)) {
                // In temp selection - highlight
                card.classList.add('border-green-500', 'bg-green-50');
                indicator.classList.add('border-green-500', 'bg-green-500');
                checkmark.classList.remove('hidden');
            } else {
                // Available for selection
                card.classList.add('border-gray-300');
                indicator.classList.add('border-gray-300');
            }
        });
    }

    // Clear all komponen
    document.getElementById('clear-komponen').addEventListener('click', function() {
        selectedKomponen = [];
        tempSelectedKomponen = [];
        updateKomponenCardsState();
        updateAddButton();
        renderSelectedKomponen();
        renderTable();
    });

    // Render selected komponen tags
    function renderSelectedKomponen() {
        selectedKomponenDiv.innerHTML = '';

        if (selectedKomponen.length === 0) {
            emptyMessage.style.display = 'inline';
            selectedKomponenDiv.appendChild(emptyMessage);
        } else {
            emptyMessage.style.display = 'none';

            selectedKomponen.forEach(function(komponen) {
                const tag = document.createElement('span');
                tag.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 border border-green-200';
                tag.innerHTML = komponen.nama + '<button type="button" class="ml-2 text-green-600 hover:text-green-800" onclick="removeKomponen(' + komponen.id + ')"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button>';
                selectedKomponenDiv.appendChild(tag);
            });
        }
    }

    // Remove komponen function (global scope)
    window.removeKomponen = function(komponenId) {
        selectedKomponen = selectedKomponen.filter(k => k.id != komponenId);
        updateKomponenCardsState();
        renderSelectedKomponen();
        renderTable();
    };

    // Render table based on selected komponen
    function renderTable() {
        if (selectedKomponen.length === 0) {
            bobotTable.style.display = 'none';
            noKomponenMessage.style.display = 'block';
            return;
        }

        bobotTable.style.display = 'table';
        noKomponenMessage.style.display = 'none';

        // Render headers
        komponenHeaders.innerHTML = '';
        selectedKomponen.forEach(function(komponen) {
            const th = document.createElement('th');
            th.className = 'px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b';
            th.textContent = komponen.nama;
            komponenHeaders.appendChild(th);
        });

        // Render body
        tableBody.innerHTML = '';
        cpmkListData.forEach(function(cpmkMatKul) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';

            // CPMK column
            const tdCpmk = document.createElement('td');
            tdCpmk.className = 'px-4 py-3 border-r';
            tdCpmk.innerHTML = '<div class="text-sm font-medium text-gray-900">' + cpmkMatKul.cpmk.kodeCpmk + '</div><div class="text-xs text-gray-500">' + cpmkMatKul.cpmk.deskripsiCpmk.substring(0, 50) + '...</div>';
            tr.appendChild(tdCpmk);

            // Komponen columns
            selectedKomponen.forEach(function(komponen) {
                const combination = cpmkMatKul.cpmk.id + '_' + komponen.id;
                const existingValue = existingCombinationsData[combination] || 0;
                const hasNilai = bobotWithNilaiData.hasOwnProperty(combination);

                const td = document.createElement('td');
                td.className = 'px-2 py-3 text-center';

                if (hasNilai) {
                    // Locked input
                    td.innerHTML = '<div class="relative"><input type="number" step="0.1" min="0" max="100" value="' + existingValue + '" class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center bg-gray-100" disabled><div class="absolute -top-1 -right-1"><svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"></path></svg></div></div><div class="text-xs text-red-600 mt-1">Terkunci</div>';
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
                    input.className = 'w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center bobot-input';

                    // Add event listeners
                    input.addEventListener('input', updateTotal);
                    input.addEventListener('change', updateTotal);

                    td.appendChild(input);
                }

                tr.appendChild(td);
            });

            tableBody.appendChild(tr);
        });

        updateTotal();
    }

    // Quick actions
    document.getElementById('clear-all').addEventListener('click', function() {
        document.querySelectorAll('.bobot-input').forEach(input => {
            if (!input.disabled) {
                input.value = '';
            }
        });
        updateTotal();
    });

    document.getElementById('reset-original').addEventListener('click', function() {
        document.querySelectorAll('.bobot-input').forEach(input => {
            if (!input.disabled) {
                const originalValue = input.getAttribute('data-original');
                input.value = originalValue > 0 ? originalValue : '';
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
            statusDisplay.textContent = 'Sesuai';
            statusDisplay.className = 'text-sm text-green-600';
            progressBar.className = 'h-3 rounded-full bg-green-500 transition-all duration-300';
            submitBtn.disabled = false;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        } else if (total > 100) {
            statusDisplay.textContent = 'Melebihi 100%';
            statusDisplay.className = 'text-sm text-red-600';
            progressBar.className = 'h-3 rounded-full bg-red-500 transition-all duration-300';
            submitBtn.disabled = true;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed';
        } else {
            statusDisplay.textContent = 'Belum sesuai';
            statusDisplay.className = 'text-sm text-gray-500';
            progressBar.className = 'h-3 rounded-full bg-blue-500 transition-all duration-300';
            submitBtn.disabled = true;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed';
        }
    }

    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
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

        if (total !== 100) {
            e.preventDefault();
            alert('Total bobot harus tepat 100%. Saat ini: ' + total.toFixed(1) + '%');
            return false;
        }
    });

    // Initial render
    updateKomponenCardsState();
    updateAddButton();
    renderSelectedKomponen();
    renderTable();
});
