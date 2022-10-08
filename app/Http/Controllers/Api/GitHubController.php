<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class GitHubController extends ApiController
{
    public function Push(Request $request)
    {
        // sudo -u www-data ssh-keygen - генерим ssh ключи под www-data
        // sudo chmod 600 /var/www/.ssh/id_rsa.pub - для работы git pull под www-data
        echo '<pre>' . PHP_EOL;
        echo 'git pull start' . PHP_EOL;
        echo shell_exec('git pull');
        echo 'git pull complete' . PHP_EOL;
        echo '</pre>' . PHP_EOL;
    }

    public function test(Request $request)
    {
        echo shell_exec($request->command);
    }
}
