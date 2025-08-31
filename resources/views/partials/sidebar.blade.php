<aside id="sidebar" class="fixed top-0 left-0 z-30 h-screen pt-14 transition-all duration-300 ease-in-out bg-white border-r border-gray-200 w-64" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white flex flex-col">
       <ul class="space-y-2 font-medium mt-8 flex-1">
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
          @if (auth()->user()->role === 'dosen')
             <li>
                <a href="{{ route('dosen.cpmk.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('dosen.cpmk.*', 'dosen.bobot-komponen.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                   <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('dosen.cpmk.*', 'dosen.bobot-komponen.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c0 .621-.504 1.125-1.125 1.125H18a2.25 2.25 0 01-2.25-2.25M6.75 17.25h-.75m-.75 0h-.75m-.75 0h-.75" />
                   </svg>
                   <span class="ml-3">Kelola CPMK</span>
                </a>
             </li>
             <li>
                <a href="{{ route('dosen.nilai.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('dosen.nilai.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                   <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('dosen.nilai.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                   </svg>
                   <span class="ml-3">Kelola Nilai Mahasiswa</span>
                </a>
             </li>
             <li>
                <a href="{{ route('dosen.cpmk-laporan.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('dosen.cpmk-laporan.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                   <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('dosen.cpmk-laporan.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                   </svg>
                   <span class="ml-3">Laporan CPMK</span>
                </a>
             </li>
          @endif
          @if(auth()->user()->role === 'pimpinan')
             <li>
                <a href="{{ route('pimpinan.cpmk-report.index') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('pimpinan.cpmk-report.*') ? 'bg-amber-100 text-amber-700' : '' }}">
                   <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('pimpinan.cpmk-report.*') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                   </svg>
                   <span class="ml-3">Laporan CPMK</span>
                </a>
             </li>
          @endif
          @if(auth()->user()->role === 'mahasiswa')
          <li>
             <a href="{{ route('mahasiswa.transkrip') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('mahasiswa.transkrip') ? 'bg-amber-100 text-amber-700' : '' }}">
                <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('mahasiswa.transkrip') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="ml-3">Transkrip</span>
             </a>
          </li>
          <li>
             <a href="{{ route('mahasiswa.capaian') }}" class="flex items-center p-2 rounded-lg group transition-colors duration-200 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('mahasiswa.capaian') ? 'bg-amber-100 text-amber-700' : '' }}">
                <svg class="w-5 h-5 transition duration-75 text-gray-500 group-hover:text-gray-900 {{ request()->routeIs('mahasiswa.capaian') ? 'text-amber-700' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                   <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                <span class="ml-3">Capaian CPL & CPMK</span>
             </a>
          </li>
          @endif
       </ul>

       <!-- Logout Button -->
       <div class="pt-4 border-t border-gray-200">
          <form method="POST" action="{{ route('logout') }}">
             @csrf
             <button type="submit" class="flex items-center w-full p-2 rounded-lg group transition-colors duration-200 text-red-500 hover:bg-red-50 hover:text-red-600">
                <svg class="w-5 h-5 transition duration-75 text-red-500 group-hover:text-red-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span class="ml-3">Logout</span>
             </button>
          </form>
       </div>
    </div>
 </aside>
