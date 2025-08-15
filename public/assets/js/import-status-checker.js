/**
 * Import Status Checker
 * Handles real-time checking of import status
 */
class ImportStatusChecker {
    constructor(matkulId, checkInterval = 5000) {
        this.matkulId = matkulId;
        this.checkInterval = checkInterval;
        this.isChecking = false;
        this.checkCount = 0;
        this.maxChecks = 60; // Max 5 minutes (60 * 5 seconds)
        
        this.init();
    }

    init() {
        // Check if import was started
        if (this.hasImportStarted()) {
            this.startChecking();
        }
    }

    hasImportStarted() {
        // Check session storage or DOM for import status
        return document.querySelector('[data-import-started="true"]') !== null ||
               sessionStorage.getItem('import_started') === 'true';
    }

    startChecking() {
        if (this.isChecking) return;
        
        this.isChecking = true;
        this.showProcessingMessage();
        this.checkStatus();
    }

    checkStatus() {
        if (this.checkCount >= this.maxChecks) {
            this.handleTimeout();
            return;
        }

        fetch(`/dosen/nilai/${this.matkulId}/import-status`)
            .then(response => response.json())
            .then(data => {
                this.checkCount++;
                
                if (data.status === 'completed') {
                    this.handleSuccess(data.results);
                } else if (data.status === 'error') {
                    this.handleError(data.message);
                } else {
                    // Still processing, check again
                    setTimeout(() => this.checkStatus(), this.checkInterval);
                }
            })
            .catch(error => {
                console.error('Error checking import status:', error);
                this.handleError('Terjadi kesalahan saat mengecek status import');
            });
    }

    showProcessingMessage() {
        // Show processing message
        const messageContainer = this.createMessageContainer();
        messageContainer.innerHTML = `
            <div class="flex items-center p-4 mb-4 text-blue-800 border border-blue-300 rounded-lg bg-blue-50" role="alert">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-800 mr-3"></div>
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="ml-3">
                    <span class="font-medium">Import sedang diproses...</span>
                    <p class="text-sm">File sedang diproses di background. Halaman ini akan otomatis update ketika selesai.</p>
                </div>
            </div>
        `;
        
        this.insertMessage(messageContainer);
    }

    handleSuccess(results) {
        this.isChecking = false;
        
        const messageContainer = this.createMessageContainer();
        let message = `
            <div class="flex items-center p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <div class="ml-3">
                    <span class="font-medium">Import berhasil diselesaikan!</span>
                    <div class="mt-2 text-sm">
                        <p>✅ Berhasil memproses ${results.success || 0} mahasiswa</p>
        `;
        
        if (results.created_students > 0) {
            message += `<p>✅ Dibuat ${results.created_students} akun mahasiswa baru</p>`;
        }
        
        if (results.updated_grades > 0) {
            message += `<p>✅ Diperbarui ${results.updated_grades} nilai</p>`;
        }
        
        if (results.errors && results.errors.length > 0) {
            message += `<p>⚠️ Terdapat ${results.errors.length} error</p>`;
        }
        
        message += `
                    </div>
                </div>
            </div>
        `;
        
        messageContainer.innerHTML = message;
        this.insertMessage(messageContainer);
        
        // Show detailed results if available
        if (results.created_student_list && results.created_student_list.length > 0) {
            this.showCreatedStudents(results.created_student_list);
        }
        
        if (results.errors && results.errors.length > 0) {
            this.showErrors(results.errors);
        }
        
        // Clear session storage
        sessionStorage.removeItem('import_started');
        
        // Refresh page after 3 seconds to show updated data
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    handleError(errorMessage) {
        this.isChecking = false;
        
        const messageContainer = this.createMessageContainer();
        messageContainer.innerHTML = `
            <div class="flex items-center p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <div class="ml-3">
                    <span class="font-medium">Import gagal!</span>
                    <p class="text-sm">${errorMessage}</p>
                </div>
            </div>
        `;
        
        this.insertMessage(messageContainer);
        
        // Clear session storage
        sessionStorage.removeItem('import_started');
    }

    handleTimeout() {
        this.isChecking = false;
        
        const messageContainer = this.createMessageContainer();
        messageContainer.innerHTML = `
            <div class="flex items-center p-4 mb-4 text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <div class="ml-3">
                    <span class="font-medium">Import masih diproses...</span>
                    <p class="text-sm">Import memakan waktu lebih lama dari yang diharapkan. Silakan refresh halaman untuk melihat status terbaru.</p>
                </div>
            </div>
        `;
        
        this.insertMessage(messageContainer);
    }

    showCreatedStudents(students) {
        const container = this.createMessageContainer();
        let html = `
            <div class="p-4 mb-4 text-blue-800 border border-blue-300 rounded-lg bg-blue-50">
                <h4 class="font-medium mb-2">Mahasiswa Baru yang Dibuat:</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">NIM</th>
                                <th class="text-left p-2">Nama</th>
                                <th class="text-left p-2">Email</th>
                                <th class="text-left p-2">Password</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        students.forEach(student => {
            html += `
                <tr class="border-b">
                    <td class="p-2">${student.nim}</td>
                    <td class="p-2">${student.nama}</td>
                    <td class="p-2">${student.email}</td>
                    <td class="p-2 font-mono">${student.password}</td>
                </tr>
            `;
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        this.insertMessage(container);
    }

    showErrors(errors) {
        const container = this.createMessageContainer();
        let html = `
            <div class="p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50">
                <h4 class="font-medium mb-2">Error yang Ditemukan:</h4>
                <ul class="list-disc list-inside text-sm space-y-1">
        `;
        
        errors.forEach(error => {
            html += `<li>${error}</li>`;
        });
        
        html += `
                </ul>
            </div>
        `;
        
        container.innerHTML = html;
        this.insertMessage(container);
    }

    createMessageContainer() {
        const container = document.createElement('div');
        container.className = 'import-status-message';
        return container;
    }

    insertMessage(container) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.import-status-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Insert new message at the top of the content area
        const contentArea = document.querySelector('.content-area') || 
                           document.querySelector('main') || 
                           document.querySelector('.container') ||
                           document.body;
        
        contentArea.insertBefore(container, contentArea.firstChild);
    }
}

// Auto-initialize if on nilai show page
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the nilai show page
    const importForm = document.querySelector('form[action*="/import"]');
    if (importForm) {
        // Extract matkul ID from the form action
        const action = importForm.getAttribute('action');
        const match = action.match(/\/nilai\/(\d+)\/import/);
        
        if (match) {
            const matkulId = match[1];
            new ImportStatusChecker(matkulId);
        }
    }
    
    // Set import started flag when form is submitted
    const importForms = document.querySelectorAll('form[action*="/import"]');
    importForms.forEach(form => {
        form.addEventListener('submit', function() {
            sessionStorage.setItem('import_started', 'true');
            this.setAttribute('data-import-started', 'true');
        });
    });
});
