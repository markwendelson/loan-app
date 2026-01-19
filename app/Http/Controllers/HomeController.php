<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use App\Models\Loan;
use App\Models\Savings;
use App\Enums\LoanStatus;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $top_products = LoanProduct::popular()->get();

        $counts = [];
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $counts = [
                'for_approval' => Loan::where('status', LoanStatus::REQUEST_APPROVE)->orWhere('status', LoanStatus::PENDING)->count(),
                'branch_cash_request' => 0,
                'daily_loan_transaction' => Loan::whereDate('created_at', now())->count(),
                'daily_payment_transaction' => Savings::whereDate('transaction_date', now())->where('transaction_type', 'deposit')->count(),
                'daily_expenses' => \App\Models\Expense::whereDate('expense_date', now())->count()
            ];
        } elseif ($user->hasRole('supervisor')) {
            $counts = [
                'for_approval' => Loan::where('status', LoanStatus::REQUEST_APPROVE)->orWhere('status', LoanStatus::PENDING)->count(),
                'branch_cash_request_approval' => 0,
                'for_released' => Loan::where('status', LoanStatus::APPROVE)->count(),
                'statement_of_account' => 0,
                'daily_loan_transaction' => Loan::whereDate('created_at', now())->count(),
                'daily_payment_transaction' => Savings::whereDate('transaction_date', now())->where('transaction_type', 'deposit')->count(),
                'daily_expenses' => \App\Models\Expense::whereDate('expense_date', now())->count()
            ];
        } elseif ($user->hasRole('staff')) {
            $counts = [
                'for_released' => Loan::where('status', LoanStatus::APPROVE)->count(),
                'for_approval' => Loan::pending()->where('created_by', $user->id)->count(),
                'statement_of_account' => 0,
                'endlisted_clients' => 0,
                'daily_loan_transaction' => Loan::whereDate('created_at', now())->count(),
                'daily_payment_transaction' => Savings::whereDate('transaction_date', now())->where('transaction_type', 'deposit')->count(),
                'daily_expenses' => \App\Models\Expense::whereDate('expense_date', now())->count()
            ];
        }

        return view('dashboard', compact('top_products', 'counts'));
    }

    public function getMetric(Request $request)
    {
        $metric = $request->input('metric');
        $period = $request->input('period', 'daily'); // daily, weekly, monthly, yearly

        $query = null;

        if ($metric === 'loan_transaction') {
            $query = Loan::query();
            $dateColumn = 'loan_date';
        } elseif ($metric === 'payment_transaction') {
            $query = Savings::where('transaction_type', 'deposit');
            $dateColumn = 'transaction_date';
        } elseif ($metric === 'expense_transaction') {
            $query = \App\Models\Expense::query();
            $dateColumn = 'expense_date';
        }

        if (!$query) {
            return response()->json(['count' => 0]);
        }

        switch ($period) {
            case 'weekly':
                $query->whereBetween($dateColumn, [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth($dateColumn, now()->month)
                    ->whereYear($dateColumn, now()->year);
                break;
            case 'yearly':
                $query->whereYear($dateColumn, now()->year);
                break;
            case 'daily':
            default:
                $query->whereDate($dateColumn, now());
                break;
        }

        // Apply role-based filters if consistent with index method
        $user = auth()->user();
        if ($user->hasRole('staff') && $metric === 'loan_transaction') {
            // Note: index() didn't strictly filter 'daily_loan_transaction' by created_by for staff, but 'for_approval' was.
            // Looking at index(): 'daily_loan_transaction' => Loan::whereDate('created_at', now())->count(),
            // So it seems global for all roles based on the current code in index().
            // If we want it to be user specific, we should add it here.
            // For now, I will keep it consistent with the existing index() logic which seems to be global for daily_loan_transaction.
        }

        return response()->json(['count' => $query->count()]);
    }
}
