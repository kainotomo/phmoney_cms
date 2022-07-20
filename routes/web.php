<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'phmoney', 'middleware' => 'web'], function () {
    Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\UserController::class, 'index'])->name('phmoney');

    Route::prefix('/user')->group(function () {
        Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\UserController::class, 'show'])->name('phmoney.user');
    });

    Route::get('/dashboard2', [\Kainotomo\PHMoney\Http\Controllers\DashboardController::class, 'index'])->name('phmoney.dashboard2');

    Route::prefix('/accounts')->group(function () {
        Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\AccountsController::class, 'index'])->name('phmoney.accounts');
        Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\AccountsController::class, 'create'])->name('phmoney.accounts.create');
        Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\AccountsController::class, 'store'])->name('phmoney.accounts.store');
        Route::get('/edit/{account}', [\Kainotomo\PHMoney\Http\Controllers\AccountsController::class, 'edit'])->name('phmoney.accounts.edit');
        Route::post('/update/{account}', [\Kainotomo\PHMoney\Http\Controllers\AccountsController::class, 'update'])->name('phmoney.accounts.update');
        Route::delete('/destroy/{account}', [\Kainotomo\PHMoney\Http\Controllers\AccountsController::class, 'destroy'])->name('phmoney.accounts.destroy');
    });

    Route::prefix('/reconcile/{account}')->group(function () {
        Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\ReconcileController::class, 'index'])->name('phmoney.reconcile');
        Route::post('/update', [\Kainotomo\PHMoney\Http\Controllers\ReconcileController::class, 'update'])->name('phmoney.reconcile.update');
    });

    Route::prefix('/transactions/{account}')->group(function () {
        Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'index'])->name('phmoney.transactions');
        Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'create'])->name('phmoney.transactions.create');
        Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'store'])->name('phmoney.transactions.store');
        Route::get('/edit/{transaction}', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'edit'])->name('phmoney.transactions.edit');
        Route::get('/duplicate/{transaction}', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'duplicate'])->name('phmoney.transactions.duplicate');
        Route::post('/update/{transaction}', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'update'])->name('phmoney.transactions.update');
        Route::delete('/destroy/{transaction}', [\Kainotomo\PHMoney\Http\Controllers\TransactionsController::class, 'destroy'])->name('phmoney.transactions.destroy');
    });

    Route::prefix('/settings')->group(function () {
        Route::put('/store', [\Kainotomo\PHMoney\Http\Controllers\SettingController::class, 'store'])->name('phmoney.settings.store');
        Route::post('/update/{setting}', [\Kainotomo\PHMoney\Http\Controllers\SettingController::class, 'update'])->name('phmoney.settings.update');
        Route::delete('/destroy/{setting}', [\Kainotomo\PHMoney\Http\Controllers\SettingController::class, 'destroy'])->name('phmoney.settings.destroy');
    });

    Route::prefix('/import')->group(function () {
        Route::prefix('/transactions-from-csv')->group(function () {
            Route::get('/page1', [\Kainotomo\PHMoney\Http\Controllers\Import\TransactionsFromCsvController::class, 'page1'])->name('phmoney.import.transactions-from-csv.page1');
            Route::get('/page2', [\Kainotomo\PHMoney\Http\Controllers\Import\TransactionsFromCsvController::class, 'page2'])->name('phmoney.import.transactions-from-csv.page2');
            Route::get('/page3', [\Kainotomo\PHMoney\Http\Controllers\Import\TransactionsFromCsvController::class, 'page3'])->name('phmoney.import.transactions-from-csv.page3');
            Route::post('/page3/update', [\Kainotomo\PHMoney\Http\Controllers\Import\TransactionsFromCsvController::class, 'page3Update'])->name('phmoney.import.transactions-from-csv.page3.update');
            Route::get('/page4', [\Kainotomo\PHMoney\Http\Controllers\Import\TransactionsFromCsvController::class, 'page4'])->name('phmoney.import.transactions-from-csv.page4');
        });
    });

    Route::prefix('/export')->group(function () {
        Route::prefix('/transactions-to-csv')->group(function () {
            Route::get('/index', [\Kainotomo\PHMoney\Http\Controllers\Export\TransactionsToCsvController::class, 'index'])->name('phmoney.import.transactions-from-csv.index');
            Route::post('/download', [\Kainotomo\PHMoney\Http\Controllers\Export\TransactionsToCsvController::class, 'download'])->name('phmoney.import.transactions-from-csv.download');
        });
    });

    Route::prefix('/reports')->group(function () {
        Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\ReportController::class, 'index'])->name('phmoney.reports');
        Route::get('/transactions', [\Kainotomo\PHMoney\Http\Controllers\ReportController::class, 'transactions'])->name('phmoney.reports.transactions');
        Route::prefix('/assets_liabilities')->group(function () {
            Route::get('/balance_sheet', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'balance_sheet'])->name('phmoney.reports.balance_liabilities.balance_sheet');
            Route::get('/general_ledger', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'general_ledger'])->name('phmoney.reports.balance_liabilities.general_ledger');
            Route::get('/assets_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'assets_columnchart'])->name('phmoney.reports.balance_liabilities.assets_columnchart');
            Route::get('/assets_piechart', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'assets_piechart'])->name('phmoney.reports.balance_liabilities.assets_piechart');
            Route::get('/liabilities_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'liabilities_columnchart'])->name('phmoney.reports.balance_liabilities.liabilities_columnchart');
            Route::get('/liabilities_piechart', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'liabilities_piechart'])->name('phmoney.reports.balance_liabilities.liabilities_piechart');
            Route::get('/networth_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'networth_columnchart'])->name('phmoney.reports.balance_liabilities.networth_columnchart');
            Route::get('/networth_linechart', [\Kainotomo\PHMoney\Http\Controllers\Reports\AssetsLiabilitiesController::class, 'networth_linechart'])->name('phmoney.reports.balance_liabilities.networth_linechart');
        });
        Route::prefix('/business')->group(function () {
            Route::get('/customer_report', [\Kainotomo\PHMoney\Http\Controllers\Reports\BusinessController::class, 'customer_report'])->name('phmoney.reports.business.customer_report');
            Route::get('/customer_summary', [\Kainotomo\PHMoney\Http\Controllers\Reports\BusinessController::class, 'customer_summary'])->name('phmoney.reports.business.customer_summary');
            Route::get('/employee_report', [\Kainotomo\PHMoney\Http\Controllers\Reports\BusinessController::class, 'employee_report'])->name('phmoney.reports.business.employee_report');
            Route::get('/vendor_report', [\Kainotomo\PHMoney\Http\Controllers\Reports\BusinessController::class, 'vendor_report'])->name('phmoney.reports.business.vendor_report');
        });
        Route::prefix('/income_expense')->group(function () {
            Route::get('/cash_flow', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'cash_flow'])->name('phmoney.reports.income_expense.cash_flow');
            Route::get('/cash_flow_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'cash_flow_columnchart'])->name('phmoney.reports.income_expense.cash_flow_columnchart');
            Route::get('/expenses_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'expenses_columnchart'])->name('phmoney.reports.income_expense.expenses_columnchart');
            Route::get('/expenses_piechart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'expenses_piechart'])->name('phmoney.reports.income_expense.expenses_piechart');
            Route::get('/incomeexpense_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'incomeexpense_columnchart'])->name('phmoney.reports.income_expense.incomeexpense_columnchart');
            Route::get('/incomeexpense_linechart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'incomeexpense_linechart'])->name('phmoney.reports.income_expense.incomeexpense_linechart');
            Route::get('/income_columnchart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'income_columnchart'])->name('phmoney.reports.income_expense.income_columnchart');
            Route::get('/income_piechart', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'income_piechart'])->name('phmoney.reports.income_expense.income_piechart');
            Route::get('/profit_loss', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'profit_loss'])->name('phmoney.reports.income_expense.profit_loss');
            Route::get('/trial_balance', [\Kainotomo\PHMoney\Http\Controllers\Reports\IncomeExpenseController::class, 'trial_balance'])->name('phmoney.reports.income_expense.trial_balance');
        });
    });

    Route::prefix('/business')->group(function () {
        Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\BusinessController::class, 'index'])->name('phmoney.business');

        Route::prefix('/payment')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\PaymentController::class, 'index'])->name('phmoney.business.payment');
            Route::post('/', [\Kainotomo\PHMoney\Http\Controllers\Business\PaymentController::class, 'store'])->name('phmoney.business.payment.store');
        });

        Route::prefix('/customers')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\CustomerController::class, 'index'])->name('phmoney.business.customers');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\CustomerController::class, 'create'])->name('phmoney.business.customers.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\CustomerController::class, 'store'])->name('phmoney.business.customers.store');
            Route::get('/edit/{customer}', [\Kainotomo\PHMoney\Http\Controllers\Business\CustomerController::class, 'edit'])->name('phmoney.business.customers.edit');
            Route::post('/update/{customer}', [\Kainotomo\PHMoney\Http\Controllers\Business\CustomerController::class, 'update'])->name('phmoney.business.customers.update');
            Route::delete('/destroy/{customer}', [\Kainotomo\PHMoney\Http\Controllers\Business\CustomerController::class, 'destroy'])->name('phmoney.business.customers.destroy');
        });

        Route::prefix('/vendors')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\VendorController::class, 'index'])->name('phmoney.business.vendors');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\VendorController::class, 'create'])->name('phmoney.business.vendors.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\VendorController::class, 'store'])->name('phmoney.business.vendors.store');
            Route::get('/edit/{vendor}', [\Kainotomo\PHMoney\Http\Controllers\Business\VendorController::class, 'edit'])->name('phmoney.business.vendors.edit');
            Route::post('/update/{vendor}', [\Kainotomo\PHMoney\Http\Controllers\Business\VendorController::class, 'update'])->name('phmoney.business.vendors.update');
            Route::delete('/destroy/{vendor}', [\Kainotomo\PHMoney\Http\Controllers\Business\VendorController::class, 'destroy'])->name('phmoney.business.vendors.destroy');
        });

        Route::prefix('/employees')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\EmployeeController::class, 'index'])->name('phmoney.business.employees');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\EmployeeController::class, 'create'])->name('phmoney.business.employees.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\EmployeeController::class, 'store'])->name('phmoney.business.employees.store');
            Route::get('/edit/{employee}', [\Kainotomo\PHMoney\Http\Controllers\Business\EmployeeController::class, 'edit'])->name('phmoney.business.employees.edit');
            Route::post('/update/{employee}', [\Kainotomo\PHMoney\Http\Controllers\Business\EmployeeController::class, 'update'])->name('phmoney.business.employees.update');
            Route::delete('/destroy/{employee}', [\Kainotomo\PHMoney\Http\Controllers\Business\EmployeeController::class, 'destroy'])->name('phmoney.business.employees.destroy');
        });

        Route::prefix('/jobs')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\JobController::class, 'index'])->name('phmoney.business.jobs');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\JobController::class, 'create'])->name('phmoney.business.jobs.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\JobController::class, 'store'])->name('phmoney.business.jobs.store');
            Route::get('/edit/{job}', [\Kainotomo\PHMoney\Http\Controllers\Business\JobController::class, 'edit'])->name('phmoney.business.jobs.edit');
            Route::post('/update/{job}', [\Kainotomo\PHMoney\Http\Controllers\Business\JobController::class, 'update'])->name('phmoney.business.jobs.update');
            Route::delete('/destroy/{job}', [\Kainotomo\PHMoney\Http\Controllers\Business\JobController::class, 'destroy'])->name('phmoney.business.jobs.destroy');
        });

        Route::prefix('/invoices')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'index'])->name('phmoney.business.invoices');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'create'])->name('phmoney.business.invoices.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'store'])->name('phmoney.business.invoices.store');
            Route::get('/edit/{invoice}', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'edit'])->name('phmoney.business.invoices.edit');
            Route::post('/update/{invoice}', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'update'])->name('phmoney.business.invoices.update');
            Route::delete('/destroy/{invoice}', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'destroy'])->name('phmoney.business.invoices.destroy');
            Route::get('/post/{invoice}', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'edit_post'])->name('phmoney.business.invoices.edit_post');
            Route::post('/post/{invoice}', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'post'])->name('phmoney.business.invoices.post');
            Route::delete('/post/{invoice}', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'unpost'])->name('phmoney.business.invoices.unpost');
            Route::get('/jobs', [\Kainotomo\PHMoney\Http\Controllers\Business\InvoiceController::class, 'jobs'])->name('phmoney.business.invoices.jobs');

            Route::prefix('/{invoice}/entrys')->group(function () {
                Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\EntryController::class, 'index'])->name('phmoney.business.entrys');
                Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\EntryController::class, 'create'])->name('phmoney.business.entrys.create');
                Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\EntryController::class, 'store'])->name('phmoney.business.entrys.store');
                Route::get('/edit/{entry}', [\Kainotomo\PHMoney\Http\Controllers\Business\EntryController::class, 'edit'])->name('phmoney.business.entrys.edit');
                Route::post('/update/{entry}', [\Kainotomo\PHMoney\Http\Controllers\Business\EntryController::class, 'update'])->name('phmoney.business.entrys.update');
                Route::delete('/destroy/{entry}', [\Kainotomo\PHMoney\Http\Controllers\Business\EntryController::class, 'destroy'])->name('phmoney.business.entrys.destroy');
            });
        });

        Route::prefix('/taxtables')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableController::class, 'index'])->name('phmoney.business.taxtables');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableController::class, 'create'])->name('phmoney.business.taxtables.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableController::class, 'store'])->name('phmoney.business.taxtables.store');
            Route::get('/edit/{taxtable}', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableController::class, 'edit'])->name('phmoney.business.taxtables.edit');
            Route::post('/update/{taxtable}', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableController::class, 'update'])->name('phmoney.business.taxtables.update');
            Route::delete('/destroy/{taxtable}', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableController::class, 'destroy'])->name('phmoney.business.taxtables.destroy');

            Route::prefix('/{taxtable}/taxtableentrys')->group(function () {
                Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableEntryController::class, 'index'])->name('phmoney.business.taxtableentrys');
                Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableEntryController::class, 'create'])->name('phmoney.business.taxtableentrys.create');
                Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableEntryController::class, 'store'])->name('phmoney.business.taxtableentrys.store');
                Route::get('/edit/{taxtableentry}', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableEntryController::class, 'edit'])->name('phmoney.business.taxtableentrys.edit');
                Route::post('/update/{taxtableentry}', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableEntryController::class, 'update'])->name('phmoney.business.taxtableentrys.update');
                Route::delete('/destroy/{taxtableentry}', [\Kainotomo\PHMoney\Http\Controllers\Business\TaxtableEntryController::class, 'destroy'])->name('phmoney.business.taxtableentrys.destroy');
            });
        });

        Route::prefix('/billterms')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Business\BilltermController::class, 'index'])->name('phmoney.business.billterms');
            Route::get('/create', [\Kainotomo\PHMoney\Http\Controllers\Business\BilltermController::class, 'create'])->name('phmoney.business.billterms.create');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Business\BilltermController::class, 'store'])->name('phmoney.business.billterms.store');
            Route::get('/edit/{billterm}', [\Kainotomo\PHMoney\Http\Controllers\Business\BilltermController::class, 'edit'])->name('phmoney.business.billterms.edit');
            Route::post('/update/{billterm}', [\Kainotomo\PHMoney\Http\Controllers\Business\BilltermController::class, 'update'])->name('phmoney.business.billterms.update');
            Route::delete('/destroy/{billterm}', [\Kainotomo\PHMoney\Http\Controllers\Business\BilltermController::class, 'destroy'])->name('phmoney.business.billterms.destroy');
        });
    });

    Route::prefix('/tools')->group(function () {
        Route::prefix('/closebook')->group(function () {
            Route::get('/', [\Kainotomo\PHMoney\Http\Controllers\Tools\ClosebookController::class, 'index'])->name('phmoney.tools.closebook');
            Route::post('/store', [\Kainotomo\PHMoney\Http\Controllers\Tools\ClosebookController::class, 'store'])->name('phmoney.tools.closebook.store');
        });
    });

    Route::prefix('/teams')->group(function () {
        Route::prefix('/{team}')->group(function () {
            Route::get('/options/show', [\Kainotomo\PHMoney\Http\Controllers\Teams\OptionsController::class, 'index'])->name('phmoney.teams.options.show');
            Route::put('/options/store', [\Kainotomo\PHMoney\Http\Controllers\Teams\OptionsController::class, 'store'])->name('phmoney.teams.options.store');
            Route::get('/database/download', [\Kainotomo\PHMoney\Http\Controllers\Teams\OptionsController::class, 'download'])->name('phmoney.teams.database.download');
            Route::post('/database/upload', [\Kainotomo\PHMoney\Http\Controllers\Teams\OptionsController::class, 'upload'])->name('phmoney.teams.database.upload');
            Route::get('/samples', [\Kainotomo\PHMoney\Http\Controllers\Teams\OptionsController::class, 'samples'])->name('phmoney.teams.samples.index');
            Route::post('/samples/load', [\Kainotomo\PHMoney\Http\Controllers\Teams\OptionsController::class, 'loadSample'])->name('phmoney.teams.samples.load');
        });
    });
});
