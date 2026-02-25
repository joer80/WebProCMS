# Project-Specific Notes

## Vite HMR and Runtime-Written Files

`refresh: true` in the Laravel Vite plugin watches `config/**` and `routes/**` by default.
Any PHP code that writes to files in those directories at runtime (not just during development)
will trigger a full Vite page reload, wiping all Alpine state — toasts, modals, open dropdowns, etc.

**This project writes these files at runtime:**
- `config/navigation.php` — written by the menus page when saving nav/footer links
- `routes/web.php` — written by `VoltFileService` when pages are cloned or created
- `resources/views/pages/**` — Volt page files written by `VoltFileService` when pages are cloned or created

All are excluded from Vite's file watcher in `vite.config.js` under `server.watch.ignored`.

**Symptoms to watch for:** Check `vite.config.js` first before debugging Alpine or Livewire.
- A toast or modal disappears ~1 second after a Livewire action
- The page visibly refreshes or flickers after a Livewire action
