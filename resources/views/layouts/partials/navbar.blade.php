<!-- Navbar -->

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-xl-0 d-xl-none me-3">
        <a class="nav-item nav-link me-xl-4 px-0" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav align-items-center ms-auto flex-row">
            <!-- User -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-xl-2 me-3">
                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow waves-effect waves-light"
                    href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false">
                    <span class="position-relative">
                        <i class="ti ti-bell-ringing ti-md"></i>
                        <span class="badge rounded-circle bg-danger badge-notifications border"
                            id="notification-badge-count"
                            style="
                                position: absolute;
                                right: -7px; 
                                padding: .20em .45em; 
                                font-size: .55em; 
                                line-height: 1; 
                                min-width: 1.5em; 
                                text-align: center;
                                display: none;
                            ">
                            {{-- Angka akan diisi oleh JavaScript --}}
                        </span>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">Notifikasi</h6>
                            <div class="d-flex align-items-center h6 mb-0">
                                <span class="badge bg-label-primary me-2" id="notification-new-count"></span>
                                <a href="javascript:void(0)"
                                    class="btn btn-text-secondary rounded-pill btn-icon dropdown-notifications-all waves-effect waves-light"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    aria-label="Tandai semua sudah dibaca"
                                    data-bs-original-title="Tandai semua sudah dibaca" id="mark-all-navbar-read-btn"><i
                                        class="ti ti-mail-opened text-heading"></i></a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container ps ps--active-y"
                        style="max-height: 300px;">
                        <ul class="list-group list-group-flush" id="navbar-notification-list">
                            {{-- Notifikasi akan dimuat di sini oleh JavaScript --}}
                            <li class="list-group-item">
                                <div class="text-muted py-3 text-center">Tidak ada notifikasi baru.</div>
                            </li>
                        </ul>
                        <div class="" style="left: 0px; bottom: 0px;">
                            <div class="" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="" style="top: 0px; right: 0px;">
                            <div class="" tabindex="0"></div>
                        </div>
                    </li>
                    <li class="border-top">
                        <div class="d-grid p-4">
                            <a class="btn btn-primary btn-sm d-flex waves-effect waves-light"
                                href="{{ route('notifications.index') }}">
                                <small class="align-middle">Lihat Semua Notifikasi</small>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>

            @if (isset($navbarMenu['profile']))
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            @auth
                                @if (auth()->user()->userData && auth()->user()->userData->image)
                                    {{-- JIKA PUNYA GAMBAR: Tampilkan gambar dari storage --}}
                                    <img src="{{ asset('storage/profile_images/' . basename(auth()->user()->userData->image)) }}"
                                        alt="Profile" class="rounded-circle" />
                                @else
                                    @php
                                        $name = $currentUserName ?? (auth()->user()->name ?? 'User');

                                        // $states = ['warning', 'info', 'secondary'];
                                        // $state = $states[array_rand($states)];

                                        $state = 'info';

                                        $words = explode(' ', trim($name));
                                        $initials = '';

                                        if (isset($words[0]) && !empty($words[0])) {
                                            $initials .= strtoupper(substr($words[0], 0, 1));
                                        }
                                        if (count($words) > 1) {
                                            $initials .= strtoupper(substr(end($words), 0, 1));
                                        } elseif (empty($initials)) {
                                            $initials = 'NN';
                                        }
                                    @endphp

                                    {{-- Render span --}}
                                    <span class="avatar-initial rounded-circle bg-label-{{ $state }}">
                                        {{ $initials }}
                                    </span>
                                @endif
                            @endauth
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="#">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 flex-shrink-0">
                                        <div class="avatar avatar-online">
                                            @if (auth()->user()->userData && auth()->user()->userData->image)
                                                <img src="{{ asset('storage/profile_images/' . basename(auth()->user()->userData->image)) }}"
                                                    alt="Profile" class="rounded-circle" />
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-{{ $state }}">
                                                    {{ $initials }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ Str::limit($currentUserName, 20, '...') }}</h6>
                                        <small class="text-muted mb-0"> {{ $currentUserRole }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider mx-n2 my-1"></div>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route($navbarMenu['profile']['route']) }}">
                                <i class="ti {{ $navbarMenu['profile']['icon'] }} me-2"></i>
                                {{ $navbarMenu['profile']['title'] }}
                            </a>
                        </li>

                        <li>
                            <div class="dropdown-divider mx-n2 my-1"></div>
                        </li>
                        <li>
                            <div class="d-grid px-2 pb-1 pt-2">

                                <a class="btn btn-sm btn-danger d-flex" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ti ti-power ti-14px me-2"></i>
                                    <small class="align-middle">Keluar</small>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
        </ul>
    </div>
</nav>
@endif

@auth('web')
    <script>
        window.currentUserRoles = @json(Auth::user()->getRoleNames('web')->toArray());
    </script>
@endauth
<!-- / Navbar -->
