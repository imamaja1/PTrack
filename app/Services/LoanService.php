<?php

namespace App\Services;

use App\Models\Loan;

class LoanService
{
    /**
     * Get paginated loans of a specific type for a user.
     *
     * @param string $type 'lent' (piutang) or 'borrowed' (hutang)
     */
    public function getLoans(int $userId, string $type, int $perPage = 10)
    {
        return Loan::where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('status', 'desc') // 'unpaid' before 'paid'
            ->orderBy('loan_date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get the total unpaid amount for a specific loan type.
     *
     * @param string $type 'lent' or 'borrowed'
     */
    public function getTotalUnpaid(int $userId, string $type): float
    {
        return Loan::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', 'unpaid')
            ->sum('amount');
    }

    /**
     * Create a new loan record.
     *
     * @param string $type 'lent' or 'borrowed'
     */
    public function create(int $userId, string $type, array $data): Loan
    {
        return Loan::create([
            'user_id'              => $userId,
            'type'                 => $type,
            'borrower_name'        => $data['borrower_name'],
            'amount'               => $data['amount'],
            'status'               => 'unpaid',
            'loan_date'            => $data['loan_date'],
            'expected_return_date' => $data['expected_return_date'] ?? null,
            'description'          => $data['description'] ?? null,
        ]);
    }

    /**
     * Find a loan belonging to a user.
     */
    public function findForUser(int $id, int $userId, string $type): ?Loan
    {
        return Loan::where('id', $id)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->first();
    }

    /**
     * Update a loan belonging to a user.
     */
    public function update(int $id, int $userId, string $type, array $data): bool
    {
        $loan = $this->findForUser($id, $userId, $type);
        if (!$loan) {
            return false;
        }

        return $loan->update([
            'borrower_name'        => $data['borrower_name'],
            'amount'               => $data['amount'],
            'loan_date'            => $data['loan_date'],
            'expected_return_date' => $data['expected_return_date'] ?? null,
            'description'          => $data['description'] ?? null,
        ]);
    }

    /**
     * Toggle the paid/unpaid status of a loan.
     */
    public function toggleStatus(int $id, int $userId, string $type): ?Loan
    {
        $loan = $this->findForUser($id, $userId, $type);
        if (!$loan) {
            return null;
        }

        $loan->status = $loan->status === 'paid' ? 'unpaid' : 'paid';
        $loan->save();

        return $loan;
    }

    /**
     * Delete a loan belonging to a user.
     */
    public function delete(int $id, int $userId, string $type): bool
    {
        $loan = $this->findForUser($id, $userId, $type);
        if (!$loan) {
            return false;
        }

        return $loan->delete();
    }
}
