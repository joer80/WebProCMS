<?php

use App\Models\Setting;

it('loads navigation settings from the database', function (): void {
    expect(Setting::get('navigation.menus', null))->toBeArray();
    expect(Setting::get('navigation.show_auth_links', null))->not->toBeNull();
    expect(Setting::get('navigation.footer_slugs', null))->toBeArray();
    expect(Setting::get('navigation.show_account_in_footer', null))->not->toBeNull();
});
