<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function generateReports()
    {
        return Product::with(['transactions' => function ($query) {
            $query->select('product_id', DB::raw('
                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                 SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotal()
    {
        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales+trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
                            FROM transaction_details
                            GROUP BY product_id
                        ) as trans'), 'products.product_id', '=', 'trans.product_id')
            ->groupBy('category')
            ->get();

        return $categorySales;
    }
    public function generateReportsUser($id)
    {
        return Product::with(['transactions' => function ($query) use ($id) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price

            '))
                ->where('user_id', $id)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalUser($id)
    {
        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales + trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales + trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE transaction_details.user_id = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$id])
            ->get();
        return $categorySales;
    }
    public function generateReportsDate($date)
    {
        $Dateto = Carbon::parse($date)->format('Y-m-d');

        return Product::with(['transactions' => function ($query) use ($Dateto) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->whereDate('created_at', $Dateto)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalDate($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted )) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted )) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE DATE(transaction_details.created_at) = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$formattedDate])
            ->get();

        return $categorySales;
    }

    public function generateReportsDateRange($from, $to)
    {
        $Datefrom = Carbon::parse($from)->format('Y-m-d');
        $Dateto = Carbon::parse($to)->format('Y-m-d');

        return Product::with(['transactions' => function ($query) use ($Dateto,$Datefrom) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->whereBetween('created_at', [$Datefrom,$Dateto])
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalDateRange($from, $to)
    {
        $Datefrom = Carbon::parse($from)->format('Y-m-d');
        $Dateto = Carbon::parse($to)->format('Y-m-d');

        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE DATE(transaction_details.created_at) BETWEEN ? AND ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$Datefrom, $Dateto])
            ->get();

        return $categorySales;
    }

    public function generateReportsDateRangeUser($id, $from, $to)
    {
        $Datefrom = Carbon::parse($from)->format('Y-m-d');
        $Dateto = Carbon::parse($to)->format('Y-m-d');

        return Product::with(['transactions' => function ($query) use ($id, $Dateto,$Datefrom) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->whereBetween('created_at', [$Datefrom,$Dateto])
                ->where('user_id', $id)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalDateRangeUser($id, $from, $to)
    {
        $Datefrom = Carbon::parse($from)->format('Y-m-d');
        $Dateto = Carbon::parse($to)->format('Y-m-d');

        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE DATE(transaction_details.created_at) BETWEEN ? AND ? AND
                            transaction_details.user_id = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$Datefrom, $Dateto, $id])
            ->get();

        return $categorySales;
    }


    public function generateReportsUserDate($id, $date)
    {
        $Dateto = Carbon::create($date)->format('Y-m-d');
        return Product::with(['transactions' => function ($query) use ($Dateto, $id) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->whereDate('created_at', $Dateto)
                ->where('user_id', $id)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalUserDate($id, $date)
    {
        $formattedDate = Carbon::create($date)->format('Y-m-d');
        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales+trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE DATE(transaction_details.created_at) = ? AND
                            transaction_details.user_id = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$formattedDate, $id])
            ->get();
        return $categorySales;
    }


    public function generateReportsCurrentUser()
    {
        $id = Auth::user()->user_id;
        return Product::with(['transactions' => function ($query) use ($id) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->where('user_id', $id)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalCurrentUser()
    {
        $id = Auth::user()->user_id;
        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE transaction_details.user_id = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$id])
            ->get();
        return $categorySales;
    }



    public function generateReportsCurrentUserDate($date)
    {
        $id = Auth::user()->user_id;
        $Dateto = Carbon::create($date)->format('Y-m-d');
        return Product::with(['transactions' => function ($query) use ($Dateto, $id) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->whereDate('created_at', $Dateto)
                ->where('user_id', $id)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalCurrentUserDate($date)
    {
        $id = Auth::user()->user_id;
        $formattedDate = Carbon::create($date)->format('Y-m-d');
        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE DATE(transaction_details.created_at) = ? AND
                            transaction_details.user_id = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$formattedDate, $id])
            ->get();

        return $categorySales;
    }

    public function generateReportsDateRangeCurrentUser($from, $to)
    {
        $Datefrom = Carbon::parse($from)->format('Y-m-d');
        $Dateto = Carbon::parse($to)->format('Y-m-d');
        $id = Auth::user()->user_id;

        return Product::with(['transactions' => function ($query) use ($id, $Dateto,$Datefrom) {
            $query->select('product_id', DB::raw('
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_quantity,
                SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) as opening,
                SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END) as delivery,
                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales,
                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price
            '))
                ->whereBetween('created_at', [$Datefrom,$Dateto])
                ->where('user_id', $id)
                ->groupBy('product_id');
        }])->get();
    }
    public function generateReportsTotalDateRangeCurrentUser($from, $to)
    {
        $Datefrom = Carbon::parse($from)->format('Y-m-d');
        $Dateto = Carbon::parse($to)->format('Y-m-d');
        $id = Auth::user()->user_id;

        $categorySales = Product::select('category', DB::raw('
                SUM(trans.total_quantity) as total_quantity,
                SUM(trans.total_wasted) as total_wasted,
                SUM(trans.closing) as closing,
                SUM(trans.total_sales) as total_sales,
                SUM(trans.total_sales_price) as total_sales_price,
                SUM(trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) as descrepancy_kg,
                SUM((trans.total_quantity - (trans.closing + trans.total_sales +trans.total_wasted)) * products.retail_price) as descrepancy
            '))
            ->join(DB::raw('(SELECT product_id,
                                (SUM(CASE WHEN remarks = 1 THEN quantity ELSE 0 END) + SUM(CASE WHEN remarks = 3 THEN quantity ELSE 0 END)+ SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END)) as total_quantity,
                                SUM(CASE WHEN remarks = 4 THEN quantity ELSE 0 END) as total_wasted,
                                SUM(CASE WHEN remarks = 5 THEN quantity ELSE 0 END) as closing,
                                SUM(CASE WHEN remarks = 2 THEN total_price ELSE 0 END) as total_sales_price,
                                SUM(CASE WHEN remarks = 2 THEN quantity ELSE 0 END) as total_sales
                            FROM transaction_details
                            WHERE DATE(transaction_details.created_at) BETWEEN ? AND ? AND
                            transaction_details.user_id = ?
                            GROUP BY product_id
                        ) as trans'), function ($join) {
                $join->on('products.product_id', '=', 'trans.product_id');
            })
            ->groupBy('category')
            ->setBindings([$Datefrom, $Dateto, $id])
            ->get();

        return $categorySales;
    }
}
