<?php

namespace Celysium\Launcher\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand as BaseSeedCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Finder\Finder;

class GenerateSecretCommand extends BaseSeedCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'launcher:secret';

    public function handle()
    {
        do {
            $secert = $this->secret('Enter the password to access API');
        } while (trim($secert) == '');
        Cache::store('file')->put('launcher_secret', Hash::make($secert));

        $this->info('Password registered successfully.');
    }
}
