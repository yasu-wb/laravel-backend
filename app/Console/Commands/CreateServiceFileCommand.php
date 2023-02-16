<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateServiceFileCommand extends Command
{
    /**
     * @const string service dir path
     */
    public const SERVICES_PATH = 'app/Services/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {serviceClassName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $serviceFileName;

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
        $this->className = $this->argument('serviceClassName');

        if (!$this->className) {
            $this->error('Service Class Name invalid');

            return;
        }

        $this->serviceFileName = self::SERVICES_PATH . $this->className . 'Service.php';
        if ($this->isExistFiles()) {
            $this->error('Service already exist');

            return;
        }

        $this->createServiceFile();
        $this->info('Service created successfully');
    }

    /**
     * Create Service File.
     */
    private function createServiceFile(): void
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Services;\n\nclass {$this->className}Service extends BaseService\n{\n}\n";
        file_put_contents($this->serviceFileName, $content);
    }

    /**
     * Check if the same files exists.
     * @return bool
     */
    private function isExistFiles(): bool
    {
        return file_exists($this->serviceFileName);
    }
}
