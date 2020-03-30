<?php

namespace App\Console\Commands;

use App\Console\Commands\BaseCommand;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use GuzzleHttp\Client as Guzzle;
use Symfony\Component\Console\Input\InputArgument;

class RandomSeedIndexCommand extends BaseCommand
{
    protected $signature = 'random:seed {domain} {rows}';
    protected $description = 'Seed random data to an index into elastic';

    private $allowedTypes = [
        'integer',
        'name',
        'list',
    ];

    /**
     * create a new command instance
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * get the console command arguments
     * @return array
     */
    protected function getArguments()
    {
        $arguments = [
            [
                [
                    'name', InputArgument::REQUIRED, 'Domain name.',
                ],
                [
                    'rows', InputArgument::REQUIRED, 'Number of rows.',
                ]
            ]
        ];
        return $arguments;
    }

    /**
     * execute the console command
     * @return mixed
     */
    public function handle()
    {
        $domainOriginal = strtolower($this->argument('domain'));
        $rows = (int) $this->argument('rows');

        $validDomain = preg_match('/^[a-z_]+$/', $domainOriginal);
        if (!$validDomain) {
            $this->error('Domain name must have only lowercase letters and underscore!');
            die;
        }

        if (!is_int($rows) || $rows <= 0 || $rows > 1000000) {
            $this->error('Rows must be an integer between 1 and 1.000.000');
            die;
        }

        $domain = $this->prepareDomainName($this->argument('domain'));
        $domainCaps = ucfirst($domain);

        $class =  '\App\Seeds\\' . $domainCaps . 'Seed';
        $seedClass = $this->newSeed($class);
        $seedClass = $seedClass->getSeed();

        $indexName = $seedClass['index'];
        $indexFields = $seedClass['fields'];

        for ($i = 0; $i < $rows; $i++) {
            echo '.';
            $saveData = [];
            foreach ($indexFields as $field => $params) {
                $saveData[$field] = $this->getRandomValue($params);
            }
            $guzzle = $this->newGuzzle();

            $url = 'localhost:8101/' . $indexName . '/add';
            $request = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $saveData,
            ];

            $guzzle->POST(
                $url,
                $request
            );
        }

        $this->info('');
        $this->info('All done!');
    }

    /**
     * get random value based on parameters
     * @param array $params
     * @return mixed
     */
    public function getRandomValue(
        array $params
    ) {
        if (!isset($params['type'])) {
            $this->error('Invalid structure on seed');
            die;
        }
        if (!in_array($params['type'], $this->allowedTypes)) {
            $this->error('Forbidden type');
            die;
        }

        $callMethod = 'generate' . ucwords($params['type']);
        return $this->$callMethod(
            $params
        );
    }

    /**
     * generate random string
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function generateName()
    {
        $faker = $this->newFaker();
        return $faker->firstNameMale . ' ' . $faker->lastName;
    }

    /**
     * generate random integer
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function generateInteger(
        array $params
    ) : int {
        if (!isset($params['min']) || !isset($params['max'])){
            $this->error('Missing int parameters min and/or max');
            die;
        }
        if (!is_int($params['min']) || !is_int($params['max'])){
            $this->error('Int parameters min and max must be integer');
            die;
        }
        return rand($params['min'], $params['max']);
    }

    /**
     * generate random item from list
     * @param array $params
     * @return string
     */
    public function generateList(
        array $params
    ) : string {
        if (!isset($params['values'])){
            $this->error('Missing list parameter values');
            die;
        }
        if (!is_array($params['values'])){
            $this->error('List parameter values must be an array');
            die;
        }
        $last = count($params['values']) - 1;
        $index = rand(0, $last);
        return $params['values'][$index];
    }

    /**
     * @codeCoverageIgnore
     * create new seed instance
     * @return mixed
     */
    public function newSeed(
        string $name
    ) {
        return new $name();
    }

    /**
     * @codeCoverageIgnore
     * create new Faker instance
     * @return Faker
     */
    public function newFaker(): Faker
    {
        return FakerFactory::create();
    }

    /**
     * @codeCoverageIgnore
     * method newGuzzle
     * create and return new GuzzleHttp\Client object
     * @return GuzzleHttp\Client
     */
    public function newGuzzle()
    {
        return new Guzzle();
    }
}
