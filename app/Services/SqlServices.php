<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SqlServices
{
    private $keyword = [
        'DELETE',
        'DROP',
        'INSERT',
        'UPDATE',
        'TRUNCATE',
        'ALTER',
        'CREATE',
        'REPAIR',
        'MERGE'
    ];

    public function validate(?string $sql): string
    {
        if (empty($sql)) {
            return 'SQL is required';
        }

        //阻止其他操作
        foreach ($this->keyword as $val) {
            if (stripos($sql, $val) !== false) {
                return 'Only select queries are allowed';
            }
        }

        if (!Auth::user() || (Auth::user())->name != 'admin') {
            return 'not admin user';
        }

        return 'success';
    }

    /**
     * 编辑sql，获取到总的条数
     */
    public function editSql(string $sql): string
    {

        $countSql = preg_replace('/^SELECT.*FROM/i', 'SELECT COUNT(*) AS total FROM', $sql);

        return $countSql;

    }


}
