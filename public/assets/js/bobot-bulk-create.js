document.addEventListener('DOMContentLoaded', function() {
    try {
        // Get data from window object (set by Blade)
        const cpmkListData = window.cpmkListData || [];
        const komponenListData = window.komponenListData || [];
        const existingCombinationsData = window.existingCombinationsData || {};
        const bobotWithNilaiData = window.bobotWithNilaiData || {};
        const komponenLockedData = window.komponenLockedData || {};
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

            // Check if button is disabled (locked)
            if (btn.disabled) {
                return;
            }

            const card = btn.closest('.komponen-card');
            const komponenId = card.getAttribute('data-id').toString(); // Ensure string
            const komponenNama = card.getAttribute('data-nama');

            console.log('Komponen clicked:', komponenId, komponenNama);

            // Check if currently selected
            const isSelected = selectedKomponen.find(k => k.id === komponenId);
            const isLocked = komponenLockedData[komponenId] || false;

            if (isSelected) {
                // Don't allow removal if komponen is locked
                if (isLocked) {
                    alert('Komponen ini tidak dapat dihapus karena sudah memiliki nilai mahasiswa.');
                    return;
                }
                // Remove from selected komponen
                selectedKomponen = selectedKomponen.filter(k => k.id !== komponenId);
                console.log('Komponen removed:', komponenId);
            } else {
                // Check if any komponen is locked (has nilai)
                const hasAnyLockedKomponen = Object.values(komponenLockedData).some(locked => locked);
                if (hasAnyLockedKomponen) {
                    alert('Tidak dapat menambahkan komponen baru karena sudah ada nilai mahasiswa. Komponen yang sudah ada nilai tidak dapat diubah atau ditambah.');
                    return;
                }
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
            const isLocked = komponenLockedData[komponenId] || false;
            const hasAnyLockedKomponen = Object.values(komponenLockedData).some(locked => locked);

            if (isSelected) {
                card.classList.remove('border-gray-300', 'hover:border-green-500', 'hover:bg-green-50');
                if (isLocked) {
                    card.classList.add('border-red-500', 'bg-red-50');
                    toggleBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'bg-red-600', 'hover:bg-red-700');
                    toggleBtn.classList.add('bg-gray-600', 'cursor-not-allowed');
                    toggleBtn.disabled = true;
                } else {
                    card.classList.add('border-green-500', 'bg-green-50');
                    toggleBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'bg-gray-600', 'cursor-not-allowed');
                    toggleBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                    toggleBtn.disabled = false;
                }
                addIcon.classList.add('hidden');
                removeIcon.classList.remove('hidden');
            } else {
                // Not selected - show as available with + icon
                card.classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50');

                // If any komponen is locked, disable adding new komponen
                if (hasAnyLockedKomponen) {
                    card.classList.add('border-gray-300', 'bg-gray-100', 'cursor-not-allowed');
                    toggleBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'bg-red-600', 'hover:bg-red-700');
                    toggleBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    toggleBtn.disabled = true;
                } else {
                    card.classList.add('border-gray-300', 'hover:border-green-500', 'hover:bg-green-50');
                    toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'bg-gray-400', 'cursor-not-allowed');
                    toggleBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    toggleBtn.disabled = false;
                }
                addIcon.classList.remove('hidden');
                removeIcon.classList.add('hidden');
            }
        });
    }

    // Clear all komponen
    clearKomponenBtn.addEventListener('click', function() {
        // Keep only locked komponen
        selectedKomponen = selectedKomponen.filter(komponen => {
            return komponenLockedData[komponen.id] || false;
        });
        updateKomponenCardsState();
        renderTable();
    });

    // Render selected komponen tags (removed since we no longer have separate display area)
    // Components are now shown with visual state changes directly on the cards    // Render table based on selected komponen
    let currentInputValues = {};
    function renderTable() {
        console.log('renderTable called with selectedKomponen:', selectedKomponen);

        // Show/hide no komponen message
        if (selectedKomponen.length === 0) {
            noKomponenMessage.style.display = 'block';
        } else {
            noKomponenMessage.style.display = 'none';
        }

        // Simpan semua nilai input sebelum render ulang, dari tableBody saja
        let currentInputValues = {};
        tableBody.querySelectorAll('input[name^="bobot["]').forEach(input => {
            currentInputValues[input.name] = input.value;
        });

        // Clear existing komponen headers (keep Parent CPMK and Sub-CPMK headers)
        const parentCpmkHeader = tableHeaderRow.querySelector('th:first-child');
        const subCpmkHeader = tableHeaderRow.querySelector('th:nth-child(2)');
        tableHeaderRow.innerHTML = '';
        tableHeaderRow.appendChild(parentCpmkHeader);
        tableHeaderRow.appendChild(subCpmkHeader);

        // Add komponen headers
        selectedKomponen.forEach(function(komponen) {
            const th = document.createElement('th');
            th.className = 'px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-r border-gray-200';
            th.textContent = komponen.nama;
            tableHeaderRow.appendChild(th);
        });
        // Tambahkan header kolom Total Bobot CPMK
        const thTotalCpmk = document.createElement('th');
        thTotalCpmk.className = 'px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200';
        thTotalCpmk.textContent = 'Total Bobot CPMK';
        tableHeaderRow.appendChild(thTotalCpmk);

        // Bersihkan body tabel
        tableBody.innerHTML = '';

        // Render baris CPMK dengan hierarki menggunakan struktur kolom
        cpmkListData.forEach(function(cpmkData, rowIndex) {
            const hasChildren = cpmkData.children && cpmkData.children.length > 0;
            const isParent = !cpmkData.parent_id;
            const isChild = cpmkData.parent_id;

            if (isParent && hasChildren) {
                // Parent CPMK dengan children
                cpmkData.children.forEach(function(childCpmk, index) {
                    const childTr = document.createElement('tr');
                    childTr.className = 'hover:bg-gray-50';

                    if (index === 0) {
                        // Parent CPMK cell dengan rowspan
                        const tdParent = document.createElement('td');
                        tdParent.className = 'px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-200 bg-blue-50';
                        tdParent.rowSpan = cpmkData.children.length;
                        tdParent.innerHTML = `
                            <div class="flex flex-col">
                                <span class="font-semibold text-blue-800">${cpmkData.kodeCpmk ?? 'N/A'}</span>
                                <span class="text-xs text-gray-600 mt-1">${(cpmkData.deskripsi ?? '').substring(0, 50)}</span>
                                <span class="text-xs text-blue-600 mt-1 font-medium">Parent CPMK</span>
                            </div>
                        `;
                        childTr.appendChild(tdParent);
                    }

                    // Sub-CPMK cell
                    const tdSub = document.createElement('td');
                    tdSub.className = 'px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-200';
                    tdSub.innerHTML = `
                        <div class="flex flex-col">
                            <span class="font-semibold text-green-700">${childCpmk.kodeCpmk ?? 'N/A'}</span>
                            <span class="text-xs text-gray-600 mt-1">${(childCpmk.deskripsi ?? '').substring(0, 50)}</span>
                        </div>
                    `;
                    childTr.appendChild(tdSub);

                    // Kolom bobot per komponen untuk sub-CPMK
                    let totalCpmk = 0;
                    selectedKomponen.forEach(function(komponen) {
                        const combination = childCpmk.id + '_' + komponen.id;
                        let existingValue = existingCombinationsData[combination] || 0;
                        const hasNilai = bobotWithNilaiData.hasOwnProperty(combination);
                        const isKomponenLocked = komponenLockedData[komponen.id] || false;

                        // Jika ada input, gunakan value input
                        const inputName = 'bobot[' + combination + ']';
                        const inputElem = document.querySelector(`input[name='${inputName}']`);
                        if (inputElem && inputElem.value !== '') {
                            existingValue = parseFloat(inputElem.value) || 0;
                        }
                        totalCpmk += parseFloat(existingValue) || 0;

                        const td = document.createElement('td');
                        td.className = 'px-2 py-3 text-center border-r border-gray-200';

                        // Lock if either this specific combination has nilai OR the entire komponen is locked
                        if (hasNilai || isKomponenLocked) {
                            td.innerHTML = '<div class="relative inline-block"><input type="number" step="0.1" min="0" max="100" value="' + existingValue + '" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center bg-gray-100" disabled><div class="absolute -top-1 -right-1"><svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"></path></svg></div></div><div class="text-xs text-red-600 mt-1">Terkunci</div>';
                        } else {
                            const input = document.createElement('input');
                            input.type = 'number';
                            input.step = '0.1';
                            input.min = '0';
                            input.max = '100';
                            input.name = inputName;
                            if (currentInputValues[inputName] !== undefined) {
                                input.value = currentInputValues[inputName];
                            } else {
                                input.value = existingValue > 0 ? existingValue : '';
                            }
                            input.setAttribute('data-original', existingValue);
                            input.placeholder = '0.0';
                            input.className = 'w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center bobot-input focus:ring-2 focus:ring-blue-500 focus:border-blue-500';

                            // Add event listeners
                            input.addEventListener('input', updateTotalBobotRowRealtime);
                            input.addEventListener('change', updateTotalBobotRowRealtime);

                            td.appendChild(input);
                        }
                        childTr.appendChild(td);
                    });

                    // Kolom total bobot CPMK untuk sub-CPMK
                    const tdTotalCpmk = document.createElement('td');
                    tdTotalCpmk.className = 'px-4 py-3 text-center font-bold text-blue-700';
                    tdTotalCpmk.textContent = totalCpmk.toFixed(2);
                    childTr.appendChild(tdTotalCpmk);

                    tableBody.appendChild(childTr);
                });
            } else if (isParent && !hasChildren) {
                // Parent CPMK tanpa children
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';

                // Kolom Parent CPMK
                const tdParent = document.createElement('td');
                tdParent.className = 'px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-200';
                tdParent.innerHTML = `<div class="flex flex-col"><span class="font-semibold">${cpmkData.kodeCpmk ?? 'N/A'}</span><span class="text-xs text-gray-600 mt-1">${(cpmkData.deskripsi ?? '').substring(0, 50)}</span></div>`;
                tr.appendChild(tdParent);

                // Kolom Sub-CPMK (kosong)
                const tdSub = document.createElement('td');
                tdSub.className = 'px-4 py-3 text-sm text-gray-500 border-r border-gray-200 bg-gray-50';
                tdSub.innerHTML = '<span class="italic">Tidak ada sub-CPMK</span>';
                tr.appendChild(tdSub);

                // Kolom bobot per komponen
                let totalCpmk = 0;
                selectedKomponen.forEach(function(komponen) {
                    const combination = cpmkData.id + '_' + komponen.id;
                    let existingValue = existingCombinationsData[combination] || 0;
                    const hasNilai = bobotWithNilaiData.hasOwnProperty(combination);
                    const isKomponenLocked = komponenLockedData[komponen.id] || false;

                    // Jika ada input, gunakan value input
                    const inputName = 'bobot[' + combination + ']';
                    const inputElem = document.querySelector(`input[name='${inputName}']`);
                    if (inputElem && inputElem.value !== '') {
                        existingValue = parseFloat(inputElem.value) || 0;
                    }
                    totalCpmk += parseFloat(existingValue) || 0;

                    const td = document.createElement('td');
                    td.className = 'px-2 py-3 text-center border-r border-gray-200';

                    // Lock if either this specific combination has nilai OR the entire komponen is locked
                    if (hasNilai || isKomponenLocked) {
                        td.innerHTML = '<div class="relative inline-block"><input type="number" step="0.1" min="0" max="100" value="' + existingValue + '" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center bg-gray-100" disabled><div class="absolute -top-1 -right-1"><svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"></path></svg></div></div><div class="text-xs text-red-600 mt-1">Terkunci</div>';
                    } else {
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.step = '0.1';
                        input.min = '0';
                        input.max = '100';
                        input.name = inputName;
                        if (currentInputValues[inputName] !== undefined) {
                            input.value = currentInputValues[inputName];
                        } else {
                            input.value = existingValue > 0 ? existingValue : '';
                        }
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
                // Kolom total bobot CPMK
                const tdTotalCpmk = document.createElement('td');
                tdTotalCpmk.className = 'px-4 py-3 text-center font-bold text-blue-700';
                tdTotalCpmk.textContent = totalCpmk.toFixed(2);
                tr.appendChild(tdTotalCpmk);

                tableBody.appendChild(tr);
            }
            // Skip child CPMK yang sudah ditampilkan di atas
        });

        // Baris total per komponen
        const trTotal = document.createElement('tr');
        trTotal.className = 'bg-blue-50 font-bold';
        const tdLabel = document.createElement('td');
        tdLabel.className = 'px-4 py-3 text-blue-900 text-base text-center';
        tdLabel.colSpan = 2;
        tdLabel.textContent = 'Total per Komponen';
        trTotal.appendChild(tdLabel);
        let totalKeseluruhan = 0;
        selectedKomponen.forEach(function(komponen) {
            let totalPerKomponen = 0;
            cpmkListData.forEach(function(cpmkData) {
                const hasChildren = cpmkData.children && cpmkData.children.length > 0;
                const isParent = !cpmkData.parent_id;
                const isChild = cpmkData.parent_id;

                if (isParent && hasChildren) {
                    // Hitung dari sub-CPMK
                    cpmkData.children.forEach(function(childCpmk) {
                        const combination = childCpmk.id + '_' + komponen.id;
                        let existingValue = existingCombinationsData[combination] || 0;
                        // Jika ada input, gunakan value input
                        const inputName = 'bobot[' + combination + ']';
                        const inputElem = document.querySelector(`input[name='${inputName}']`);
                        if (inputElem && inputElem.value !== '') {
                            existingValue = parseFloat(inputElem.value) || 0;
                        }
                        totalPerKomponen += parseFloat(existingValue) || 0;
                    });
                } else if (isParent && !hasChildren) {
                    // Hitung dari parent CPMK tanpa children
                    const combination = cpmkData.id + '_' + komponen.id;
                    let existingValue = existingCombinationsData[combination] || 0;
                    // Jika ada input, gunakan value input
                    const inputName = 'bobot[' + combination + ']';
                    const inputElem = document.querySelector(`input[name='${inputName}']`);
                    if (inputElem && inputElem.value !== '') {
                        existingValue = parseFloat(inputElem.value) || 0;
                    }
                    totalPerKomponen += parseFloat(existingValue) || 0;
                }
                // Skip child CPMK yang sudah dihitung di atas
            });
            totalKeseluruhan += totalPerKomponen;
            const td = document.createElement('td');
            td.className = 'px-4 py-3 text-center text-blue-900 text-base';
            td.textContent = totalPerKomponen.toFixed(2);
            trTotal.appendChild(td);
        });
        // Kolom total keseluruhan di pojok kanan bawah
        const tdTotalKeseluruhan = document.createElement('td');
        tdTotalKeseluruhan.className = 'px-4 py-3 text-center font-bold text-green-700';
        tdTotalKeseluruhan.textContent = totalKeseluruhan.toFixed(2);
        trTotal.appendChild(tdTotalKeseluruhan);
        tableBody.appendChild(trTotal);
    }

    // Fungsi untuk update baris total bobot per komponen secara realtime
    function updateTotalBobotRowRealtime() {
        // Hitung total per komponen (kolom)
        const tableBodyRows = tableBody.querySelectorAll('tr');
        let totalKeseluruhan = 0;
        let komponenTotals = [];
        let cpmkTotals = [];
        // Inisialisasi array
        for (let i = 0; i < selectedKomponen.length; i++) komponenTotals[i] = 0;
        for (let i = 0; i < cpmkListData.length; i++) cpmkTotals[i] = 0;

        // Loop semua input dan akumulasi ke array dengan mempertimbangkan hierarki CPMK
        tableBody.querySelectorAll('input[type="number"]').forEach(input => {
            const name = input.name;
            const match = name.match(/bobot\[(\d+)_([\w-]+)\]/);
            if (match) {
                const cpmkId = match[1];
                const komponenId = match[2];
                const cpmkData = cpmkListData.find(c => c.id == cpmkId);
                const komponenIdx = selectedKomponen.findIndex(k => k.id == komponenId);
                const value = parseFloat(input.value) || 0;

                if (cpmkData && komponenIdx >= 0) {
                    // Cek apakah ini adalah sub-CPMK
                    const isChild = cpmkData.parent_id;
                    const isParentWithChildren = !cpmkData.parent_id && cpmkData.children && cpmkData.children.length > 0;

                    // Hanya hitung jika bukan parent CPMK dengan children
                    if (!isParentWithChildren) {
                        komponenTotals[komponenIdx] += value;
                        totalKeseluruhan += value;

                        // Untuk total CPMK, hitung berdasarkan hierarki
                        if (isChild) {
                            // Cari parent CPMK
                            const parentCpmk = cpmkListData.find(c => c.id == cpmkData.parent_id);
                            if (parentCpmk) {
                                const parentIdx = cpmkListData.findIndex(c => c.id == parentCpmk.id);
                                if (parentIdx >= 0) {
                                    cpmkTotals[parentIdx] += value;
                                }
                            }
                        } else {
                            // Parent CPMK tanpa children
                            const cpmkIdx = cpmkListData.findIndex(c => c.id == cpmkId);
                            if (cpmkIdx >= 0) {
                                cpmkTotals[cpmkIdx] += value;
                            }
                        }
                    }
                }
            }
        });
        // Update baris total per komponen (row terakhir)
        const tableBodyRows2 = tableBody.querySelectorAll('tr');
        if (tableBodyRows2.length > 0) {
            const lastRow = tableBodyRows2[tableBodyRows2.length - 1];
            // Update cell total per komponen
            for (let i = 0; i < selectedKomponen.length; i++) {
                const td = lastRow.children[i+2]; // +2 karena ada 2 kolom label (Parent CPMK + Sub-CPMK)
                if (td) td.textContent = komponenTotals[i].toFixed(2);
            }
            // Update cell total keseluruhan
            const tdTotal = lastRow.lastElementChild;
            if (tdTotal) {
                tdTotal.textContent = totalKeseluruhan.toFixed(2);
                tdTotal.classList.remove('text-green-700', 'text-yellow-500', 'text-red-600');
                if (totalKeseluruhan < 100) {
                    tdTotal.classList.add('text-yellow-500');
                } else if (totalKeseluruhan == 100) {
                    tdTotal.classList.add('text-green-700');
                } else if (totalKeseluruhan > 100) {
                    tdTotal.classList.add('text-red-600');
                }
            }
        }
        // Update kolom total bobot CPMK (per baris) dengan mempertimbangkan hierarki
        let rowIndex = 0;
        for (let i = 0; i < cpmkListData.length; i++) {
            const cpmkData = cpmkListData[i];
            const hasChildren = cpmkData.children && cpmkData.children.length > 0;
            const isParent = !cpmkData.parent_id;
            const isChild = cpmkData.parent_id;

            if (isParent && hasChildren) {
                // Skip parent CPMK row (header row)
                rowIndex++;
                // Update sub-CPMK rows
                cpmkData.children.forEach(function(childCpmk) {
                    const row = tableBodyRows2[rowIndex];
                    if (row) {
                        const td = row.lastElementChild;
                        if (td) {
                            // Hitung total untuk sub-CPMK ini
                            let subCpmkTotal = 0;
                            selectedKomponen.forEach(function(komponen) {
                                const combination = childCpmk.id + '_' + komponen.id;
                                const inputName = 'bobot[' + combination + ']';
                                const inputElem = document.querySelector(`input[name='${inputName}']`);
                                if (inputElem && inputElem.value !== '') {
                                    subCpmkTotal += parseFloat(inputElem.value) || 0;
                                }
                            });
                            td.textContent = subCpmkTotal.toFixed(2);
                        }
                    }
                    rowIndex++;
                });
            } else if (isParent && !hasChildren) {
                // Parent CPMK tanpa children
                const row = tableBodyRows2[rowIndex];
                if (row) {
                    const td = row.lastElementChild;
                    if (td) td.textContent = cpmkTotals[i].toFixed(2);
                }
                rowIndex++;
            }
            // Skip child CPMK yang sudah diupdate di atas
        }
        // Tambahkan/hapus icon warning di setiap input jika total keseluruhan > 100
        tableBody.querySelectorAll('input[type="number"]').forEach(input => {
            // Cari parent relative
            let parent = input.parentElement;
            if (!parent.classList.contains('relative')) {
                parent.classList.add('relative');
            }
            // Hapus icon warning lama jika ada
            const oldWarn = parent.querySelector('.bobot-warning-icon');
            if (oldWarn) oldWarn.remove();
            if (totalKeseluruhan > 100) {
                // Tambahkan icon warning
                const warn = document.createElement('span');
                warn.className = 'bobot-warning-icon absolute top-1 right-1';
                warn.innerHTML = '<svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 16.084A2 2 0 0116.342 18H3.658A2 2 0 011.99 16.084l6.342-11.084a2 2 0 013.336 0l6.342 11.084zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V9a1 1 0 112 0v2a1 1 0 01-1 1z" clip-rule="evenodd"/></svg>';
                parent.appendChild(warn);
            }
        });
        // Update status tombol submit
        let allValid = true;
        let adaIsi = false;

        // Validasi per komponen (maksimal 100% per komponen)
        for (let i = 0; i < selectedKomponen.length; i++) {
            if (komponenTotals[i] > 100) {
                allValid = false;
            }
            if (komponenTotals[i] > 0) {
                adaIsi = true;
            }
        }

        // Cek juga jika ada input bobot > 0
        if (!adaIsi) {
            tableBody.querySelectorAll('input[type="number"]').forEach(input => {
                if (parseFloat(input.value) > 0) adaIsi = true;
            });
        }

        // Validasi total keseluruhan tidak boleh lebih dari 100
        if (totalKeseluruhan > 100) {
            allValid = false;
        }

        if (allValid && adaIsi && selectedKomponen.length > 0) {
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
        updateTotalBobotRowRealtime();
    });

    // Update total calculation (deprecated - using updateTotalBobotRowRealtime instead)
    function updateTotal() {
        updateTotalBobotRowRealtime();
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
            let errorMessage = '';

            Object.keys(komponenTotals).forEach(komponenId => {
                if (komponenTotals[komponenId] > 100) {
                    allValid = false;
                    const komponenData = komponenListData.find(k => k.id.toString() === komponenId);
                    const komponenNama = komponenData ? komponenData.nama : 'Komponen ' + komponenId;
                    errorMessage += `• Total bobot untuk komponen "${komponenNama}" melebihi 100% (${komponenTotals[komponenId].toFixed(2)}%)\n`;
                }
            });

            // Validasi total keseluruhan
            let totalKeseluruhan = 0;
            Object.values(komponenTotals).forEach(total => {
                totalKeseluruhan += total;
            });

            if (totalKeseluruhan > 100) {
                allValid = false;
                errorMessage += `• Total bobot keseluruhan melebihi 100% (${totalKeseluruhan.toFixed(2)}%)\n`;
            }

            if (!allValid) {
                e.preventDefault();
                alert('Validasi bobot gagal:\n\n' + errorMessage + '\nMohon periksa kembali.');
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

        // Also add komponen that are locked (have nilai)
        if (komponenLockedData) {
            Object.keys(komponenLockedData).forEach(komponenId => {
                if (komponenLockedData[komponenId] && !selectedKomponen.find(k => k.id === komponenId)) {
                    const komponenData = komponenListData.find(k => k.id.toString() === komponenId);
                    if (komponenData) {
                        selectedKomponen.push({
                            id: komponenId,
                            nama: komponenData.nama
                        });
                    }
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
