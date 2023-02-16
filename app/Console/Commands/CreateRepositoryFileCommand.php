<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateRepositoryFileCommand extends Command
{
    /**
     * @const string repository dir path
     */
    public const REPOSITORIES_PATH = 'app/Repositories/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {repositoryName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

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
    private $repositoryFileName;

    /**
     * @var string
     */
    private $interfaceFileName;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->className = $this->argument('repositoryName');

        if (!$this->className) {
            $this->error('Repository Name invalid');
        }

        $this->dirName = $this->className;

        if (!$this->isExistDirectory()) {
            $this->createDirectory();
        }

        $this->repositoryFileName = self::REPOSITORIES_PATH . $this->dirName . '/' . $this->className . 'Repository.php';
        $this->interfaceFileName = self::REPOSITORIES_PATH . $this->dirName . '/' . $this->className . 'RepositoryInterface.php';
        if ($this->isExistFiles()) {
            $this->error('Repository already exist');

            return;
        }

        $this->createRepositoryFile();
        $this->createInterFaceFile();
        $this->info('Repository created successfully');
    }

    /**
     * Create Repository File.
     */
    private function createRepositoryFile(): void
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Repositories\\{$this->dirName};\n\nuse App\\Models\\{$this->className};\n\nclass {$this->className}" . "Repository implements {$this->className}" . "RepositoryInterface\n{\n    private \$model;\n\n    public function __construct({$this->className} \$model)\n    {\n        \$this->model = \$model;\n    }\n}\n";
        file_put_contents($this->repositoryFileName, $content);
    }

    /**
     * Create Repository Interface File.
     */
    private function createInterFaceFile(): void
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Repositories\\{$this->dirName};\n\ninterface {$this->className}" . "RepositoryInterface\n{\n}\n";
        file_put_contents($this->interfaceFileName, $content);
    }

    /**
     * Confirm the same Files.
     * @return bool
     */
    private function isExistFiles(): bool
    {
        return file_exists($this->repositoryFileName) && file_exists($this->interfaceFileName);
    }

    /**
     * Check if the same directory exists.
     * @return bool
     */
    private function isExistDirectory(): bool
    {
        return file_exists(self::REPOSITORIES_PATH . $this->dirName);
    }

    /**
     * Create directory.
     */
    private function createDirectory(): void
    {
        mkdir(self::REPOSITORIES_PATH . $this->dirName, 0755, true);
    }
}
