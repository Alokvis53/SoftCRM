<?php
/**
 * Created by PhpStorm.
 * User: kamilgrzechulski
 * Date: 30.07.2018
 * Time: 09:20
 */

namespace App\Services;

use App\Models\CompaniesModel;
use App\Models\DealsModel;

class CompaniesService
{
    private $companiesModel;

    public function __construct()
    {
        $this->companiesModel = new CompaniesModel();
    }

    public function execute($requestedData, int $adminId)
    {
        return $this->companiesModel->insertCompanie($requestedData, $adminId);
    }

    public function update(int $companiesId, $requestedData)
    {
        return $this->companiesModel->updateCompanie($companiesId, $requestedData);
    }

    public function loadCompanies()
    {
        return $this->companiesModel::all()->sortByDesc('created_at');
    }

    public function loadPagination()
    {
        return $this->companiesModel->getPaginate();
    }

    public function pluckData()
    {
        return $this->companiesModel::pluck('name', 'id');
    }

    public function loadCompanie(int $companiesId)
    {
        return $this->companiesModel::find($companiesId);
    }

    public function countAssignedDeals(int $companiesId)
    {
        return DealsModel::where('companies_id', $companiesId)->get()->count();
    }

    public function countAssignedFile(int $companiesId)
    {
//        return $this->companiesModel
    }

    public function loadSetActive($companiesId, $value)
    {
        return $this->companiesModel->setActive($companiesId, $value);
    }

    public function loadCompaniesByCreatedAt()
    {
        return $this->companiesModel->getCompaniesSortedByCreatedAt();
    }

    public function loadCountCompanies()
    {
        return $this->companiesModel->countCompanies();
    }

    public function loadDeactivatedCompanies()
    {
        return $this->companiesModel->getDeactivated();
    }

    public function loadCompaniesInLatestMonth()
    {
        return $this->companiesModel->getCompaniesInLatestMonth() . '%' ? : '0.00%';
    }
}