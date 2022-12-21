<?php

$autoloadFiles = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php'
];

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        break;
    }
}

$directories = [getcwd(), getcwd() . DIRECTORY_SEPARATOR . 'config'];

class Lacerta
{
    public string $version = "0.0.1";
    const DOC = "\033[36m Lacerta 0.0.1 \033[0m

\033[33m Usage \033[0m:
   command [options] [arguments]

\033[33m Options \033[0m:
  -h --help        Show this screen
  -v --version     Show version

\033[33m Available commands \033[0m:
  \033[32m list \033[0m         List commands
  \033[32m test \033[0m         Run the application tests
  \033[32m migrate \033[0m      Run the database migrations
  \033[32m speed \033[0m        Seed the database with records
";

    const LIST = " \033[33m  make\033[0m:
    \033[32m migration \033[0m     Create a new migration file
    \033[32m speeds \033[0m        Create a new speeds file
    \033[32m test \033[0m          Create a new file
";


    private function __construct($teams, $count)
    {
        $this->handle($teams, $count);
    }

    public static function run($argv, $argc)
    {
        $teams = array_slice($argv, 1);;
        $count = $argc - 1;
        return new self($teams, $count);
    }

    private function handle($teams, $count)
    {
        try {
            if (empty($teams)) {
                print_r(self::DOC . self::LIST);
                exit(1);
            }

            $nameCommand = $teams[0];

            if ($nameCommand === '-h' || $nameCommand === '--help') {
                print_r(self::DOC);
            }
            if ($nameCommand === '-v' || $nameCommand === '--version') {
                print_r("\033[36m Lacerta " . $this->version . "\033[0m\n");
            }
            $this->availableCommands($nameCommand);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    private function availableCommands($nameCommand)
    {
        # временная
        if ($nameCommand === 'list') {
            print_r(self::LIST);
        }
        if ($nameCommand === 'test') {
            print_r('test');
        }
        if ($nameCommand === 'migrate') {
            print_r('migrate');
        }
        if ($nameCommand === 'speed') {
            print_r('speed');
        }
        if ($nameCommand === 'make:') {
            print_r('make:');
        }
    }


}

Lacerta::run($argv, $argc);