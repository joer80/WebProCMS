<flux:dropdown position="bottom" align="start">
    <flux:sidebar.profile
        :name="auth()->user()->name"
        :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                @if (auth()->user()->isPreviewingRole())
                    <span class="truncate text-xs font-medium text-amber-600 dark:text-amber-400">Viewing as {{ ucfirst(session('preview_role')) }}</span>
                @else
                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                @endif
            </div>
        </div>
        <flux:menu.separator />
        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>
            @if (auth()->user()->role === \App\Enums\Role::Super)
                <ui-submenu data-flux-menu-submenu>
                <flux:menu.item icon="eye" icon:trailing="chevron-right">
                    {{ __('Preview as') }}
                </flux:menu.item>
                <flux:menu>
                    @if (auth()->user()->isPreviewingRole())
                        <form method="POST" action="{{ route('dashboard.preview-as.destroy') }}" class="w-full">
                            @csrf
                            @method('DELETE')
                            <flux:menu.item as="button" type="submit" icon="arrow-uturn-left" class="w-full cursor-pointer">
                                {{ __('Exit Preview') }}
                            </flux:menu.item>
                        </form>
                        <flux:menu.separator />
                    @endif
                    @foreach (collect(\App\Enums\Role::cases())->reverse() as $role)
                        @if ($role !== \App\Enums\Role::Super)
                            @php $isActive = session('preview_role') === strtolower($role->name); @endphp
                            <form method="POST" action="{{ route('dashboard.preview-as.store') }}" class="w-full">
                                @csrf
                                <input type="hidden" name="role" value="{{ strtolower($role->name) }}">
                                @if ($isActive)
                                    <flux:menu.item as="button" type="submit" icon="check" class="w-full cursor-pointer">
                                        {{ $role->name }}
                                    </flux:menu.item>
                                @else
                                    <flux:menu.item as="button" type="submit" class="w-full cursor-pointer">
                                        {{ $role->name }}
                                    </flux:menu.item>
                                @endif
                            </form>
                        @endif
                    @endforeach
                </flux:menu>
            </ui-submenu>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
