<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Header sidebar -->
    <div class="app-brand demo">
        <a href="{{ auth()->check()
            ? (auth()->user()->hasRole('admin')
                ? route('admin.dashboard')
                : (auth()->user()->hasRole('teknisi')
                    ? route('teknisi.dashboard')
                    : route('user.dashboard')))
            : route('login') }}"
            class="app-brand-link">
            <span class="app-brand-logo demo menu-text fw-bold" style="min-width: 138px;">
                Water Monitoring
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($menuItems as $slug => $item)
            @continue(!in_array(auth()->user()->roles->first()->name, $item['roles']))

            @if ($item['type'] === 'submenu')
                <!-- Menu dengan submenu -->
                <li class="menu-item {{ $item['is_active'] ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons ti {{ $item['icon'] }}"></i>
                        <div data-i18n="{{ $item['title'] }}">{{ $item['title'] }}</div>
                    </a>
                    <ul class="menu-sub">
                        @foreach ($item['submenu'] as $subItem)
                            <li class="menu-item {{ $subItem['is_active'] ? 'active' : '' }}">
                                <a href="{{ route($subItem['route']) }}" class="menu-link">
                                    <div data-i18n="{{ $subItem['title'] }}">{{ $subItem['title'] }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                <!-- Menu tunggal -->
                <li class="menu-item {{ $item['is_active'] ? 'active' : '' }}">
                    <a href="{{ route($item['route']) }}" class="menu-link">
                        <i class="menu-icon tf-icons ti {{ $item['icon'] }}"></i>
                        <div data-i18n="{{ $item['title'] }}">{{ $item['title'] }}</div>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</aside>
<!-- / Menu -->
