<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateEnumCommand extends Command
{
    /**
     * @const string Use Case dir path
     */
    public const ENUM_PATH = 'app/Enums/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {enumName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Enum class';

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $dirName;

    /**
     * @var string
     */
    private $enumFileName;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (preg_match('/\//', $this->argument('enumName'))) {
            preg_match('/(.*)(?=\/)/', $this->argument('enumName'), $matches);
            $this->dirName = $matches[0];
            preg_match('/[^\/]+$/', $this->argument('enumName'), $matches);
            $this->className = $matches[0];
        } else {
            $this->className = $this->argument('enumName');
        }

        if (!$this->className) {
            $this->error('Enum Class Name invalid');

            return;
        }

        if (!file_exists(self::ENUM_PATH)) {
            mkdir(self::ENUM_PATH, 0755, true);
        }

        $this->enumFileName = self::ENUM_PATH . ($this->dirName ? $this->dirName . '/' : null) . $this->className . '.php';

        if ($this->isExistFiles()) {
            $this->error('Enum already exist');

            return;
        }

        if ($this->dirName && !$this->isExistDirectory()) {
            $this->createDirectory();
        }

        $this->createEnumFile();
        $this->info('Enum created successfully');
    }

    /**
     * Create Use Case File.
     */
    private function createEnumFile(): void
    {
        $nameSpace = '';
        if ($this->dirName) {
            foreach (explode('/', $this->dirName) as $dir) {
                $nameSpace .= '\\' . $dir;
            }
        }
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums{$nameSpace};\n\n/**\n * {$this->className} enum.\n */\nenum {$this->className}: int\n{\n    case SAMPLE = 0;\n\n    /**\n     * @return string\n     */\n    public function label(): string\n    {\n        return match (\$this) {\n            self::SAMPLE => 'sample',\n        };\n    }\n}\n";

        file_put_contents($this->enumFileName, $content);
    }

    /**
     * Check if the same files exists.
     * @return bool
     */
    private function isExistFiles(): bool
    {
        return file_exists($this->enumFileName);
    }

    /**
     * Create directory.
     */
    private function createDirectory(): void
    {
        mkdir(self::ENUM_PATH . $this->dirName, 0755, true);
    }

    /**
     * Check if the same directory exists.
     * @return bool
     */
    private function isExistDirectory(): bool
    {
        return file_exists(self::ENUM_PATH . $this->dirName);
    }
}
