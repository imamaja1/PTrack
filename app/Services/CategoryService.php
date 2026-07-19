<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories for a user filtered by type.
     *
     * @param string $type 'income' or 'expense'
     */
    public function getByType(int $userId, string $type): Collection
    {
        return Category::where('user_id', $userId)
            ->where('type', $type)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find a category belonging to a user.
     */
    public function findForUser(int $id, int $userId): ?Category
    {
        return Category::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Create a new category.
     *
     * @param string $type 'income' or 'expense'
     */
    public function create(int $userId, string $type, array $data): Category
    {
        return Category::create([
            'user_id' => $userId,
            'type'    => $type,
            'name'    => $data['name'],
            'color'   => $data['color'],
            'icon'    => $type === 'income' ? 'currency-dollar' : 'shopping-cart',
        ]);
    }

    /**
     * Update a category belonging to a user.
     */
    public function update(int $id, int $userId, array $data): bool
    {
        $category = $this->findForUser($id, $userId);
        if (!$category) {
            return false;
        }

        return $category->update([
            'name'  => $data['name'],
            'color' => $data['color'],
        ]);
    }

    /**
     * Delete a category belonging to a user.
     */
    public function delete(int $id, int $userId): bool
    {
        $category = $this->findForUser($id, $userId);
        if (!$category) {
            return false;
        }

        return $category->delete();
    }
}
