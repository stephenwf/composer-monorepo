<?php
require('./vendor/autoload.php');

use bultonFr\DependencyTree\DependencyTree;
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$files = array_slice(scandir(realpath(__DIR__ . '/packages')), 2);
$tree = new DependencyTree;
$nameMap = [];

foreach ($files as $key => $file) {
    $composer = json_decode(file_get_contents("./packages/$file/composer.json"), true);
    $nameMap[$composer['name']] = $file;
    try {
        $tree->addDependency($composer['name'], $key, array_keys($composer['require'] ?? []));
    } catch (Throwable $e) {}
}

$it = new RecursiveIteratorIterator(new RecursiveArrayIterator($tree->generateTree()));

$origDir = __DIR__;
ini_set('memory_limit', -1);

foreach ($it as $dependency) {
    chdir(__DIR__ . '/packages/' . $nameMap[$dependency]);

    $input = new ArrayInput(['command' => 'install']);
    $application = new Application();
    $application->setAutoExit(false); // prevent `$application->run` method from exitting the script
    try {
        $application->run($input);
    } catch (Throwable $e) {}

    chdir($origDir);
}
