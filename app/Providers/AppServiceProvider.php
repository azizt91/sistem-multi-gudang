<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;
use Masbug\Flysystem\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // --- REGISTRASI DRIVER GOOGLE DRIVE (PERBAIKAN) ---
        try {
            Storage::extend('google', function($app, $config) {
                $client = new Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new Drive($client);
                $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/');

                $driver = new Filesystem($adapter);

                // PERBAIKAN DISINI:
                // Kita bungkus driver raw (Flysystem) ke dalam Adapter resmi Laravel
                return new FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            // Hindari crash jika konfigurasi belum lengkap
        }
        // --- SELESAI ---

        // Share company profile with all views
        if (!app()->runningInConsole()) {
            $companyProfile = \App\Models\CompanyProfile::first();
            view()->share('companyProfile', $companyProfile);
        }
    }
}
