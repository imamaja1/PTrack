<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;

class TransactionService
{
    /**
     * Get paginated income transactions for a user.
     */
    public function getIncome(int $userId, int $perPage = 10)
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get paginated expense transactions for a user.
     */
    public function getExpense(int $userId, int $perPage = 10)
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get income categories for a user.
     */
    public function getIncomeCategories(int $userId)
    {
        return Category::where('user_id', $userId)
            ->where('type', 'income')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get expense categories for a user.
     */
    public function getExpenseCategories(int $userId)
    {
        return Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new income transaction.
     */
    public function createIncome(int $userId, array $data): Transaction
    {
        return Transaction::create([
            'user_id'          => $userId,
            'category_id'      => $data['category_id'],
            'type'             => 'income',
            'amount'           => $data['amount'],
            'description'      => $data['description'] ?? null,
            'transaction_date' => $data['transaction_date'],
        ]);
    }

    /**
     * Create a new expense transaction.
     */
    public function createExpense(int $userId, array $data): Transaction
    {
        return Transaction::create([
            'user_id'          => $userId,
            'category_id'      => $data['category_id'],
            'type'             => 'expense',
            'amount'           => $data['amount'],
            'description'      => $data['description'] ?? null,
            'transaction_date' => $data['transaction_date'],
        ]);
    }

    /**
     * Find a transaction belonging to a user.
     */
    public function findForUser(int $id, int $userId): ?Transaction
    {
        return Transaction::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Update a transaction belonging to a user.
     */
    public function update(int $id, int $userId, array $data): bool
    {
        $transaction = $this->findForUser($id, $userId);
        if (!$transaction) {
            return false;
        }

        return $transaction->update([
            'amount'           => $data['amount'],
            'category_id'      => $data['category_id'],
            'description'      => $data['description'] ?? null,
            'transaction_date' => $data['transaction_date'],
        ]);
    }

    /**
     * Delete a transaction belonging to a user.
     */
    public function delete(int $id, int $userId): bool
    {
        $transaction = $this->findForUser($id, $userId);
        if (!$transaction) {
            return false;
        }

        return $transaction->delete();
    }
}
