document.addEventListener('DOMContentLoaded', function() {
    try {
        // Get data from window object (set by Blade)
        const cpmkListData = window.cpmkListData || [];
        const komponenListData = window.komponenListData || [];
        const existingCombinationsData = window.existingCombinationsData || {};
        const bobotWithNilaiData = window.bobotWithNilaiData || {};
        const usedKomponenIdsData = window.usedKomponenIdsData || [];

        // Debug logging
        console.log('CPMK List Data:', cpmkListData);
        console.log('Komponen List Data:', komponenListData);
        console.log('Existing Combinations:', existingCombinationsData);
    // DOM elements
    const availableKomponenDiv = document.getElementById('available-komponen');
    const bobotTable = document.getElementById('bobot-table');
    const noKomponenMessage = document.getElementById('no-komponen-message');
    const tableHeaderRow = document.getElementById('table-header-row');
    const tableBody = document.getElementById('table-body');
    const submitBtn = document.getElementById('submit-btn');
    const clearAllBtn = document.getElementById('clear-all');
    const clearKomponenBtn = document.getElementById('clear-komponen');

    // Check if essential elements exist
    if (!availableKomponenDiv || !bobotTable || !noKomponenMessage || !tableHeaderRow || !tableBody ||
        !submitBtn || !clearAllBtn || !clearKomponenBtn) {
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

            console.log('Komponen clicked:', komponenId, komponenNama);

            // Check if currently selected
            const isSelected = selectedKomponen.find(k => k.id === komponenId);

            if (isSelected) {
                // Remove from selected komponen
                selectedKomponen = selectedKomponen.filter(k => k.id !== komponenId);
                console.log('Komponen removed:', komponenId);
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
        console.log('renderTable called with selectedKomponen:', selectedKomponen);

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
            if (selectedKomponen.length > 0 && tr.id !== 'total-bobot-row') {
                const cpmkData = cpmkListData[index];
                console.log('Processing CPMK at index', index, ':', cpmkData);

                selectedKomponen.forEach(function(komponen) {
                    const combination = cpmkData.id + '_' + komponen.id;
                    const existingValue = existingCombinationsData[combination] || 0;
                    const hasNilai = bobotWithNilaiData.hasOwnProperty(combination);

                    console.log('Creating input for combination:', combination, 'existing value:', existingValue);

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
                        input.addEventListener('input', updateTotalBobotRowRealtime);
                        input.addEventListener('change', updateTotalBobotRowRealtime);

                        td.appendChild(input);
                    }

                    tr.appendChild(td);
                });
            }
        });

        // Update baris total bobot per komponen setelah render
        updateTotalBobotRowRealtime();

        updateTotal();
    }

    // Fungsi untuk update baris total bobot per komponen secara realtime
    function updateTotalBobotRowRealtime() {
        const totalRow = document.getElementById('total-bobot-row');
        let allValid = true;
        // Hitung total per komponen dan simpan ke objek
        const komponenTotals = {};
        selectedKomponen.forEach(function(komponen) {
            komponenTotals[komponen.id] = 0;
            document.querySelectorAll('input[type="number"]').forEach(input => {
                if (input.name && input.name.endsWith('_' + komponen.id + ']')) {
                    komponenTotals[komponen.id] += parseFloat(input.value) || 0;
                }
            });
        });
        if (totalRow) {
            // Hapus semua kolom kecuali kolom label pertama
            while (totalRow.children.length > 1) {
                totalRow.removeChild(totalRow.lastChild);
            }
            // Render total per komponen
            selectedKomponen.forEach(function(komponen) {
                const total = komponenTotals[komponen.id] || 0;
                let color = 'text-blue-700';
                if (total > 100) {
                    color = 'text-red-600 font-bold';
                    allValid = false;
                } else if (total === 100) {
                    color = 'text-green-600 font-bold';
                }
                const td = document.createElement('td');
                td.className = `px-2 py-3 text-center border-r border-gray-200 ${color}`;
                td.textContent = total.toFixed(1) + '%';
                totalRow.appendChild(td);
            });
        }
        // Tambahkan/hapus icon warning di input field jika total > 100
        selectedKomponen.forEach(function(komponen) {
            const total = komponenTotals[komponen.id] || 0;
            document.querySelectorAll('input[type="number"]').forEach(input => {
                if (input.name && input.name.endsWith('_' + komponen.id + ']')) {
                    // Cari parent relative
                    let parent = input.parentElement;
                    if (!parent.classList.contains('relative')) {
                        parent.classList.add('relative');
                    }
                    // Hapus icon warning lama jika ada
                    const oldWarn = parent.querySelector('.bobot-warning-icon');
                    if (oldWarn) oldWarn.remove();
                    if (total > 100) {
                        // Tambahkan icon warning
                        const warn = document.createElement('span');
                        warn.className = 'bobot-warning-icon absolute top-1 right-1';
                        warn.innerHTML = '<svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 16.084A2 2 0 0116.342 18H3.658A2 2 0 011.99 16.084l6.342-11.084a2 2 0 013.336 0l6.342 11.084zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V9a1 1 0 112 0v2a1 1 0 01-1 1z" clip-rule="evenodd"/></svg>';
                        parent.appendChild(warn);
                    }
                }
            });
        });
        // Update tombol submit
        if (allValid && selectedKomponen.length > 0) {
            submitBtn.disabled = false;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed';
        }
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
        // Ambil semua input bobot yang aktif
        const komponenTotals = {};
        let komponenValid = {};
        let komponenCount = selectedKomponen.length;
        let allValid = true;

        // Inisialisasi total per komponen
        selectedKomponen.forEach(komponen => {
            komponenTotals[komponen.id] = 0;
            komponenValid[komponen.id] = true;
        });

        // Hitung total per komponen (kolom)
        document.querySelectorAll('input[type="number"]').forEach(input => {
            if (input.name && input.name.startsWith('bobot[')) {
                // Format: bobot[cpmkId_komponenId]
                const match = input.name.match(/bobot\[(\d+)_([\w-]+)\]/);
                if (match) {
                    const komponenId = match[2];
                    const value = parseFloat(input.value) || 0;
                    if (komponenTotals.hasOwnProperty(komponenId)) {
                        komponenTotals[komponenId] += value;
                    }
                }
            }
        });

        // Update tombol submit
        if (allValid && komponenCount > 0) {
            submitBtn.disabled = false;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed';
        }
    }

    // Form validation before submit (per komponen)
    const formElement = document.querySelector('form');
    if (formElement) {
        formElement.addEventListener('submit', function(e) {
            if (selectedKomponen.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu komponen penilaian');
                return false;
            }
            // Validasi per komponen
            const komponenTotals = {};
            selectedKomponen.forEach(komponen => {
                komponenTotals[komponen.id] = 0;
            });
            document.querySelectorAll('input[type="number"]').forEach(input => {
                if (input.name && input.name.startsWith('bobot[')) {
                    const match = input.name.match(/bobot\[(\d+)_([\w-]+)\]/);
                    if (match) {
                        const komponenId = match[2];
                        const value = parseFloat(input.value) || 0;
                        if (komponenTotals.hasOwnProperty(komponenId)) {
                            komponenTotals[komponenId] += value;
                        }
                    }
                }
            });
            let allValid = true;
            Object.keys(komponenTotals).forEach(komponenId => {
                if (komponenTotals[komponenId] > 100) {
                    allValid = false;
                }
            });
            if (!allValid) {
                e.preventDefault();
                alert('Total bobot pada salah satu komponen melebihi 100%. Mohon periksa kembali.');
                return false;
            }
        });
    }

    // Initialize with existing used components
    function initializeUsedKomponen() {
        if (usedKomponenIdsData && usedKomponenIdsData.length > 0) {
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
        }
    }

    // Initial setup - with delay to ensure DOM is fully ready
    setTimeout(function() {
        initializeUsedKomponen();
        updateKomponenCardsState();
        renderTable();
    }, 100);

    } catch (error) {
        console.error('Error in bobot-bulk-create script:', error);
    }
});
