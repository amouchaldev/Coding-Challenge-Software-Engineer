<?php

namespace App\Console\Commands;

use App\Repositories\CategoryRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new product via CLI';

    /**
     * categoryRepository
     *
     * @var mixed
     */
    private $categoryRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $categories = $this->getCategories();
            // dd($categories);
            $productData = $this->gatherProductData($categories);

            $response = $this->sendCreateProductRequest($productData);

            $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    private function getCategories(): array
    {
        return $this->categoryRepository->getAll()->pluck('name', 'id')->toArray();
    }

    protected function selectCategoryIds()
    {
        $categories = $this->getCategories();

        $this->info('Available Categories:');

        foreach ($categories as $id => $name) {
            $this->line("ID: $id - Name: $name");
        }

        $selectedIds = $this->askRequired('Enter category IDs (comma-separated)');

        return $selectedIds;
    }

    private function gatherProductData(array $categories): array
    {
        return [
            'name' => $this->askRequired('name'),
            'description' => $this->askRequired('description'),
            'price' => $this->askRequired('price'),
            'image' => $this->askRequired('image'),
            // 'categories' => $this->ask('please enter categories id separate by comma: ')
            'categories' => $this->selectCategoryIds()
        ];
    }

    private function sendCreateProductRequest(array $productData)
    {
        $imagePath = $productData['image'];

        $multipartData = [
            [
                'name'     => 'name',
                'contents' => $productData['name']
            ],
            [
                'name'     => 'description',
                'contents' => $productData['description']
            ],
            [
                'name'     => 'price',
                'contents' => $productData['price']
            ],
            [
                'name'     => 'image',
                'contents' => fopen($imagePath, 'r'),
                'filename' => basename($imagePath)
            ],
            [
                'name'     => 'categories',
                'contents' => $productData['categories']
            ]
        ];

        return Http::asMultipart()
            ->withHeaders(['Accept' => 'application/json'])
            ->post(url('/api/products'), $multipartData);
    }

    private function handleResponse($response)
    {
        if ($response->status() === 201) {
            $this->info('Product created successfully');
        } else {
            $this->warn('Failed to create product. Status Code: ' . $response->status());
            $this->handleErrors($response->collect()['errors']);
        }
    }

    private function handleErrors(array $errors)
    {
        foreach ($errors as $error) {
            $this->error($error[0]);
            $this->newLine();
        }
    }

    private function askRequired(string $question): string
    {
        $response = '';
        while (empty($response)) {
            $response = $this->ask($question);
            if (empty($response)) {
                $this->error("This field is required.");
            }
        }
        return $response;
    }
}
