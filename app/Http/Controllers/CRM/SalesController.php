<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesStoreRequest;
use App\Models\ProductsModel;
use App\Models\SalesModel;
use App\Services\SalesService;
use App\Services\SystemLogService;
use App\Traits\Language;
use Illuminate\Support\Facades\Input;
use View;
use Request;
Use Illuminate\Support\Facades\Redirect;
use Config;

class SalesController extends Controller
{
    use Language;
    
    private $systemLogs;
    private $language;
    private $salesModel;
    private $salesService;

    public function __construct()
    {
        $this->systemLogs = new SystemLogService();
        $this->salesModel = new SalesModel();
        $this->salesService = new SalesService();
    }

    private function getDataAndPagination()
    {
        $dataWithSaless = [
            'sales' => $this->salesService->getSales(),
            'salesPaginate' => $this->salesService->getPaginate()
        ];

        return $dataWithSaless;
    }

    public function index()
    {
        return View::make('crm.sales.index')->with($this->getDataAndPagination());
    }
    
    public function create()
    {
        $dataOfProducts = ProductsModel::pluck('name', 'id');

        return View::make('crm.sales.create')->with(
            [
                'dataOfProducts' => $dataOfProducts,
                'inputText' => $this->getMessage('messages.InputText')
            ]);
    }
    
    public function show($saleId)
    {
        return View::make('crm.sales.show')
            ->with([
                'sales' => $this->salesService->getSale($saleId),
            ]);
    }

    public function edit($saleId)
    {
        $dataWithPluckOfProducts = ProductsModel::pluck('name', 'id');

        return View::make('crm.sales.edit')
            ->with([
                'sales' => $this->salesService->getSale($saleId),
                'dataWithPluckOfProducts' => $dataWithPluckOfProducts
            ]);
    }

    public function store(SalesStoreRequest $request)
    {
        if ($sale = $this->salesService->execute($request->validated())) {
            $this->systemLogs->insertSystemLogs('SalesModel has been add with id: ' . $sale, 200);
            return Redirect::to('sales')->with('message_success', $this->getMessage('messages.SuccessSalesStore'));
        } else {
            return Redirect::back()->with('message_success', $this->getMessage('messages.ErrorSalesStore'));
        }
    }

    public function update(Request $request, int $saleId)
    {
        if ($this->salesService->update($saleId, $request->all())) {
            return Redirect::to('sales')->with('message_success', $this->getMessage('messages.SuccessSalesStore'));
        } else {
            return Redirect::back()->with('message_danger', $this->getMessage('messages.ErrorSalesStore'));
        }
    }

    public function destroy($saleId)
    {
        $salesDetails = $this->salesService->getSale($saleId);
        $salesDetails->delete();

        $this->systemLogs->insertSystemLogs('SalesModel has been deleted with id: ' . $salesDetails->id, 200);

        return Redirect::to('sales')->with('message_success', $this->getMessage('messages.SuccessSalesDelete'));
    }

    public function isActiveFunction($saleId, $value)
    {
        if ($this->salesService->loadIsActiveFunction($saleId, $value)) {
            $this->systemLogs->insertSystemLogs('SalesModel has been enabled with id: ' . $saleId, 200);
            return Redirect::back()->with('message_success', $this->getMessage('messages.SuccessSalesActive'));
        } else {
            return Redirect::back()->with('message_danger', $this->getMessage('messages.SalesIsActived'));
        }
    }

    public function search()
    {
        return true; // TODO
    }
}
