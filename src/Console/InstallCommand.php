<?php

namespace JohannDesarrollador\Notifications\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'johann-notifications:install {stack : The development stack that should be installed (inertia,livewire)}
    {--teams : Indicates if team support should be installed}
    {--api : Indicates if API support should be installed}
    {--verification : Indicates if email verification support should be installed}
    {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Instalar Modulo Johann - notificaciones';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {

    // Publish...
    $this->callSilent('vendor:publish', ['--tag' => 'notifications-config', '--force' => true]);
    $this->callSilent('vendor:publish', ['--tag' => 'notifications-migrations', '--force' => true]);

    // Install Stack...
    if ($this->argument('stack') === 'livewire')
    {
      $this->installLivewireStack();
    } 
    elseif ($this->argument('stack') === 'inertia')
    {
      $this->installInertiaStack();
    }


  }

  /**
   * Install the Livewire stack into the application.
   *
   * @return void
   */
  protected function installLivewireStack()
  {

    $this->line('');
    $this->info('Livewire scaffolding installed successfully.');
    $this->comment('Please execute "npm install && npm run dev" to build your assets.');

  }
  
  /**
   * Install the Inertia stack into the application.
   *
   * @return void
   */
  protected function installInertiaStack()
  {
    // Install Inertia...
    // $this->requireComposerPackages('inertiajs/inertia-laravel:^0.5.2', 'tightenco/ziggy:^1.0');

    // Directories...
    // (new Filesystem)->ensureDirectoryExists(app_path('Actions/Fortify'));

    // (new Filesystem)->ensureDirectoryExists(app_path('Actions/Fortify'));
    // (new Filesystem)->ensureDirectoryExists(app_path('Actions/Jetstream'));
    // (new Filesystem)->ensureDirectoryExists(resource_path('css'));
    // (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/Auth'));

    // Actions...
    copy(__DIR__.'/../../stubs/app/Utils/NotificacionUtil.php', app_path('Utils/NotificacionUtil.php'));
    // copy(__DIR__.'/../../stubs/app/Actions/Fortify/CreateNewUser.php', app_path('Actions/Fortify/CreateNewUser.php'));
    // copy(__DIR__.'/../../stubs/app/Actions/Fortify/UpdateUserProfileInformation.php', app_path('Actions/Fortify/UpdateUserProfileInformation.php'));
    // copy(__DIR__.'/../../stubs/app/Actions/Jetstream/DeleteUser.php', app_path('Actions/Jetstream/DeleteUser.php'));

    // Inertia Pages...
    // copy(__DIR__.'/../../stubs/inertia/resources/js/Pages/Dashboard.vue', resource_path('js/Pages/Dashboard.vue'));
    (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia/resources/js/Pages/Notifications', resource_path('js/Pages/Notifications'));


    $this->line('');
    $this->info('Modulo Johann - notificaciones instalado correctamente.');
    $this->comment('Please execute "npm install && npm run dev" to build your assets.');

  }
  /**
   * Install the Inertia team stack into the application.
   *
   * @return void
   */
  protected function installInertiaTeamStack()
  {
      // Directories...
      (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/Profile'));

      // Pages...
      (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia/resources/js/Pages/Teams', resource_path('js/Pages/Teams'));

      // Tests...
      $stubs = $this->getTestStubsPath();

      copy($stubs.'/inertia/CreateTeamTest.php', base_path('tests/Feature/CreateTeamTest.php'));
      copy($stubs.'/inertia/DeleteTeamTest.php', base_path('tests/Feature/DeleteTeamTest.php'));
      copy($stubs.'/inertia/InviteTeamMemberTest.php', base_path('tests/Feature/InviteTeamMemberTest.php'));
      copy($stubs.'/inertia/LeaveTeamTest.php', base_path('tests/Feature/LeaveTeamTest.php'));
      copy($stubs.'/inertia/RemoveTeamMemberTest.php', base_path('tests/Feature/RemoveTeamMemberTest.php'));
      copy($stubs.'/inertia/UpdateTeamMemberRoleTest.php', base_path('tests/Feature/UpdateTeamMemberRoleTest.php'));
      copy($stubs.'/inertia/UpdateTeamNameTest.php', base_path('tests/Feature/UpdateTeamNameTest.php'));

      $this->ensureApplicationIsTeamCompatible();
  }


  
  
  /**
   * Installs the given Composer Packages into the application.
   *
   * @param  mixed  $packages
   * @return void
   */
  protected function requireComposerPackages($packages)
  {
      $composer = $this->option('composer');

      if ($composer !== 'global') {
          $command = [$this->phpBinary(), $composer, 'require'];
      }

      $command = array_merge(
          $command ?? ['composer', 'require'],
          is_array($packages) ? $packages : func_get_args()
      );

      (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
          ->setTimeout(null)
          ->run(function ($type, $output) {
              $this->output->write($output);
          });
  }
  /**
   * Install the given Composer Packages as "dev" dependencies.
   *
   * @param  mixed  $packages
   * @return void
   */
  protected function requireComposerDevPackages($packages)
  {
      $composer = $this->option('composer');

      if ($composer !== 'global') {
          $command = [$this->phpBinary(), $composer, 'require', '--dev'];
      }

      $command = array_merge(
          $command ?? ['composer', 'require', '--dev'],
          is_array($packages) ? $packages : func_get_args()
      );

      (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
          ->setTimeout(null)
          ->run(function ($type, $output) {
              $this->output->write($output);
          });
  }
  /**
   * Update the "package.json" file.
   *
   * @param  callable  $callback
   * @param  bool  $dev
   * @return void
   */
  protected static function updateNodePackages(callable $callback, $dev = true)
  {
      if (! file_exists(base_path('package.json'))) {
          return;
      }

      $configurationKey = $dev ? 'devDependencies' : 'dependencies';

      $packages = json_decode(file_get_contents(base_path('package.json')), true);

      $packages[$configurationKey] = $callback(
          array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
          $configurationKey
      );

      ksort($packages[$configurationKey]);

      file_put_contents(
          base_path('package.json'),
          json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
      );
  }
  /**
   * Replace a given string within a given file.
   *
   * @param  string  $search
   * @param  string  $replace
   * @param  string  $path
   * @return void
   */
  protected function replaceInFile($search, $replace, $path)
  {
      file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
  }
  /**
   * Get the path to the appropriate PHP binary.
   *
   * @return string
   */
  protected function phpBinary()
  {
      return (new PhpExecutableFinder())->find(false) ?: 'php';
  }

}
