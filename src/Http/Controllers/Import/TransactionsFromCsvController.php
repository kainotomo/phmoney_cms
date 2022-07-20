<?php

namespace Kainotomo\PHMoney\Http\Controllers\Import;

use Kainotomo\PHMoney\Http\Controllers\Controller;
use Kainotomo\PHMoney\Models\Account;
use Kainotomo\PHMoney\Models\Base;
use Kainotomo\PHMoney\Models\Split;
use Kainotomo\PHMoney\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Providers\Jetstream\Jetstream;
use League\Csv\Reader;
use Kainotomo\PHMoney\Models\Setting;

class TransactionsFromCsvController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $account_guid
     * @return \Inertia\Response
     */
    public function page1()
    {
        return Jetstream::inertia()->render(request(), 'Import/TransactionsFromCsv/Page1', []);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function page2()
    {
        return Jetstream::inertia()->render(request(), 'Import/TransactionsFromCsv/Page2', [
           'import_settings' =>  Setting::where(['type' => 'import_transactions_csv'])->get()
        ]);
    }

    /**
     * Validate a csv file.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function page3(Request $request)
    {
        $validated = $request->session()->get('import_transactions_csv', []);
        return Jetstream::inertia()->render(request(), 'Import/TransactionsFromCsv/Page3', $validated);
    }

    /**
     * Validate a csv file.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function page3Update(Request $request)
    {
        // validate payload
        $validated = Validator::make($request->all(), [
            'upload_file' => ['required_if:file_path,null', 'file', 'nullable'],
            'file_path' => ['required_if:upload_file,null', 'string', 'nullable'],
            'items' => ['nullable'],
            'delimiter' => ['required', 'string', 'max:1'],
            'enclosure' => ['required', 'string', 'max:1'],
            'date_format' => ['required', 'string'],
            'currency_format' => ['required', 'string', 'max:1'],
            'selected_columns' => ['nullable'],
        ])->validate();

        // when a new file is sent
        if (!is_null($request->upload_file)) {
            Session::forget('import_transactions_csv');
            $validated['file_path'] = $request->file('upload_file')->store('import');
            $validated['upload_file'] = null;
            $validated['items'] = [];
            $validated['selected_columns'] = [];
        }

        // if empty items
        if (empty($validated['items'])) {
            $csvString = Storage::get($validated['file_path']);
            $csvString = mb_convert_encoding($csvString, "UTF-8", "auto");
            $csv = Reader::createFromString($csvString);
            $csv->setDelimiter($validated['delimiter']);
            $csv->setEnclosure($validated['enclosure']);
            $validated['items'] = collect();
            foreach ($csv->getRecords() as $value) {
                $validated['items'][] = [
                    'checked' => false,
                    'is_valid' => true,
                    'validation_message' => null,
                    'source_account' => null,
                    'destination_account' => null,
                    'value' => $value
                ];
            }

            $validated['selected_columns'] = collect();
            foreach ($validated['items'][0]['value'] as $value) {
                $validated['selected_columns'][] = null;
            }
        } else {
            foreach ($validated['items'] as $key => $item) {
                $validated['items'][$key]['is_valid'] = true;
                $validated['items'][$key]['validation_message'] = null;
            }
        }

        // remove duplication
        $selected_columns = collect($validated['selected_columns']);
        $duplicates = $selected_columns->duplicates();
        foreach ($duplicates as $key => $value) {
            if (!is_null($value)) {
                $selected_columns[$key] = null;
            }
        }
        // validate selected columns
        $items = collect($validated['items'])->where('checked', true);
        foreach ($selected_columns as $key => $value) {
            if (!is_null($value)) {
                foreach ($items as $index => $item) {
                    if ($value === 'Date') {
                        if ($this->validateDate($item['value'][$key], $validated['date_format']) === false) {
                            $validated['items'][$index]['is_valid'] = false;
                            $validated['items'][$index]['validation_message'] = 'Invalid date format';
                        }
                    }
                    if ($value === 'Amount' || $value === 'Shares') {
                        if ($this->validateNumber($item['value'][$key], $validated['currency_format']) === false) {
                            $validated['items'][$index]['is_valid'] = false;
                            $validated['items'][$index]['validation_message'] = 'Invalid number format';
                        }
                    }
                }
            }
        }

        // can proceed
        $validated['can_proceed'] = true;
        $validated['can_proceed_message'] = null;
        $items = collect($validated['items'])->where('checked', true);
        if ($items->count() == 0) {
            $validated['can_proceed'] = false;
            $validated['can_proceed_message'] = 'No item is selected';
        }
        if (!$request->skip_errors) {
            $items = collect($validated['items'])->where('is_valid', false);
            if ($items->count() > 0) {
                $validated['can_proceed'] = false;
                $validated['can_proceed_message'] = 'Some items are non valid';
            }
        }
        $items = collect($validated['items'])->where('source_account', null);
        if ($items->count() > 0) {
            $validated['can_proceed'] = false;
            $validated['can_proceed_message'] = 'All items must have source account';
        }
        $items = collect($validated['items'])->where('destination_account', null);
        if ($items->count() > 0) {
            $validated['can_proceed'] = false;
            $validated['can_proceed_message'] = 'All items must have destination account';
        }
        $selected_columns_check = collect($validated['selected_columns']);
        if ($selected_columns_check->search('Date') === false) {
            $validated['can_proceed'] = false;
            $validated['can_proceed_message'] = 'Need to select Date column';
        }
        if ($selected_columns_check->search('Amount') === false) {
            $validated['can_proceed'] = false;
            $validated['can_proceed_message'] = 'Need to select Amount column';
        }

        $validated['selected_columns'] = $selected_columns->toArray();

        $validated['accounts'] = Account::getFlatList();

        Session::put('import_transactions_csv', $validated);

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'import-updated');
    }

    /**
     * Import a csv file.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function page4(Request $request)
    {
        $validated = $request->session()->get('import_transactions_csv', []);

        $items = collect($validated['items']);
        $items = $items->where('checked', true);
        $items = $items->where('is_valid', true);

        $selected_columns = collect($validated['selected_columns'])->filter();

        $transactions = [];
        $splits = [];
        foreach ($items as $item) {
            $transaction = [
                    'guid' => Base::uuid(),
                    'currency_guid' => $item['source_account']['commodity']['guid']
                ];

            $date_key = $selected_columns->search('Date');
            $date = Carbon::createFromFormat($validated['date_format'], $item['value'][$date_key])->format('Y-m-d');
            $transaction['post_date'] = $date;
            $transaction['enter_date'] = $date;

            $num_key = $selected_columns->search('Num');
            if ($num_key !== false) {
                $transaction['num'] = $item['value'][$num_key];
            }

            $description_key = $selected_columns->search('Description');
            if ($description_key !== false) {
                $transaction['description'] = $item['value'][$description_key];
            }

            $transactions[] = $transaction;

            $amount_key = $selected_columns->search('Amount');
            $amount = $item['value'][$amount_key];
            if ($validated['currency_format'] === ',') {
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            }
            $amount = round($amount * $item['source_account']['commodity']['fraction']);
            $shares_key = $selected_columns->search('Shares');
            if ($shares_key !== false) {
                $shares = $item['value'][$shares_key];
                if ($validated['currency_format'] === ',') {
                    $shares = str_replace('.', '', $shares);
                    $shares = str_replace(',', '.', $shares);
                }
                $shares = round($shares * $item['source_account']['commodity']['fraction']);
            } else {
                $shares = $amount;
            }

            $splits[] = [
                'guid' => Base::uuid(),
                'tx_guid' => $transaction['guid'],
                'account_guid' => $item['source_account']['guid'],
                'memo' => '',
                'action' => '',
                'reconcile_state' => 'c',
                'reconcile_date' => $transaction['post_date'],
                'value_num' => -$amount,
                'value_denom' => $item['source_account']['commodity']['fraction'],
                'quantity_num' => -$shares,
                'quantity_denom' => $item['source_account']['commodity']['fraction'],
            ];
            $splits[] = [
                'guid' => Base::uuid(),
                'tx_guid' => $transaction['guid'],
                'account_guid' => $item['destination_account']['guid'],
                'memo' => '',
                'action' => '',
                'reconcile_state' => 'c',
                'reconcile_date' => $transaction['post_date'],
                'value_num' => $amount,
                'value_denom' => $item['destination_account']['commodity']['fraction'],
                'quantity_num' => $shares,
                'quantity_denom' => $item['destination_account']['commodity']['fraction'],
            ];

        }

        Transaction::insert($transactions);
        Split::insert($splits);

        return Jetstream::inertia()->render(request(), 'Import/TransactionsFromCsv/Page4', []);
    }

    /**
     * Validate a date string based on format
     *
     * @param string $value
     * @param string $format
     * @return \Carbon\Carbon|false
     */
    protected function validateDate(string $value, string $format)
    {
        try {
            Carbon::createFromFormat($format, $value);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    /**
     * Validate a numeric string based on format
     *
     * @param string $value
     * @param string $format
     * @return \Carbon\Carbon|false
     */
    protected function validateNumber(string $value, string $format)
    {
        if ($format === ',') {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        return is_numeric($value);
    }
}
