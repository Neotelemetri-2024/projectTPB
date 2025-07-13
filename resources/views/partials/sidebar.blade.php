<aside id="sidebar" class="fixed top-0 left-0 z-30 h-screen pt-14 transition-all duration-300 ease-in-out bg-white border-r border-gray-200 w-64" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
       <ul class="space-y-2 font-medium mt-8">
          <li>
             <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('dashboard', 'admin.dashboard', 'dosen.dashboard', 'mahasiswa.dashboard', 'pimpinan.dashboard') ? 'bg-amber-100 text-amber-700' : '' }}">
                <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('dashboard', 'admin.dashboard', 'dosen.dashboard', 'mahasiswa.dashboard', 'pimpinan.dashboard') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span class="ml-3">Dashboard</span>
             </a>
          </li>

          <!-- Admin Menu - Mahasiswa -->
          @if(auth()->user()->role === 'admin')
          <li>
             <a href="{{ route('admin.mahasiswa.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('admin.mahasiswa.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span class="ml-3">Mahasiswa</span>
             </a>
          </li>

          <!-- Admin Menu - Dosen -->
          <li>
             <a href="{{ route('admin.dosen.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.dosen.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('admin.dosen.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                </svg>
                <span class="ml-3">Dosen</span>
             </a>
          </li>

          <!-- Admin Menu - Data Master -->
          <li class="relative">
             <button type="button"
                     class="flex items-center w-full p-2 text-base rounded-lg group transition duration-200 text-gray-900 hover:bg-gray-100"
                     data-collapse-toggle="dropdown-master"
                     aria-expanded="false">
                <svg class="flex-shrink-0 w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                <span class="flex-1 ml-3 text-left whitespace-nowrap">Data Master</span>
                <svg class="w-3 h-3 transition-transform duration-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                   <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
             </button>
             <ul id="dropdown-master" class="py-2 space-y-1 {{ request()->routeIs('admin.tahun-ajaran.*', 'admin.mata-kuliah.*', 'admin.cpl.*', 'admin.komponen.*', 'admin.tahun-ajaran-matkul.*') ? '' : 'hidden' }}">
                <li>
                   <a href="{{ route('admin.tahun-ajaran.index') }}" class="flex items-center w-full p-2 rounded-lg pl-11 transition duration-75 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.tahun-ajaran.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                  
                      Tahun Ajaran
                   </a>
                </li>
                <li>
                   <a href="{{ route('admin.mata-kuliah.index') }}" class="flex items-center w-full p-2 rounded-lg pl-11 transition duration-75 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.mata-kuliah.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                
                      Mata Kuliah
                   </a>
                </li>
                <li>
                   <a href="{{ route('admin.cpl.index') }}" class="flex items-center w-full p-2 rounded-lg pl-11 transition duration-75 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.cpl.*') ? 'bg-amber-100 text-amber-700' : '' }}">
             
                      CPL
                   </a>
                </li>
                <li>
                   <a href="{{ route('admin.komponen.index') }}" class="flex items-center w-full p-2 rounded-lg pl-11 transition duration-75 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.komponen.*') ? 'bg-amber-100 text-amber-700' : '' }}">
        
                      Komponen
                   </a>
                </li>
            
             </ul>
          </li>

          <li>
            <a href="{{ route('admin.tahun-ajaran-matkul.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.tahun-ajaran-matkul.*') ? 'bg-amber-100 text-amber-700' : '' }}">
               <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('admin.dosen.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
               </svg>
               <span class="ml-3">Mata Kuliah Per TA</span>
            </a>
         </li>
          @endif
       </ul>
    </div>
 </aside>