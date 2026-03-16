<?php

use Ramsey\Uuid\Uuid;

if (!function_exists('simulate_member_transactions')) {
   
    function simulate_member_transactions(
        int $memberId,
        string $memberNo,
        ?string $savingTypeName = null,
        ?string $loanNo = null,
        string $productType = 'saving',
        string $startDate = '2024-01-01'
    ): array {
        $transactions = [];
        $totalCredits = 0;
        $totalDebits = 0;
        $balance = 0;

        $now = new DateTime();
        $date = new DateTime($startDate);

        while ($date <= $now) {
        
            $randomDay = rand(22, 30);
            $transactionDate = clone $date;
            $transactionDate->setDate(
                (int)$date->format('Y'),
                (int)$date->format('m'),
                $randomDay
            );

          
            $transactionType = rand(0, 1) ? 'credit' : 'debit';
            $amount = rand(500, 5000);

            if ($transactionType === 'credit') {
                $totalCredits += $amount;
                $balance += $amount;
            } else {
                $totalDebits += $amount;
                $balance -= $amount;
            }

            $transactions[] = [
                'uuid'             => Uuid::uuid4()->toString(),
                'reference_number' => strtoupper(substr(md5(uniqid()), 0, 8)),
                'member_id'        => $memberId,
                'member_no'        => $memberNo,
                'product_type'     => $productType, 
                'loan_no'          => $productType === 'loan' ? ($loanNo ?? 'LN-' . strtoupper(substr(md5($memberNo), 0, 6))) : null,
                'saving_type'      => $productType === 'saving' ? ($savingTypeName ?? 'Regular Savings') : null,
                'transaction_type' => $transactionType, 
                'amount'           => $amount,
                'date_posted'      => $transactionDate->format('Y-m-d'),
                'notes'            => ucfirst($transactionType) . ' transaction for ' . $transactionDate->format('F Y'),
                'balance_after'    => $balance,
            ];

            
            $date->modify('+1 month');
        }

        return [
            'member_id'      => $memberId,
            'member_no'      => $memberNo,
            'product_type'   => $productType,
            'total_credits'  => $totalCredits,
            'total_debits'   => $totalDebits,
            'balance'        => $balance,
            'transactions'   => $transactions
        ];
    }
}
