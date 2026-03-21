<?php

namespace App\Http\Controllers;

use App\Services\InstallerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class InstallerController extends Controller
{
    public function welcome(): View|RedirectResponse
    {
        return view('install.welcome');
    }

    public function requirements(): View|RedirectResponse
    {
        $requirements = InstallerService::getRequirements();
        $satisfied = InstallerService::requirementsSatisfied();

        return view('install.requirements', [
            'requirements' => $requirements,
            'satisfied' => $satisfied,
        ]);
    }

    public function database(): View|RedirectResponse
    {
        if (! InstallerService::requirementsSatisfied()) {
            return redirect()->route('install.requirements');
        }

        return view('install.database');
    }

    public function storeDatabase(Request $request): RedirectResponse
    {
        $driver = $request->input('db_connection', 'sqlite');

        $rules = [
            'db_connection' => 'required|in:sqlite,mysql,mariadb,pgsql',
        ];

        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'])) {
            $rules['db_host'] = 'required|string|max:255';
            $rules['db_port'] = 'nullable|string|max:10';
            $rules['db_database'] = 'required|string|max:255';
            $rules['db_username'] = 'required|string|max:255';
            $rules['db_password'] = 'nullable|string';
        } else {
            $rules['db_database'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $config = [
            'driver' => $validated['db_connection'],
            'database' => $validated['db_database'] ?? database_path('database.sqlite'),
        ];

        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'])) {
            $config['host'] = $validated['db_host'];
            $config['port'] = $validated['db_port'] ?? ($driver === 'pgsql' ? '5432' : '3306');
            $config['username'] = $validated['db_username'];
            $config['password'] = $validated['db_password'] ?? '';
        }

        $result = InstallerService::testDatabaseConnection($driver, $config);

        if (! $result['success']) {
            $message = $result['message'] ?? 'Could not connect to the database. Please check your credentials.';

            return back()->withInput()->withErrors([
                'db_connection' => $message,
            ]);
        }

        InstallerService::putInstallData('database', $config);

        return redirect()->route('install.application');
    }

    public function application(): View|RedirectResponse
    {
        $dbData = InstallerService::getInstallData('database');

        if (! $dbData) {
            return redirect()->route('install.database');
        }

        return view('install.application');
    }

    public function storeApplication(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        $db = InstallerService::getInstallData('database');
        if (! $db) {
            return redirect()->route('install.database');
        }
        $data = [
            'database' => $db,
            'app' => [
                'name' => $validated['app_name'],
                'url' => rtrim($validated['app_url'], '/'),
            ],
            'admin' => [
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $validated['admin_password'],
            ],
        ];

        InstallerService::putInstallData('application', $data);

        return redirect()->route('install.run');
    }

    public function run(): View|RedirectResponse
    {
        if (! InstallerService::getInstallData('application')) {
            return redirect()->route('install.application');
        }

        return view('install.run');
    }

    public function executeRun(): RedirectResponse
    {
        $data = InstallerService::getInstallData('application');
        if (! $data) {
            return redirect()->route('install.application');
        }

        $errors = InstallerService::install($data);

        if (! empty($errors)) {
            return redirect()->route('install.run')->withErrors(['install' => implode(' ', $errors)]);
        }

        InstallerService::forgetInstallData();

        return redirect()->route('install.complete');
    }

    public function complete(): View|RedirectResponse
    {
        if (! InstallerService::isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return view('install.complete');
    }
}
