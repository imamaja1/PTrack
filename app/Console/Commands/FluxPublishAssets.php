<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FluxPublishAssets extends Command
{
    protected $signature = 'flux:publish-assets';

    protected $description = 'Copy Flux JS assets to public/flux/ for static serving via Nginx';

    public function handle(): void
    {
        $fluxDist = base_path('vendor/livewire/flux/dist');
        $destination = public_path('flux');

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        // Copy the correct flux JS files
        copy("{$fluxDist}/flux-lite.min.js", "{$destination}/flux.js");
        copy("{$fluxDist}/flux-lite.min.js", "{$destination}/flux.min.js");

        $this->info('✅ Flux assets published to public/flux/');
        $this->line('   - public/flux/flux.js');
        $this->line('   - public/flux/flux.min.js');
    }
}
