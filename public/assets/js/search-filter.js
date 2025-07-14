/**
 * Search and Filter utilities for data tables and cards
 */

class SearchFilter {
    constructor(options = {}) {
        this.searchInput = options.searchInput || '#search-input';
        this.filterSelectors = options.filterSelectors || [];
        this.sortSelector = options.sortSelector || '#sort-by';
        this.resetButton = options.resetButton || '#reset-filters';
        this.itemSelector = options.itemSelector || '.data-item';
        this.noResultsElement = options.noResultsElement || '#no-results';
        this.emptyStateElement = options.emptyStateElement || '#empty-state';
        this.totalCountElement = options.totalCountElement || '#total-count';
        this.containerElement = options.containerElement || '#data-container';

        this.init();
    }

    init() {
        // Get DOM elements
        this.searchEl = document.querySelector(this.searchInput);
        this.filterEls = this.filterSelectors.map(selector => document.querySelector(selector));
        this.sortEl = document.querySelector(this.sortSelector);
        this.resetBtn = document.querySelector(this.resetButton);
        this.items = document.querySelectorAll(this.itemSelector);
        this.noResults = document.querySelector(this.noResultsElement);
        this.emptyState = document.querySelector(this.emptyStateElement);
        this.totalCount = document.querySelector(this.totalCountElement);
        this.container = document.querySelector(this.containerElement);

        // Bind events
        this.bindEvents();

        // Initial filter
        this.filterAndSort();
    }

    bindEvents() {
        if (this.searchEl) {
            this.searchEl.addEventListener('input', () => this.filterAndSort());
        }

        this.filterEls.forEach(filterEl => {
            if (filterEl) {
                filterEl.addEventListener('change', () => this.filterAndSort());
            }
        });

        if (this.sortEl) {
            this.sortEl.addEventListener('change', () => this.filterAndSort());
        }

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.resetFilters());
        }
    }

    filterAndSort() {
        const searchTerm = this.searchEl?.value.toLowerCase() || '';
        const filterValues = this.filterEls.map(el => el?.value || '');
        const sortValue = this.sortEl?.value || '';

        let visibleItems = [];

        // Filter items
        this.items.forEach(item => {
            const searchData = this.getSearchData(item);
            const filterData = this.getFilterData(item);

            const matchesSearch = this.matchesSearchTerm(searchData, searchTerm);
            const matchesFilters = this.matchesFilters(filterData, filterValues);

            if (matchesSearch && matchesFilters) {
                item.style.display = 'block';
                visibleItems.push(item);
            } else {
                item.style.display = 'none';
            }
        });

        // Sort visible items
        if (sortValue && visibleItems.length > 0) {
            this.sortItems(visibleItems, sortValue);
        }

        // Update UI
        this.updateUI(visibleItems.length);
    }

    getSearchData(item) {
        const nama = item.dataset.nama || '';
        const kode = item.dataset.kode || '';
        return { nama, kode };
    }

    getFilterData(item) {
        return {
            tahunAjaran: item.dataset.tahunAjaran || '',
            jenis: item.dataset.jenis || '',
            sks: item.dataset.sks || ''
        };
    }

    matchesSearchTerm(data, searchTerm) {
        if (!searchTerm) return true;
        return data.nama.includes(searchTerm) || data.kode.includes(searchTerm);
    }

    matchesFilters(data, filterValues) {
        const [tahunAjaran, jenis, sks] = filterValues;

        const matchesTahunAjaran = !tahunAjaran || data.tahunAjaran === tahunAjaran;
        const matchesJenis = !jenis || data.jenis === jenis;
        const matchesSks = !sks || data.sks === sks;

        return matchesTahunAjaran && matchesJenis && matchesSks;
    }

    sortItems(items, sortValue) {
        items.sort((a, b) => {
            switch (sortValue) {
                case 'name-asc':
                    return a.dataset.nama.localeCompare(b.dataset.nama);
                case 'name-desc':
                    return b.dataset.nama.localeCompare(a.dataset.nama);
                case 'code-asc':
                    return a.dataset.kode.localeCompare(b.dataset.kode);
                case 'code-desc':
                    return b.dataset.kode.localeCompare(a.dataset.kode);
                case 'sks-asc':
                    return parseInt(a.dataset.sks) - parseInt(b.dataset.sks);
                case 'sks-desc':
                    return parseInt(b.dataset.sks) - parseInt(a.dataset.sks);
                case 'newest':
                    return new Date(b.dataset.created) - new Date(a.dataset.created);
                case 'oldest':
                    return new Date(a.dataset.created) - new Date(b.dataset.created);
                default:
                    return 0;
            }
        });

        // Reorder DOM elements
        items.forEach(item => {
            if (this.container) {
                this.container.appendChild(item);
            }
        });
    }

    updateUI(visibleCount) {
        // Update count
        if (this.totalCount) {
            this.totalCount.textContent = visibleCount;
        }

        // Show/hide no results message
        if (visibleCount === 0 && this.items.length > 0) {
            if (this.noResults) this.noResults.classList.remove('hidden');
            if (this.container) this.container.classList.add('hidden');
        } else {
            if (this.noResults) this.noResults.classList.add('hidden');
            if (this.container) this.container.classList.remove('hidden');
        }
    }

    resetFilters() {
        if (this.searchEl) this.searchEl.value = '';

        this.filterEls.forEach(filterEl => {
            if (filterEl) filterEl.value = '';
        });

        if (this.sortEl) this.sortEl.value = 'name-asc';

        this.filterAndSort();
    }

    updateVisibleItems() {
        // Re-query items to get current visibility state
        this.items = document.querySelectorAll(this.itemSelector);
        // Trigger filter to work with currently visible items
        this.filterAndSort();
    }
}

// View Toggle utility
class ViewToggle {
    constructor(options = {}) {
        this.cardViewBtn = options.cardViewBtn || '#view-cards';
        this.tableViewBtn = options.tableViewBtn || '#view-table';
        this.cardContainer = options.cardContainer || '#card-view';
        this.tableContainer = options.tableContainer || '#table-view';
        this.storageKey = options.storageKey || 'viewPreference';

        this.init();
    }

    init() {
        this.cardBtn = document.querySelector(this.cardViewBtn);
        this.tableBtn = document.querySelector(this.tableViewBtn);
        this.cardView = document.querySelector(this.cardContainer);
        this.tableView = document.querySelector(this.tableContainer);

        if (this.cardBtn && this.tableBtn) {
            this.bindEvents();
            this.loadSavedView();
        }
    }

    bindEvents() {
        this.cardBtn.addEventListener('click', () => this.setView('cards'));
        this.tableBtn.addEventListener('click', () => this.setView('table'));
    }

    setView(viewType) {
        if (viewType === 'table') {
            if (this.cardView) this.cardView.classList.add('hidden');
            if (this.tableView) this.tableView.classList.remove('hidden');
            this.updateButtonStyles(this.tableBtn, this.cardBtn);
        } else {
            if (this.cardView) this.cardView.classList.remove('hidden');
            if (this.tableView) this.tableView.classList.add('hidden');
            this.updateButtonStyles(this.cardBtn, this.tableBtn);
        }

        localStorage.setItem(this.storageKey, viewType);
    }

    updateButtonStyles(activeBtn, inactiveBtn) {
        // Active button
        activeBtn.classList.add('bg-blue-600', 'text-white');
        activeBtn.classList.remove('bg-gray-100', 'text-gray-600');

        // Inactive button
        inactiveBtn.classList.remove('bg-blue-600', 'text-white');
        inactiveBtn.classList.add('bg-gray-100', 'text-gray-600');
    }

    loadSavedView() {
        const savedView = localStorage.getItem(this.storageKey) || 'cards';
        this.setView(savedView);
    }
}

// Make classes available globally
window.SearchFilter = SearchFilter;
window.ViewToggle = ViewToggle;
