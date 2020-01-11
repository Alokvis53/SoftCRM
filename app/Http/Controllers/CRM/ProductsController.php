<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductsStoreRequest;
use App\Services\ProductsService;
use App\Services\SystemLogService;
use View;
use Illuminate\Http\Request;
Use Illuminate\Support\Facades\Redirect;

class ProductsController extends Controller
{
    private $productsService;
    private $systemLogsService;

    public function __construct()
    {
        $this->middleware('auth');

        $this->productsService = new ProductsService();
        $this->systemLogsService = new SystemLogService();
    }

    public function processListOfProducts()
    {
        return View::make('crm.products.index')->with(
            [
                'products' => $this->productsService->loadProducts(),
                'productsPaginate' => $this->productsService->loadPagination()
            ]
        );
    }

    public function showCreateForm()
    {
        return View::make('crm.products.create');
    }

    public function viewProductsDetails(int $productId)
    {
        return View::make('crm.products.show')->with(['products' => $this->productsService->loadProduct($productId)]);
    }

    public function showUpdateForm(int $productId)
    {
        return View::make('crm.products.edit')->with(['products' => $this->productsService->loadProduct($productId)]);
    }

    public function processCreateProducts(ProductsStoreRequest $request)
    {
        if ($product = $this->productsService->execute($request->validated(), $this->getAdminId())) {
            $this->systemLogsService->loadInsertSystemLogs('Product has been add with id: ' . $product, $this->systemLogsService::successCode, $this->getAdminId());
            return Redirect::to('products')->with('message_success', $this->getMessage('messages.SuccessProductsStore'));
        } else {
            return Redirect::back()->with('message_success', $this->getMessage('messages.ErrorProductsStore'));
        }
    }

    public function processUpdateProducts(Request $request, int $productId)
    {
        if ($this->productsService->update($productId, $request->all())) {
            return Redirect::to('products')->with('message_success', $this->getMessage('messages.SuccessProductsStore'));
        } else {
            return Redirect::back()->with('message_danger', $this->getMessage('messages.ErrorProductsStore'));
        }
    }

    public function processDeleteProducts(int $productId)
    {
        $clientAssigned = $this->productsService->checkIfProductHaveAssignedSale($productId);

        if (!empty($clientAssigned)) {
            return Redirect::back()->with('message_danger', $clientAssigned);
        } else {
            $productsDetails = $this->productsService->loadProduct($productId);
            $productsDetails->delete();
        }

        $this->systemLogsService->loadInsertSystemLogs('ProductsModel has been deleted with id: ' . $productsDetails->id, $this->systemLogsService::successCode, $this->getAdminId());

        return Redirect::to('products')->with('message_success', $this->getMessage('messages.SuccessProductsDelete'));
    }

    public function processSetIsActive(int $productId, bool $value)
    {
        if ($this->productsService->loadIsActiveFunction($productId, $value)) {
            $this->systemLogsService->loadInsertSystemLogs('ProductsModel has been enabled with id: ' . $productId, $this->systemLogsService::successCode, $this->getAdminId());
            return Redirect::to('products')->with('message_success', $this->getMessage('messages.SuccessProductsActive'));
        } else {
            return Redirect::back()->with('message_danger', $this->getMessage('messages.ProductsIsActived'));
        }
    }
}
