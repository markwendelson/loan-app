<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Loan;
use App\Models\Savings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function addPdfHeaderFooter($pdf, $title, $branchName, $dateLabel)
    {
        $pdf->render(); // CRITICAL: Render HTML to PDF first, before drawing on the canvas

        $canvas = $pdf->getDomPDF()->getCanvas();
        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
        $fontBold = $fontMetrics->getFont('Helvetica', 'bold');
        $fontNormal = $fontMetrics->getFont('Helvetica');
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        $appName = config('app.name', 'GUIUAN LENDING CORP.');

        $canvas->page_text(30, 15, $appName, $fontBold, 14);
        $canvas->page_text(30, 33, $title, $fontBold, 10);
        $canvas->page_text(30, 47, $branchName, $fontNormal, 9);
        $canvas->page_text(30, 59, strtoupper($dateLabel), $fontNormal, 9);

        $canvas->page_text(30, $h - 28, "Date Generated: " . date('M d, Y h:i A'), $fontNormal, 8);
        $canvas->page_text($w - 100, $h - 28, "Page {PAGE_NUM} of {PAGE_COUNT}", $fontNormal, 8);
    }
    public function index()
    {
        $branches = Branch::all();
        return view('report.index', compact('branches'));
    }

    public function generate(Request $request)
    {
        $type = $request->report_type;
        $frequency = $request->frequency;
        $branchId = $request->branch_id;
        $dateStr = $request->date; 
        $action = $request->action; // 'pdf' or 'excel'

        $startDate = null;
        $endDate = null;
        $dateLabel = '';

        if ($frequency == 'daily') {
            $startDate = Carbon::parse($dateStr)->startOfDay();
            $endDate = Carbon::parse($dateStr)->endOfDay();
            $dateLabel = $startDate->format('F d, Y');
        } elseif ($frequency == 'weekly') {
            $date = Carbon::now();
            if (preg_match('/(\d{4})-W(\d{2})/', $dateStr, $matches)) {
                $date->setISODate($matches[1], $matches[2]);
            } else {
                $date = Carbon::parse($dateStr);
            }
            $startDate = $date->copy()->startOfWeek();
            $endDate = $date->copy()->endOfWeek();
            $dateLabel = $startDate->format('F d, Y') . ' - ' . $endDate->format('F d, Y');
        } elseif ($frequency == 'monthly') {
            $month = substr($dateStr, 0, 7);
            $date = Carbon::parse($month . '-01');
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $dateLabel = $startDate->format('F Y');
        } elseif ($frequency == 'annually') {
            $year = substr($dateStr, 0, 4);
            $date = Carbon::createFromFormat('Y', $year)->startOfYear();
            $startDate = $date->copy()->startOfYear();
            $endDate = $date->copy()->endOfYear();
            $dateLabel = $startDate->format('Y');
        } elseif ($frequency == 'quarterly') {
            $year = substr($dateStr, 0, 4);
            $quarter = substr($dateStr, -1);
            
            $startDate = Carbon::create($year, ($quarter * 3) - 2, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfQuarter()->endOfDay();
            $dateLabel = 'Q' . $quarter . ' ' . $year;
        }

        $branchName = 'ALL BRANCHES';
        if ($branchId && $branchId != 'all') {
            $branch = Branch::find($branchId);
            if ($branch) {
                $branchName = strtoupper($branch->name);
            }
        }

        if ($type == 'loan') {
            $query = Loan::with(['borrower.details', 'loanProduct.loanType', 'loanCharges', 'payments'])
                         ->where('status', \App\Enums\LoanStatus::APPROVE)
                         ->whereNotNull('disbursed_date')
                         ->whereBetween('disbursed_date', [$startDate, $endDate]);

            if ($branchId && $branchId != 'all') {
                $query->where('branch_id', $branchId);
            }

            $loans = $query->orderBy('disbursed_date', 'asc')->get();

            if ($action == 'pdf') {
                $pdf = Pdf::loadView('report.loan_export', compact('loans', 'branchName', 'dateLabel'))
                          ->setPaper('legal', 'landscape');
                $this->addPdfHeaderFooter($pdf, "SCHEDULE OF DAILY LOAN TRANSACTIONS", $branchName, $dateLabel);
                return $pdf->download('Loan_Transactions_' . $dateLabel . '.pdf');
            } elseif ($action == 'excel') {
                return response(view('report.loan_export', compact('loans', 'branchName', 'dateLabel')))
                    ->header('Content-Type', 'application/vnd.ms-excel')
                    ->header('Content-Disposition', 'attachment; filename="Loan_Transactions_' . $dateLabel . '.xls"');
            }

            return view('report.loan', compact('loans', 'branchName', 'dateLabel'));
        }

        if ($type == 'petty_cash') {
            // Currently no Petty Cash logic exists in the backend, return an empty array of records.
            // When backend is implemented, populate $records with objects containing:
            // date, particulars, cash_in, expense, balance, branch_name
            $records = [];
            
            if ($action == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.petty_cash_export', compact('records', 'branchName', 'dateLabel'))
                          ->setPaper('a4', 'portrait');
                $this->addPdfHeaderFooter($pdf, "PETTY CASH REPORT", $branchName, $dateLabel);
                return $pdf->download('Petty_Cash_Report_' . $dateLabel . '.pdf');
            } elseif ($action == 'excel') {
                return response(view('report.petty_cash_export', compact('records', 'branchName', 'dateLabel')))
                    ->header('Content-Type', 'application/vnd.ms-excel')
                    ->header('Content-Disposition', 'attachment; filename="Petty_Cash_Report_' . $dateLabel . '.xls"');
            }

            return view('report.petty_cash', compact('records', 'branchName', 'dateLabel'));
        }

        if ($type == 'expenses') {
            $query = \App\Models\ExpenseItems::select(
                'expense_items.id',
                'expense_items.expense_id',
                'expense_items.description',
                'expense_items.ref_no',
                'expense_items.amount',
                'expense_items.created_at',
                'vendors.name as vendor_name',
                'vendors.tin',
                'vendors.tax_classification',
                'branch.name as branch_name',
                'expense_categories.name AS category_name',
                'expenses.expense_date'
            )
            ->leftJoin('expenses', 'expenses.id', '=', 'expense_items.expense_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('branch', 'branch.id', '=', 'expenses.branch_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'expense_items.vendor_id')
            ->whereBetween('expenses.expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($branchId && $branchId != 'all') {
                $query->where('expenses.branch_id', $branchId);
            }

            $expenses = $query->orderBy('expenses.expense_date', 'asc')->get();

            if ($action == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report.expenses_export', compact('expenses', 'branchName', 'dateLabel'))
                          ->setPaper('a4', 'landscape');
                $this->addPdfHeaderFooter($pdf, "EXPENSES REPORT", $branchName, $dateLabel);
                return $pdf->download('Expenses_Report_' . $dateLabel . '.pdf');
            } elseif ($action == 'excel') {
                return response(view('report.expenses_export', compact('expenses', 'branchName', 'dateLabel')))
                    ->header('Content-Type', 'application/vnd.ms-excel')
                    ->header('Content-Disposition', 'attachment; filename="Expenses_Report_' . $dateLabel . '.xls"');
            }

            return view('report.expenses', compact('expenses', 'branchName', 'dateLabel'));
        }

        if ($type == 'payment') {
            // transaction_type = withdraw is used for "Payment (Loan)"
            $query = Savings::with(['client.details', 'loan.loanProduct.loanType'])
                            ->where('transaction_type', 'withdraw')
                            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($branchId && $branchId != 'all') {
                $query->whereHas('client.details', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }

            $payments = $query->orderBy('transaction_date', 'asc')->get();
            
            if ($action == 'pdf') {
                $pdf = Pdf::loadView('report.payment_export', compact('payments', 'branchName', 'dateLabel'))
                          ->setPaper('a4', 'landscape');
                $this->addPdfHeaderFooter($pdf, "SCHEDULE OF DAILY PAYMENT TRANSACTIONS", $branchName, $dateLabel);
                return $pdf->download('Payment_Transactions_' . $dateLabel . '.pdf');
            } elseif ($action == 'excel') {
                return response(view('report.payment_export', compact('payments', 'branchName', 'dateLabel')))
                    ->header('Content-Type', 'application/vnd.ms-excel')
                    ->header('Content-Disposition', 'attachment; filename="Payment_Transactions_' . $dateLabel . '.xls"');
            }

            return view('report.payment', compact('payments', 'branchName', 'dateLabel'));
        }

        if ($type == 'cash_request') {
            // Currently no Cash Request logic exists in the backend, return an empty array of records.
            $records = [];
            
            if ($action == 'pdf') {
                $pdf = Pdf::loadView('report.cash_request_export', compact('records', 'branchName', 'dateLabel'))
                          ->setPaper('a4', 'portrait');
                $this->addPdfHeaderFooter($pdf, "CASH REQUEST REPORT", $branchName, $dateLabel);
                return $pdf->download('Cash_Request_Report_' . $dateLabel . '.pdf');
            } elseif ($action == 'excel') {
                return response(view('report.cash_request_export', compact('records', 'branchName', 'dateLabel')))
                    ->header('Content-Type', 'application/vnd.ms-excel')
                    ->header('Content-Disposition', 'attachment; filename="Cash_Request_Report_' . $dateLabel . '.xls"');
            }

            return view('report.cash_request', compact('records', 'branchName', 'dateLabel'));
        }

        return "<div class='alert alert-warning'>Report type '$type' is not yet implemented.</div>";
    }
}
