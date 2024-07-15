<?php

namespace App\Console\Commands;

use App\Http\Requests\ProductRequest;
use App\Repositories\CategoryRepository;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Throwable;

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

    private $categoryRepository;

    private $productService;


    /**
     *
     * @return void
     */
    public function __construct(CategoryRepository $categoryRepository, ProductService $productService)
    {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->productService = $productService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $productData = $this->gatherProductData();

            $validatedData = $this->validateInput($productData);

            $product = $this->productService->createProduct($validatedData);

            $this->info('product created successfully with id: ' . $product->id);

            return 1;
        } catch (Throwable) {
            $this->error('failed to create the product');
            return 0;
        }
    }

    /**
     * validateInput
     *
     * @param  array $inputData
     * @return array
     */
    private function validateInput(array $inputData): array
    {
        $validator = Validator::make($inputData, (new ProductRequest())->rules());

        if ($validator->fails()) {
            $this->showErrors($validator->errors()->all());
        }

        return $inputData;
    }

    /**
     * getCategories
     *
     * @return array
     */
    private function getCategories(): array
    {
        return $this->categoryRepository->getAll()->pluck('name', 'id')->toArray();
    }

    /**
     * selectCategoryIds
     *
     * @return string
     */
    protected function selectCategoryIds(): string
    {
        $categories = $this->getCategories();

        $this->info('Available Categories:');

        foreach ($categories as $id => $name) {
            $this->line("ID: $id - Name: $name");
        }

        $selectedIds = $this->askRequired('Enter category IDs (comma-separated)');

        return $selectedIds;
    }

    /**
     * gatherProductData
     *
     * @return array
     */
    private function gatherProductData(): array
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

    /**
     * showErrors
     *
     * @param  array $errors
     * @return void
     */
    private function showErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->error($error);
            $this->newLine();
        }
    }

    /**
     * askRequired
     *
     * @param  string $question
     * @return string
     */
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
